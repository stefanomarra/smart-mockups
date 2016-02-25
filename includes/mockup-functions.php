<?php

/**
 * Mockup Functions
 *
 * @link       http://www.stefanomarra.com
 * @since      1.1.0
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/functions
 * @author     Stefano <stefano.marra1987@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves a mockup post object
 *
 * @since 1.1.0
 * @param int $mockup Mockup ID
 * @return Smart_Mockups_Post
 */
function sm_get_mockup( $mockup = 0 ) {
	if ( is_numeric( $mockup ) ) {
		$mockup = new Smart_Mockups_Post( $mockup );

		if ( !$mockup || SMART_MOCKUPS_POSTTYPE !== get_post_type( $mockup ) ) {
			return null;
		}
	}
	else {
		$args = array(
				'post_type' 	=> SMART_MOCKUPS_POSTTYPE,
				'name' 			=> $mockup,
				'numberposts'	=> 1
			);
		$mockup = get_posts( $args );

		if ( $mockup ) {
			$mockup = $mockup[0];
		}

		$mockup = new Smart_Mockups_Post( $mockup->ID );
	}

	if ( $mockup ) {
		return $mockup;
	}

	return null;
}

/**
 * Retrieve mockup image by mockup ID
 *
 * @since 1.1.0
 * @param int $mockup_id Mockup ID
 * @return array Mockup Image
 */
function sm_get_mockup_image( $mockup_id = 0 ) {
	$attach_id = get_post_meta( $mockup_id, 'mockup_image_id', true);

	$mockup = array(
		'id'  => $attach_id,
		'url' => ''
	);

	if ( $mockup['id'] ) {
		$mockup['url'] = wp_get_attachment_url( $mockup['id'] );
	}

	return apply_filters( 'smartmockups_mockup_image', $mockup );
}

/**
 * Retrieve all mockup feedbacks by mockup ID
 *
 * @since 1.1.0
 * @param int $mockup_id Mockup ID
 * @return array Feedbacks
 */
function sm_get_mockup_feedbacks( $mockup_id = 0 ) {
	$feedbacks = get_post_meta( $mockup_id, '_feedbacks', true);

	return apply_filters( 'smartmockups_feedbacks', $feedbacks );
}

/**
 * Retrieve mockup discussion by mockup ID
 *
 * @since 1.1.0
 * @param int $mockup_id Mockup ID
 * @return array Discussion
 */
function sm_get_mockup_discussion( $mockup_id = 0 ) {
	$discussion = get_post_meta( $mockup_id, '_discussion', true);

	return apply_filters( 'smartmockups_discussion', $discussion );
}

/**
 * Retrieve mockup approval signature by mockup ID
 *
 * @since 1.1.0
 * @param int $mockup_id Mockup ID
 * @return array Approval Signature Data
 */
function sm_get_mockup_approval_signature( $mockup_id = 0 ) {
	$approval_signature = get_post_meta( $mockup_id, '_approval', true);

	return apply_filters( 'smartmockups_approval_signature', $approval_signature );
}

/**
 * Retrieve mockup help text by mockup ID
 *
 * @since 1.1.0
 * @param int $mockup_id Mockup ID
 * @return string Help Text
 */
function sm_get_mockup_help_text( $mockup_id = 0 ) {
	$help_text = get_post_meta( $mockup_id, 'help_text_content', true);

	return apply_filters( 'smartmockups_help_text', $help_text );
}

/**
 * Retrieve mockup custom permalink by mockup ID
 *
 * @since 1.1.0
 * @param int $mockup_id Mockup ID
 * @return string Mockup Custom Permalink
 */
function sm_get_mockup_custom_permalink( $mockup_id = 0 ) {
	$post = get_post( $mockup_id );
	if ( $post && SMART_MOCKUPS_POSTTYPE == $post->post_type ) {
		$custom_permalink = get_site_url() . '/' . sm_get_custom_slug() . '/' . $post->post_name . '/';
	}
	else {
		return false;
	}

	return apply_filters( 'smartmockups_custom_permalink', $custom_permalink );
}

