<?php
    /**
     * Name: Plugin Options Page
     * Description: Admin Options Page
     * Version: 1.0
     * Author: Zach Karp
     * Author URI: https://www.atomicdata.com
     */

    // created and display the page
    function fndtn_charge_slack_page() {
        if( isset( $_POST['test_slack'] ) ) {
            $slack = new COS_Slack();

            $order_number = $_POST['order_number']; 
            $type = $_POST['type']; 
            $message = $_POST['message']; 

            if ( "success" === $type ) {
                $slack->send_order_message( $order_number, $message, "success" );
            } else if ("warning" === $type) {
                $slack->send_order_message( $order_number, $message, "warning" );
            } else if ("error" === $type) {
                $slack->send_order_message( $order_number, $message, "error" );
            }
        }
    ?>
    <div class="wrap">
        <div class="welcome-panel">
            <h1>FNDTN Charge On Ship - Slack Settings</h1>
            <em>Stop the truck!</em>

            <form method="post" action="options.php" name="options">
                <?php settings_fields( 'fndtn-charge-plugin-settings-group-slack' ); ?>
                <?php do_settings_sections( 'fndtn-charge-plugin-settings-group-slack' ); ?>
                <p><em>Enabling Slack will send notifications to the configured Channel via its Hook.</em></p>
                <input name="fndtn_charge_on_ship_slack_enabled" type="checkbox" value="1" <?php checked( '1', get_option( 'fndtn_charge_on_ship_slack_enabled' ) ); ?> /> Enable

                <p><em>The Slack hook key to broadcast notifications, comes after /services/ e.g. T02N999N3/B07777702D/fBs1rfdgOJgPzAil7iaae2VK</em></p>
                <input type="text" name="fndtn_charge_on_ship_slack_hook_key" value="<?php echo esc_attr( get_option('fndtn_charge_on_ship_slack_hook_key') ); ?>" style="width:50%"/>
                
                <p><em>The Slack backup email incase sending notifications fails.</p>
                <input type="text" name="fndtn_charge_on_ship_slack_backup_email" value="<?php echo esc_attr( get_option('fndtn_charge_on_ship_slack_backup_email') ); ?>" style="width:50%"/>
                
                <br/>
                <?php submit_button(); ?>
            </form>

            <p><em>Test Slack notifications</em></p>
            <form method="post" action="#" >
                <div>
                    <p>Type of notification:</p>
                    <input type="radio" id="success" name="type" value="success">
                    <label for="success">Success</label>
                    <input type="radio" id="warning" name="type" value="warning">
                    <label for="warning">Warning</label>
                    <input type="radio" id="error" name="type" value="error">
                    <label for="error">Error</label>
                </div>
                <div style="padding-top:1em;">
                    <label for="order_number">Order Number</label>
                    <input type="number" name="order_number" />
                </div>
                <div style="padding-top:1em;">
                    <label for="message">Message</label>
                    <input type="text" name="message"  style="width:50%;" />
                </div>
                <?php submit_button("Test", "primary", "test_slack"); ?>

            </form>
        </div>
    </div>
<?php } ?>