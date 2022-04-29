<?php
    /**
     * Script Name: Woocommerece Helper
     * Description: Assist in all things Woo
     * Version: 1.0
     * Author: Zach Karp
     * Author URI: https://www.atomicdata.com
	 * 
	 * @package SalonX
     */

	/**
	 * Creates a <select> element containing salonIDs
	 *
	 * @param array    $salon_ids
	 * @param string   $select_id
	 * @param string   $select_classes
	 *
	 * @return HTML
	 */
	function woocommerce_salon_id_dropdown($salon_ids, $select_id, $select_classes) {
		if($salon_ids > 0) {
			$select = '<select id="'.$select_id.'" class="'.$select_classes.'" name="'.$select_id.'" data-placeholder="Choose Salons...">';
			foreach( $salon_ids as $id ) {
				$select .= '<option value="'.$id.'">'.$id.'</option>';
			};
			$select .= '<option value="Other">Other</option>';
			$select .= '</select>';
			return $select;
		} else {
			return woocommerce_salon_id_input();
		}
	}

	/**
	 * Creates a multiple <select> element containing salonIDs
	 *
	 * @param array    $salon_ids
	 * @param string   $select_id
	 * @param string   $select_classes
	 *
	 * @return HTML
	 */
	function woocommerce_salon_id_multiple_select($salon_ids, $select_id, $select_classes) {
		if($salon_ids > 0) {
			asort($salon_ids);
			$selected_salon_ids = (isset($_GET["SalonIDs"])) ? $_GET["SalonIDs"] : false;
			$select = '<select id="'.$select_id.'" class="'.$select_classes.'" name="'.$select_id.'[]" multiple data-placeholder="Choose Salons..." required>';
			foreach( $salon_ids as $id ) {
				$isSelected = "";
				if($selected_salon_ids > 0) {
					if(strpos($selected_salon_ids, $id) !== false) {
						$isSelected = "selected";
					}
				}
				$select .= '<option '.$isSelected.' value="'.$id.'">'.$id.'</option>';
			};
			if($selected_salon_ids) {
				foreach( (array)$selected_salon_ids as $id ) {
					if(in_array($id, $salon_ids) == false && $id != 'null')
					{
						$select .= '<option selected value="'.$id.'">'.$id.'</option>';
					}
				}
			}
			$select .= '<option value="Other">Other</option>';
			$select .= '</select>';
			return $select;
		} else {
			return woocommerce_salon_id_input('salon-id-input', 'salon-id-dropdown', 'Enter Salon...');
		}
	}

	/**
	 * Creates a multiple <select> element containing salonIDs
	 *
	 * @param array    $input_id
	 * @param string   $input_classes
	 * @param string   $input_placeholder
	 *
	 * @return HTML
	 */
	function woocommerce_salon_id_input($input_id, $input_classes, $input_placeholder) {
		$label = '<div><span><small><em>Comma separated list</em></small></span>';
		$input = '<input type="text" id="'.$input_id.'" name="'.$input_id.'" placeholder="'.$input_placeholder.'" class="'.$input_classes.'"/>'.$label .'</div>';
		return $input;
	}

	/**
	 * Creates a dropdown containing all products to filter
	 *
	 * @param array    $input_id
	 * @param string   $input_classes
	 * @param string   $input_placeholder
	 *
	 * @return HTML
	 */
	function woocommerce_all_products_dropdown($select_id, $select_classes) {
		$products = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'orderby' => 'name', 'order' => 'ASC', 'tag' => array( 'searchable' ) ) );
		$selected_product_ids = 0;
		if (isset($_GET["ProductIDs"])) {
			$selected_product_ids = is_array($_GET["ProductIDs"]) ? $_GET["ProductIDs"] : explode(',', $_GET["ProductIDs"]);
		}
			
		$select = '<select id="'.$select_id.'" class="'.$select_classes.'" name="'.$select_id.'[]" multiple data-placeholder="Choose Products...">';
		
		foreach( $products as $product ) {
			$isSelected = "";
			$productID = $product->get_id();
			if($selected_product_ids > 0 && in_array ($productID, $selected_product_ids)) {				
				//echo '<script>console.log(' . json_encode(strpos($selected_product_ids, $productID)) . ');</script>';
				$isSelected = "selected";
			}
			$select .= '<option value="'.$product->get_id().'"' .$isSelected.'>'.$product->get_name().'</option>';
		};

		$select .= '</select>';

		return $select;
	}


	/**
	 * Gets Orders for the specified customer
	 *
	 * @param string   $customer_id
	 *
	 * @return HTML
	 */
	function woocommerce_get_orders($customer_id, $min_date, $max_date, $salons, $products, $sort, $status, $order_page) {
		$sql = get_query_order_summary($customer_id, $min_date, $max_date, $salons, $products, $sort, $status, $order_page);
		include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
		include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
		include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php';
		global $wpdb;
		//echo $sql;
		$result = $wpdb->get_results($sql);
		//echo $sql;
		return $result;
	}

	/**
	 * Gets Orders for the specified customer
	 *
	 * @param string   $customer_id
	 *
	 * @return HTML
	 */
	function woocommerce_get_subscriptions($customer_id, $min_date, $max_date, $salons, $products, $sort) {
		//echo '<script>console.log("stevetest");</script>';
		$sql = get_query_subscription_summary($customer_id, $min_date, $max_date, $salons, $products, $sort);

		include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
		include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
		include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php';
		global $wpdb;

		$result = $wpdb->get_results(
			$wpdb->prepare(
				$sql,
				"post"
			)
		);
		//echo $sql;
		//echo '<script>console.log(' . json_encode($result) . ');</script>';
		return $result;
	}

?>