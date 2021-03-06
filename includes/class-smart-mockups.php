<?php

/**
 * The file that defines the core plugin class
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/includes
 */

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/includes
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Mockups {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Smart_Mockups_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'smart-mockups';
		$this->version = SMART_MOCKUPS_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->plugin_setup();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-mockups-setup.php';

		/**
		 * Template functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/template-functions.php';

		/**
		 * Misc functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/misc-functions.php';

		/**
		 * Mockup functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/mockup-functions.php';

		/**
		 * The class responsable for defining the plugin's mockup post type
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-mockups-post.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-mockups-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-mockups-i18n.php';

		/**
		 * The class responsible for handling the emails that occur during notification
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-mockups-emails.php';

		/**
		 * The class responsible for handling notifications
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-mockups-notifications.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-smart-mockups-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-smart-mockups-public.php';

		$this->loader = new Smart_Mockups_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Smart_Mockups_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Setup the plugin and add-ons
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function plugin_setup() {
		$plugin_setup = new Smart_Mockups_Setup( $this->get_plugin_name(), $this->get_version() );

		// Register Post Type
		$this->loader->add_action( 'init', $plugin_setup, 'register_post_types' );

		// Register Settings Page
		$this->loader->add_action( 'admin_init', $plugin_setup, 'register_plugin_options' );
		$this->loader->add_action( 'admin_menu', $plugin_setup, 'register_plugin_settings_page' );

		$notifications = new Smart_Mockups_Notifications();

		// Register Notifications
		$this->loader->add_action( 'update_option_smartmockups_notifications', $notifications, 'update_schedule', 12, 2);
		$this->loader->add_action( 'update_option_smartmockups_notifications_recurrence', $notifications, 'update_schedule', 12, 2);

		$this->loader->add_action( 'smartmockups_after_save_feedback', $notifications, 'add_to_queue', 999, 1 );
		$this->loader->add_action( 'smartmockups_after_save_discussion_comment', $notifications, 'add_to_queue', 999, 1 );
		$this->loader->add_action( 'smartmockups_after_save_approval_signature', $notifications, 'add_to_queue', 999, 1 );
		$this->loader->add_action( 'notification_event', $notifications, 'send_email' );

		$plugin_public = new Smart_Mockups_Public($this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'smartmockups_after_save_feedback', $plugin_public, 'update_user_feedbacks' );
		$this->loader->add_action( 'smartmockups_after_delete_feedback', $plugin_public, 'update_user_feedbacks' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Smart_Mockups_Admin( $this->get_plugin_name(), $this->get_version() );

		// Enqueue Scripts and Styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Register Metaboxes and related
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'define_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_post_meta');

		// Set Post Type Columns
		$this->loader->add_filter( 'manage_' . SMART_MOCKUPS_POSTTYPE . '_posts_columns', $plugin_admin, 'set_posttype_columns' );
		$this->loader->add_action( 'manage_' . SMART_MOCKUPS_POSTTYPE . '_posts_custom_column', $plugin_admin, 'posttype_column', 10, 2 );

		// Set Post Type Row Actions
		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'set_posttype_row_actions', 10, 2 );

		// Plugin Hooks
		$this->loader->add_action( 'smartmockups_before_render_meta_fields', $plugin_admin, 'display_approval_status' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Smart_Mockups_Public( $this->get_plugin_name(), $this->get_version() );

		// Enqueue Scripts and Styles
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Override Single Tempate
		$this->loader->add_action( 'single_template', $plugin_public, 'single_template' );

		// Override Custom Post Type Slug
		$this->loader->add_action( 'init', $plugin_public, 'override_slug' );

		// Set User Color in Cookie
		$this->loader->add_action( 'init', $plugin_public, 'setcookie_user_color' );

		// Ajax Save/Update Feedback/Comments
		$this->loader->add_action( 'wp_ajax_save_feedback', $plugin_public, 'save_feedback_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_save_feedback', $plugin_public, 'save_feedback_ajax' );

        // Ajax Update Feedback Position
        $this->loader->add_action( 'wp_ajax_update_feedback_position', $plugin_public, 'update_feedback_position_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_update_feedback_position', $plugin_public, 'update_feedback_position_ajax' );

        // Ajax Delete Feedback
        $this->loader->add_action( 'wp_ajax_delete_feedback_post', $plugin_public, 'delete_feedback_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_delete_feedback_post', $plugin_public, 'delete_feedback_ajax' );

        // Ajax Save Discussion Comment
        $this->loader->add_action( 'wp_ajax_save_discussion_comment', $plugin_public, 'save_discussion_comment_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_save_discussion_comment', $plugin_public, 'save_discussion_comment_ajax' );

        // Ajax Save Approval Signature Signature
        $this->loader->add_action( 'wp_ajax_smart_mockups_save_approval', $plugin_public, 'save_approval_signature_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_smart_mockups_save_approval', $plugin_public, 'save_approval_signature_ajax' );

        // Plugin Hooks
		$this->loader->add_action( 'smartmockups_feedbacks', $plugin_public, 'get_feedbacks' );
		$this->loader->add_action( 'smartmockups_discussion', $plugin_public, 'get_discussion' );
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
	 * @return    Smart_Mockups_Loader    Orchestrates the hooks of the plugin.
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
