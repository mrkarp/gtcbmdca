<?php

/**
 * Plugin Name: Charge On Ship
 * Plugin URI: 
 * Description: Custom payment hook when an Order is completed, this handles Stripes Capture-Later functionality using Payment Intents
 * https://stripe.com/docs/payments/capture-later
 * Version: 1.1.7
 * Author: Zach Karp
 * Author URI: http://www.atomicdata.com
 * 
 * @package SalonX
 */

// includes
include 'includes/fndtn-charge-on-ship-woocommerce.php';
include 'includes/fndtn-charge-on-ship-options-page.php';
include 'includes/fndtn-charge-on-ship-slack-options-page.php';
include 'includes/fndtn-charge-on-ship-slack.php';

/**
 * Enqueue styles and javascript
 */
function fndtn_charge_enqueue()
{
    wp_enqueue_style('admincssfile', plugin_dir_url(__FILE__) . 'css/style.css', false);
    wp_enqueue_script('adminscript', plugin_dir_url(__FILE__) . 'js/main.js', array('jquery'), 1.1, true);
}
add_action('admin_enqueue_scripts', 'fndtn_charge_enqueue');

// create custom plugin settings menu
add_action('admin_menu', 'fndtn_charge_create_menu');

function fndtn_charge_create_menu()
{
    //create new top-level menu
    add_menu_page('Charge On Ship', 'Charge On Ship', 'administrator', 'fndtn-cos-sub-menu-stripe', false, plugins_url('/images/icon.png', __FILE__));
    add_submenu_page('fndtn-cos-sub-menu-stripe', 'Stripe Settings', 'Stripe Settings', 'administrator', 'fndtn-cos-sub-menu-stripe', 'fndtn_charge_settings_page');
    add_submenu_page('fndtn-cos-sub-menu-stripe', 'Slack Settings', 'Slack Settings', 'administrator', 'fndtn-cos-sub-menu-slack', 'fndtn_charge_slack_page');
    //call register settings function
    add_action('admin_init', 'register_fndtn_charge_plugin_settings');
}

// register all of the options on the page and database
function register_fndtn_charge_plugin_settings()
{
    //register our settings
    register_setting('fndtn-charge-plugin-settings-group', 'fndtn_charge_on_ship_enabled');
    //register_setting( 'fndtn-charge-plugin-settings-group', 'fndtn_charge_on_ship_recalculate_taxes' );
    register_setting('fndtn-charge-plugin-settings-group', 'fndtn_charge_on_ship_order_complete_email');

    register_setting('fndtn-charge-plugin-settings-group-slack', 'fndtn_charge_on_ship_slack_enabled');
    register_setting('fndtn-charge-plugin-settings-group-slack', 'fndtn_charge_on_ship_slack_hook_key');
    register_setting('fndtn-charge-plugin-settings-group-slack', 'fndtn_charge_on_ship_slack_backup_email');
}
