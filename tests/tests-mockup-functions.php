<?php
class Tests_Mockup_Functions extends WP_UnitTestCase {

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
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/mockup-functions.php' );
	}

	function test_sm_get_mockup() {
		$this->assertTrue( null == sm_get_mockup() );
		$this->assertTrue( null == sm_get_mockup( 1 ) );

		$mockup = sm_get_mockup( $this->_post->ID );

		$this->assertTrue( $this->_post->ID == $mockup->ID );
		$this->assertEquals( SMART_MOCKUPS_POSTTYPE, $mockup->post_type );

		$this->assertInternalType( 'array', $mockup->get_feedbacks() );
		$this->assertEquals( $mockup->get_feedbacks(), sm_get_mockup_feedbacks( $this->_post->ID ) );

		$this->assertInternalType( 'array', $mockup->get_discussion() );
		$this->assertEquals( $mockup->get_discussion(), sm_get_mockup_discussion( $this->_post->ID ) );

		// Test a not approved mockup
		$this->assertInternalType( 'string', $mockup->get_approval_signature() );
		$this->assertEquals( '', $mockup->get_approval_signature() );
		$this->assertEquals( $mockup->get_approval_signature(), sm_get_mockup_approval_signature( $this->_post->ID ) );

		// Approve the mockup
		SM_Helper_Mockup::approve_mockup( $this->_post->ID );

		// Test an approved mockup
		$this->assertInternalType( 'array', $mockup->get_approval_signature() );
		$this->assertEquals( $mockup->get_approval_signature(), sm_get_mockup_approval_signature( $this->_post->ID ) );

		$this->assertEquals( 1, $mockup->is_enabled('feedbacks') );
		$this->assertEquals( 1, $mockup->is_enabled('discussion') );
		$this->assertEquals( 1, $mockup->is_enabled('approval') );
		$this->assertEquals( 1, $mockup->is_enabled('help_text') );

		SM_Helper_Mockup::disable_feedbacks( $this->_post->ID );
		SM_Helper_Mockup::disable_discussion( $this->_post->ID );
		SM_Helper_Mockup::disable_approval( $this->_post->ID );
		SM_Helper_Mockup::disable_help_text( $this->_post->ID );

		$this->assertEquals( 0, $mockup->is_enabled('feedbacks', false ) );
		$this->assertEquals( 0, $mockup->is_enabled('discussion', false ) );
		$this->assertEquals( 0, $mockup->is_enabled('approval', false ) );
		$this->assertEquals( 0, $mockup->is_enabled('help_text', false ) );
	}

	function test_sm_get_mockup_image() {
		$this->assertInternalType( 'array', sm_get_mockup_image() );
		$this->assertInternalType( 'array', sm_get_mockup_image( $this->_post->ID ) );
	}

	function test_sm_get_mockup_feedbacks() {
		$this->assertInternalType( 'array', sm_get_mockup_feedbacks() );
		$this->assertInternalType( 'array', sm_get_mockup_feedbacks( $this->_post->ID ) );
	}

	function test_sm_get_mockup_discussion() {
		$this->assertInternalType( 'array', sm_get_mockup_discussion() );
		$this->assertInternalType( 'array', sm_get_mockup_discussion( $this->_post->ID ) );
	}

	function test_sm_get_mockup_approval_signature() {
		$this->assertEquals( false, sm_get_mockup_approval_signature() );

		// Test a not approved mockup
		$this->assertInternalType( 'string', sm_get_mockup_approval_signature( $this->_post->ID ) );

		// Approve the mockup
		SM_Helper_Mockup::approve_mockup( $this->_post->ID );

		// Test an approved mockup
		$this->assertInternalType( 'array', sm_get_mockup_approval_signature( $this->_post->ID ) );
	}

	function test_sm_get_mockup_help_text() {
		$this->assertEquals( false, sm_get_mockup_help_text() );
		$this->assertInternalType( 'string', sm_get_mockup_help_text( $this->_post->ID ) );
	}

	function test_sm_get_mockup_custom_permalink() {
		$this->assertFalse( sm_get_mockup_custom_permalink() );
		$this->assertInternalType( 'string', sm_get_mockup_custom_permalink( $this->_post->ID ) );
	}
}