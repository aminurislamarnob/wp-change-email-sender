<?php

namespace WeLabs\WpChangeEmailSender\Admin\REST;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

/**
 * Test email REST API controller.
 */
class TestEmailController extends WP_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The base of this controller's route.
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'wp-change-email-sender/v1';
		$this->rest_base = 'send-test-email';
	}

	/**
	 * Register the routes for the controller.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'send_test_email' ),
					'permission_callback' => array( $this, 'send_test_email_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
			)
		);
	}

	/**
	 * Send a test email.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return \WP_REST_Response|WP_Error The response or error object.
	 */
	public function send_test_email( $request ) {
		$recipient = sanitize_email( $request->get_param( 'recipient' ) );

		if ( ! is_email( $recipient ) ) {
			return new WP_Error(
				'invalid_email',
				__( 'Please enter a valid email address.', 'wp-change-email-sender' ),
				array( 'status' => 400 )
			);
		}

		$site_name = get_bloginfo( 'name' );
		$settings  = get_option( 'wp_change_email_sender_settings', array() );

		$from_name  = ! empty( $settings['wp_change_email_sender_name'] ) ? $settings['wp_change_email_sender_name'] : __( 'Not configured', 'wp-change-email-sender' );
		$from_email = ! empty( $settings['wp_change_email_sender_email_address'] ) ? $settings['wp_change_email_sender_email_address'] : __( 'Not configured', 'wp-change-email-sender' );

		/* translators: %s: site name */
		$subject = sprintf( __( 'Test Email from %s', 'wp-change-email-sender' ), $site_name );
		$custom_message = sanitize_textarea_field( $request->get_param( 'message' ) );
		$body           = $this->get_email_body( $site_name, $from_name, $from_email, $recipient, $custom_message );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$mail_error    = null;
		$capture_error = function ( $wp_error ) use ( &$mail_error ) {
			$mail_error = $wp_error;
		};

		add_action( 'wp_mail_failed', $capture_error );
		$result = wp_mail( $recipient, $subject, $body, $headers );
		remove_action( 'wp_mail_failed', $capture_error );

		if ( ! $result ) {
			$error_message = __( 'Failed to send test email.', 'wp-change-email-sender' );

			if ( $mail_error instanceof WP_Error ) {
				$error_message .= ' ' . $mail_error->get_error_message();
			}

			return new WP_Error( 'email_failed', $error_message, array( 'status' => 500 ) );
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Test email sent successfully! Check your inbox.', 'wp-change-email-sender' ),
			)
		);
	}

	/**
	 * Build the HTML body for the test email.
	 *
	 * @param string $site_name      The site name.
	 * @param string $from_name      The configured sender name.
	 * @param string $from_email     The configured sender email.
	 * @param string $recipient      The recipient email address.
	 * @param string $custom_message Optional custom message from the user.
	 * @return string
	 */
	private function get_email_body( $site_name, $from_name, $from_email, $recipient, $custom_message = '' ) {
		$from_name_label  = esc_html__( 'From Name', 'wp-change-email-sender' );
		$from_email_label = esc_html__( 'From Email', 'wp-change-email-sender' );
		$sent_to_label    = esc_html__( 'Sent To', 'wp-change-email-sender' );

		/* translators: %s: site name */
		$heading = sprintf( esc_html__( 'Test Email from %s', 'wp-change-email-sender' ), esc_html( $site_name ) );
		$intro   = esc_html__( 'This is a test email sent by the WP Change Email Sender plugin to verify your email sender settings.', 'wp-change-email-sender' );
		$footer  = esc_html__( 'If you received this email, your sender settings are working correctly.', 'wp-change-email-sender' );

		return '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f7;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7;padding:40px 0;">
<tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
	<tr><td style="background:#5539FD;padding:24px 32px;">
		<h1 style="margin:0;color:#ffffff;font-size:20px;font-weight:600;">' . $heading . '</h1>
	</td></tr>
	<tr><td style="padding:32px;">
		<p style="margin:0 0 24px;color:#51545e;font-size:15px;line-height:1.6;">' . $intro . '</p>'
		. ( ! empty( $custom_message ) ? '<p style="margin:0 0 24px;color:#1f2937;font-size:15px;line-height:1.6;background:#f9fafb;padding:16px;border-radius:6px;">' . nl2br( esc_html( $custom_message ) ) . '</p>' : '' ) . '
		<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:6px;overflow:hidden;">
			<tr style="background:#f9fafb;">
				<td style="padding:10px 16px;font-size:13px;color:#6b7280;border-bottom:1px solid #e5e7eb;width:120px;">' . $from_name_label . '</td>
				<td style="padding:10px 16px;font-size:14px;color:#1f2937;border-bottom:1px solid #e5e7eb;">' . esc_html( $from_name ) . '</td>
			</tr>
			<tr>
				<td style="padding:10px 16px;font-size:13px;color:#6b7280;border-bottom:1px solid #e5e7eb;">' . $from_email_label . '</td>
				<td style="padding:10px 16px;font-size:14px;color:#1f2937;border-bottom:1px solid #e5e7eb;">' . esc_html( $from_email ) . '</td>
			</tr>
			<tr style="background:#f9fafb;">
				<td style="padding:10px 16px;font-size:13px;color:#6b7280;">' . $sent_to_label . '</td>
				<td style="padding:10px 16px;font-size:14px;color:#1f2937;">' . esc_html( $recipient ) . '</td>
			</tr>
		</table>
		<p style="margin:24px 0 0;color:#51545e;font-size:14px;line-height:1.6;">' . $footer . '</p>
	</td></tr>
</table>
</td></tr>
</table>
</body>
</html>';
	}

	/**
	 * Check if a given request has access to send a test email.
	 *
	 * @return bool True if the request has access, false otherwise.
	 */
	public function send_test_email_permissions_check() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get the schema for a single item, if any.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'test-email',
			'type'       => 'object',
			'properties' => array(
				'recipient' => array(
					'description' => __( 'Recipient email address for the test email.', 'wp-change-email-sender' ),
					'type'        => 'string',
					'format'      => 'email',
					'required'    => true,
					'context'     => array( 'edit' ),
				),
				'message'   => array(
					'description' => __( 'Optional custom message to include in the test email.', 'wp-change-email-sender' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
				),
			),
		);
	}
}
