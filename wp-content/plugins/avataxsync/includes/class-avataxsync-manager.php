<?php

/**
 * The Avataxsync_Manager class.

 * @since      1.0.0
 * @package    Avataxsync
 * @subpackage Avataxsync/includes
 * @author     Zach Karp <zkarp@atomicdata.com>
 */



class Avataxsync_Manager extends WC_Background_Updater
{
	protected $loader;

	public function __construct($data)
	{
		error_log('---------- [TaxSync] Task Init ----------');
		$this->task($data);
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task($data = null)
	{

		error_log('---------- [TaxSync] Task Starting ----------');
		$this->process_orders($data);
		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete()
	{
		error_log('---------- [TaxSync] Task Complete ----------');

		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

	public function process_orders($sort_order = "ASC")
	{
		error_log('---------- [TaxSync] Syncing Start Order By: ' . $sort_order . ' ----------');

		$args = array(
			'limit' => 9999,
			'return' => 'ids',
			'status' => 'wc-on-hold',
			'order' => $sort_order
		);

		# Get on-hold orders
		$query = new WC_Order_Query($args);
		$orders = $query->get_orders();

		error_log("[TaxSync] Orders found: " . strval(count($orders)));
		# iterate through each order
		for ($x = 0; $x < count($orders); $x++) {
			$order = wc_get_order($orders[$x]);
			# ensure order is an object
			if ($order) {
				$order_id = $order->get_id();
				error_log("[TaxSync] ReTaxing Order (PostID): " . strval($order_id));
				error_log("[TaxSync] " . strval($x) . " / " . strval(count($orders)));

				# capture initial creation date
				$initial_date = $order->get_date_created()->date('Y-m-d H:i:s');
				#error_log("[TaxSync] Initial Order Date: " . $initial_date);

				# update creation date to now and save order
				$now = date('Y-m-d H:i:s');
				$order->set_date_created($now);
				$order->save();
				#error_log("[TaxSync] New Order Date: " . $order->get_date_created()->date('Y-m-d H:i:s'));

				# init AvaTax and submit for processing
				# $updated_order = $order_handler->process_order($order);
				$result = wc_avatax()->get_order_handler()->calculate_order_tax($order, false, true);

				$order->set_date_created(date($initial_date));
				$order->save();

				if (isset($result)) {
					#error_log(json_encode($result));
					#do_action('woocommerce_saved_order_items', $order_id);
					$order->add_order_note("[TaxSync] Re-calculated taxes at " . date("Y-m-d H:i:s", time() - date("Z")));
				} else {
					$order->add_order_note("[TaxSync] Re-calculate failed!");
				}

				# sleep introduced to help aleviate timeout
				sleep(1);
			} else {
				error_log('[TaxSync] Failed getting Order');
			}
		}
		error_log('---------- [TaxSync] Syncing End ----------');
	}

	public function resync_cron_job()
	{
		//$this->process_orders()();
		error_log("Cron Executed");
	}
}
