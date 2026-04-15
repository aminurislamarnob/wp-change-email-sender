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
     * Change WordPress Default Mail Sender Email Address
     *
     * @param string $old Default from email.
     * @return string
     */
    public function wpces_mail_sender_from_email( $old ) {
        $wpces_sender_email_address_value = get_option( 'wpces_sender_email_address' );

        if ( empty( $wpces_sender_email_address_value ) ) {
            return $old;
        }

        return sanitize_email( $wpces_sender_email_address_value );
    }


    /**
     * Change WordPress Default Mail Sender Name
     *
     * @param string $old Default from name.
     * @return string
     */
    public function wpces_mail_sender_from_email_name( $old ) {
        $wpces_email_sender_name_value = get_option( 'wpces_email_sender_name' );

        if ( empty( $wpces_email_sender_name_value ) ) {
            return $old;
        }

        return esc_html( $wpces_email_sender_name_value );
    }
}
