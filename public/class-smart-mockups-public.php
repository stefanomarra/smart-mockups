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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min/smart-mockups-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smart-mockups-public.js', array( 'jquery' ), $this->version, false );
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

		$approval['status'] = $this->save_approval_signature( $approval );

		echo json_encode( $approval );
		die();
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
			'comment'  		=> '<li><div class="sr-avatar"><img src="' . $this->get_user_avatar() . '" /></div><div class="sr-comment-content"><span class="sr-user-display" style="background-color: ' . $this->get_user_color() . '">' . $this->get_user_display_name() . '</span> <span class="sr-comment-time">' . current_time( get_option( 'date_format' ) ) . '</span> <span class="sr-comment-text">' . stripslashes( $_POST['comment'] ) . '</span></div></li>',
			'action'		=> 'save_discussion_comment'
		);

		$comment['status'] = $this->save_discussion_comment( $comment );

		echo json_encode( $comment );
		die();
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
			'comment'  		=> '<li><div class="sr-avatar"><img src="' . $this->get_user_avatar() . '" /></div><div class="sr-comment-content"><span class="sr-user-display" style="background-color: ' . $this->get_user_color() . '">' . $this->get_user_display_name() . '</span> <span class="sr-comment-time">' . current_time( get_option( 'date_format' ) ) . '</span> <span class="sr-comment-text">' . stripslashes( $_POST['comment'] ) . '</span></div></li>',
			'action'		=> 'save_feedback'
		);

		if ( ! $feedback['feedback_id'] ) {
			$feedback['feedback_id'] = $this->generate_feedback_id();
		}

		$feedback['status'] = $this->save_feedback( $feedback );

		echo json_encode( $feedback );
		die();
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

		$feedback['status'] = $this->save_feedback( $feedback );

		echo json_encode( $feedback );
		die();
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

		$feedback['status'] = $this->delete_feedback( $feedback );

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
			$rand_str = md5( time() . rand( 0, 999 ) );
			$rgb = '#';
			$rgb .= substr( $rand_str, 0, 2 );
			$rgb .= substr( $rand_str, 2, 2 );
			$rgb .= substr( $rand_str, 4, 2 );
			setcookie('sr_usr_col', $rgb, time() + 3600 * 24 * 365);
		}

		return $rgb;
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
	public function get_discussion( $discussion ) {
		if ( ! is_array( $discussion ) ) {
			return array( 'comments' => '' );
		}
		else if ( isset( $discussion['comments'] ) ) {
			return array( 'comments' => join( '', $discussion['comments'] ) );
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
	public function get_feedbacks( $feedbacks ) {
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
