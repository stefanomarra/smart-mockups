<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/includes
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Reviews {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Smart_Reviews_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'smart-reviews';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->plugin_setup();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Smart_Reviews_Loader. Orchestrates the hooks of the plugin.
	 * - Smart_Reviews_i18n. Defines internationalization functionality.
	 * - Smart_Reviews_Admin. Defines all hooks for the admin area.
	 * - Smart_Reviews_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining all the plugin variables like post types
		 * and metas
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-reviews-setup.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-reviews-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-reviews-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-smart-reviews-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-smart-reviews-public.php';

		$this->loader = new Smart_Reviews_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Smart_Reviews_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Smart_Reviews_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Setup the plugin and add-ons
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function plugin_setup() {
		$plugin_setup = new Smart_Reviews_Setup( $this->get_plugin_name(), $this->get_version() );

		// Register Post Type
		$this->loader->add_action( 'init', $plugin_setup, 'register_post_types' );

		// Register Settings Page
		$this->loader->add_action( 'admin_init', $plugin_setup, 'register_plugin_options' );
		$this->loader->add_action( 'admin_menu', $plugin_setup, 'register_plugin_settings_page' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Smart_Reviews_Admin( $this->get_plugin_name(), $this->get_version() );

		// Enqueue Scripts and Styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Register Metaboxes and related
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'define_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_post_meta');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Smart_Reviews_Public( $this->get_plugin_name(), $this->get_version() );

		// Enqueue Scripts and Styles
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Override Single Tempate
		$this->loader->add_action( 'single_template', $plugin_public, 'single_template' );

		// Override Custom Post Type Slug
		$this->loader->add_action( 'init', $plugin_public, 'override_slug' );

		// Ajax
		$this->loader->add_action( 'wp_ajax_save_feedback', $plugin_public, 'save_feedback_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_save_feedback', $plugin_public, 'save_feedback_ajax' );

        $this->loader->add_action( 'wp_ajax_update_feedback_position', $plugin_public, 'update_feedback_position_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_update_feedback_position', $plugin_public, 'update_feedback_position_ajax' );

        $this->loader->add_action( 'wp_ajax_delete_feedback', $plugin_public, 'delete_feedback_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_delete_feedback', $plugin_public, 'delete_feedback_ajax' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Smart_Reviews_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
