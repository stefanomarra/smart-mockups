<?php
/**
 * @group ajax
 */
class Tests_Smart_Mockups_Public extends WP_Ajax_UnitTestCase {

	protected $_feedback_id;

	protected $_post;

	protected $_plugin_public;

	public function setUp() {
		parent::setUp();

		$this->_post = SM_Helper_Mockup::create_mockup();

		$this->_feedback_id = '0308e0c80d62b4bf3b6b5fa4e310c65e';

		$this->_plugin_public = new Smart_Mockups_Public( 'smart-mockups', SMART_MOCKUPS_VERSION );
	}

	public function tearDown() {
		parent::tearDown();

		SM_Helper_Mockup::delete_mockup( $this->_post->ID );
	}

	/**
	 * TODO
	 */
	function test_save_approval_signature_ajax() {
		$_POST['post_id'] = $this->_post->ID;
		$_POST['signature'] = 'Stefano Marra';

		// Test as logged in user (administrator)
		$this->_setRole( 'administrator' );
		try {
			$this->_handleAjax( 'smart_mockups_save_approval' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );

		// Check if return status of ajax request is set
		$this->assertArrayHasKey( 'status', $response );

		// Check that the approval signature has been saved
		$this->assertEquals( 'approval_saved', $response['status'] );

		// Clear last_response
		$this->_last_response = '';

		// Test as logged out user
		$this->logout();
		try {
			$this->_handleAjax( 'smart_mockups_save_approval' );
		} catch ( WPAjaxDieContinueException $e ) {
			// We expected this, do nothing.
		}
		$response = json_decode( $this->_last_response, true );

		// Check if return status of ajax request is set
		$this->assertArrayHasKey( 'status', $response );

		// Check that the approval signature has been saved
		$this->assertEquals( 'approval_exists', $response['status'] );
	}

	function test_save_approval_signature() {
		$args = array(
				'post_id' 		=> $this->_post->ID,
				'time'			=> current_time( get_option( 'date_format' ) ),
				'signature'  	=> 'Signature Test'
			);

		$this->assertEquals( 'approval_saved', $this->_plugin_public->save_approval_signature( $args ) );
		$this->assertEquals( 'approval_exists', $this->_plugin_public->save_approval_signature( $args ) );
	}

	/**
	 * TODO
	 */
	function test_save_discussion_comment_ajax() {}

	function test_save_discussion_comment() {
		$args = array(
				'post_id' 		=> $this->_post->ID,
				'time'			=> current_time( get_option( 'date_format' ) ),
				'comment'  		=> '<li><div class="sr-avatar"><img src="' . $this->_plugin_public->get_user_avatar() . '" /></div><div class="sr-comment-content"><span class="sr-user-display" style="background-color: #888">' . $this->_plugin_public->get_user_display_name() . '</span> <span class="sr-comment-time">' . current_time( get_option( 'date_format' ) ) . '</span> <span class="sr-comment-text">Test Comment</span></div></li>',
				'action'		=> 'save_discussion_comment'
			);

		$this->assertEquals( 'new_discussion_comment_saved', $this->_plugin_public->save_discussion_comment( $args ) );
	}

	/**
	 * TODO
	 */
	function test_save_feedback_ajax() {}

	/**
	 * TODO
	 */
	function test_update_feedback_position_ajax() {}

	/**
	 * TODO
	 */
	function test_delete_feedback_ajax() {}

	function test_save_feedback() {
		$args = array(
				'post_id' 		=> $this->_post->ID,
				'time'			=> current_time( get_option( 'date_format' ) ),
				'x'				=> '39.6875%',
				'y'				=> '17.65625%',
				'feedback_id'  	=> $this->_feedback_id,
				'comment'  		=> '<li><div class="sr-avatar"><img src="' . $this->_plugin_public->get_user_avatar() . '" /></div><div class="sr-comment-content"><span class="sr-user-display" style="background-color: #888">' . $this->_plugin_public->get_user_display_name() . '</span> <span class="sr-comment-time">' . current_time( get_option( 'date_format' ) ) . '</span> <span class="sr-comment-text">Test Feedback Comment</span></div></li>',
				'action'		=> 'update_feedback_position'
			);

		// Test updating a non existing feedback
		$this->assertEquals( 'no_feedbacks', $this->_plugin_public->save_feedback( $args ) );

		// Test saving first feedback
		$args['action'] = 'save_feedback';
		$this->assertEquals( 'new_feedback_saved', $this->_plugin_public->save_feedback( $args ) );

		// Test saving the same first feedback
		$this->assertEquals( 'feedback_updated', $this->_plugin_public->save_feedback( $args ) );

		// Test updating first feedback position
		$args['action'] = 'update_feedback_position';
		$this->assertEquals( 'feedback_position_updated', $this->_plugin_public->save_feedback( $args ) );

		// Test saving second feedback
		$args['feedback_id'] = '0308e0c30d6db4bf3b6b5fa4e310c65e';
		$args['action'] = 'save_feedback';
		$this->assertEquals( 'new_feedback_saved', $this->_plugin_public->save_feedback( $args ) );
	}

	function test_delete_feedback() {
		// Test deleting a mockup with no feedbacks
		$args = array(
				'post_id' 		=> $this->_post->ID,
				'feedback_id'  	=> 'no_feedback_id',
				'action'		=> 'delete_feedback'
			);
		$this->assertEquals( 'no_feedbacks', $this->_plugin_public->delete_feedback( $args ) );

		// Add a feedback
		SM_Helper_Mockup::add_mockup_feedbacks( $this->_post->ID, $this->_feedback_id );

		$args = array(
				'post_id' 		=> $this->_post->ID,
				'feedback_id'  	=> 'no_feedback_id',
				'action'		=> 'delete_feedback'
			);
		// Test deleting a non existing feedback
		$this->assertEquals( 'feedback_not_found', $this->_plugin_public->delete_feedback( $args ) );

		// Test deleting an existing feedback
		$args['feedback_id'] = $this->_feedback_id;
		$this->assertEquals( 'feedback_deleted', $this->_plugin_public->delete_feedback( $args ) );
	}

	function test_get_user_display_name() {
		$this->assertInternalType( 'string', $this->_plugin_public->get_user_display_name() );
	}

	function test_get_user_avatar() {
		$this->assertInternalType( 'string', $this->_plugin_public->get_user_avatar() );
	}

	function test_generate_rgb_color() {
		$color = $this->_plugin_public->generate_rgb_color();
		$this->assertInternalType( 'string', $color );
		$this->assertStringStartsWith( '#', $color );
		$this->assertStringMatchesFormat( '%x', substr($color, 1) );
	}

	/**
	 * TODO
	 */
	function test_setcookie_user_color() {}

	function test_get_user_color() {
		$color = $this->_plugin_public->get_user_color();
		$this->assertInternalType( 'string', $color );
		$this->assertStringStartsWith( '#', $color );
		$this->assertStringMatchesFormat( '%x', substr($color, 1) );
	}

	function test_generate_feedback_id() {
		$this->assertInternalType( 'string', $this->_plugin_public->generate_feedback_id() );
	}

	function test_password_form() {
		$this->assertInternalType( 'string', $this->_plugin_public->password_form() );
	}

	function test_get_discussion() {
		$this->assertInternalType( 'array', $this->_plugin_public->get_discussion() );
		$this->assertInternalType( 'array', $this->_plugin_public->get_discussion( null ) );
		$this->assertInternalType( 'array', $this->_plugin_public->get_discussion( array() ) );
		$this->assertInternalType( 'array', $this->_plugin_public->get_discussion( array( 'comments' ) ) );
		$this->assertInternalType( 'array', $this->_plugin_public->get_discussion( array( 'comments' => array() ) ) );
		$this->assertInternalType( 'array', $this->_plugin_public->get_discussion( array( 'comments' => array('Test', 'Test 2') ) ) );
	}

	function test_get_feedbacks() {
		$this->assertInternalType( 'array', $this->_plugin_public->get_feedbacks() );
		$this->assertInternalType( 'array', $this->_plugin_public->get_feedbacks( null ) );
		$this->assertInternalType( 'array', $this->_plugin_public->get_feedbacks( array() ) );
	}

	/**
	 * TODO
	 */
	function test_single_template() {}

	/**
	 * TODO
	 */
	function test_override_slug() {}
}