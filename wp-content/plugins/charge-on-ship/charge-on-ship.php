<?php

/**
 * Plugin Name: Charge On Ship
 * Plugin URI: 
 * Description: Custom payment hook when an Order is completed, this handles Stripes Capture-Later functionality using Payment Intents
 * https://stripe.com/docs/payments/capture-later
 * Version: 1.1.7
 * Author: Zach Karp
 * 
 * @package Karp
 */

// includes
include 'includes/charge-on-ship-woocommerce.php';
include 'includes/charge-on-ship-options-page.php';
include 'includes/charge-on-ship-slack-options-page.php';
include 'includes/charge-on-ship-slack.php';

/**
 * Enqueue styles and javascript
 */
function charge_enqueue()
{
    wp_enqueue_style('admincssfile', plugin_dir_url(__FILE__) . 'css/style.css', false);
    wp_enqueue_script('adminscript', plugin_dir_url(__FILE__) . 'js/main.js', array('jquery'), 1.1, true);
}
add_action('admin_enqueue_scripts', 'charge_enqueue');

// create custom plugin settings menu
add_action('admin_menu', 'charge_create_menu');

function charge_create_menu()
{
    //create new top-level menu
    add_menu_page('Charge On Ship', 'Charge On Ship', 'administrator', 'cos-sub-menu-stripe', false, plugins_url('/images/icon.png', __FILE__));
    add_submenu_page('cos-sub-menu-stripe', 'Stripe Settings', 'Stripe Settings', 'administrator', 'cos-sub-menu-stripe', 'charge_settings_page');
    add_submenu_page('cos-sub-menu-stripe', 'Slack Settings', 'Slack Settings', 'administrator', 'cos-sub-menu-slack', 'charge_slack_page');
    //call register settings function
    add_action('admin_init', 'register_charge_plugin_settings');
}

// register all of the options on the page and database
function register_charge_plugin_settings()
{
    //register our settings
    register_setting('charge-plugin-settings-group', 'charge_on_ship_enabled');
    //register_setting( 'charge-plugin-settings-group', 'charge_on_ship_recalculate_taxes' );
    register_setting('charge-plugin-settings-group', 'charge_on_ship_order_complete_email');

    register_setting('charge-plugin-settings-group-slack', 'charge_on_ship_slack_enabled');
    register_setting('charge-plugin-settings-group-slack', 'charge_on_ship_slack_hook_key');
    register_setting('charge-plugin-settings-group-slack', 'charge_on_ship_slack_backup_email');
}
