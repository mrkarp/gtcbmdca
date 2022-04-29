<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              atomicdata.com
 * @since             1.1.1
 * @package           Avataxsync
 *
 * @wordpress-plugin
 * Plugin Name:       AvaTaxSync
 * Plugin URI:        atomicdata.com
 * Description:       Sheduled task(s) to re-calculate taxes for On-Hold Orders.
 * Version:           1.1.1
 * Author:            Zach Karp
 * Author URI:        atomicdata.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       avataxsync
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('AVATAXSYNC_VERSION', '1.1.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-avataxsync-activator.php
 */
function activate_avataxsync()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-avataxsync-activator.php';
	Avataxsync_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-avataxsync-deactivator.php
 */
function deactivate_avataxsync()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-avataxsync-deactivator.php';
	Avataxsync_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_avataxsync');
register_deactivation_hook(__FILE__, 'deactivate_avataxsync');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-avataxsync.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_avataxsync()
{

	$plugin = new Avataxsync();
	$plugin->run();
}
run_avataxsync();


function weekly_recurrence($schedules)
{
	$schedules['weekly'] = array(
		'interval' => 604800, // The number in second of one week
		'display' => __('Once Weekly')
	);

	return $schedules;
}

//add_filter('cron_schedules', 'weekly_recurrence');

function resync_deactivate()
{
	wp_clear_scheduled_hook('avataxsync_cron');
}

/* Re-Enable this when CRON is ready
add_action('init', function () {
	add_action('avataxsync_cron', 'resync_cron_job');
	register_deactivation_hook(__FILE__, 'resync_deactivate');

	if (!('avataxsync_cron')) {
		wp_schedule_event(time(), 'weekly', 'avataxsync_cron');
	}
});
*/