<?php

/**
 * Handles and defines all the plugin variables
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/includes
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Mockups_Setup {

	/**
	 * Plugin custom post types
	 *
	 * @since    1.0.0
	 */
	public static function post_types() {

		return array(
			SMART_MOCKUPS_POSTTYPE => array(
				'labels' 	=> array(
						'name'          => __( 'Smart Mockups', SMART_MOCKUPS_DOMAIN ),
						'singular_name' => __( 'Mockup', SMART_MOCKUPS_DOMAIN ),
						'all_items' 	=> __( 'All Mockups', SMART_MOCKUPS_DOMAIN ),
						'new_item'      => __( 'New Mockup', SMART_MOCKUPS_DOMAIN ),
						'add_new'       => __( 'Add New', SMART_MOCKUPS_DOMAIN ),
						'add_new_item'  => __( 'Add New Mockup', SMART_MOCKUPS_DOMAIN ),
						'edit_item'     => __( 'Edit Mockup', SMART_MOCKUPS_DOMAIN ),
						'view_item'     => __( 'View Mockup', SMART_MOCKUPS_DOMAIN ),
						'search_items'  => __( 'Search Mockups', SMART_MOCKUPS_DOMAIN ),
					),
				'rewrite'     => array( 'slug' => 'smart-mockups' ),
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

		$post_types = Smart_Mockups_Setup::post_types();

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
        add_submenu_page( 'edit.php?post_type=' . SMART_MOCKUPS_POSTTYPE, 'Settings', 'Settings', 'manage_options', 'smartmockups_settings', array(&$this, 'settings_page' ));
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
            echo '<a class="nav-tab' . $class . '" href="edit.php?post_type=' . SMART_MOCKUPS_POSTTYPE . '&page=smartmockups_settings&tab=' . $tab . '">' . $name . '</a>';
        }
        echo '</h2>';

    	switch ( $curr ) {
    		case 'general':
    		default:
    			include( SMART_MOCKUPS_DIR . 'admin/templates/settings-general.php');
    			break;
    	}
    }

    /**
	 * Register all plugin options
	 *
	 * @since 1.0.0
	 */
    public function register_plugin_options() {
    	register_setting( 'smartmockups_settings', 'smartmockups_slug', array(&$this, 'sanitize_slug') );
    	register_setting( 'smartmockups_settings', 'smartmockups_credits', 'intval' );
    }

    /**
	 * Register Sanitize Callback function
	 *
	 * @since 1.0.0
	 */
    public function sanitize_slug( $new_slug ) {
    	if ( empty( $new_slug ) ) {
    		$post_types = self::post_types();
    		$new_slug = $post_types[SMART_MOCKUPS_POSTTYPE]['rewrite']['slug'];
    	}
    	else {
    		$new_slug = sanitize_title_with_dashes($new_slug);
    	}

    	return $new_slug;
    }

}
