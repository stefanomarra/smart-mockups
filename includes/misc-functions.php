<?php

/**
 * Mockup Functions
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.5
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/functions
 * @author     Stefano <stefano.marra1987@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves custom slug option
 *
 * @since 1.0.5
 * @return string Custom Slug
 */
function sm_get_custom_slug() {
	$post_types = Smart_Mockups_Setup::post_types();
	$custom_slug = get_option('smartmockups_slug', $post_types[SMART_MOCKUPS_POSTTYPE]['rewrite']['slug']);

	return apply_filters( 'smartmockups_custom_slug', $custom_slug );
}

/**
 * Retrieves notification option
 *
 * @since 1.1.0
 * @return bool
 */
function sm_is_notification_enabled() {
	return get_option('smartmockups_notifications', 0);
}

/**
 * Retrieves notification recurrence (frequency) option
 *
 * @since 1.1.0
 * @return string
 */
function sm_get_notification_recurrence( $default = 'hourly' ) {
	return get_option( 'smartmockups_notifications_recurrence', $default );
}

/**
 * Checks if Guest Display Name is required or not
 *
 * @since 1.1.0
 * @return bool
 */
function sm_is_guest_display_name_required( $mockup_id = 0 ) {
	if ( $mockup_id === 0 ) {
		$mockup_id = get_the_ID();
	}

	$mockup = sm_get_mockup( $mockup_id );

	if ( is_user_logged_in() )
		return false;

	if ( ! $mockup->is_enabled( 'guest' ) )
		return true;

	$post_types = Smart_Mockups_Setup::post_types();

	return $post_types[SMART_MOCKUPS_POSTTYPE]['post_meta']['guest_enabled']['default']?false:true;
}

/**
 * Check if user can delete feedbacks
 *
 * @since 1.2.0
 * @return bool
 */
function sm_can_user_delete_feedback($user_id = null, $feedback_id = null) {
	// If no user_id is passed try to get current user from session
	if ( $user_id == null) {
		$user = wp_get_current_user();

		// If user is not logged in return false
		if ( $user->ID == 0 ) {

			// Check if the user is the owner of feedback_id
			if ( !is_null($feedback_id) && in_array( $feedback_id, sm_get_user_feedbacks() ) ) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			$user_id = $user->ID;
		}
	}

	// Allow only the admin to be able to delete a feedback
	if ( is_super_admin( $user_id ) ) {
		return true;
	}
	else {
		// Check if the user is the owner of feedback_id
		if ( !is_null($feedback_id) && in_array( $feedback_id, sm_get_user_feedbacks() ) ) {
			return true;
		}
		else {
			return false;
		}
	}
}

/**
 * Get user feedback IDs from cookie
 *
 * @since 1.2.0
 * @return array of feedback IDs
 */
function sm_get_user_feedbacks() {
	if ( isset( $_COOKIE['sm_my_fdbks'] ) ) {
		$feedbacks = json_decode( base64_decode( $_COOKIE['sm_my_fdbks']), true );
	}
	else {
		$feedbacks = array();
	}

	return apply_filters( 'smartmockups_get_user_feedbacks', $feedbacks );
}