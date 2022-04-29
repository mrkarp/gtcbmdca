<?php
    /**
     * Script Name: Charge On Ship Stripe Payment and API Handler
     * Description: Custom payment hook when an Order is completed, this handles Stripes Capture-Later functionality
     * https://stripe.com/docs/payments/capture-later
     * Version: 1.1.0
     * Author: Zach Karp
     * Author URI: https://www.atomicdata.com
     * 
     * @package SalonX
    */

    // Defines
    define("DEBUG", get_option('fndtn_charge_on_ship_debug'));
    define("SLACK_DEBUG", get_option('fndtn_charge_on_ship_slack_debug'));

    // Order of operations, need to have plugins loaded before calling / referencing other classes
    add_action( 'plugins_loaded', 'fndtn_charge_on_ship_init' );

    /**
	 * Main init function of the system
	 *
	 * @since 1.0.0
	 */
    function fndtn_charge_on_ship_init () {

        /**
         * Add To: Recipient @ WooCommerce Completed Order Email
         */
        
        add_filter( 'woocommerce_email_recipient_customer_completed_order', 'cos_order_completed_email_add_to', 9999, 3 );
        
            function cos_order_completed_email_add_to( $email_recipient, $email_object, $email ) {
            $email_recipient .= ',' . get_option('fndtn_charge_on_ship_order_complete_email');
            return $email_recipient;
        }

        /**
         * The main function that processes Orders when marked complete
         * Hooked onto woocommerce_order_status_completed
         * 
         * @since 1.0.0
         * @param  int $order_id
         * @return void
         */
        function charge_on_ship_hook($order_id) {
            // Ensure it isnt already processed
            $cos_touched = is_tagged( $order_id );

            // Check for plugin option enabled
            if( get_option( 'fndtn_charge_on_ship_enabled' ) && !$cos_touched ) { 
                error_log("----- Order set to Completed, processing -----");

                // Get Order
                $order = wc_get_order( $order_id );
            
                // Make sure Order is valid
                if ( ! is_object( $order ) ) {
                    WC_Stripe_Logger::log( '[CoS Error]: Order is not an object! This is a critical failure!' );
                    return;
                }

                // Do not touch subscriptions / renewals
                if ( $order->is_created_via("subscription") )  {
                    return;
                }

                // Vars
                $slack = new COS_Slack();

                // Check for Coupons
                $coupon_codes = $order->get_coupon_codes($order);
                if (count($coupon_codes) > 0) {
                    error_log("[COS] Coupon Found on Order");
                    $order->add_order_note("<b style='color:red'>[CoS] Coupon(s) Found</b> <br/> Passed on processing this Order.");
                    tag_order_meta($order_id);
                    $slack->send_order_message($order_id, "[CoS] Skipped for coupon(s) found", "error");
                    return;
                }

                // Check for missing transaction_id
                $transaction_id = get_post_meta($order_id, "_transaction_id", true);
                if (!isset($transaction_id) || empty($transaction_id)) {
                    error_log("[COS] Missing Transaction ID, passing Order");
                    $order->add_order_note("<b style='color:red'>[CoS] Missing transaction_id</b><br/> Passed on processing this Order.");
                    tag_order_meta($order_id);
                    $slack->send_order_message($order_id, "[CoS] Skipped for missing transaction id", "error");
                    return;
                }

                // Get Payment Intent for Order
                $stripe_intent_id = $order->get_meta( '_stripe_intent_id' );
                $source_id = $order->get_meta( '_stripe_source_id' );
                $stripe_intent = WC_Stripe_API::request( array(), "payment_intents/$stripe_intent_id", 'GET' );

                error_log("Stripe Intent attached to Order: ");
                error_log(json_encode($stripe_intent));

                // Process Payment Intent
                if ( $stripe_intent ) {
                    // Error checks
                    if ( ! empty( $stripe_intent->error ) ) {
                        // Payment Intent invalid
                        if( "resource_missing" === $stripe_intent->error->code && str_contains($stripe_intent->error->message, 'No such payment') ) { 
                            // https://stripe.com/docs/error-codes#resource-missing
                            error_log("----- resource_missing -----");

                            $result = create_new_payment_intent_and_capture($order, $source_id);

                            if($result) {
                                $order->add_order_note( "<b style='color:green'>[CoS] Successful</b><br/> Authorized and captured." );
                                tag_order_meta( $order_id );
                                $slack->send_order_message( $order_id , "Successfully authorized and captured.", "success");
                                return;
                            }

                        // Order edited     
                        } elseif ( "amount_too_large" === $stripe_intent->error->code ) { 
                            // https://stripe.com/docs/error-codes#amount-too-large
                            error_log("----- amount_too_large -----");

                            $result = create_new_payment_intent_and_capture($order, $source_id);
                            
                            if($result) {
                                $order->add_order_note( "<b style='color:green'>[CoS] Successful</b><br/> Authorized and captured." );
                                tag_order_meta( $order_id  );
                                $slack->send_order_message( $order_id, "Successfully authorized and captured.", "success" );
                                return;
                            }
                        }
                    }
                    // Status checks
                    if( !empty( $stripe_intent->status ) ) {
                        // Canceled is an expired Payment Intent
                        if ( 'canceled' === $stripe_intent->status ) {
                            error_log("----- canceled -----");
                            $result = create_new_payment_intent_and_capture($order, $source_id);

                            if($result) {
                                $order->add_order_note( "<b style='color:green'>[CoS] Successful</b><br/> Authorized and captured." );
                                tag_order_meta( $order_id );
                                $slack->send_order_message( $order_id, "Successfully authorized and captured.", "success" );
                                return;
                            }
                        // Succeeded, nothing to do
                        } elseif ( 'succeeded' === $stripe_intent->status ) {
                            error_log("----- succeeded -----");
                            $order->add_order_note( "<b style='color:green'>[CoS] Already charged</b><br> No authorization needed." );
                            $slack->send_order_message( $order_id, "Already charged, no authorization needed.", "success" );
                            return;
                        } 
                    } else {
                        $slack->send_order_message( $order_id, "PaymentIntent status is null. {$stripe_intent_id}", "error" );
                        $order->add_order_note( "<b style='color:red'>[CoS] Stripe Error</b><br/> PaymentIntent status is null " . $stripe_intent_id );
                    }
                } else {
                    $slack->send_order_message( $order_id, "No Payment Intent attached to Order. {$stripe_intent_id}", "error" );
                    $order->add_order_note( "<b style='color:red'>[CoS] Stripe Error</b><br/> No Payment Intent attached to Order. " . $stripe_intent_id );
                }
                error_log("----- Done processing -----");
            }
        }

        // Hook onto WooCommerce Order Complete
        add_action('woocommerce_order_status_completed', 'charge_on_ship_hook', 12, 3);

        /**
         * Create a new Payment Intent and confirm
         *
         * @since 1.0.0
         * @param  object $order
         * @param  int $source_id
         * @return bool $successful
        */
        function create_new_payment_intent_and_capture( $order, $source_id ) {
            try {
                if ( !isset(WC()->session) ) {
                    WC()->session = new WC_Session_Handler();
                    WC()->session->init();
                }
                $slack = new COS_Slack();
                if ( class_exists('WC_Gateway_Stripe') ) {
                    // Declare extension helpers
                    $gateway = new WC_Gateway_Stripe();
                    $handler = new WC_Stripe_Order_Handler();

                    error_log("----- Creating new Payment Intent and Capturing -----");
                    $post_id = $order->get_id();
                    error_log(print_r("PostID: {$post_id}", true));
                    error_log(print_r("SourceID: {$source_id}", true));

                    
                    $is_legacy = $gateway->is_type_legacy_card($source_id);
                    error_log(print_r("Legacy: {$is_legacy}", true));

                    if(!$is_legacy) {
                        // Get Payment source
                        $prepared_source = $gateway->get_source_object( $source_id ); // failing because of sources/card_xxxxx, 
                        $prepared_source->source = $source_id;
                    } else {
                        // Create legacy source
                        $customer_id = get_user_meta( $order->get_customer_id(), '_stripe_customer_id', true );
                        $prepared_source = (object) [
                            "source" => $source_id,
                            "customer" => $customer_id
                        ];
                    }
                    error_log(print_r("CustomerID: " . $order->get_customer_id(), true));
                    error_log(print_r("Prepared Source: ", true));
                    error_log("Source: ". print_r($prepared_source->source, true));
                    error_log("Customer StripeID". print_r($prepared_source->customer, true));

                    // Create new Payment Intent
                    $intent = $gateway->create_intent( $order, $prepared_source );
                    error_log("Created Intent: ");
                    error_log(json_encode($intent));

                    // Save new Intent to Order
                    $gateway->save_intent_to_order( $order, $intent );
                    error_log("Saved new Intent to order");

                    // Confirm Intent
                    $intent = $gateway->confirm_intent( $intent, $order, $prepared_source );
                    error_log("Confirmed new Intent");

                    // Check for confirmation errors and process
                    if ( !empty($intent->error) ) {
                        error_log(json_encode($intent->error));
                        WC_Stripe_Logger::log( 'CoS Error: New Payment Intent confirmation failed! ' . $intent->error->message );
                        do_action( 'wc_gateway_stripe_process_payment_error', $intent->error, $order );

                        $order->add_order_note( "<b style='color:red'>[CoS] Stripe Error</br><br/> Code:" . $intent->error->code . ", " . $intent->error->message );
                        $slack->send_order_message( $order->get_id(), '[CoS] Stripe Error: ' .  $intent->error->message, "error" );
                        // Payment Intent Confiramtion error, Order failed
                        $order->set_status("processing");
                        $order->save();
                        return False;
                    } 

                    // Process response and update Order notes
                    $response = end( $intent->charges->data );

                    error_log("Intent Response");
                    error_log(json_encode($response));

                    $message = sprintf( __( "<b style='green:red'>[CoS] New Stripe Charge</b><br/> Authorized (Charge ID: %s).", "woocommerce-gateway-stripe" ), $response->id );
                    $order->add_order_note( $message );
                    error_log("Added Order note and ping Slack");

                    // Manually Capture new Payment Intent
                    $handler->capture_payment($order->get_id());
                    error_log("Manually Capture new Payment Intent");

                    // Update Order with new Payment Intent Transaction ID
                    $order->set_transaction_id($response->id);
                    error_log(print_r("New TransactionID: {$response->id}", true));

                    // Unlock the Order
                    $gateway->unlock_order_payment( $order );
                    error_log("----- Order unlocked and processed -----");
                    return True;
                } else {
                    add_action('admin_notices', 'wc_not_loaded');
                }
            } catch ( WC_Stripe_Exception $e ) {
                WC_Stripe_Logger::log( 'CoS Error: ' . $e->getMessage() );
                do_action( 'wc_gateway_stripe_process_payment_error', $e, $order );
                $slack->send_order_message( $order->get_id(), "Error: " . $e->getMessage(), "error" );
            }
        }

        /**
         * Processes Digital Orders, when all Order Items are flagged _virtual
         * Hooked onto woocommerce_order_status_changed
         * 
         * @since 1.1.0
         * @param  int $order_id
         * @return void
         */
        function charge_on_ship_digital_order_hook( $order_id ) {

            // Ensure it isnt already processed
            $cos_touched = is_tagged( $order_id );
            error_log("charge_on_ship_digital_order_hook touched {$cos_touched}");

            // Check for plugin option enabled
            if( get_option( 'fndtn_charge_on_ship_enabled' ) && !$cos_touched ) {
                error_log("Processing digital order...");

                // Get Order
                $order = wc_get_order( $order_id );
                $captured = ( "yes" === get_post_meta( $order_id, '_stripe_charge_captured', true ) ) ? True : False;
            
                // Make sure Order is valid
                if ( ! is_object( $order ) ) {
                    WC_Stripe_Logger::log( '[CoS Error]: Digital Order is not an object! This is a critical failure!' );
                    return;
                }

                // Do not touch subscriptions / renewals
                if ( $order->is_created_via("subscription") )  {
                    return;
                }

                // Do not touch cancelled, failed, refunded statuses
                if ( in_array( $order->get_status(), array( 'processing', 'on-hold' ), true ) && !$captured ) {
                    // Digital Products are auto-completed
                    $order_items = $order->get_items();
                    $order_item_count = $order->get_item_count();

                    if ( !empty($order_items) ) {
                        error_log("ITERATING THROUGH ITEMS");
                        $number_of_virtual_items = 0;

                        foreach( $order_items as $item ) {
                            $product_id = $item->get_product_id();
                            $is_virtual = get_post_meta( $product_id, "_virtual", true );

                            if ( "yes" === $is_virtual ) {
                                error_log(print_r("Virtual Product Found!", true));
                                $number_of_virtual_items++;
                            }
                        }

                        if ($order_item_count === $number_of_virtual_items ) {
                            error_log(print_r("Pure Virtual Order Found!", true));
                            $result = charge_on_ship_capture_payment_intent($order);

                            if ( $result ) {
                                // Tag after successful capture
                                tag_order_meta( $order->get_id() );
                                error_log(print_r($order->get_status(), true));

                                if ( in_array( $order->get_status(), array( 'processing', 'on-hold' ), true ) ) {
                                    error_log(print_r("Setting completed manually", true));

                                    $order->set_status("completed");
                                    $order->save();
                                    
                                    $slack = new COS_Slack();

                                    $order->add_order_note( "<b style='green:green'>[CoS] Digital only</b><br/> Processed to completed.");
                                    $slack->send_order_message( $order_id, "Digital only, processed to completed.", "success" );
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }
        // Hook onto WooCommerce Order Changed
        add_action('woocommerce_order_status_changed', 'charge_on_ship_digital_order_hook', 12, 3);

        /**
         * Processes Digital Orders, when all Order Items are flagged _virtual
         * Hooked onto woocommerce_order_status_changed
         * 
         * @since 1.0.0
         * @param  WC_Order $order
         * @return bool $successful
         */
        function charge_on_ship_capture_payment_intent( $order ) {
            try {
                if ( !isset(WC()->session)) {
                    WC()->session = new WC_Session_Handler();
                    WC()->session->init();
                }

                if ( class_exists('WC_Stripe_Order_Handler') ) {
                    // Declare extension helpers
                    $handler = new WC_Stripe_Order_Handler();
                    // Capture Payment Intent
                    $handler->capture_payment( $order->get_id() );
                    // Custom tag Order
                    return True;
                } else {
                    add_action('admin_notices', 'wc_not_loaded');
                }
            } catch ( WC_Stripe_Exception $e ) {
                WC_Stripe_Logger::log( 'CoS Error: ' . $e->getMessage() );
                do_action( 'wc_gateway_stripe_process_payment_error', $e, $order );
                $slack = new COS_Slack();
                $slack->send_order_message( $order->get_id(), "Error: " . $e->getMessage(), "error" );
                return False;
            }
        }

        /**
         * Checks if an Order is tagged
         *
         * @since 1.1.0
         * @param  int $order_id
         * @return bool $is_tagged
        */
        function is_tagged( $order_id ) {
            $tagged = get_post_meta( $order_id, '_charged_on_ship', true );
            if (null !== $tagged) {
                if ( "1" === $tagged ) { 
                    return True;
                } else {
                    return False;
                }
                return False;
            }
        }
        
        /**
         * Tags a processed Order with a new meta_key and value
         *
         * @since 1.0.0
         * @param  int $order_id
         * @param  string $meta_key
         * @param  mixed $value
         * @return int $order_id
        */
        function tag_order_meta($order_id, $meta_key = '_charged_on_ship', $value = true) {
            $order = wc_get_order( $order_id );
            $order->update_meta_data( $meta_key, $value );
            return $order->save();
        }
    }
?>
