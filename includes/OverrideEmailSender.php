<?php

namespace WeLabs\WpChangeEmailSender;

class OverrideEmailSender {

    /**
     * The constructor.
     */
    public function __construct() {
        add_filter( 'wp_mail_from', array( $this, 'wpces_mail_sender_from_email' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'wpces_mail_sender_from_email_name' ) );
    }

    /**
     * Change WordPress Default Mail Sender Email Address.
     *
     * @param string $old Default from email.
     * @return string
     */
    public function wpces_mail_sender_from_email( $old ) {
        $email = $this->get_sender_value( 'wp_change_email_sender_email_address', 'wpces_sender_email_address' );

        if ( '' === $email ) {
            return $old;
        }

        return sanitize_email( $email );
    }

    /**
     * Change WordPress Default Mail Sender Name.
     *
     * @param string $old Default from name.
     * @return string
     */
    public function wpces_mail_sender_from_email_name( $old ) {
        $name = $this->get_sender_value( 'wp_change_email_sender_name', 'wpces_email_sender_name' );

        if ( '' === $name ) {
            return $old;
        }

        return sanitize_text_field( $name );
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
