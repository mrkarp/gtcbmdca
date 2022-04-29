<?php
/**
 * Charge On Ship Slack Add-On.
 *
 * @package   SalonX
 * @author    Zach Karp
 * @since     1.0
 * 
 */

class COS_Slack {
	/**
	 * Base Slack Hook URL.
	 *
	 * @since  1.0
	 * @var    string
	 * @access protected
	 */
	protected $hooks_url = 'https://hooks.slack.com/services/';

	protected $emojis = array(
		'success' => ':white_check_mark:', 
		'warning' => ':warning:', 
		'error' => ':rotating_light:'
	);

    /**
	 * Full Slack Channel Hooks URL.
	 *
	 * @since  1.0
	 * @var    array
	 */
    protected $hook;

	/**
	 * Slack Channel notification tag.
	 *
	 * @since  1.0
	 * @var    string
	 */
    public $tag;

	/**
	 * Constructor.
	 *
	 * @since  1.0
	 */
    public function __construct()
    {
		$this->set_hook( get_option('fndtn_charge_on_ship_slack_hook_key') );
    }

	/**
	 * Add Slack Channel Hook.
	 *
	 * @since  1.0
	 * @param string $new_hook
	 * @return bool if the hook was set
	 */
    public function set_hook( $new_hook ) { 
		if ( "" != $new_hook ) {
			$this->hook = $new_hook;
			return true;
		} else {
			return false;
		}
    }

	/**
	 * Get Slack Channel Hooks.
	 *
	 * @since  1.0
	 * @return array $hooks
	 */
	public function get_hook() {
		return $this->hook;
	}

	/**
	 * Send Slack Notification.
	 * https://app.slack.com/block-kit-builder
	 * @since  1.0
	 * @param string $message
	 * @param string $emoji
	 * @return array|WP_Error the response or WP_Error on failure
	 */
	public function send_message( $message, $emoji ) {
		try {
			error_log("[Cos Slack] Sending Message");

			$post_url = $this->hooks_url.$this->hook;
			// Header
			$header = array(
				'type' => 'header', 
				'text' => array( 'type' => 'plain_text', 'text' => $this->emojis[$emoji] . " {$message}", 'emoji' => True )
			);

			$data = array( 'blocks' => $header );

			$body = json_encode( $data , JSON_UNESCAPED_SLASHES );
			error_log("[CoS Slack] Sending notification:");
			error_log($body);

			$args = array( 'headers' => array ('Content-type' => 'application/json'), 'body' => $body );

			$response = wp_remote_post($post_url, $args);

			error_log("[CoS Slack] Response:");
			error_log(json_encode($response));

			return $response; 
		} catch ( WP_Error $e ) {
			WC_Stripe_Logger::log( 'CoS Slack Error: ' . $e->get_error_message() );
			error_log( 'CoS Slack Error: ' . $e->get_error_message() );
		}
	}

	/**
	 * Send Slack Order Updated Notification.
	 * https://app.slack.com/block-kit-builder
	 * @since  1.0
	 * @param int $order_id
	 * @param string $message
	 * @param string $emoji
	 * @return array|WP_Error the response or WP_Error on failure
	 */
	public function send_order_message($order_id, $message, $emoji) {
		try {
			error_log("[CoS Slack] Sending Notification from PostID: {$order_id} of type {$emoji}");

			$post_url = $this->hooks_url.$this->hook;
			$edit_url = get_edit_post_link($order_id, '');

			if ( null === $edit_url ) {
				error_log("[CoS Slack] get_edit_post_link failed, manually concatinating");
				$edit_url = get_site_url() . "/wp-admin/post.php?post={$order_id}&action=edit";
				error_log(print_r( $edit_url, true));
			}
			
			$order = wc_get_order($order_id);

			if ( !$order ) {
				return;
			}
			
			if (strlen($message) > 145) {
				$message = explode(".", $message)[0];
			}

			$order_number = $order->get_meta('_order_number');

			if( empty($order_number) ) {
				$order_number = $order_id;
			}

			$status = $order->get_status();
				
			$billing_first_name = $order->get_billing_first_name();
			$billing_last_name  = $order->get_billing_last_name();
			$billing_amount = $order->get_total();
			$created = $order->get_date_created();
			$created_formatted = date_format($created, 'm/d/y');

			$full_name = $billing_first_name . " " . $billing_last_name;
			
			$action = array(
				'type' => 'button', 
				'text' => array( 
					'type' => 'plain_text', 
					'text' => 'View Order', 
					'emoji' => True
				),
				'value' => 'View Order',
				'url' => $edit_url,
				'action_id' => 'button'
			);

			// Header
			$header = array(
				'type' => 'header', 
				'text' => array( 'type' => 'plain_text', 'text' => $this->emojis[$emoji] . " Order: {$order_number} - {$message}", 'emoji' => True )
			);

			$section = array(
				'type' => 'section',
				'fields' => array(
					array('type' => 'mrkdwn', 'text' => "*Created:* {$created_formatted}"),
					array('type' => 'mrkdwn', 'text' => "*Ordered by:* {$full_name}"),
					array('type' => 'mrkdwn', 'text' => "*Amount Charged:* {$billing_amount}"),
					array('type' => 'mrkdwn', 'text' => "*Status:* {$status}")
				),
				'accessory' => $action
			);

			$data = array( 'blocks' => array( $header, $section ) );

			$body = json_encode( $data , JSON_UNESCAPED_SLASHES );
			error_log("[CoS Slack] Notification Body:");
			error_log($body);

			$args = array( 'headers' => array ('Content-type' => 'application/json'), 'body' => $body, 'timeout' => 60);

			$response = wp_remote_post($post_url, $args);

			error_log("[CoS Slack] Response:");
			error_log(json_encode($response));

			if(isset($response->errors)) {
				$backup_email = get_option('fndtn_charge_on_ship_slack_backup_email');

				$to = "";
				if( isset($backup_email) ) { 
					$to = $backup_email;
				} else {
					$to = get_bloginfo('admin_email');
				}

				$subject = "Order: {$order_number} - {$message}";
				
				$body = "*Created:* {$created_formatted} <br/>" . 
				"*Ordered by:* {$full_name} <br/>" . 
				"*Amount Charged:* {$billing_amount} <br/>" .
				"*Status:* {$status}";

				$headers = array('Content-Type: text/html; charset=UTF-8');

				try {
					error_log('[CoS Slack] Sending Backup Email');
					$sent = wp_mail( $to, $subject, $body, $headers );
				} catch ( PHPMailer\PHPMailer\Exception $e ) {
					error_log($e);
					return false;
				}
			}

			return $response;
		} catch ( WP_Error $e ) {
			WC_Stripe_Logger::log( 'CoS Slack Error: ' . $e->get_error_message() );
			error_log( 'CoS Slack Error: ' . $e->get_error_message() );
		}
	}
}
