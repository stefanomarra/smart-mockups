<?php
class Tests_Smart_Mockups_Post extends WP_UnitTestCase {

	protected $_post;
	protected $_obj;

	public function setUp() {
		parent::setUp();

		$this->_post = SM_Helper_Mockup::create_mockup();
		$this->_obj = new Smart_Mockups_Post( $this->_post->ID );
	}

	public function tearDown() {
		parent::tearDown();

		SM_Helper_Mockup::delete_mockup( $this->_post->ID );
	}

	function test_includes() {
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/class-smart-mockups-post.php' );
	}

	function test_smart_mockups_post() {
		$mockup = new Smart_Mockups_Post();
		$this->assertInternalType( 'string', get_post_type( $mockup ) );
		$this->assertEquals( 'post', get_post_type( $mockup ) );

		$mockup = new Smart_Mockups_Post( $this->_post->ID );
		$this->assertInternalType( 'string', get_post_type( $mockup ) );
		$this->assertEquals( SMART_MOCKUPS_POSTTYPE, get_post_type( $mockup ) );
		$this->assertEquals( $this->_post->ID, $mockup->ID );
	}

	function test_get_mockup_image() {
		$this->assertInternalType( 'array', $this->_obj->get_mockup_image() );
		$this->assertEquals( sm_get_mockup_image( $this->_post->ID ), $this->_obj->get_mockup_image() );
	}

	function test_get_feedbacks() {
		$this->assertInternalType( 'array', $this->_obj->get_feedbacks() );
		$this->assertEquals( sm_get_mockup_feedbacks( $this->_post->ID ), $this->_obj->get_feedbacks() );
	}

	function test_get_discussion() {
		$this->assertInternalType( 'array', $this->_obj->get_discussion() );
		$this->assertEquals( sm_get_mockup_discussion( $this->_post->ID ), $this->_obj->get_discussion() );
	}

	function test_get_approval_signature() {
		$this->assertEquals( false, $this->_obj->get_approval_signature() );

		// Test a not approved mockup
		$this->assertInternalType( 'string', $this->_obj->get_approval_signature() );

		// Approve the mockup
		SM_Helper_Mockup::approve_mockup( $this->_post->ID );

		// Test an approved mockup
		$this->assertInternalType( 'array', $this->_obj->get_approval_signature() );

		$this->assertEquals( sm_get_mockup_approval_signature( $this->_post->ID ), $this->_obj->get_approval_signature() );
	}

	function test_get_help_text() {
		$this->assertInternalType( 'string', $this->_obj->get_help_text() );
	}

	function test_get_custom_permalink() {
		$this->assertInternalType( 'string', $this->_obj->get_custom_permalink( $this->_post->ID ) );
	}
}