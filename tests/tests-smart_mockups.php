<?php
class Tests_Smart_Mockups extends WP_UnitTestCase {

	protected $_plugin;

	public function setUp() {
		parent::setUp();
		$this->_plugin = new Smart_Mockups();
	}

	public function tearDown() {
		parent::tearDown();
	}

	function test_load_dependencies() {
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/class-smart-mockups-setup.php' );
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/class-smart-mockups-loader.php' );
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'includes/class-smart-mockups-i18n.php' );
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'admin/class-smart-mockups-admin.php' );
		$this->assertFileExists( SMART_MOCKUPS_DIR . 'public/class-smart-mockups-public.php' );
	}

	/**
	 * TODO
	 */
	function test_set_locale() {}

	/**
	 * TODO
	 */
	function test_plugin_setup() {}

	/**
	 * TODO
	 */
	function test_define_admin_hooks() {}

	/**
	 * TODO
	 */
	function test_define_public_hooks() {}

	/**
	 * TODO
	 */
	function test_run() {}

	function test_get_plugin_name() {
		$this->assertEquals( 'smart-mockups', $this->_plugin->get_plugin_name() );
	}

	/**
	 * TODO
	 */
	function test_get_loader() {}


	function test_get_version() {
		$this->assertTrue( defined( 'SMART_MOCKUPS_VERSION' ) );
		$this->assertEquals( SMART_MOCKUPS_VERSION, $this->_plugin->get_version() );
	}
}