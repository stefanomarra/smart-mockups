<?php
/**
 * Class SM_Helper_Mockup.
 *
 * Helper class to create and delete a mockup easily.
 */
class SM_Helper_Mockup extends WP_UnitTestCase {

	/**
	 * Delete a mockup
	 *
	 * @since 1.0.3
	 *
	 * @param int $mockup_id ID of the mockup to delete
	 */
	public static function delete_mockup( $mockup_id ) {
		// Delete the post
		wp_delete_post( $mockup_id, true );
	}

	/**
	 * Approve a mockup
	 *
	 * @since 1.0.3
	 *
	 * @param int $mockup_id ID of the mockup to approve
	 */
	public static function approve_mockup( $mockup_id ) {
		// approve the post
		update_post_meta( $mockup_id, '_approval', array(
			'time'      => current_time( get_option( 'date_format' ) ),
			'signature' => 'Signature Test'
		) );
	}

	/**
	 * Create a mockup
	 *
	 * @since 1.0.3
	 */
	public static function create_mockup() {
		$post_id = wp_insert_post( array(
			'post_title'    => 'Test Mockup',
			'post_name'     => 'test-mockup',
			'post_type'     => 'smart_mockup',
			'post_status'   => 'publish'
		) );

		$meta = array(
			'mockup_image_id'    => 1,
			'feedbacks_enabled'  => true,
			'discussion_enabled' => true,
			'approval_enabled'   => true,
			'help_text_enabled'  => true,
			'help_text_content'  => 'Help Text Content',
			'color_background'   => '#6B7A90',
			'color_feedback_dot' => '#FF6160'
		);

		foreach( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		return get_post( $post_id );
	}

	/**
	 * Add a feedback
	 *
	 * @since 1.0.3
	 */
	public static function add_mockup_feedbacks( $mockup_id, $feedback_id = 'no_feedback_id' ) {
		$meta = array();
		$meta[ $feedback_id ] = array(
				'feedback_id' 	=> $feedback_id,
				'x' 			=> '30%',
				'y' 			=> '19%',
				'time' 			=> current_time( get_option( 'date_format' ) ),
				'comments' 		=> array('Test Feedback Comment')
			);
		update_post_meta( $mockup_id, '_feedbacks', $meta );
	}
}