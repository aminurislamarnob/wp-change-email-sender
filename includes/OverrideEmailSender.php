<?php

namespace WeLabs\WpChangeEmailSender;

class OverrideEmailSender {
	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action('admin_init', array( $this, 'wpces_email_sender_register' ));
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'wpces_mail_sender_action_links' ) );
        add_filter('wp_mail_from', array( $this, 'wpces_mail_sender_from_email' ));
        add_filter('wp_mail_from_name', array( $this, 'wpces_mail_sender_from_email_name' ));
	}

	/**
     * Plugin settings page
     */
    public function wpces_email_sender_register() {
        
        // register a new section
        add_settings_section(
            'wpces_email_sender_settings_section', 
            __('WP Change Default Mail Sender Name and Email Address Options', 'wp-change-email-sender'), array( $this, 'wpces_email_sender_section_text' ), 
            'general'
        );

        // register a new field in the "wpces_email_sender_settings_section" section
        add_settings_field(
            'wpces_email_sender_name', 
            __('Email Sender Name','wp-change-email-sender'), array( $this, 'wpces_sender_name_field_callback' ), 
            'general',  
            'wpces_email_sender_settings_section'
        );

        // register a new setting for sender name field
        register_setting('general', 'wpces_email_sender_name');

        // register a new field in the "wpces_email_sender_settings_section" section
        add_settings_field(
            'wpces_sender_email_address', 
            __('Sender Email Address', 'wp-change-email-sender'), array( $this, 'wpces_sender_email_address_field_callback' ), 
            'general',  
            'wpces_email_sender_settings_section'
        );

        // register a new setting for email address field
        register_setting('general', 'wpces_sender_email_address');

    }

    //Sender Name field content
    public function wpces_sender_name_field_callback(){
        $wpces_email_sender_name_value = get_option('wpces_email_sender_name');
        printf('<input name="wpces_email_sender_name" type="text" class="regular-text" value="%s" placeholder="%s"/>', esc_attr($wpces_email_sender_name_value), esc_attr__('Mail Sender Name', 'wp-change-email-sender'));
    }

    //Sender Email field content
    public function wpces_sender_email_address_field_callback() {
        $wpces_sender_email_address_value = get_option('wpces_sender_email_address');
        printf('<input name="wpces_sender_email_address" type="email" class="regular-text" value="%s" placeholder="info@yourdomain.com"/>', esc_attr($wpces_sender_email_address_value));
    }

    //Plugin settings page section text
    public function wpces_email_sender_section_text() {
        printf('%s %s %s', '<p>', esc_attr__('You can change WordPress Default Mail Sender Name and Email Address', 'wp-change-email-sender'), '</p>');
    }

    /**
     * Add settings page link with plugin.
     */
    public function wpces_mail_sender_action_links( $links ){
        $wpces_mail_sender_plugin_action_links = array(
        '<a href="' . esc_url(admin_url( 'options-general.php' )) . '"> '. __('Settings', 'wp-change-email-sender') . '</a>',
        );
        return array_merge( $links, $wpces_mail_sender_plugin_action_links );
    }


    /**
     * Change Wordpress Default Mail Sender Email Address
     */
    public function wpces_mail_sender_from_email($old) {
        $wpces_sender_email_address_value = get_option('wpces_sender_email_address');
        return esc_html( $wpces_sender_email_address_value );
    }


    /**
     * Change Wordpress Default Mail Sender Name
     */
    public function wpces_mail_sender_from_email_name($old) {
        $wpces_email_sender_name_value = get_option('wpces_email_sender_name');
        return esc_html( $wpces_email_sender_name_value );
    }
}
