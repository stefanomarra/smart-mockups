<?php

/**
 * @link              http://www.stefanomarra.com
 * @since             1.0.0
 * @package           Smart_Mockups
 *
 * Plugin Name:       Smart Mockups
 * Plugin URI:        http://www.stefanomarra.com
 * Description:       Get instant reviews, comments and feedbacks from your clients.
 * Version:           1.0.1
 * Author:            Stefano Marra
 * Author URI:        http://www.stefanomarra.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smart-mockups
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SMART_MOCKUPS_DOMAIN'			, 'smart-mockups' );
define( 'SMART_MOCKUPS_DIR'				, plugin_dir_path( __FILE__ ) );
define( 'SMART_MOCKUPS_URL'				, plugin_dir_url( __FILE__ ) );
define( 'SMART_MOCKUPS_POSTTYPE'		, 'smart_mockup' );

/**
 * The code that runs during plugin activation.
 */
function activate_smart_mockups() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-mockups-activator.php';
	Smart_Mockups_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_smart_mockups() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-mockups-deactivator.php';
	Smart_Mockups_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_smart_mockups' );
register_deactivation_hook( __FILE__, 'deactivate_smart_mockups' );

/**
 * The core plugin class
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smart-mockups.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_smart_mockups() {

	$plugin = new Smart_Mockups();
	$plugin->run();

}
run_smart_mockups();
