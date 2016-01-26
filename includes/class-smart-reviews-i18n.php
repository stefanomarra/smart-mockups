<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/includes
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Reviews_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'smart-reviews',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
