<?php
    /**
     * Name: DTS Helper
     * Description: DTS Bearer Token.
     * Version: 1.0
     * Author: Zach Karp
     * Author URI: https://www.atomicdata.com
     * 
     * @package SalonX
     */

     /**
	 * Gets a Bearer token.
	 *
	 * @return cURL response
	 */
    function get_bearer_token() {
        global $token; // should be DTS token

        $curl = curl_init();
        $dts_url = get_option('gci_crm_dts_url');
        $device_username = get_option('gci_crm_device_username');
        $device_password = get_option('gci_crm_device_password');
        $device_id = get_option('gci_crm_device_id');
        $dts_scope = get_option('gci_crm_dts_scope');
        $api_key = get_option('gci_crm_api_key');
        $grant_type = get_option('gci_crm_grant_type');
        $os_type = get_option('gci_crm_os_type');
        $authorizationInfo = base64_encode($device_username . ":" .  $device_password);

        curl_setopt_array($curl, array(
        CURLOPT_URL => $dts_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "deviceid=".$device_id."&apikey=".$api_key."&ostype=".$os_type."&grant_type=".$grant_type."&scope=deviceapi%20device%20profile%20openid",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic ".$authorizationInfo,
            "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            $GLOBALS['token'] = false;
            $_SESSION['user_id'] = false;
        } else {
            $token = json_decode($response) -> {'access_token'};
            // TEMP
            global $bearerToken;
            $bearerToken['token'] = $token;
            $_SESSION['user_id'] = get_current_user_id();
            do_action('get_salons_ids');
            return $response;
        }
        
    }
    // If enabled, fire on set_current_user hook
    if(get_option('gci_crm_enable')) {
        add_action('set_current_user', 'get_bearer_token');
    }
?>