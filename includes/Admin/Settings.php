<?php

namespace WeLabs\WpChangeEmailSender\Admin;

/**
 * Plugin admin page settings class
 */
class Settings {
	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_settings_menu' ), 100 );
		add_filter( 'plugin_action_links_' . WP_CHANGE_EMAIL_SENDER_BASENAME, array( $this, 'plugin_action_link' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_settings_scripts' ), 10 );
		}
	}

	/**
	 * Register settings main menu
	 *
	 * @return void
	 */
	public function add_admin_settings_menu() {
        add_options_page(
            __( 'WP Change Email Sender Settings', 'wp-change-email-sender' ),
            __( 'Change Email Sender', 'wp-change-email-sender' ),
            'manage_options',
            'wp_change_email_sender-settings',
            array( $this, 'settings_page_content' ),
			1
        );
	}

	/**
	 * Add Settings action link on the plugin screen.
	 *
	 * @param mixed $links Plugin Action links.
	 *
	 * @return array
	 */
    public function plugin_action_link( $links ) {
        $plugin_action_links = array(
			'<a href="' . esc_url( admin_url( 'options-general.php?page=wp_change_email_sender-settings' ) ) . '"> ' . __( 'Settings', 'wp-change-email-sender' ) . '</a>',
        );
        return array_merge( $plugin_action_links, $links );
    }

	/**
	 * Plugin settings page
	 *
	 * @return void
	 */
	public function settings_page_content() {
		echo '<div id="WpChangeEmailSenderSettings"></div>';
	}

	/**
	 * Enqueue admin settings scripts
	 *
	 * @return void
	 */
	public function enqueue_admin_settings_scripts() {
		$screen = get_current_screen();

		if ( 'settings_page_wp_change_email_sender-settings' !== $screen->id ) {
			return;
		}

		$asset_file_path = WP_CHANGE_EMAIL_SENDER_DIR . '/assets/build/admin/script.asset.php';

		if ( ! file_exists( $asset_file_path ) ) {
			return;
		}

		$asset_file = include $asset_file_path;
		$handle     = 'wp_change_email_sender_admin_page';

		wp_enqueue_script(
			$handle,
			WP_CHANGE_EMAIL_SENDER_PLUGIN_ASSET . '/build/admin/script.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		wp_add_inline_script(
			$handle,
			sprintf(
				'window.__wpcesBuildURL = %s;',
				wp_json_encode( WP_CHANGE_EMAIL_SENDER_PLUGIN_ASSET . '/build/' )
			),
			'before'
		);

		wp_add_inline_script(
			$handle,
			sprintf(
				'window.__wpcesCurrentUserEmail = %s;',
				wp_json_encode( wp_get_current_user()->user_email )
			),
			'before'
		);

		wp_enqueue_style(
			'wp_change_email_sender_admin_styles',
			WP_CHANGE_EMAIL_SENDER_PLUGIN_ASSET . '/build/admin.css',
			array( 'wp-components' ),
			$asset_file['version'],
		);

		wp_enqueue_style( 'wp-components' );
	}
}