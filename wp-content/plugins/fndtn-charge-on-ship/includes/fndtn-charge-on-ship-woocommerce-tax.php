<?php
    /**
     * Script Name: Charge On Ship Taxes
     * 
     * @package SalonX
    */

    //add_action( 'plugins_loaded', 'fndtn_charge_on_ship_taxes_init' );

    /**
	 * Main init function of the system
	 *
	 * @since 1.0.0
	 */
    function fndtn_charge_on_ship_taxes_init () {
        
        /**
         * Recalculates taxes on Order status changed, should take first priority. 
         * Hook: onto woocommerce_order_status_changed
         *
         * @since 1.0.0
         * @param  object $order
         * @return bool if the taxes were recalculated
        */
        function recalculate_taxes_hook( $hook_order ) {
            if( get_option('fndtn_charge_on_ship_recalculate_taxes') ) {
                $order = wc_get_order( $hook_order );
                $previous_tax = $order->get_total_tax();

                if ( ! is_object( $order ) ) {
                    WC_Stripe_Logger::log( '[CoS Error]: Order is not an object! This is a critical failure!' );

                    return false;
                } else {
                    $order->calculate_taxes();
                    $new_tax = $order->get_total_tax();
                    $order->add_order_note( "[CoS] Recalculated taxes, previous: {$previous_tax}, new: {$new_tax}");

                    return true;
                }
            }
        }
        //add_action('woocommerce_order_status_changed', 'recalculate_taxes_hook', 1, 3);
    }
?>