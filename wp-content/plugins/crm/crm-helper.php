<?php
    /**
     * Script Name: CRM Helper
     * Description: Currently only gets ContactsSalons via gcProviderV2.
     * Version: 1.0
     * Author: Zach Karp
     * Author URI: https://www.atomicdata.com
     * 
     * @package SalonX
     */

    /**
	 * cURL call to get current users salonids and save to MySQL.
	 *
	 * @return cURL response
	 */
    function get_contact_salons() {
    
        $current_user_email = wp_get_current_user() -> user_email;//NEED TO ADD CHECK LOGIC
        // Get DTS vars
        $gcprovider_endpoint = get_option('gci_crm_gcprovider_endpoint' );
        $opt_host = get_option('gci_crm_opt_host' );

        $curl = curl_init();
        $token = isset($GLOBALS['token']) ? $GLOBALS['token'] : false;
        
        if($token && $current_user_email) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $gcprovider_endpoint.$current_user_email,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Accept-Encoding: gzip, deflate",
                    "Authorization: Bearer ". $token,
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Length: 0",
                    "Host: ". $opt_host,
                    "cache-control: no-cache"
                ),
            ));
        
            $response = curl_exec($curl);
            $err = curl_error($curl);
           
            curl_close($curl);
    
            if ($err) {
                write_log($err);
            } else {
                processResponse($response);
            }
        }
    }
    add_action('get_salons_ids', 'get_contact_salons');

    // Process response from /GetContactsSalons
	function processResponse($response)
	{
		$salonsIDs = array();
		$current_user_id = get_current_user_id();

		if ($response) {
			$salonContacts = json_decode($response); // creates an array of SalonContacts

			if ($salonContacts) {
				foreach ($salonContacts as $salonContact) {
					if ($salonContact->{'SalonStatus'} == "Opened" || $salonContact->{'SalonStatus'} == "Coming Soon") {
						array_push($salonsIDs, $salonContact->{'SalonNumber'});
					}
				}
				if ($salonsIDs) {
					sort($salonsIDs);
					$salon_ids_string = implode(', ', $salonsIDs);
					process_user_salonids($salon_ids_string);
				}
			}
		} else {
			return false;
		}
	}

    /**
	 * Check for existing salonIDs
	 *
	 * @param string    $salon_ids_string
	 *
	 * @return bool
	 */
    function process_user_salonids($salon_ids_string) { //SELECT * FROM wp_usermeta where `user_id` = 1 and `meta_key` = 'salonids';
        global $wpdb;// needed to talk to DB
        $current_user_id = get_current_user_id();

        // Check if current user has salons ids
        $sql_statement = "SELECT * FROM `wp_usermeta` where `user_id` = " . $current_user_id . " and `meta_key` = 'salonids'";
        $existing_ids = $wpdb->get_results($wpdb->prepare($sql_statement,"post"));
        //echo 'User has existing salon ids: ' . (count($existing_ids) > 0 ? 'Yes' : 'No');
        
        // process SQL results
        $existing_salon_ids_array = array();
        if(count($existing_ids) > 0) {
            // User has existing IDs
            foreach($existing_ids as $existing_id) {
                array_push($existing_salon_ids_array, $existing_id->{'meta_value'});
            }
            $existing_salon_ids_string = implode(', ', $existing_salon_ids_array); // create new string to compare
            //echo 'Existing salon id string: ' . $existing_salon_ids_string;
        }

        // Check if user has existing salonids
        if(count($existing_ids) > 0 && !empty($existing_salon_ids_string)) { // user has salonids, just check again!
            if($existing_salon_ids_string == $salon_ids_string) {
                // incoming is the same as db, do nothing
            } else {
                // update users salonids
                $update = update_salonids($current_user_id, $salon_ids_string);
                if(!$update) {
                    write_log("Updating Salons Failed!" . $wpdb->print_error());
                    echo "Updating Salons Failed!" . $wpdb->print_error();
                }
                return $update;
            }
        } else { // user has no salonids
            // insert new row of salonids
            $insert = insert_salonids($current_user_id, $salon_ids_string);
            if(!$insert) {
                write_log("Inserting Salons Failed!" . $wpdb->print_error());
                echo "Inserting Salons Failed!" . $wpdb->print_error();
            }
            return $insert;
        }
    }

    /**
	 * Insert MySQL rows
	 *
	 * @param int    $user_id
	 * @param string    $salon_ids_string
	 *
	 * @return bool
	 */
    function insert_salonids($user_id, $salon_ids_string) {
        global $wpdb;// needed to talk to DB, maye pass instead of create?
        //echo "Inserting Salons: " . $salon_ids_string;

        $insert = $wpdb->insert( //NEED TO ADD CHECK LOGIC https://codex.wordpress.org/Class_Reference/wpdb
            'wp_usermeta', 
            array( 
                'user_id' => $user_id, 
                'meta_key' => 'salonids' ,
                'meta_value' => $salon_ids_string
            ),
            array(//NEED TO ADD CHECK LOGIC
              '%d',
              '%s',
              '%s'
            ) 
        );
        //echo "Inserted Salons: " . $insert;

        return $insert;
    }

    /**
	 * Update MySQL rows
	 *
	 * @param int    $user_id
	 * @param string    $salon_ids_string
	 *
	 * @return bool
	 */
    function update_salonids($user_id, $salon_ids_string) {
        global $wpdb;// needed to talk to DB
        //echo "Inserting Salons: " . $salon_ids_string;

        $data_update = array('meta_value' => $salon_ids_string);
        $data_where = array('user_id' => $user_id, 'meta_key' => 'salonids');
        $updated = $wpdb->update('wp_usermeta', $data_update, $data_where);

        return $updated;
    }

    /**
	 * Gets a users salon ids.
	 *
	 * @param int    $user_id
	 *
	 * @return array
	 */
    function get_salonids($user_id) {
        global $wpdb;// needed to talk to DB
        if(is_int($user_id)) {
            $user_salon_ids = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM wp_usermeta where user_id = %d and meta_key = %s", array($user_id , 'salonids'))
            );
            if($user_salon_ids) {
                $salon_ids = $user_salon_ids->{'meta_value'};
                return $salon_ids;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

?>