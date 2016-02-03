<?php

/**
 * Handles and defines all the plugin variables
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/includes
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Reviews_Setup {

	/**
	 * Plugin custom post types
	 *
	 * @since    1.0.0
	 */
	public static function post_types() {

		return array(
			'smartreview' => array(
				'labels' 	=> array(
						'name'          => __( 'Smart Reviews', SMART_REVIEWS_DOMAIN ),
						'singular_name' => __( 'Mockup', SMART_REVIEWS_DOMAIN ),
						'all_items' 	=> __( 'All Mockups', SMART_REVIEWS_DOMAIN ),
						'new_item'      => __( 'Add New Mockup', SMART_REVIEWS_DOMAIN ),
						'add_new'       => __( 'Add New', SMART_REVIEWS_DOMAIN ),
						'add_new_item'  => __( 'Add New Mockup', SMART_REVIEWS_DOMAIN ),
						'edit_item'     => __( 'Edit Mockup', SMART_REVIEWS_DOMAIN ),
						'view_item'     => __( 'View Mockup', SMART_REVIEWS_DOMAIN ),
						'search_items'  => __( 'Search Mockups', SMART_REVIEWS_DOMAIN ),
					),
				'rewrite'     => array( 'slug' => 'smart-reviews' ),
				'public'      => true,
				'has_archive' => false,
				'menu_icon'   => 'dashicons-exerpt-view',
				'supports' 	  => array( 'title' ),
				'post_meta'	=> array(
						'mockup_image_id',
						'feedbacks_enabled',
						'discussion_enabled',
						'approval_enabled'
					)
			)
		);
	}

	/**
	 * Register default settings tab
	 */
	private $tabs = array(
			'general' => 'General'
		);

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register plugin custom post type
	 *
	 * @since 	1.0.0
	 */
	public function register_post_types() {

		$post_types = Smart_Reviews_Setup::post_types();

		foreach($post_types as $post_type => $type_opt) {
			register_post_type($post_type, array(
				'labels' 	=> array(
								'name'          => $type_opt['labels']['name'],
								'singular_name' => $type_opt['labels']['singular_name'],
								'all_items' 	=> $type_opt['labels']['all_items'],
								'new_item'      => $type_opt['labels']['new_item'],
								'add_new'       => $type_opt['labels']['add_new'],
								'add_new_item'  => $type_opt['labels']['add_new_item'],
								'edit_item'     => $type_opt['labels']['edit_item'],
								'view_item'     => $type_opt['labels']['view_item'],
								'search_items'  => $type_opt['labels']['search_items'],
							),
				'rewrite'     => $type_opt['rewrite'],
				'public'      => $type_opt['public'],
				'has_archive' => $type_opt['has_archive'],
				'menu_icon'   => $type_opt['menu_icon'],
				'supports' 	  => $type_opt['supports']
			));
		}

		flush_rewrite_rules();
	}

	/**
	 * Register Admin Menu
	 *
	 * @since 1.0.0
	 */
	public function register_plugin_settings_page() {
        add_submenu_page( 'edit.php?post_type=smartreview', 'Settings', 'Settings', 'manage_options', 'smartreviews_settings', array(&$this, 'settings_page' ));
    }

    /**
	 * Callback Function for settings page
	 *
	 * @since 1.0.0
	 */
    public function settings_page() {
    	$tab = ( isset( $_GET['tab'] ) && !empty( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';
    	$this->settings_tabs( $tab );
    }

	/**
	 * Shows tabs and handles the selected tab
	 *
	 * @since 1.0.0
	 */
    public function settings_tabs($curr = 'general') {
    	echo '<div class="wrap">';
    	echo '<h1>Settings</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach( $this->tabs as $tab => $name ){
            $class = ( $tab == $curr ) ? ' nav-tab-active' : '';
            echo '<a class="nav-tab' . $class . '" href="edit.php?post_type=smartreview&page=smartreviews_settings&tab=' . $tab . '">' . $name . '</a>';
        }
        echo '</h2>';

    	switch ( $curr ) {
    		case 'general':
    		default:
    			include( SMART_REVIEWS_DIR . 'admin/templates/settings-general.php');
    			break;
    	}
    }

    /**
	 * Register all plugin options
	 *
	 * @since 1.0.0
	 */
    public function register_plugin_options() {
    	register_setting( 'smartreviews_settings', 'smartreviews_slug', array(&$this, 'sanitize_slug') );
    	register_setting( 'smartreviews_settings', 'smartreviews_credits', 'intval' );
    }

    /**
	 * Register Sanitize Callback function
	 *
	 * @since 1.0.0
	 */
    public function sanitize_slug( $new_slug ) {
    	if ( empty( $new_slug ) ) {
    		$post_types = self::post_types();
    		$new_slug = $post_types['smartreview']['rewrite']['slug'];
    	}
    	else {
    		$new_slug = sanitize_title_with_dashes($new_slug);
    	}

    	return $new_slug;
    }

}
