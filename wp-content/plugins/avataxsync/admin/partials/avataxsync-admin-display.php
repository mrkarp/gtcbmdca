<?php

/**
 * Name: Plugin Options Page
 * Description: Admin Options Page.
 * Version: 1.0
 * Author: Zach Karp
 * Author URI: https://www.atomicdata.com
 */

// create custom plugin settings menu
add_action('admin_menu', 'avataxsync_create_menu');

function avataxsync_create_menu()
{
    //create new top-level menu
    add_menu_page('AvaTaxSync', 'AvaTaxSync', 'administrator', __FILE__, 'avataxsync_settings_page', 'dashicons-image-rotate');
    //call register settings function
    add_action('admin_init', 'register_avataxsync_plugin_settings');
}

// register all of the options on the page and database
function register_avataxsync_plugin_settings()
{
    //register our settings
    register_setting('avataxsync-plugin-settings-group', 'frequency');
}

// created and display the page
function avataxsync_settings_page()
{
    $rd_args = array(
        'meta_key' => '_retaxed',
        'meta_value' => 1,
    );
    $orders = wc_get_orders($rd_args);

    if (isset($_POST['asc'])) {
        $am_asc = new Avataxsync_Manager('asc');
    }
    if (isset($_POST['desc'])) {
        $am_desc = new Avataxsync_Manager('desc');
    }
    //echo '<pre>' . print_r(_get_cron_array()) . '</pre>';
?>
    <div class="wrap">
        <h1>AvaTaxSync</h1>
        <?php /*
        <h2>Next Sync: <?php echo date('Y-m-d H:i:s', wp_next_scheduled('avataxsync_cron')); ?></h2>
        <h3>Number Of Orders ReTaxed: <?php echo count($orders); ?></h3>
        <form method="post" action="options.php" name="options">
            <?php settings_fields('avataxsync-plugin-settings-group'); ?>
            <?php do_settings_sections('avataxsync-plugin-settings-group'); ?>
            <label for="frequency">Frequency Of Sync (Weeks)</label>
            <input type="number" name="frequency" value="<?php echo esc_attr(get_option('frequency')); ?>" style="width:7%" />
            <?php submit_button(); ?>
        </form>
        <br />
        */ ?>
        <form method="post" action="#">
            <?php submit_button("ReSync Ascending", "primary", "asc", false); ?>
            <?php submit_button("ReSync Descending", "secondary", "desc", false); ?>
        </form>
    </div>
<?php } ?>