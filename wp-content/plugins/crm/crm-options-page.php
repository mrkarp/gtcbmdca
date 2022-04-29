<?php
    /**
     * Name: Plugin Options Page
     * Description: Admin Options Page.
     * Version: 1.0
     * Author: Zach Karp
     * Author URI: https://www.atomicdata.com
     */

    // create custom plugin settings menu
    add_action('admin_menu', 'gci_crm_create_menu');

    function gci_crm_create_menu() {
        //create new top-level menu
        add_menu_page('GCI CRM', 'GCI CRM Settings', 'administrator', __FILE__, 'gci_crm_settings_page' , plugins_url('/images/icon.png', __FILE__) );
        //call register settings function
        add_action( 'admin_init', 'register_gci_crm_plugin_settings' );
    }

    // register all of the options on the page and database
    function register_gci_crm_plugin_settings() {
        //register our settings
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_enable' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_dts_url' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_gcprovider_endpoint' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_opt_host' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_device_username' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_device_password' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_device_id' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_dts_scope' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_api_key' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_grant_type' );
        register_setting( 'gci-crm-plugin-settings-group', 'gci_crm_os_type' );
    }

    // created and display the page
    function gci_crm_settings_page() {
    ?>
    <div class="wrap">
    <h1>GCI CRM Integration</h1>
    <form method="post" action="options.php" name="options">
        <?php settings_fields( 'gci-crm-plugin-settings-group' ); ?>
        <?php do_settings_sections( 'gci-crm-plugin-settings-group' ); ?>
        <input name="gci_crm_enable" type="checkbox" value="1" <?php checked( '1', get_option( 'gci_crm_enable' ) ); ?> /> Enable
        <table class="form-table">
            <h2 class="p-0">DTS Settings</h2>
            <tr valign="top">
                <th scope="row">DTS URL</th>
                <td>
                    <input class="full-input" type="text" name="gci_crm_dts_url" value="<?php echo esc_attr( get_option('gci_crm_dts_url') ); ?>" />
                    <small class="d-block"><em>The DTS token endpoint</em></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Opt Host</th>
                <td>
                    <input class="full-input" type="text" name="gci_crm_opt_host" value="<?php echo esc_attr( get_option('gci_crm_opt_host') ); ?>" />
                    <small class="d-block"><em>the FQDN of the endpoin(s)</em></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">DTS Device Username</th>
                <td>
                    <input class="full-input" type="text" name="gci_crm_device_username" value="<?php echo esc_attr( get_option('gci_crm_device_username') ); ?>" />
                    <small class="d-block"><em>The DTS token device username</em></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    DTS Device Password
                    <button type="checkbox" id="device-password-toggle">Show</button>
                </th>
                <td>
                    <input class="full-input" type="password" name="gci_crm_device_password" value="<?php echo esc_attr( get_option('gci_crm_device_password') ); ?>" id="device-password"/>
                    <small class="d-block"><em>The DTS token device password</em></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">DTS Device ID</th>
                <td>
                    <input class="full-input" type="text" name="gci_crm_device_id" value="<?php echo esc_attr( get_option('gci_crm_device_id') ); ?>" />
                    <small class="d-block"><em>The DTS device ID, e.g. gcprovider</em></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">DTS Scope</th>
                <td>
                    <input class="full-input" type="text" name="gci_crm_dts_scope" value="<?php echo esc_attr( get_option('gci_crm_dts_scope') ); ?>" />
                    <small class="d-block"><em>The DTS token scope, e.g. deviceapi device profile openid</em></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">DTS Grant Type</th>
                <td>
                    <input class="full-input" type="text" name="gci_crm_grant_type" value="<?php echo esc_attr( get_option('gci_crm_grant_type') ); ?>" />
                    <small class="d-block"><em>The DTS grant type, e.g. device</em></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Device OS Type</th>
                <td>
                    <input class="full-input" type="text" name="gci_crm_os_type" value="<?php echo esc_attr( get_option('gci_crm_os_type') ); ?>" />
                    <small class="d-block"><em>The DTS token OS type, e.g. windows</em></small>
                </td>
            </tr>
        </table>

        <table class="form-table">
        <h2>GcProviderV2</h2>
        <tr valign="top">
                <th scope="row">GcProvider Endpoint</th>
                <td>
                    <input class="full-input" type="text" name="gci_crm_gcprovider_endpoint" value="<?php echo esc_attr( get_option('gci_crm_gcprovider_endpoint') ); ?>" />
                    <small class="d-block"><em>The service endpoint, including the query string requirement, <strong style="color: orange"> e.g. ?email=</strong></em></small>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    GCI API Key
                    <button class="ml-1" type="checkbox" id="api-key-toggle">Show</button>
                </th>
                <td>
                    <input class="full-input" type="password" name="gci_crm_api_key" value="<?php echo esc_attr( get_option('gci_crm_api_key') ); ?>" id="api-key"/>
                    <small class="d-block"><em>The API key used to call GCI endpoints</em></small>
                </td>
            </tr>
            <tr>
                <td>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
    </div>
<?php } ?>