<?php
class Tests_SM extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		run_smart_mockups();
	}

	public function tearDown() {
		parent::tearDown();
	}

	function test_constants() {
		$this->assertTrue( defined( 'SMART_MOCKUPS_DOMAIN' ) );
		$this->assertEquals( 'smart-mockups', SMART_MOCKUPS_DOMAIN );

		$this->assertTrue( defined( 'SMART_MOCKUPS_DIR' ) );

		$this->assertTrue( defined( 'SMART_MOCKUPS_URL' ) );

		$this->assertTrue( defined( 'SMART_MOCKUPS_POSTTYPE' ) );
		$this->assertEquals( 'smart_mockup', SMART_MOCKUPS_POSTTYPE );

		$this->assertTrue( defined( 'SMART_MOCKUPS_VERSION' ) );
		$this->assertEquals( '1.0.4', SMART_MOCKUPS_VERSION );
	}

	function test_includes() {
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/class-smart-mockups.php' );
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/class-smart-mockups-activator.php' );
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/class-smart-mockups-deactivator.php' );
	}

	/**
	 * TODO
	 */
	function test_activate_smart_mockups() {}

	/**
	 * TODO
	 */
	function test_deactivate_smart_mockups() {}

	function test_run_smart_mockups() {
		$this->assertTrue( function_exists( 'run_smart_mockups' ) );
	}
}