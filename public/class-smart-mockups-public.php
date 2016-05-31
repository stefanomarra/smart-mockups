<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.stefanomarra.com
 * @since      1.0.0
 *
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/public
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Mockups_Public {

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

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

	}

	/**
	 * Save approval signature from the public-facing side of the site
	 *
	 * @since 1.0.0
	 */
	public function save_approval_signature_ajax() {
		$approval = array(
				'post_id' 		=> strip_tags( trim( $_POST['post_id'] ) ),
				'time'			=> current_time( get_option( 'date_format' ) ),
				'signature'  	=> sanitize_text_field( $_POST['signature'] )
			);

		do_action( 'smartmockups_before_save_approval_signature', $approval );

		$approval['status'] = $this->save_approval_signature( $approval );

		do_action( 'smartmockups_after_save_approval_signature', $approval );

		echo json_encode( $approval );
		wp_die();
	}

	/**
	 * This function stores a mockup approval as post_meta
	 *
	 * @since 1.0.0
	 */
	public function save_approval_signature($approval) {

		$result_approval = get_post_meta( $approval['post_id'], '_approval', true );

		if ( ! $result_approval ) {

			$result_approval = array(
					'time'      => $approval['time'],
					'signature' => $approval['signature']
				);

			update_post_meta( $approval['post_id'], '_approval', $result_approval );

			return 'approval_saved';
		}

		return 'approval_exists';
	}


	/**
	 * Save a new discussion comment from the public-facing side of the site
	 *
	 * @since 1.0.0
	 */
	public function save_discussion_comment_ajax() {
		$comment = array(
			'post_id' 		=> strip_tags( trim( $_POST['post_id'] ) ),
			'time'			=> current_time( get_option( 'date_format' ) ),
			'comment'  		=> '<li><div class="sr-avatar"><img src="' . $this->get_user_avatar() . '" /></div><div class="sr-comment-content"><span class="sr-user-display" style="background-color: ' . $this->get_user_color() . '">' . $this->get_user_display_name() . '</span> <span class="sr-comment-time">' . current_time( get_option( 'date_format' ) ) . '</span> <span class="sr-comment-text">' . make_clickable( stripslashes( $_POST['comment'] ) ) . '</span></div></li>',
			'action'		=> 'save_discussion_comment'
		);

		do_action( 'smartmockups_before_save_discussion_comment', $comment );

		$comment['status'] = $this->save_discussion_comment( $comment );

		do_action( 'smartmockups_after_save_discussion_comment', $comment );

		echo json_encode( $comment );
		wp_die();
	}

	/**
	 * This function stores a discussion as post_meta
	 *
	 * @since 1.0.0
	 */
	public function save_discussion_comment($comment) {

		// Get post discussion
		$discussion = get_post_meta( $comment['post_id'], '_discussion', true );

		// If not an array, initialize an empty array
		if ( !is_array( $discussion ) ) {
			$discussion = array(
				'time'     => $comment['time'],
				'comments' => array()
			);
		}

		// Add new comment
		$discussion['comments'][] = $comment['comment'];

		// Update post discussion
		update_post_meta( $comment['post_id'], '_discussion', $discussion );

		return 'new_discussion_comment_saved';
	}

	/**
	 * This function handles ajax requests that saves a new feedback from the front-end
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
			'comment'  		=> '<li><div class="sr-avatar"><img src="' . $this->get_user_avatar() . '" /></div><div class="sr-comment-content"><span class="sr-user-display" style="background-color: ' . $this->get_user_color() . '">' . $this->get_user_display_name() . '</span> <span class="sr-comment-time">' . current_time( get_option( 'date_format' ) ) . '</span> <span class="sr-comment-text">' . make_clickable( stripslashes( $_POST['comment'] ) ) . '</span></div></li>',
			'action'		=> 'save_feedback'
		);

		if ( ! $feedback['feedback_id'] ) {
			$feedback['feedback_id'] = $this->generate_feedback_id();
		}

		do_action( 'smartmockups_before_save_feedback', $feedback );

		$feedback['status'] = $this->save_feedback( $feedback );

		do_action( 'smartmockups_after_save_feedback', $feedback );

		echo json_encode( $feedback );
		wp_die();
	}

	/**
	 * This function updates an existing feedback position
	 *
	 * @since 1.0.0
	 */
	public function update_feedback_position_ajax() {
		$feedback = array(
			'post_id' 		=> strip_tags( trim( $_POST['post_id'] ) ),
			'x'				=> $_POST['x'],
			'y'				=> $_POST['y'],
			'feedback_id'  	=> $_POST['feedback_id'],
			'action'		=> 'update_feedback_position'
		);

		do_action( 'smartmockups_before_update_feedback_position', $feedback );

		$feedback['status'] = $this->save_feedback( $feedback );

		do_action( 'smartmockups_after_update_feedback_position', $feedback );

		echo json_encode( $feedback );
		wp_die();
	}

	/**
	 * This function deletes an existing feedback
	 *
	 * @since 1.0.0
	 */
	public function delete_feedback_ajax() {
		$feedback = array(
			'post_id' 		=> strip_tags( trim( $_POST['post_id'] ) ),
			'feedback_id'  	=> $_POST['feedback_id'],
			'action'		=> 'delete_feedback'
		);

		do_action( 'smartmockups_before_delete_feedback', $feedback );

		$feedback['status'] = $this->delete_feedback( $feedback );

		do_action( 'smartmockups_after_delete_feedback', $feedback );

		echo json_encode( $feedback );
		wp_die();
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

			// If action is update_feedback_position and mockup has no feedbacks
			if ( $feedback['action'] == 'update_feedback_position')
				return 'no_feedbacks';

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

				// If action is not update_feedback_position add comment
				if ( $feedback['action'] != 'update_feedback_position')
					$comments[] = $feedback['comment'];

				$feedbacks[ $feedback['feedback_id'] ] = array(
					'feedback_id' 	=> $feedback['feedback_id'],
					'x' 			=> $feedback['x'],
					'y' 			=> $feedback['y'],
					'time' 			=> $feedbacks[ $feedback['feedback_id'] ]['time'],
					'comments' 		=> $comments
				);
				update_post_meta( $feedback['post_id'], '_feedbacks', $feedbacks );

				if ( $feedback['action'] == 'update_feedback_position')
					return 'feedback_position_updated';
				else
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
	 * This function deletes a feedback
	 *
	 * @since 1.0.0
	 */
	public function delete_feedback($feedback) {

		$feedbacks = array();

		// Post has no feedbacks
		if ( !get_post_meta( $feedback['post_id'], '_feedbacks', true ) ) {
			return 'no_feedbacks';
		}

		// Post has feedbacks
		else {
			$feedbacks = get_post_meta( $feedback['post_id'], '_feedbacks', true );

			// If the feedback id exists in this post, delete it
			if ( isset( $feedbacks[ $feedback['feedback_id'] ] ) ) {

				unset( $feedbacks[ $feedback['feedback_id'] ] );
				update_post_meta( $feedback['post_id'], '_feedbacks', $feedbacks );

				return 'feedback_deleted';
			}

			// If feedback doesn't
			else {
				return 'feedback_not_found';
			}

		}
	}

	/**
	 * Update user feedbacks cookie
	 *
	 * @since 1.2.0
	 */
	public function update_user_feedbacks($feedback) {
		if ( $feedback['status'] == 'new_feedback_saved' ) {
			$this->setcookie_user_feedback($feedback['feedback_id']);
		}

		if ( $feedback['status'] == 'feedback_deleted' ) {
			$this->setcookie_remove_user_feedback($feedback['feedback_id']);
		}
	}

	/**
	 * This function handles guest display name
	 *
	 * @since 1.0.0
	 * @return string The guest display name
	 */
	public function get_guest_display_name() {

		if ( sm_is_guest_display_name_required() ) {

			if ( isset( $_POST['guest_display_name'] ) ) {
				$name = sanitize_user( $_POST['guest_display_name'] );

				if ( ! isset( $_COOKIE['sm_gst_dsp_n'] ) || ( isset( $_COOKIE['sm_gst_dsp_n'] ) && $_COOKIE['sm_gst_dsp_n'] != $name ) ) {
					$this->setcookie_guest_display_name( $name );
				}
				$display_name = $name;
			}
			else if ( isset( $_COOKIE['sm_gst_dsp_n'] ) ) {
				$display_name = $_COOKIE['sm_gst_dsp_n'];
			}
			else {
				$display_name = 'Guest';
			}

		}
		else {
			$display_name = 'Guest';
		}

		return apply_filters( 'smartmockups_guest_display_name', $display_name );
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
			return $this->get_guest_display_name();
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
	 * Generate a random RGB color
	 *
	 * @since 1.0.3
	 */
	public function generate_rgb_color() {
		$rand_str = md5( time() . rand( 0, 999 ) );
		$rgb = '#';
		$rgb .= substr( $rand_str, 0, 2 );
		$rgb .= substr( $rand_str, 2, 2 );
		$rgb .= substr( $rand_str, 4, 2 );

		return apply_filters( 'smartmockups_generate_rgb_color', $rgb );
	}

	/**
	 * Set random generated user color in cookie
	 *
	 * @since 1.0.3
	 */
	public function setcookie_user_color() {
		if ( ! isset( $_COOKIE['sr_usr_col'] ) ) {
			$color = $this->generate_rgb_color();
			setcookie( 'sr_usr_col', apply_filters( 'smartmockups_setcookie_user_color', $color ), time() + YEAR_IN_SECONDS );
		}
	}

	/**
	 * Set guest display name in cookie
	 *
	 * @since 1.1.0
	 */
	public function setcookie_guest_display_name( $name ) {
		setcookie( 'sm_gst_dsp_n', $name, time() + MONTH_IN_SECONDS, '/', $_SERVER['SERVER_NAME'], 0, 0 );
	}

	/**
	 * Set user feedback ID in cookie
	 *
	 * @since 1.2.0
	 */
	public function setcookie_user_feedback($feedback_id) {
		$user_feedbacks = sm_get_user_feedbacks();

		if ( ! $user_feedbacks ) {
			$user_feedbacks = array();
		}

		$user_feedbacks[] = $feedback_id;
		setcookie( 'sm_my_fdbks', base64_encode( json_encode( $user_feedbacks ) ), time() + WEEK_IN_SECONDS, '/', $_SERVER['SERVER_NAME'], 0, 0 );
	}

	/**
	 * Remove a user feedback ID previously added in cookie
	 *
	 * @since 1.2.0
	 */
	public function setcookie_remove_user_feedback($feedback_id) {
		$user_feedbacks = sm_get_user_feedbacks();

		if ( ! $user_feedbacks ) {
			$user_feedbacks = array();
		}

		if ( ( $key = array_search( $feedback_id, $user_feedbacks ) ) !== false ) {
			unset($user_feedbacks[$key]);
		}

		setcookie( 'sm_my_fdbks', base64_encode( json_encode( $user_feedbacks ) ), time() + WEEK_IN_SECONDS, '/', $_SERVER['SERVER_NAME'], 0, 0 );
	}

	/**
	 * Get random generated user color from cookie or generate one and save cookie
	 *
	 * @since 1.0.0
	 * @return string URL of user avatar image
	 */
	public function get_user_color() {
		$current_user = wp_get_current_user();

		if ( isset( $_COOKIE['sr_usr_col'] ) ) {
			$rgb = $_COOKIE['sr_usr_col'];
		}
		else {
			$rgb = $this->generate_rgb_color();
		}

		return apply_filters( 'smartmockups_get_user_color', $rgb );
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
	 * Register a custom password form
	 *
	 * @since 1.0.0
	 */
	public function password_form() {
		global $post;

		$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );

		$html  = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">';
		$html .= '	<label for="' . $label . '">' . __( "Password:" ) . ' </label>';
		$html .= '	<input name="post_password" id="' . $label . '" type="password" size="20" maxlength="20" placeholder="Password" autofocus />';
		$html .= '	<input type="submit" name="Submit" value="' . esc_attr__( "Submit" ) . '" />';
		$html .= '</form>';

		return $html;
	}

	/**
	 * Filter post discussion
	 *
	 * @since 1.0.0
	 */
	public function get_discussion( $discussion = null ) {
		if ( ! is_array( $discussion ) ) {
			return array( 'comments' => '' );
		}
		else if ( isset( $discussion['comments'] ) ) {
			return array( 'comments' => join( '', (array)$discussion['comments'] ) );
		}
		else {
			return array( 'comments' => '' );
		}
	}

	/**
	 * Filter post feedbacks
	 *
	 * @since 1.0.0
	 */
	public function get_feedbacks( $feedbacks = null ) {
		if ( ! is_array( $feedbacks ) ) {
			return array();
		}

		return $feedbacks;
	}

	/**
	 * Register the function that overrides the single template layout
	 *
	 * @since    1.0.0
	 */
	public function single_template( $single ) {
		global $post;

		if ( $post->post_type != SMART_MOCKUPS_POSTTYPE )
        	return $single;

        add_filter('show_admin_bar', '__return_false');

        // If the mockup is password protected, show the password form
        if ( post_password_required() ) {
        	add_filter( 'the_password_form', array($this, 'password_form') );
        	return plugin_dir_path( __FILE__ ) . 'templates/smart-mockups-password-display.php';
        }
        else {
			return plugin_dir_path( __FILE__ ) . 'templates/smart-mockups-public-display.php';
        }

	}

	/**
	 * Overrides the custom post type slug
	 *
	 * @since    1.0.0
	 */
	public function override_slug() {
		$post_types = Smart_Mockups_Setup::post_types();
		$default_slug = $post_types[SMART_MOCKUPS_POSTTYPE]['rewrite']['slug'];

        $slug = get_option('smartmockups_slug', $default_slug);

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
