<?php

namespace WeLabs\WpChangeEmailSender;

class OverrideEmailSender {

    /**
     * Original from email set by another plugin (e.g. WooCommerce) before
     * this plugin forced its own address. Stored so it can be added as
     * Reply-To in phpmailer_init.
     *
     * @var string
     */
    private $original_from_email = '';

    /**
     * The constructor.
     */
    public function __construct() {
        add_filter( 'wp_mail_from', array( $this, 'wpces_mail_sender_from_email' ), PHP_INT_MAX );
        add_filter( 'wp_mail_from_name', array( $this, 'wpces_mail_sender_from_email_name' ), PHP_INT_MAX );
        add_action( 'phpmailer_init', array( $this, 'set_reply_to_from_original_email' ), PHP_INT_MAX );
    }

    /**
     * Change WordPress Default Mail Sender Email Address.
     *
     * @param string $old Default from email.
     * @return string
     */
    public function wpces_mail_sender_from_email( $old ) {
        $this->original_from_email = '';

        $email = $this->get_sender_value( 'wp_change_email_sender_email_address', 'wpces_sender_email_address' );
        $force = $this->is_forced_email();

        if ( '' === $email ) {
            return $old;
        }

        $default_email = $this->get_default_email();

        // Capture the original non-default from email so it can be
        // preserved as Reply-To when force overrides it.
        if ( $force && $old !== $default_email && $old !== sanitize_email( $email ) ) {
            $this->original_from_email = $old;
        }

        if ( $force || $old === $default_email ) {
            return sanitize_email( $email );
        }

        return $old;
    }

    /**
     * Change WordPress Default Mail Sender Name.
     *
     * @param string $old Default from name.
     * @return string
     */
    public function wpces_mail_sender_from_email_name( $old ) {
        $name = $this->get_sender_value( 'wp_change_email_sender_name', 'wpces_email_sender_name' );
        $force = $this->is_forced_name();

        if ( '' === $name ) {
            return $old;
        }

        if ( $force || $old === 'WordPress' ) {
            return sanitize_text_field( $name );
        }

        return $old;
    }

    /**
     * When force from email overrides a non-default sender address set by
     * another plugin (e.g. WooCommerce), preserve that address as Reply-To
     * so recipients can still reply to the original sender.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer The PHPMailer instance.
     * @return void
     */
    public function set_reply_to_from_original_email( $phpmailer ) {
        if ( empty( $this->original_from_email ) ) {
            return;
        }

        // Do not overwrite an existing Reply-To set by the sending plugin.
        if ( ! empty( $phpmailer->getReplyToAddresses() ) ) {
            $this->original_from_email = '';
            return;
        }

        $validated = filter_var( $this->original_from_email, FILTER_VALIDATE_EMAIL );

        if ( $validated ) {
            $phpmailer->addReplyTo( $validated );
        }

        // Reset for the next wp_mail() call.
        $this->original_from_email = '';
    }

    /**
     * Get the default email address based on domain name.
     *
     * @return string
     */
    private function get_default_email() {
        $server_name = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';

        if ( empty( $server_name ) ) {
            $server_name = wp_parse_url( network_home_url(), PHP_URL_HOST );
        }

        if ( empty( $server_name ) ) {
            return '';
        }

        // Get rid of www.
        $sitename = strtolower( $server_name );
        if ( substr( $sitename, 0, 4 ) === 'www.' ) {
            $sitename = substr( $sitename, 4 );
        }

        return 'wordpress@' . $sitename;
    }

    /**
     * Check if forcing from email is active.
     *
     * @return bool
     */
    private function is_forced_email() {
        $settings = get_option( 'wp_change_email_sender_settings', array() );
        return ! empty( $settings['force_from_email'] );
    }

    /**
     * Check if forcing from name is active.
     *
     * @return bool
     */
    private function is_forced_name() {
        $settings = get_option( 'wp_change_email_sender_settings', array() );
        return ! empty( $settings['force_from_name'] );
    }

    /**
     * Read a sender value from the unified settings option, falling back
     * to the legacy standalone option if the new key is empty.
     *
     * The fallback protects installs where the 3.3 migration has not yet
     * run (for example, sites updated via a non-standard path that
     * fires wp_mail() before plugins_loaded completes).
     *
     * @param string $new_key    Key inside the wp_change_email_sender_settings array.
     * @param string $legacy_key Legacy standalone option name.
     * @return string
     */
    private function get_sender_value( $new_key, $legacy_key ) {
        $settings = get_option( 'wp_change_email_sender_settings', array() );

        if ( is_array( $settings ) && ! empty( $settings[ $new_key ] ) ) {
            return (string) $settings[ $new_key ];
        }

        $legacy = get_option( $legacy_key, '' );

        return is_string( $legacy ) ? $legacy : '';
    }
}
