# WP Change Email Sender — Usage Guide

## Overview

WP Change Email Sender lets you replace WordPress's default `wordpress@yourdomain.com` sender with any name and email address you choose. It works across core WordPress emails as well as emails sent by WooCommerce, Contact Form 7, and other plugins.

---

## Installation

1. Upload the plugin folder to `wp-content/plugins/` or install it from the WordPress plugin directory.
2. Activate the plugin via **Plugins > Installed Plugins**.
3. Navigate to **Settings > WP Change Email Sender** to open the admin panel.

---

## General Settings

**Settings > Change Email Sender**

This is the main configuration screen.

| Field                    | Description                                                                                                                                                                                            |
| ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Email Sender Name**    | The "From" name that appears in the recipient's inbox (e.g. `My Store`).                                                                                                                               |
| **Sender Email Address** | The "From" address used for all outgoing WordPress emails (e.g. `hello@mystore.com`).                                                                                                                  |
| **Force From Name**      | When enabled, overrides the sender name set by WooCommerce, Contact Form 7, and any other plugin — not just WordPress core emails.                                                                     |
| **Force From Email**     | When enabled, overrides the sender address set by third-party plugins. The original plugin address is preserved as the email's `Reply-To` header so recipients can still reply to the original sender. |

Click **Save Changes** to apply.

### How the override works

Without "Force" options enabled, the plugin only replaces the WordPress default sender (`wordpress@yourdomain.com` / `WordPress`). If another plugin (e.g. WooCommerce) has already set its own sender, its value is used instead.

With "Force" options enabled, the plugin's value wins at all times regardless of what other plugins set.

---

## Send Test Email

**Settings > Change Email Sender**

Use this screen to verify that your sender configuration is working before it affects real users.

1. Enter the **Recipient Email Address** where the test should be delivered. The field is pre-filled with your WordPress user email.
2. Optionally enter a custom **Email Body** message.
3. Click **Send Test Email**.

A success or error notification appears at the bottom of the screen. Check the received email to confirm the "From" name and address match what you configured in General Settings.

---

## Import / Export

**Settings > Change Email Sender > Import / Export**

### Export

Click **Export Settings** to download your current configuration as a JSON file (`wp-change-email-sender-settings.json`). Use this to create a backup or to copy settings to another site.

The exported file contains:

```json
{
  "wp_change_email_sender_name": "My Store",
  "wp_change_email_sender_email_address": "hello@mystore.com",
  "force_from_name": true,
  "force_from_email": false
}
```

### Import

Click **Import Settings** and select a previously exported JSON file. The plugin validates the file and saves only the recognised keys — any unrelated data in the file is silently ignored.

> **Note:** Importing overwrites your current settings immediately. Export a backup first if you want to preserve the existing configuration.

---
