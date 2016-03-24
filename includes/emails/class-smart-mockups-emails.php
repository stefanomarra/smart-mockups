<?php

/**
 * Emails API
 *
 * This class handles all emails sent through Smart Mockups
 *
 * @link       http://www.stefanomarra.com
 * @since      1.1.0
 * @package    Smart_Mockups
 * @subpackage Smart_Mockups/includes/emails
 * @author     Stefano <stefano.marra1987@gmail.com>
 */
class Smart_Mockups_Emails {

	/**
	 * Holds the from address
	 *
	 * @since 1.1.0
	 */
	private $from_address;

	/**
	 * Holds the from name
	 *
	 * @since 1.1.0
	 */
	private $from_name;

	/**
	 * Holds the email content type
	 *
	 * @since 1.1.0
	 */
	private $content_type;

	/**
	 * Holds the email headers
	 *
	 * @since 1.1.0
	 */
	private $headers;

	/**
	 * Whether to send email in HTML
	 *
	 * @since 1.1.0
	 */
	private $html = true;

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

	}

	/**
	 * Get the email from name
	 *
	 * @since 1.1.0
	 */
	public function get_from_name() {
		if ( ! $this->from_name ) {
			$this->from_name = get_bloginfo( 'name' );
		}

		return wp_specialchars_decode( $this->from_name );
	}

	/**
	 * Get the email from address
	 *
	 * @since 1.1.0
	 */
	public function get_from_address() {
		if ( ! $this->from_address ) {
			$this->from_address = get_option( 'admin_email' );
		}

		return $this->from_address;
	}

	/**
	 * Get the email content type
	 *
	 * @since 1.1.0
	 */
	public function get_content_type() {
		if ( ! $this->content_type && $this->html ) {
			$this->content_type = 'text/html';
		} else if ( ! $this->html ) {
			$this->content_type = 'text/plain';
		}

		return $this->content_type;
	}

	/**
	 * Get the email headers
	 *
	 * @since 1.1.0
	 */
	public function get_headers() {
		if ( ! $this->headers ) {
			$this->headers  = "From: {$this->get_from_name()} <{$this->get_from_address()}>\r\n";
			$this->headers .= "Reply-To: {$this->get_from_address()}\r\n";
			$this->headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";
		}

		return $this->headers;
	}

	/**
	 * Build the email
	 *
	 * @since 1.1.0
	 * @param string $message
	 *
	 * @return string
	 */
	public function build_email( $message = '' ) {

		if ( false === $this->html ) {
			return wp_strip_all_tags( $message );
		}

		$message = $this->text_to_html( $message );

		ob_start();

		sm_get_template_part( 'emails/header', null, true );
		sm_get_template_part( 'emails/body', null, true );
		sm_get_template_part( 'emails/footer', null, true );


		$body    = ob_get_clean();
		$message = str_replace( '{email}', $message, $body );

		return apply_filters( 'smartmockups_email_message', $message, $this );
	}

	/**
	 * Send the email
	 *
	 * @param  string  $to               The To address to send to.
	 * @param  string  $subject          The subject line of the email to send.
	 * @param  string  $message          The body of the email to send.
	 * @param  string|array $attachments Attachments to the email in a format supported by wp_mail()
	 * @since 1.1.0
	 */
	public function send( $to, $subject, $message, $attachments = '' ) {

		$message = $this->build_email( $message );

		$sent = wp_mail( $to, $subject, $message, $this->get_headers(), $attachments );

		if( ! $sent ) {
			if ( is_array( $to ) ) {
				$to = implode( ',', $to );
			}

			$log_message = sprintf(
				__( "Email from Smart Mockups failed to send.\nSend time: %s\nTo: %s\nSubject: %s\n\n", SMART_MOCKUPS_DOMAIN ),
				date_i18n( 'F j Y H:i:s', current_time( 'timestamp' ) ),
				$to,
				$subject
			);

			error_log( $log_message );
		}

		return $sent;
	}

	/**
	 * Converts text to HTML
	 *
	 * @since 1.1.0
	 */
	public function text_to_html( $message ) {

		if ( 'text/html' == $this->content_type || true === $this->html ) {
			$message = wpautop( $message );
		}

		return $message;
	}

}