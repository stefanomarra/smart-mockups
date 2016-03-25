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

	public function get_queue_info() {
		$queue = $this->get_queue();
		$queue_info = array(
			'new_feedbacks'           => 0,
			'new_feedbacks_comments'  => 0,
			'new_discussion_comments' => 0,
			'new_approvals'           => 0,
			'total_changes' 		  => 0
		);

		foreach ( $queue as $notification ) {
			switch ( $notification['status'] ) {
				case 'new_feedback_saved':
					$queue_info['new_feedbacks']++;
				case 'feedback_updated':
					$queue_info['new_feedbacks_comments']++;
					$queue_info['total_changes']++;
					break;

				case 'new_discussion_comment_saved':
					$queue_info['new_discussion_comments']++;
					$queue_info['total_changes']++;
					break;

				case 'approval_saved':
					$queue_info['new_approvals']++;
					$queue_info['total_changes']++;
					break;
			}
		}

		return $queue_info;
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
	 * Get heading based on queue
	 *
	 * @since 1.1.0
	 */
	public function get_notification_heading() {
		$queue = $this->get_queue();
		$queue_info = $this->get_queue_info();

		return $queue_info['total_changes'] . ' New Feedbacks';
	}

	/**
	 * Get body based on queue
	 *
	 * @since 1.1.0
	 */
	public function get_notification_body() {
		$queue = $this->get_queue();
		$queue_info = $this->get_queue_info();

		return 'You have ' . $queue_info['total_changes'] . ' new feedbacks:
' . $queue_info['new_feedbacks'] . ' New Feedbacks
' . $queue_info['new_feedbacks_comments'] . ' New Feedback Comments
' . $queue_info['new_discussion_comments'] . ' New Discussion Comments
' . $queue_info['new_approvals'] . ' Approvals';
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
			$subject = $this->get_notification_heading();
			$message = $this->get_notification_body();

			if ( $email->send( $to, $subject, $message ) ) {
				$this->erase_queue();

				return true;
			}
		}

		return false;
	}
}