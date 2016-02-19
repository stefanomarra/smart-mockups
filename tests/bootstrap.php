<?php

require_once dirname( dirname( __FILE__ ) ) . '/includes/functions.php';

function _manually_load_environment() {

	// Add your theme …
	// switch_theme('your-theme-name');

	// Update array with plugins to include ...
	$plugins_to_active = array(
		'smart-mockups/smart-mockups.php'
	);

	update_option( 'active_plugins', $plugins_to_active );

}
tests_add_filter( 'muplugins_loaded', '_manually_load_environment' );

require dirname( dirname( __FILE__ ) ) . '/includes/bootstrap.php';

require_once 'helpers/class-helper-mockup.php';