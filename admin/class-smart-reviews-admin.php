<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/admin
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Reviews_Admin {

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

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Smart_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smart_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min/smart-reviews-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Smart_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smart_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/smart-reviews-admin.js', array( 'jquery' ), $this->version, false );

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
		$post_type = 'smartreview';
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
		$this->render_meta_field_mockup_image();
		echo '</div>';
	}

	/**
	 * Render plugin meta field mockup image
	 *
	 * @since 	1.0.0
	 */
	public function render_meta_field_mockup_image() {

		// Get post mockup image
		$mockuo_url = '';
		$mockup_id = get_post_meta( get_the_ID(), 'mockup_image_id', true );
		if ( $mockup_id )
			$mockup_url = wp_get_attachment_url( $mockup_id );

		$html = '';
		$html .= '<div class="row">';

		$html .= '	<div class="mockup-add-image">';
		$html .= '		<input type="hidden" name="mockup_image_id" id="mockup_image" value="' . (($mockup_id)?$mockup_id:'') . '" />';
		$html .= '		<input type="button" id="mockup-add-image-button" class="button load_media" value="Select File" />';
		$html .= '	</div>';

		$html .= '	<div class="mockup-image">';
		$html .= '		<img class="mockup-image-src ' . ((!$mockup_id)?'hide':'') . '" src="' . (($mockup_url)?$mockup_url:'') . '" />';
		$html .= '	</div>';

		$html .= '</div>';

		echo $html;
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

		$post_types = Smart_Reviews_Setup::post_types();

		// Check if post type is one of the plugin's
		if ( !in_array( get_post_type(), array_keys( $post_types ) ) )
			return $post_id;

		// Loop through the plugin post types
		foreach ( $post_types as $post_type => $type_opt ) {

			// For each post types, loop through each post meta
			foreach ( $type_opt['post_meta'] as $id ) {

				$old = get_post_meta( $post_id, $id, true );
				$new = isset( $_POST[$id] )?$_POST[$id]:'';

				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $id, $new );
				}
			}
		}

	}
}
