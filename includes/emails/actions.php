<?php

/**
 * Email Actions
 *
 * @link       http://www.stefanomarra.com
 * @since      1.1.0
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/includes/emails
 * @author     Stefano <stefano.marra1987@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) exit;


function sm_send_notification_email( $feedback ) {

	if ( false )
		return;

	$email = new Smart_Mockups_Emails();
	$email->send( 'stefano.marra1987@gmail.com', 'Feedback Notification', $feedback['status'] . ' ' . $feedback['comment'] );
}
add_action( 'smartmockups_after_save_feedback', 'sm_send_notification_email', 999, 1 );