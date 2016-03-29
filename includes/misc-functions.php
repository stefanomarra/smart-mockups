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

