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
	 * Register default settings tab
	 *
	 * @since 1.0.0
	 */
	private $tabs = array(
			'general'       => 'General',
			'notifications' => 'Notifications'
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
						'mockup_image_id' 		=> array(
								'type'        => 'media',
								'name'        => __('Mockup Image', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => 0,
								'placeholder' => '',
								'description' => __('Upload or select the mockup image', SMART_MOCKUPS_DOMAIN)
							),
						'feedbacks_enabled' 	=> array(
								'type'        => 'checkbox',
								'name'        => __('Allow Feedbacks', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => 0,
								'placeholder' => '',
								'description' => __('Enable the point and click feedback', SMART_MOCKUPS_DOMAIN)
							),
						'discussion_enabled' 	=> array(
								'type'        => 'checkbox',
								'name'        => __('Allow Discussion', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => 0,
								'placeholder' => '',
								'description' => __('Enable the discussion panel on the side', SMART_MOCKUPS_DOMAIN)
							),
						'guest_enabled' 		=> array(
								'type'        => 'checkbox',
								'name'        => __('Allow Anonymous Feedbacks', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => 0,
								'placeholder' => '',
								'description' => __('Allow guests to anonymously post a feedback (Marked as "Guest")', SMART_MOCKUPS_DOMAIN),
								'require' 	  => 'feedbacks_enabled'
							),
						'approval_enabled' 		=> array(
								'type'        => 'checkbox',
								'name'        => __('Enable Approval', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => 0,
								'placeholder' => '',
								'description' => __('Enable the user to approve the mockup with a digital signature', SMART_MOCKUPS_DOMAIN)
							),
						'help_text_enabled' 	=> array(
								'type'        => 'checkbox',
								'name'        => __('Enable Help Text', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => 0,
								'placeholder' => '',
								'description' => ''
							),
						'help_text_content' 	=> array(
								'type'        => 'textarea',
								'name'        => __('Help Text', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => '',
								'placeholder' => '',
								'description' => '',
								'require' 	  => 'help_text_enabled'
							),
						'color_background' 	=> array(
								'type'        => 'colorpicker',
								'name'        => __('Background Color', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => '#6B7A90',
								'placeholder' => '',
								'description' => ''
							),
						'color_feedback_dot' 	=> array(
								'type'        => 'colorpicker',
								'name'        => __('Dots Color', SMART_MOCKUPS_DOMAIN),
								'class'       => '',
								'default' 	  => '#FF6160',
								'placeholder' => '',
								'description' => ''
							)
					)
			)
		);
	}

	/**
	 * Render form field
	 *
	 * @since 1.0.0
	 */
	public static function render_form_field( $id, $type, $attr) {
		switch ( $type ) {
			case 'media':
				self::render_form_field_media( $id, $attr );
				break;
			case 'checkbox':
				self::render_form_field_checkbox( $id, $attr );
				break;
			case 'textarea':
				self::render_form_field_textarea( $id, $attr );
				break;
			case 'colorpicker':
				self::render_form_field_colorpicker( $id, $attr );
				break;
			case 'text':
			default:
				self::render_form_field_text( $id, $attr );
				break;
		}
	}

	/**
	 * Render form field media
	 *
	 * @since 1.0.0
	 */
	public static function render_form_field_media( $id, $attr) {

		$value = get_post_meta( get_the_ID(), $id, true );
		$url = '';
		if ( $value )
			$url = wp_get_attachment_url( $value );
		else
			$value = $attr['default'];

		$require = isset( $attr['require'] )?('data-require="#' . $attr['require'] . '"'):'';

		$html = '';
		$html .= '<tr valign"top" class="tr-' . $attr['class'] . '" ' . $require . '>';
		$html .= '	<th>';
		$html .= '		<div class="field_th">' . $attr['name'] . '</div>';
		$html .= '		<div class="field_desc">' . $attr['description'] . '</div>';

		$html .= '		<div class="field-add-media">';
		$html .= '			<input type="hidden" name="' . $id . '" class="' . $attr['class'] . '" id="' . $id . '" value="' . $value . '" />';
		$html .= '			<input type="button" id="button-' . $id . '" class="button load_media" data-target="#' . $id . '" data-preview=".media-src-' . $id . '" value="Select File" />';
		$html .= '		</div>';

		$html .= '	</th>';

		$html .= '	<td>';

		$html .= '		<div class="field-media-preview">';
		$html .= '			<img class="media-src-' . $id . ' ' . ((!$value)?'hide':'') . '" src="' . $url . '" />';
		$html .= '		</div>';

		$html .= '	</td>';
		$html .= '</tr>';

		echo $html;
	}

	/**
	 * Render form field checkbox
	 *
	 * @since 1.0.0
	 */
	public static function render_form_field_checkbox( $id, $attr) {

		$value = get_post_meta( get_the_ID(), $id, true );

		if ( $value === null ) $value = $attr['default'];

		$require = isset( $attr['require'] )?('data-require="#' . $attr['require'] . '"'):'';

		$html = '';
		$html .= '<tr valign"top" class="tr-' . $attr['class'] . '" ' . $require . '>';
		$html .= '	<th>';
		$html .= '		<div class="field_th">' . $attr['name'] . '</div>';
		$html .= '	</th>';

		$html .= '	<td>';
		$html .= '		<label>';
		$html .= '			<input type="checkbox" name="' . $id . '" class="' . $attr['class'] . '" id="' . $id . '" value="1" ' . ($value?'checked':'') . ' /> ' . $attr['description'];
		$html .= '		</label>';

		$html .= '	</td>';
		$html .= '</tr>';

		echo $html;
	}

	/**
	 * Render form field text
	 *
	 * @since 1.0.0
	 */
	public static function render_form_field_text( $id, $attr) {

		$value = get_post_meta( get_the_ID(), $id, true );

		if ( ! $value ) $value = $attr['default'];

		$require = isset( $attr['require'] )?('data-require="#' . $attr['require'] . '"'):'';

		$html = '';
		$html .= '<tr valign"top" class="tr-' . $attr['class'] . '" ' . $require . '>';
		$html .= '	<th>';
		$html .= '		<div class="field_th">' . $attr['name'] . '</div>';
		$html .= '		<div class="field_desc">' . $attr['description'] . '</div>';
		$html .= '	</th>';

		$html .= '	<td>';
		$html .= '		<label>';
		$html .= '			<input type="text" name="' . $id . '" class="' . $attr['class'] . '" id="' . $id . '" value="' . $value . '" /> ';
		$html .= '		</label>';

		$html .= '	</td>';
		$html .= '</tr>';

		echo $html;
	}

	/**
	 * Render form field textarea (wp_editor)
	 *
	 * @since 1.0.0
	 */
	public static function render_form_field_textarea( $id, $attr ) {

		$value = get_post_meta( get_the_ID(), $id, true );

		if ( ! $value ) $value = $attr['default'];

		$require = isset( $attr['require'] )?('data-require="#' . $attr['require'] . '"'):'';

		$html = '';
		$html .= '<tr valign"top" class="tr-' . $attr['class'] . '" ' . $require . '>';
		$html .= '	<th>';
		$html .= '		<div class="field_th">' . $attr['name'] . '</div>';
		$html .= '		<div class="field_desc">' . $attr['description'] . '</div>';
		$html .= '	</th>';

		$html .= '	<td>';
		$html .= '		<label>';

		ob_start();
		wp_editor($value, $id, array(
				'editor_class' => $attr['class'],
				'editor_height' => 200
			));
		$html .= ob_get_clean();

		$html .= '		</label>';

		$html .= '	</td>';
		$html .= '</tr>';

		echo $html;
	}

	/**
	 * Render form field colorpicker
	 *
	 * @since 1.0.0
	 */
	public static function render_form_field_colorpicker( $id, $attr) {

		$value = get_post_meta( get_the_ID(), $id, true );

		if ( ! $value ) $value = $attr['default'];

		$require = isset( $attr['require'] )?('data-require="#' . $attr['require'] . '"'):'';

		$html = '';
		$html .= '<tr valign"top" class="tr-' . $attr['class'] . '" ' . $require . '>';
		$html .= '	<th>';
		$html .= '		<div class="field_th">' . $attr['name'] . '</div>';
		$html .= '		<div class="field_desc">' . $attr['description'] . '</div>';
		$html .= '	</th>';

		$html .= '	<td>';
		$html .= '		<label>';
		$html .= '			<input type="text" name="' . $id . '" class="wp-color-picker ' . $attr['class'] . '" id="' . $id . '" value="' . $value . '" data-default-color="' . $attr['default'] . '" /> ';
		$html .= '		</label>';

		$html .= '	</td>';
		$html .= '</tr>';

		echo $html;
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
    		case 'notifications':
    			include( SMART_MOCKUPS_DIR . 'admin/templates/settings-notifications.php');
    			break;

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
    	register_setting( 'smartmockups_settings_general', 'smartmockups_slug', array(&$this, 'sanitize_slug') );
    	register_setting( 'smartmockups_settings_general', 'smartmockups_credits', 'intval' );

    	register_setting( 'smartmockups_settings_notifications', 'smartmockups_notifications', 'intval' );
    	register_setting( 'smartmockups_settings_notifications', 'smartmockups_notifications_recurrence' );
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
