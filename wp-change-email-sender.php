<?php
/**
 * Plugin Name: WP Change Email Sender
 * Plugin URI:  https://wordpress.org/plugins/wp-change-email-sender/
 * Description: This plugin which allows you to change WordPress default mail sender name and email address easily.
 * Version: 3.3.0
 * Author: Aminur Islam
 * Author URI: https://github.com/aminurislamarnob/
 * Text Domain: wp-change-email-sender
 * License: GPLv2 or later
 * Domain Path: /languages
 */


use WeLabs\WpChangeEmailSender\WpChangeEmailSender;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WP_CHANGE_EMAIL_SENDER_FILE' ) ) {
    define( 'WP_CHANGE_EMAIL_SENDER_FILE', __FILE__ );
}

if ( ! defined( 'WP_CHANGE_EMAIL_SENDER_BASENAME' ) ) {
    define( 'WP_CHANGE_EMAIL_SENDER_BASENAME', plugin_basename( __FILE__ ) );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Wp_Change_Email_Sender Plugin when all plugins loaded
 *
 * @return \WeLabs\WpChangeEmailSender\WpChangeEmailSender
 */
function welabs_wp_change_email_sender() {
    return WpChangeEmailSender::init();
}

// Lets Go....
welabs_wp_change_email_sender();
