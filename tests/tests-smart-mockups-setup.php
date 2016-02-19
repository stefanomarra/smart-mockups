<?php
class Tests_Smart_Mockups_Setup extends WP_UnitTestCase {

	protected $_post;

	public function setUp() {
		parent::setUp();

		$this->_post = SM_Helper_Mockup::create_mockup();
	}

	public function tearDown() {
		parent::tearDown();

		SM_Helper_Mockup::delete_mockup( $this->_post->ID );
	}

	function test_includes() {
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'admin/templates/settings-general.php' );
	}

	function test_post_types() {
		$this->assertTrue( defined( 'SMART_MOCKUPS_POSTTYPE' ) );
		$this->assertEquals( 'smart_mockup', SMART_MOCKUPS_POSTTYPE );

		$post_types = Smart_Mockups_Setup::post_types();
		$this->assertInternalType( 'array', $post_types );
		$this->assertArrayHasKey( 'smart_mockup', $post_types );
		$this->assertInternalType( 'array', $post_types['smart_mockup'] );
		$this->assertArrayHasKey( 'post_meta', $post_types['smart_mockup'] );
		$this->assertInternalType( 'array', $post_types['smart_mockup']['post_meta'] );
		$this->assertArrayHasKey( 'mockup_image_id', $post_types['smart_mockup']['post_meta'] );
		$this->assertArrayHasKey( 'feedbacks_enabled', $post_types['smart_mockup']['post_meta'] );
		$this->assertArrayHasKey( 'discussion_enabled', $post_types['smart_mockup']['post_meta'] );
		$this->assertArrayHasKey( 'approval_enabled', $post_types['smart_mockup']['post_meta'] );
		$this->assertArrayHasKey( 'help_text_enabled', $post_types['smart_mockup']['post_meta'] );
		$this->assertArrayHasKey( 'help_text_content', $post_types['smart_mockup']['post_meta'] );
		$this->assertArrayHasKey( 'color_background', $post_types['smart_mockup']['post_meta'] );
		$this->assertArrayHasKey( 'color_feedback_dot', $post_types['smart_mockup']['post_meta'] );

		global $wp_post_types;
		$this->assertArrayHasKey( 'smart_mockup', $wp_post_types );

		$this->assertEquals( 'Smart Mockups', $wp_post_types['smart_mockup']->labels->name );
		$this->assertEquals( 'Mockup', $wp_post_types['smart_mockup']->labels->singular_name );
		$this->assertEquals( 'All Mockups', $wp_post_types['smart_mockup']->labels->all_items );
		$this->assertEquals( 'New Mockup', $wp_post_types['smart_mockup']->labels->new_item );
		$this->assertEquals( 'Add New', $wp_post_types['smart_mockup']->labels->add_new );
		$this->assertEquals( 'Add New Mockup', $wp_post_types['smart_mockup']->labels->add_new_item );
		$this->assertEquals( 'Edit Mockup', $wp_post_types['smart_mockup']->labels->edit_item );
		$this->assertEquals( 'View Mockup', $wp_post_types['smart_mockup']->labels->view_item );
		$this->assertEquals( 'Search Mockups', $wp_post_types['smart_mockup']->labels->search_items );

		$this->assertEquals( true, $wp_post_types['smart_mockup']->public );
		$this->assertEquals( false, $wp_post_types['smart_mockup']->has_archive );
	}

	/**
	 * TODO
	 */
	function test_render_form_field() {}

	/**
	 * TODO
	 */
	function test_render_form_field_media() {}

	/**
	 * TODO
	 */
	function test_render_form_field_checkbox() {}

	/**
	 * TODO
	 */
	function test_render_form_field_text() {}

	/**
	 * TODO
	 */
	function test_render_form_field_textarea() {}

	/**
	 * TODO
	 */
	function test_render_form_field_colorpicker() {}

	function test_get_mockup() {
		$this->assertInternalType( 'array', Smart_Mockups_Setup::get_mockup() );
		$this->assertInternalType( 'array', Smart_Mockups_Setup::get_mockup( $this->_post->ID ) );
	}

	function test_get_feedbacks() {
		$this->assertInternalType( 'array', Smart_Mockups_Setup::get_feedbacks() );
		$this->assertInternalType( 'array', Smart_Mockups_Setup::get_feedbacks( $this->_post->ID ) );
	}

	function test_get_discussion() {
		$this->assertInternalType( 'array', Smart_Mockups_Setup::get_discussion() );
		$this->assertInternalType( 'array', Smart_Mockups_Setup::get_discussion( $this->_post->ID ) );
	}

	function test_get_approval_signature() {
		$this->assertEquals( false, Smart_Mockups_Setup::get_approval_signature() );

		// Test a not approved mockup
		$this->assertInternalType( 'string', Smart_Mockups_Setup::get_approval_signature( $this->_post->ID ) );

		// Approve the mockup
		SM_Helper_Mockup::approve_mockup( $this->_post->ID );

		// Test an approved mockup
		$this->assertInternalType( 'array', Smart_Mockups_Setup::get_approval_signature( $this->_post->ID ) );
	}

	function test_get_help_text() {
		$this->assertEquals( false, Smart_Mockups_Setup::get_help_text() );
		$this->assertInternalType( 'string', Smart_Mockups_Setup::get_help_text( $this->_post->ID ) );
	}

	function test_get_custom_slug() {
		$this->assertInternalType( 'string', Smart_Mockups_Setup::get_custom_slug() );
	}

	function test_get_custom_permalink() {
		$this->assertInternalType( 'string', Smart_Mockups_Setup::get_custom_permalink() );
		$this->assertInternalType( 'string', Smart_Mockups_Setup::get_custom_permalink( $this->_post->ID ) );
	}

	/**
	 * TODO
	 */
	function test_register_post_types() {}

	/**
	 * TODO
	 */
	function test_register_plugin_settings_page() {}

	/**
	 * TODO
	 */
	function test_settings_page() {}

	/**
	 * TODO
	 */
	function test_settings_tabs() {}

	/**
	 * TODO
	 */
	function test_register_plugin_options() {}
}