<?php
class Tests_Misc_Functions extends WP_UnitTestCase {

	protected $_post;

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	function test_includes() {
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/misc-functions.php' );
	}

	function test_sm_get_custom_slug() {
		$this->assertInternalType( 'string', sm_get_custom_slug() );
	}
}