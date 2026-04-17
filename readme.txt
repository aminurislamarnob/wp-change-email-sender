=== WP Change Email Sender ===
Contributors: aminurislam01, pluginizelab
Donate link: https://www.buymeacoffee.com/aiarnob
Tags: wp change email sender, wp change default email sender, wordpress default email sender change, wp default email change, wp default email sender name change
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 3.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily change WordPress default mail sender name and email address

== Description ==

This plugin enable you to change mail sender name and email address from WordPress default mail sender name and email.
After install go to the "WP Change Email Sender" menu in the WordPress admin sidebar.

= Plugin Features =
* Change WordPress default mail sender name.
* Change WordPress default mail sender email address.

== Screenshots ==

1. Plugin Options
1. Mail Example


== Installation ==

= FOR STANDARD INSTALLATION: =
Installing this plugin is very easy just like any other WordPress plugin. Please follow these instructions:
1. In your WordPress admin panel, go to Plugins > New Plugin, search for "WP Change Email Sender" and click on "Install Now"
2. Alternatively, download the plugin and upload the wp-change-email-sender.zip to your plugins directory, which usually is /wp-content/plugins/.
3. Activate the plugin from plugins page.

To change default mail sender name and email address go to the "WP Change Email Sender" menu in the WordPress admin sidebar, or use the Settings link beside the plugin on the Plugins page.


== Frequently Asked Questions ==

= Where is the options to change mail sender name email? =
Go to the "WP Change Email Sender" menu in the WordPress admin sidebar.

= Can I change only sender name or email individually? =
Yes, you can change only mail sender name and email individually.

== Support ==
If you find this plugin useful, consider supporting its development through a [donation](https://www.buymeacoffee.com/aiarnob).


== Changelog ==

= 3.3 =
* **Modernized Admin Dashboard**: Rebuilt the settings page as a fast, single-page React application with a premium native-like design.
* **Unified Settings**: Moved sender name and sender email address settings from Settings &rarr; General to our dedicated "WP Change Email Sender" admin page.
* **Automatic Migration**: Seamlessly migrates your existing sender settings to the new system upon plugin update.
* **Force Override Feature**: Added new options to forcefully override the sender name and email, even if other plugins (like WooCommerce or Contact Form 7) try to set their own.
* **Smart Reply-To**: When forcefully overriding another plugin's custom sender email, the original email address is now automatically preserved as the `Reply-To` address so you don't lose replies.
* **Send Test Email**: Added a "Test Email" feature complete with custom message support directly in the dashboard so you can instantly verify your configuration.
* **REST API Powered**: Refactored the backend using the WordPress REST API for robust, secure, and snappy settings updates with user-friendly validation.

= 3.1 =
* Checked with latest version of WordPress.

= 3.0 =
* Few security update.
* Checked with latest version of WordPress.

== Upgrade Notice ==

= 3.3 =
Sender settings have moved to a dedicated "WP Change Email Sender" admin menu. Your existing values migrate automatically on update. Back up your database before updating as a best practice.