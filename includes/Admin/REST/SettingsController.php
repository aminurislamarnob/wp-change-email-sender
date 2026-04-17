<?php

namespace WeLabs\WpChangeEmailSender\Admin\REST;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Admin settings REST API controller.
 */
class SettingsController extends WP_REST_Controller {

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
	 *
	 * Sets the namespace and rest base for the controller.
	 */
	public function __construct() {
		$this->namespace = 'wp-change-email-sender/v1';
		$this->rest_base = 'settings';
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'get_settings_permissions_check' ),
					'args'                => array(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'update_settings_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
			)
		);
	}

	/**
	 * Get the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error The response or error object.
	 */
	public function get_settings( $request ) {
		$settings = get_option( 'wp_change_email_sender_settings', array() );

		return rest_ensure_response( $settings );
	}

	/**
	 * Update the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error The response or error object.
	 */
	public function update_settings( $request ) {
		$wp_change_email_sender_settings = get_option( 'wp_change_email_sender_settings', array() );

		if ( $request->has_param( 'wp_change_email_sender_name' ) ) {
			$wp_change_email_sender_settings['wp_change_email_sender_name'] = sanitize_text_field( $request->get_param( 'wp_change_email_sender_name' ) );
		}

		if ( $request->has_param( 'wp_change_email_sender_email_address' ) ) {
			$email = $request->get_param( 'wp_change_email_sender_email_address' );
			if ( ! empty( $email ) && ! is_email( $email ) ) {
				return new WP_Error( 'rest_invalid_param', __( 'The email address you entered is invalid.', 'wp-change-email-sender' ), array( 'status' => 400 ) );
			}
			$wp_change_email_sender_settings['wp_change_email_sender_email_address'] = sanitize_email( $email );
		}

		if ( $request->has_param( 'force_from_name' ) ) {
			$wp_change_email_sender_settings['force_from_name'] = (bool) $request->get_param( 'force_from_name' );
		}

		if ( $request->has_param( 'force_from_email' ) ) {
			$wp_change_email_sender_settings['force_from_email'] = (bool) $request->get_param( 'force_from_email' );
		}

		update_option( 'wp_change_email_sender_settings', $wp_change_email_sender_settings );

		return $this->get_settings( $request );
	}

	/**
	 * Check if a given request has access to get the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool True if the request has access, false otherwise.
	 */
	public function get_settings_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check if a given request has access to update the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool True if the request has access, false otherwise.
	 */
	public function update_settings_permissions_check( $request ) {
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
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => array(
				'wp_change_email_sender_name'             => array(
					'description' => __( 'Email Sender Name.', 'wp-change-email-sender' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'wp_change_email_sender_email_address'    => array(
					'description' => __( 'Sender Email Address.', 'wp-change-email-sender' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'force_from_name'                         => array(
					'description' => __( 'Force from name on all emails', 'wp-change-email-sender' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
				),
				'force_from_email'                        => array(
					'description' => __( 'Force from email on all emails', 'wp-change-email-sender' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);
	}
}
