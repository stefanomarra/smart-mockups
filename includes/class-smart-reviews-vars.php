<?php

/**
 * Handles and defines all the plugin variables
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/includes
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Reviews_Vars {

	/**
	 * Plugin custom post types
	 *
	 * @since    1.0.0
	 */
	public static function post_types() {

		return array(
			'smartreview' => array(
				'labels' 	=> array(
						'name'          => __( 'Smart Reviews', SMART_REVIEWS_DOMAIN ),
						'singular_name' => __( 'Mockup', SMART_REVIEWS_DOMAIN ),
						'all_items' 	=> __( 'All Mockups', SMART_REVIEWS_DOMAIN ),
						'new_item'      => __( 'Add New Mockup', SMART_REVIEWS_DOMAIN ),
						'add_new'       => __( 'Add New', SMART_REVIEWS_DOMAIN ),
						'add_new_item'  => __( 'Add New Mockup', SMART_REVIEWS_DOMAIN ),
						'edit_item'     => __( 'Edit Mockup', SMART_REVIEWS_DOMAIN ),
						'view_item'     => __( 'View Mockup', SMART_REVIEWS_DOMAIN ),
						'search_items'  => __( 'Search Mockups', SMART_REVIEWS_DOMAIN ),
					),
				'rewrite'     => array( 'slug' => 'smart-reviews' ),
				'public'      => true,
				'has_archive' => false,
				'menu_icon'   => 'dashicons-exerpt-view',
				'supports' 	  => array( 'title' ),
				'post_meta'	=> array(
						'mockup_image_id'
					)
			)
		);
	}

}
