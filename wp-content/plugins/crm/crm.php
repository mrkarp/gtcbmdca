<?php
    /**
     * Plugin Name: GCI CRM Integration
     * Plugin URI: 
     * Description: GCI CRM integration using gcProviderV2
     * Version: 1.0
     * Author: Zach Karp
     * Author URI: http://www.atomicdata.com
     * 
     * @package SalonX
     */

    // includes
    include 'gci-crm-options-page.php';
    include 'gci-dts-helper.php';
    include 'gci-crm-helper.php';
    include 'gci-wc-helper.php';

    /**
	 * Enqueue styles and javascript
	 */
    function gci_crm_enqueue() {
        wp_enqueue_style( 'admincssfile', plugin_dir_url( __FILE__ ) . 'css/style.css', false );
        wp_enqueue_script( 'adminscript', plugin_dir_url( __FILE__ ) . 'js/main.js', array ( 'jquery' ), 1.1, true);
    }
    add_action( 'admin_enqueue_scripts', 'gci_crm_enqueue' );

    // logging
    if (!function_exists('write_log')) {
        function write_log($log) {
            if (true === WP_DEBUG) {
                if (is_array($log) || is_object($log)) {
                    error_log(print_r($log, true));
                } else {
                    error_log($log);
                }
            }
        }
    }

 ?>