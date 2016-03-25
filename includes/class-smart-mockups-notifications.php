<?php

/**
 * Notifications API
 *
 * This class handles all notifications
 *
 * @link       http://www.stefanomarra.com
 * @since      1.1.0
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/includes
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Mockups_Notifications {

	/**
	 * option name of the notification queue
	 *
	 * @since 1.1.0
	 */
	private $option_name = '_smartmockups_notifications_queue';

	/**
	 * notification queue
	 *
	 * @since 1.1.0
	 */
	private $queue;

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'notification_event', array( $this, 'send_email' ) );
	}

	public function update_schedule( $old_value, $new_value ) {

		// Schedule notifications event if notifications is active
		if ( $new_value ) {
			wp_schedule_event( time(), 'hourly', 'notification_event' );
		}

		// Clear scheduled notifications event is not active
		else {
			wp_clear_scheduled_hook('notification_event');
		}
	}

	/**
	 * Get Queue
	 *
	 * @since 1.1.0
	 */
	public function get_queue() {
		if ( ! $this->queue ) {
			$this->queue = get_option( $this->option_name );
		}

		if ( ! is_array( $this->queue ) ) {
			$this->queue = array();
		}

		return $this->queue;
	}

	/**
	 * Add to Queue
	 *
	 * @since 1.1.0
	 */
	public function add_to_queue($notification) {
		if ( ! sm_is_notification_enabled() )
			return;

		$queue = $this->get_queue();

		$queue[] = $notification;

		update_option( $this->option_name, $queue );
	}

	/**
	 * Erase Queue
	 *
	 * @since 1.1.0
	 */
	public function erase_queue() {
		delete_option( $this->option_name );
	}

	/**
	 * Send Email
	 *
	 * @since 1.1.0
	 */
	public function send_email() {
		$queue = $this->get_queue();

		if ( count( $queue ) ) {
			$email = new Smart_Mockups_Emails();

			$to = get_option( 'admin_email' );
			$subject = count( $queue ) . ' New Feedbacks';
			$message = 'You have ' . count( $queue ) . ' new mockup feedbacks';

			if ( $email->send( $to, $subject, $message ) ) {
				$this->erase_queue();

				return true;
			}
		}

		return false;
	}
}