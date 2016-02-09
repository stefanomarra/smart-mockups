<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/admin
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Mockups_Admin {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min/smart-mockups-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/smart-mockups-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );
	}

	/**
	 * Register plugin custom meta boxes
	 *
	 * @since 	1.0.0
	 */
	public function define_meta_boxes() {

		$id = $this->plugin_name . '-meta-box-mockup';
		$title = 'Mockup';
		$callback = array( $this, 'render_meta_box_mockup' );
		$post_type = SMART_MOCKUPS_POSTTYPE;
		$context = 'normal';
		$priority = 'high';
		$callback_args = array();
		add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
	}

	/**
	 * Render plugin Mockup meta box
	 *
	 * @since 	1.0.0
	 */
	public function render_meta_box_mockup() {
		echo '<div class="' . $this->plugin_name . '-meta-box-wrapper">';
		$this->render_meta_fields();
		echo '</div>';
	}

	/**
	 * Render plugin meta field mockup image
	 *
	 * @since 	1.0.0
	 */
	public function render_meta_fields() {

		do_action( 'smartmockups_before_render_meta_fields', get_the_ID() );

		echo '<table class="form-table">';
		echo '	<tbody>';

		$post_types = Smart_Mockups_Setup::post_types();
		foreach ( $post_types[SMART_MOCKUPS_POSTTYPE]['post_meta'] as $id => $attr ) {
			Smart_Mockups_Setup::render_form_field($id, $attr['type'], $attr);
		}

		echo '	</tbody>';
		echo '</table>';

		do_action( 'smartmockups_after_render_meta_fields', get_the_ID() );

	}

	/**
	 * Displays the mockup approval status
	 *
	 * @since 1.0.0
	 */
	public function display_approval_status( $post_id ) {
		$approval = get_post_meta( $post_id, '_approval', true);

		if ( ! $approval )
			return false;

		echo '<div class="mockup-status approved">Mockup Approved by ' . $approval['signature'] . ' - ' . $approval['time'] . '</div>';
	}

	/**
	 * Set plugin custom row actions
	 *
	 * @since 1.0.0
	 */
	public function set_posttype_row_actions( $actions, $post ) {

		if ( $post->post_type == SMART_MOCKUPS_POSTTYPE ) {
			$actions['custom_slug_view'] = '<a target="_blank" href="' . Smart_Mockups_Setup::get_custom_permalink( $post->ID ) . '">View (Custom Slug)</a>';
		}

		return $actions;
	}

	/**
	 * Set plugin custom columns
	 *
	 * @since 1.0.0
	 */
	public function set_posttype_columns( $columns ) {
		$columns['approved'] = __( 'Approved', SMART_MOCKUPS_DOMAIN );

		return $columns;
	}

	/**
	 * Plugin custom column handler
	 *
	 * @since 1.0.0
	 */
	public function posttype_column( $column, $post_id ) {

		switch ($column) {
			case 'approved':
				$approval_signature =  Smart_Mockups_Setup::get_approval_signature( $post_id );

				if ( is_array( $approval_signature ) )
					echo 'Approved by ' . $approval_signature['signature'] . '<br />' . '<abbr>' . $approval_signature['time'] . '</abbr>';

				break;
		}

	}

	/**
	 * Save plugin custom post meta
	 *
	 * @since 1.0.0
	 */
	public function save_post_meta( $post_id ) {

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check permissions
		if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$post_types = Smart_Mockups_Setup::post_types();

		// Check if post type is one of the plugin's
		if ( !in_array( get_post_type(), array_keys( $post_types ) ) )
			return $post_id;

		// Loop through the plugin post types
		foreach ( $post_types as $post_type => $type_opt ) {

			// For each post types, loop through each post meta
			foreach ( $type_opt['post_meta'] as $id => $attr) {

				$old = get_post_meta( $post_id, $id, true );
				$new = isset( $_POST[$id] )?$_POST[$id]:'';

				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $id, $new );
				}
				else if ( '' == $new && $old || ! isset( $_POST[$id] ) ) {
					delete_post_meta( $post_id, $id, $old );
				}
			}
		}

	}
}
