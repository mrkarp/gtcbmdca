<?php
    /**
     * Name: Plugin Stripe Options Page
     * Description: Admin Options Page
     * Version: 1.0
     * Author: Zach Karp
     * Author URI: https://www.atomicdata.com
     */


    // created and display the page
    function fndtn_charge_settings_page() {
        // Page vars
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        $rd_args = array(
            'meta_key' => '_charged_on_ship',
            'meta_value' => 1,
            'posts_per_page' => 10, // post per page
            'paged' => $paged,
            'orderby' => 'date_completed',
            'order' => 'DESC'
        );
        $orders = wc_get_orders($rd_args);
        $index = 0; 

    ?>
    <?php if(get_option('fndtn_charge_on_ship_debug')): ?>
        <div class="update-nag notice notice-warning inline"><b>Charge On Ship</b> debugging is enabled! This will write to the error_log() destination. (typically apaches logs)</div>
    <?php endif; ?>
        <div class="wrap">
            <div class="welcome-panel">
            <h1>FNDTN Charge On Ship</h1>
            <em>This process controls when an Order is completed but the Stripe Payment Intent has expired. 
            The methods check on the Payment Intents Status and will either update it to be confirmed and used, or create a new Payment Intent and then automatically capture it.</em>

            <form method="post" action="options.php" name="options">
                <?php settings_fields( 'fndtn-charge-plugin-settings-group' ); ?>
                <?php do_settings_sections( 'fndtn-charge-plugin-settings-group' ); ?>
                <br/>
                <h4 style="margin:4px 0 0 0">Enable the Charge On Ship process</h4>
                <input name="fndtn_charge_on_ship_enabled" type="checkbox" value="1" <?php checked( '1', get_option( 'fndtn_charge_on_ship_enabled' ) ); ?> /> Enable
                <br/>
                <h4 style="margin:4px 0 0 0">Order Complete Email (CC'd)</h4>
                <input type="text" name="fndtn_charge_on_ship_order_complete_email" value="<?php echo esc_attr( get_option('fndtn_charge_on_ship_order_complete_email') ); ?>" style="width:50%"/>

                <?php submit_button(); ?>
            </form>
        </div>

        <div class="welcome-panel">
            <h2 class="p-0">Charged On Ship Orders</h2>
            <em>Note: This is the top 10 most recent Orders, sorted by date_completed.</em>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Captured</th>
                        <th>Status</th>
                        <th>View/Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if( $orders ): foreach( $orders  as $order  ) : ?>
                        <tr class="<?php echo ($index % 2 == 0) ? '' : 'alternate' ?>">
                            <td><?php echo $order->get_id() ?></td>
                            <td><?php echo $order->get_order_number() ?></td>
                            <td><?php echo $order->get_billing_email(); ?></td>
                            <td><?php echo $order->get_date_paid() ?></td>
                            <td style="color: <?php echo ($order->get_status() === 'completed' ? 'green' : 'red')  ?>"><?php echo $order->get_status() ?></td>
                            <td><button class="button button-primary" onclick="window.location='<?php echo get_edit_post_link($order->get_id()) ?>';">View</button></td>
                        </tr>
                    <?php $index++; endforeach; endif; ?>
                </tbody>

                <?php /*
                <?php $total_pages = count($orders); ?> <p><?php echo $total_pages; ?> Pages</p>
                <?php if($total_pages > 1): $current_page = max(1, get_query_var('paged')); ?>
                        <?php echo paginate_links(array(
                                    'base' => get_pagenum_link(1) . '%_%',
                                    'format' => '/page/%#%',
                                    'current' => $current_page,
                                    'total' => $total_pages,
                                    'prev_text'    => __('« prev'),
                                    'next_text'    => __('next »'),
                                )); 
                        ?>
                    <?php endif; ?>

                */ ?>
                <br/>
            </table>
            <br/>
        </div>
    </div>
<?php } ?>