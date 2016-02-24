<?php

/**
 * Mockup API
 *
 * @link       http://www.stefanomarra.com
 * @since      1.1.0
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/includes
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Mockups_Post {

	/**
	 * Array of known post meta
	 *
	 * @var Array
	 */
	private $post_meta = array();

	/**
	 * Constructor.
	 *
	 * @param WP_Post|object $post Post object.
	 */
	public function __construct( $post_id ) {
		$post = get_post( $post_id );
		foreach ( get_object_vars( $post ) as $key => $value )
			$this->$key = $value;

		// Fetch meta data
		$post_types = Smart_Mockups_Setup::post_types();
		foreach ( $post_types[$this->post_type]['post_meta'] as $key => $value ) {
			$this->$key = get_post_meta( $this->ID, $key, true);
			$this->post_meta[] = $key;
		}
	}

	/**
	 * Get post mockup
	 *
	 * @since 1.1.0
	 */
	public function get_mockup() {
		$mockup = array(
			'id'  => $this->get('mockup_image_id'),
			'url' => ''
		);

		if ( $mockup['id'] ) {
			$mockup['url'] = wp_get_attachment_url( $mockup['id'] );
		}

		return apply_filters( 'smartmockups_mockup', $mockup );
	}

	/**
	 * Get post feedbacks
	 *
	 * @since 1.1.0
	 */
	public function get_feedbacks() {
		$feedbacks = $this->get('_feedbacks');

		return apply_filters( 'smartmockups_feedbacks', $feedbacks );
	}

	/**
	 * Get post discussion
	 *
	 * @since 1.1.0
	 */
	public function get_discussion() {
		$discussion = $this->get('_discussion');

		return apply_filters( 'smartmockups_discussion', $discussion );
	}

	/**
	 * Get post approval signature
	 *
	 * @since 1.1.0
	 */
	public function get_approval_signature() {
		$approval_signature = $this->get('_approval');

		return apply_filters( 'smartmockups_approval_signature', $approval_signature );
	}

	/**
	 * Get post help text content
	 *
	 * @since 1.1.0
	 */
	public function get_help_text() {
		$help_text = $this->get('help_text_content');

		return apply_filters( 'smartmockups_help_text', $help_text );
	}

	/**
	 * Get custom permalink
	 *
	 * @since 1.1.0
	 */
	public function get_custom_permalink() {
		if ( $this->ID ) {
			$custom_permalink = get_site_url() . '/' . Smart_Mockups_Setup::get_custom_slug() . '/' . $this->post_name . '/';
		}
		else {
			return false;
		}

		return apply_filters( 'smartmockups_custom_permalink', $custom_permalink );
	}

	/**
	 * Get array of style classes used to wrap the mockup content
	 */
	public function get_wrapper_classes() {
		$classes = array();

		if ( ! $this->is_enabled( 'feedbacks' ) ) {
			$classes[] = 'feedbacks-disabled';
		}
		else {
			$classes[] = 'feedbacks-enabled';
		}

		if ( ! $this->is_enabled( 'discussion' ) ) {
			$classes[] = 'discussion-disabled';
		}
		else {
			$classes[] = 'discussion-enabled';
		}

		// If is not enabled or the mockup has already been approved, disable approval
		if ( ! $this->is_enabled( 'approval' ) || $this->get_approval_signature() ) {
			$classes[] = 'approval-disabled';
		}
		else {
			$classes[] = 'approval-enabled';
		}

		return apply_filters( 'smartmockups_wrapper_classes', $classes );
	}

	/**
	 * Checks if the given option is enabled
	 *
	 * @since 1.1.0
	 */
	public function is_enabled( $option = 'feedbacks' ) {
		return $this->get( $option . '_enabled' );
	}

	/**
	 * getter
	 *
	 * @since 1.1.0
	 */
	public function get( $post_meta = null ) {
		if ( in_array( $post_meta, $this->post_meta ) ) {
			return $this->$post_meta;
		}
		else {
			return get_post_meta( $this->ID, $post_meta, true);
		}
	}
}