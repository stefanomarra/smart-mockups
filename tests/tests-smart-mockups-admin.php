<?php
class Tests_Smart_Mockups_Admin extends WP_UnitTestCase {

	protected $_feedback_id;

	protected $_post;

	protected $_plugin_admin;

	public function setUp() {
		parent::setUp();

		$this->_post = SM_Helper_Mockup::create_mockup();

		$this->_feedback_id = '0308e0c80d62b4bf3b6b5fa4e310c65e';

		$this->_plugin_admin = new Smart_Mockups_Admin( 'smart-mockups', SMART_MOCKUPS_VERSION );
	}

	public function tearDown() {
		parent::tearDown();

		SM_Helper_Mockup::delete_mockup( $this->_post->ID );
	}

	/**
	 * TODO
	 */
	function test_enqueue_styles() {}

	/**
	 * TODO
	 */
	function test_enqueue_scripts() {}

	/**
	 * TODO
	 */
	function test_define_meta_boxes() {}

	/**
	 * TODO
	 */
	function test_render_meta_box_mockup() {}

	/**
	 * TODO
	 */
	function test_render_meta_fields() {}

	/**
	 * TODO
	 */
	function test_display_approval_status() {}

	function test_set_posttype_row_actions() {
		$actions = $this->_plugin_admin->set_posttype_row_actions( array(), $this->_post );
		$this->assertArrayNotHasKey( 'custom_slug_view', $actions );

		// Change Permalink Post Structure
		update_option( 'permalink_structure', '/%postname%/' );

		// Run the above test again
		$actions = $this->_plugin_admin->set_posttype_row_actions( array(), $this->_post );
		$this->assertArrayHasKey( 'custom_slug_view', $actions );
	}

	function test_set_posttype_columns() {
		$columns = $this->_plugin_admin->set_posttype_columns( array() );
		$this->assertArrayHasKey( 'approved', $columns );

	}

	/**
	 * TODO
	 */
	function test_posttype_column() {}

	/**
	 * TODO
	 */
	function test_save_post_meta() {}


}