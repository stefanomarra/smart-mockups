<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smart_Reviews
 * @subpackage Smart_Reviews/public
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Reviews_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smart_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smart_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min/smart-reviews-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smart_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smart_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smart-reviews-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * This function handles ajax requests that saves a new feedback
	 * from the public-facing side of the site
	 *
	 * @since 1.0.0
	 */
	public function save_feedback_ajax() {
		$feedback = array(
			'post_id' 		=> strip_tags( trim( $_POST['post_id'] ) ),
			'time'			=> current_time( get_option( 'date_format' ) ),
			'x'				=> $_POST['x'],
			'y'				=> $_POST['y'],
			'feedback_id'  	=> $_POST['feedback_id'],
			'comment'  		=> '<li><div class="sr-avatar"><img src="' . $this->get_user_avatar() . '" /></div><div class="sr-comment-content"><span class="sr-user-display">' . $this->get_user_display_name() . '</span> <span class="sr-comment-time">' . current_time( get_option( 'date_format' ) ) . '</span> <span class="sr-comment-text">' . $_POST['comment'] . '</span></div></li>'
		);

		if ( ! $feedback['feedback_id'] ) {
			$feedback['feedback_id'] = $this->generate_feedback_id();
		}

		$feedback['status'] = $this->save_feedback( $feedback );

		echo json_encode( $feedback );
		die();
	}

	/**
	 * This function stores a feedback as post_meta
	 *
	 * @since 1.0.0
	 */
	public function save_feedback($feedback) {

		$feedbacks = array();

		// Post has no feedbacks
		if ( !get_post_meta( $feedback['post_id'], '_feedbacks', true ) ) {

			$feedbacks[ $feedback['feedback_id'] ] = array(
				'feedback_id' 	=> $feedback['feedback_id'],
				'x' 			=> $feedback['x'],
				'y' 			=> $feedback['y'],
				'time' 			=> $feedback['time'],
				'comments' 		=> array($feedback['comment'])
			);
			update_post_meta( $feedback['post_id'], '_feedbacks', $feedbacks );

			return 'new_feedback_saved';
		}

		// Post has feedbacks
		else {
			$feedbacks = get_post_meta( $feedback['post_id'], '_feedbacks', true );

			// If the feedback id exists in this post, update only X, Y and add new comment
			if ( isset( $feedbacks[ $feedback['feedback_id'] ] ) ) {

				// Add new comment
				$comments = $feedbacks[ $feedback['feedback_id'] ]['comments'];
				$comments[] = $feedback['comment'];

				$feedbacks[ $feedback['feedback_id'] ] = array(
					'feedback_id' 	=> $feedback['feedback_id'],
					'x' 			=> $feedback['x'],
					'y' 			=> $feedback['y'],
					'time' 			=> $feedbacks[ $feedback['feedback_id'] ]['time'],
					'comments' 		=> $comments
				);
				update_post_meta( $feedback['post_id'], '_feedbacks', $feedbacks );

				return 'feedback_updated';
			}

			// If feedback doesn't exists add this feedback
			else {
				$feedbacks[ $feedback['feedback_id'] ] = array(
					'feedback_id' 	=> $feedback['feedback_id'],
					'x' 			=> $feedback['x'],
					'y' 			=> $feedback['y'],
					'time' 			=> $feedback['time'],
					'comments' 		=> array($feedback['comment'])
				);
				update_post_meta( $feedback['post_id'], '_feedbacks', $feedbacks );

				return 'new_feedback_saved';
			}

		}
	}

	/**
	 * This function handles user display name
	 *
	 * @since 1.0.0
	 * @return string The user display name
	 */
	public function get_user_display_name() {
		$current_user = wp_get_current_user();

		if ( $current_user->ID == 0 )
			return 'Guest';
		else
			return $current_user->display_name;
	}

	/**
	 * This function handles user avatar
	 *
	 * @since 1.0.0
	 * @return string URL of user avatar image
	 */
	public function get_user_avatar() {
		$current_user = wp_get_current_user();

		return get_avatar_url( $current_user->ID, array('size' => 50) );
	}

	/**
	 * This function generated a feedback id based on actual timestamp
	 *
	 * @since 1.0.0
	 */
	public function generate_feedback_id() {
		$ts = time();

		return md5( $ts );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function single_template( $single ) {
		global $post;

		if ( $post->post_type == 'smartreview' ) {
			return plugin_dir_path( __FILE__ ) . 'templates/smart-reviews-public-display.php';
		}
        return $single;
	}

	/**
	 * Overrides the custom post type slug
	 *
	 * @since    1.0.0
	 */
	public function override_slug() {
		$post_types = Smart_Reviews_Setup::post_types();
		$default_slug = $post_types['smartreview']['rewrite']['slug'];

        $slug = get_option('smartreviews_slug', $default_slug);

        if ( ( $current_rules = get_option('rewrite_rules') ) ) {
	        foreach ( $current_rules as $key => $val ) {
	            // var_dump( $current_rules );die();
	            if ( strpos( $key, $default_slug ) !== false ) {
	                add_rewrite_rule( str_ireplace( $default_slug, $slug, $key ), $val, 'top' );
	            }
	        }
	    }

        flush_rewrite_rules();
    }

}
