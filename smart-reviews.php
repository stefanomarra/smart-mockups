<?php

/**
 * @link              http://www.stefanomarra.com
 * @since             1.0.0
 * @package           Smart_Reviews
 *
 * Plugin Name:       Smart Reviews
 * Plugin URI:        http://www.stefanomarra.com
 * Description:       Get instant reviews, comments and feedbacks from your clients.
 * Version:           1.0.0
 * Author:            Stefano Marra
 * Author URI:        http://www.stefanomarra.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smart-reviews
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'SMART_REVIEWS_DOMAIN'	, 'smart-reviews' );
define( 'SMART_REVIEWS_DIR'		, plugin_dir_path( __FILE__ ) );
define( 'SMART_REVIEWS_URL'		, plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-smart-reviews-activator.php
 */
function activate_smart_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-reviews-activator.php';
	Smart_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-smart-reviews-deactivator.php
 */
function deactivate_smart_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-reviews-deactivator.php';
	Smart_Reviews_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_smart_reviews' );
register_deactivation_hook( __FILE__, 'deactivate_smart_reviews' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smart-reviews.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_smart_reviews() {

	$plugin = new Smart_Reviews();
	$plugin->run();

}
run_smart_reviews();
