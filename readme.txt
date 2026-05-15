=== WP Change Email Sender ===
Contributors: aminurislam01, pluginizelab
Donate link: https://www.buymeacoffee.com/aiarnob
Tags: wp change email sender, wp change default email sender, wordpress default email sender change, wp default email change, wp default email sender name change
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 3.3.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily change WordPress default mail sender name and email address

== Description ==

This plugin enable you to change mail sender name and email address from WordPress default mail sender name and email.
After installation, navigate to **Settings &rarr; Change Email Sender** in the WordPress admin sidebar to open the modern configuration dashboard.

= Plugin Features =
* ✉️ **Custom Sender Details:** Easily replace the default "WordPress" name and "wordpress@yourdomain.com" email address with your own brand.
* 🛡️ **Force Overrides:** Prevent misbehaving plugins (e.g., contact forms or e-commerce plugins) from hijacking your outbound email sender details.
* ↩️ **Smart "Reply-To" Protection:** When forcing an email override, the plugin automatically captures the original sender and sets it as the "Reply-To" address so you never miss a customer reply.
* 🧪 **Built-in Email Tester:** Instantly send a test email directly from the settings page to verify your configuration is working perfectly.
* 💾 **Import & Export:** Securely back up your custom configurations to a JSON file or restore them instantly with the built-in drag-and-drop uploader.
* ⚡ **Lightning Fast Dashboard:** Enjoy a stunning, single-page React interface that saves your settings instantly without page reloads.
* 🔒 **Lightweight & Secure:** Built with the native WordPress REST API and rigorous security standards.

== Screenshots ==

1. Plugin Settings -> General Settings
2. Plugin Settings -> Send Test Email
3. Plugin Settings -> Import / Export
4. Mail Example


== Installation ==

= FOR STANDARD INSTALLATION: =
Installing this plugin is very easy just like any other WordPress plugin. Please follow these instructions:
1. In your WordPress admin panel, go to Plugins > New Plugin, search for "WP Change Email Sender" and click on "Install Now"
2. Alternatively, download the plugin and upload the wp-change-email-sender.zip to your plugins directory, which usually is /wp-content/plugins/.
3. Activate the plugin from plugins page.

To set up your custom email details, simply navigate to **Settings &rarr; Change Email Sender** in the WordPress admin sidebar. Alternatively, click the **Settings** link next to the plugin on the Plugins page. Our sleek, modern dashboard will load instantly, ready for your customizations!


== Frequently Asked Questions ==

= Where is the options to change mail sender name email? =
Simply navigate to **Settings > Change Email Sender** in the WordPress admin sidebar.

= Can I change only sender name or email individually? =
Yes, you can change only mail sender name and email individually.

= My emails still show the old sender — what should I check? =
Enable both **Force From Name** and **Force From Email** on the General Settings screen. Without those toggles, the plugin only replaces the WordPress default sender and lets other plugins (e.g. WooCommerce, Contact Form 7) keep their own sender values.

= Will enabling "Force From Email" break reply threads in WooCommerce? =
No. When Force From Email overrides a non-default address set by another plugin, the original address is automatically added as the `Reply-To` header, so replies still reach the correct destination.

= Can I use a different sender per email type? =
Not directly — this plugin sets a single global sender. We will add this feature ASAP.

= I migrated from an older version — will my settings carry over? =
Yes. The plugin automatically migrates the legacy standalone options into the unified setting on first load after updating. No manual action is required.

== Support ==
If you find this plugin useful, consider supporting its development through a [donation](https://www.buymeacoffee.com/aiarnob).


== Changelog ==

= 3.3.1 =
* Fix: After upgrading from 3.2, emails sent by other plugins (e.g. WooCommerce, contact forms) could be rejected by SMTP relays because the configured sender was not being applied. Restored 3.2 behaviour by enabling "Force From Name" and "Force From Email" automatically on upgrade. You can change these toggles from the plugin's settings page if you want other plugins' sender addresses to be preserved.
* Hardening: Fall back to the original sender if the saved sender email becomes empty after sanitisation, preventing silent send failures.

= 3.3.0 =
* **Modernized Admin Dashboard**: Rebuilt the settings page as a fast, single-page React application with a premium native-like design.
* **Unified Settings**: Moved sender name and sender email address settings from Settings &rarr; General to our dedicated "WP Change Email Sender" admin page.
* **Automatic Migration**: Seamlessly migrates your existing sender settings to the new system upon plugin update.
* **Force Override Feature**: Added new options to forcefully override the sender name and email, even if other plugins (like WooCommerce or Contact Form 7) try to set their own.
* **Smart Reply-To**: When forcefully overriding another plugin's custom sender email, the original email address is now automatically preserved as the `Reply-To` address so you don't lose replies.
* **Send Test Email**: Added a "Test Email" feature complete with custom message support directly in the dashboard so you can instantly verify your configuration.
* **Import & Export Configs**: Instantly download your exact configurations as a JSON backup, or upload a JSON settings file to safely replicate your configuration across different websites.
* **REST API Powered**: Refactored the backend using the WordPress REST API for robust, secure, and snappy settings updates with user-friendly validation.

= 3.2 =
* Checked with latest version of WordPress.

= 3.1 =
* Checked with latest version of WordPress.

= 3.0 =
* Few security update.
* Checked with latest version of WordPress.

== Upgrade Notice ==

= 3.3.0 =
We've upgraded to a lightning-fast, dedicated premium React dashboard! All your existing sender settings will automatically and seamlessly migrate over as soon as you update.