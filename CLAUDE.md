# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development commands

```bash
# Install dependencies
composer install        # PHP deps (dev)
npm install             # JS deps

# Frontend (React admin page)
npm run start           # webpack watch (dev)
npm run build           # production build → assets/build/admin/
npm run format          # wp-scripts format
npm run lint:js         # wp-scripts lint-js
npm run lint:css        # wp-scripts lint-style

# PHP coding standards (WordPress rules, PHP 7.4 baseline)
composer phpcs          # lint (parallel, shows sniff names)
composer phpcs:report   # write report to phpcs-report.txt
composer phpcbf         # auto-fix

# Release ZIP (reads Version from plugin header, builds to ./build/)
chmod +x bin/build.sh
bin/build.sh
```

The `phpcs.xml` ruleset excludes `assets/`, `build/`, `vendor/`, `node_modules/`, `tests/`, and all `*.js`/`*.css`/`*.scss` — only `includes/`, `templates/`, and the root plugin file are linted. CI (`.github/workflows/phpcs.yml`) runs PHPCS only on PHP files changed in a PR.

## Architecture

This is a WordPress plugin with **two parallel settings surfaces** — be careful not to conflate them:

1. **Legacy settings (the part that actually changes email sender):** `includes/OverrideEmailSender.php` adds fields to **Settings → General** using the Settings API, stores values in the standalone options `wpces_email_sender_name` and `wpces_sender_email_address`, and hooks `wp_mail_from` / `wp_mail_from_name` to override the sender.
2. **Newer React admin page (scaffolded, not yet wired to email behavior):** `includes/Admin/Settings.php` registers a top-level admin menu that renders `<div id="WpChangeEmailSenderSettings">`. `src/admin.js` mounts a React app there via HashRouter (General / Appearance / Product tabs). This surface reads/writes the option `wp_change_email_sender_settings` through a REST controller at `wp-change-email-sender/v1/settings` (`includes/Admin/REST/SettingsController.php`). **These REST settings are not consumed by `OverrideEmailSender` yet** — the React page is currently a UI shell.

### Plugin bootstrapping pattern

`wp-change-email-sender.php` defines `WP_CHANGE_EMAIL_SENDER_FILE` / `_BASENAME`, requires `vendor/autoload.php`, and calls `welabs_wp_change_email_sender()` → `WpChangeEmailSender::init()`.

`includes/WpChangeEmailSender.php` is a singleton that:
- Registers activation/deactivation/REST hooks in its constructor
- On `plugins_loaded` → `init_plugin()` → hooks `init` priority 4 → `init_classes()`
- Instantiates subsystems into `$this->container[...]`:
  - `scripts` → `Assets` (registers generic admin/frontend script handles — separate from the React bundle)
  - `admin_settings` → `Admin\Settings` (the React-page menu + enqueue)
  - `admin_settings_rest` → `Admin\REST\SettingsController`
  - `email_sender` → `OverrideEmailSender` (the actual mail-sender filters)
- Exposes container entries via magic `__get`, so `welabs_wp_change_email_sender()->admin_settings_rest` etc. work from anywhere.
- Provides `get_template( $name, $args )` for loading files from `templates/` (currently empty) with `wp_change_email_sender_before/after_template_part` actions.

PSR-4 autoload (`composer.json`): `WeLabs\WpChangeEmailSender\` → `includes/`. All PHP classes live under this namespace.

### Constants defined at boot

`WP_CHANGE_EMAIL_SENDER_FILE`, `_BASENAME`, `_PLUGIN_VERSION`, `_DIR`, `_INC_DIR`, `_TEMPLATE_DIR`, `_PLUGIN_ASSET`, `_PLUGIN_ADMIN_ASSET`, `_PLUGIN_PUBLIC_ASSET`, `_LOAD_STYLE`, `_LOAD_SCRIPTS`. Use these rather than recomputing paths.

### React admin bundle

`webpack.config.js` extends `@wordpress/scripts` defaults with a single entry `src/admin.js` → `assets/build/admin/script.js` (plus `script.asset.php` consumed by `Admin\Settings::enqueue_admin_settings_scripts` for dependency/version detection). Styling mixes Tailwind (`tailwind.config.js`, `postcss.config.js`) with SCSS/CSS in `src/styles/` and component CSS. The enqueue is gated by `$screen->id === 'toplevel_page_wp_change_email_sender-settings'`.

### Versioning

The plugin version lives in **three** places and must stay in sync:
- Plugin header `Version:` in `wp-change-email-sender.php` (this is what `bin/build.sh` extracts for the ZIP filename)
- `$version` property in `includes/WpChangeEmailSender.php`
- `Stable tag:` in `readme.txt`

### Text domain

All i18n strings use the `wp-change-email-sender` text domain (enforced by `phpcs.xml`). Translations load from `/languages`.
