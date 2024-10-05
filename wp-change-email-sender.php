<?php
/*
Plugin Name: WP Change Email Sender
Plugin URI: https://wordpress.org/plugins/wp-change-email-sender/
Description: This plugin which allows you to change WordPress default mail sender name and email address easily.
Version: 2.0
Author: Aminur Islam
Author URI: https://github.com/aminurislamarnob
License: GPLv2 or later
Text Domain: wp-change-email-sender
Domain Path: /languages
*/


/**
 * Restrict this file to call directly
*/
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Currently plugin version.
*/
define('WPCES_EMAIL_SENDER_PLUGIN_VERSION', '1.0');

 
/**
 * Load plugin textdomain.
 */
function wpces_email_sender_load_textdomain() {
    load_plugin_textdomain( 'wp-change-email-sender', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'wpces_email_sender_load_textdomain' );


/**
 * Plugin settings page
 */
function wpces_email_sender_register() {
    
    // register a new section
    add_settings_section(
        'wpces_email_sender_settings_section', 
        __('WP Change Default Mail Sender Name and Email Address Options', 'wp-change-email-sender'), 'wpces_email_sender_section_text', 
        'general'
    );

    // register a new field in the "wpces_email_sender_settings_section" section
    add_settings_field(
        'wpces_email_sender_name', 
        __('Email Sender Name','wp-change-email-sender'), 'wpces_sender_name_field_callback', 
        'general',  
        'wpces_email_sender_settings_section'
    );

    // register a new setting for sender name field
	register_setting('general', 'wpces_email_sender_name');

    // register a new field in the "wpces_email_sender_settings_section" section
	add_settings_field(
        'wpces_sender_email_address', 
        __('Sender Email Address', 'wp-change-email-sender'), 'wpces_sender_email_address_field_callback', 
        'general',  
        'wpces_email_sender_settings_section'
    );

    // register a new setting for email address field
	register_setting('general', 'wpces_sender_email_address');

}
add_action('admin_init', 'wpces_email_sender_register');


//Sender Name field content
function wpces_sender_name_field_callback(){
    $wpces_email_sender_name_value = get_option('wpces_email_sender_name');
	printf('<input name="wpces_email_sender_name" type="text" class="regular-text" value="%s" placeholder="%s"/>', esc_attr($wpces_email_sender_name_value), __('Mail Sender Name', 'wp-change-email-sender'));
}

//Sender Email field content
function wpces_sender_email_address_field_callback() {
    $wpces_sender_email_address_value = get_option('wpces_sender_email_address');
	printf('<input name="wpces_sender_email_address" type="email" class="regular-text" value="%s" placeholder="info@yourdomain.com"/>', esc_attr($wpces_sender_email_address_value));
}

//Plugin settings page section text
function wpces_email_sender_section_text() {
	printf('%s %s %s', '<p>', __('You can change WordPress Default Mail Sender Name and Email Address', 'wp-change-email-sender'), '</p>');
}


/**
 * Add settings page link with plugin.
 */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpces_mail_sender_action_links' );
function wpces_mail_sender_action_links( $links ){
    $wpces_mail_sender_plugin_action_links = array(
    '<a href="' . esc_url(admin_url( 'options-general.php' )) . '"> '. __('Settings', 'wp-change-email-sender') . '</a>',
    );
    return array_merge( $links, $wpces_mail_sender_plugin_action_links );
}


/**
 * Change Wordpress Default Mail Sender Email Address
 */
add_filter('wp_mail_from', 'wpces_mail_sender_from_email');
function wpces_mail_sender_from_email($old) {
    $wpces_sender_email_address_value = get_option('wpces_sender_email_address');
	return esc_html( $wpces_sender_email_address_value );
}


/**
 * Change Wordpress Default Mail Sender Name
 */
add_filter('wp_mail_from_name', 'wpces_mail_sender_from_email_name');
function wpces_mail_sender_from_email_name($old) {
    $wpces_email_sender_name_value = get_option('wpces_email_sender_name');
	return esc_html( $wpces_email_sender_name_value );
}