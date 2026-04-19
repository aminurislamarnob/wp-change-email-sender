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

The `phpcs.xml` ruleset excludes `assets/`, `build/`, `vendor/`, `node_modules/`, `tests/`, `languages/`, and all `*.js`/`*.css`/`*.scss` — only `includes/`, `templates/`, and the root plugin file are linted. CI (`.github/workflows/phpcs.yml`) runs PHPCS only on PHP files changed in a PR.

## Architecture

This is a WordPress plugin that overrides the default `wp_mail` sender name and email address. As of v3.3, all settings are managed through a single unified option.

### Settings and data flow

All settings live in a single `wp_change_email_sender_settings` option (an associative array) with these keys:
- `wp_change_email_sender_name` (string) — custom from name
- `wp_change_email_sender_email_address` (string) — custom from email
- `force_from_name` (bool) — override from name set by all plugins
- `force_from_email` (bool) — override from email set by all plugins

The React admin page is the primary settings UI; the legacy standalone options (`wpces_email_sender_name`, `wpces_sender_email_address`) are no longer written to but are still read as a fallback.

- **React admin page:** `includes/Admin/Settings.php` registers a top-level admin menu that renders `<div id="WpChangeEmailSenderSettings">`. It also injects `window.__wpcesCurrentUserEmail` (current admin's email) for the test-email form default. `src/admin.js` mounts a React app via HashRouter with **three routes**:
  - `/` — `GeneralSettings` (sender name/email, force toggles)
  - `/send-test-email` — `SendTestEmail` (send a test email to verify settings)
  - `/import-export` — `ImportExport` (JSON backup & restore)
  
  The app reads/writes through REST controllers at `wp-change-email-sender/v1/settings` and `wp-change-email-sender/v1/send-test-email`. State is managed by a React context provider (`src/context/SettingsContext.js`).

- **Email sender override:** `includes/OverrideEmailSender.php` hooks `wp_mail_from` / `wp_mail_from_name` (both at `PHP_INT_MAX` priority) and `phpmailer_init`. It reads from the unified option first, falling back to legacy standalone options via `get_sender_value()` — this protects installs where the 3.3 migration hasn't run yet. The override supports two modes:
  - **Non-force (default):** Only replaces the WordPress defaults (`wordpress@domain` / `"WordPress"`). If another plugin (WooCommerce, CF7) sets a custom from address/name, it is respected.
  - **Force:** Overrides all from addresses/names regardless of source. When force-from-email overrides a non-default address set by another plugin, the original address is preserved as a `Reply-To` header (via `set_reply_to_from_original_email` on `phpmailer_init`), unless a Reply-To is already set.

- **Upgrader:** `includes/Upgrader.php` runs on every `plugins_loaded` via `init_plugin()`. It compares the stored DB version (`wp_change_email_sender_db_version`) against the plugin version and runs one-shot, idempotent migrations. The 3.3 migration copies legacy standalone options into the unified option.

### Plugin bootstrapping pattern

`wp-change-email-sender.php` defines `WP_CHANGE_EMAIL_SENDER_FILE` / `_BASENAME`, requires `vendor/autoload.php`, and calls `welabs_wp_change_email_sender()` → `WpChangeEmailSender::init()`.

`includes/WpChangeEmailSender.php` is a singleton that:
- Registers activation/deactivation/REST hooks in its constructor
- On `plugins_loaded` → `init_plugin()`:
  1. Loads the text domain via `load_textdomain()`
  2. Runs `Upgrader::maybe_upgrade()` for pending data migrations
  3. Calls `init_hooks()` which hooks `init` priority 4 → `init_classes()`
- Instantiates subsystems into `$this->container[...]`:
  - `scripts` → `Assets` (registers generic admin/frontend script handles — separate from the React bundle)
  - `admin_settings` → `Admin\Settings` (the React-page menu + enqueue)
  - `admin_settings_rest` → `Admin\REST\SettingsController`
  - `test_email_rest` → `Admin\REST\TestEmailController` (send test email endpoint)
  - `email_sender` → `OverrideEmailSender` (the actual mail-sender filters)
- Exposes container entries via magic `__get`, so `welabs_wp_change_email_sender()->admin_settings_rest` etc. work from anywhere.
- Provides `get_template( $name, $args )` for loading files from `templates/` with `wp_change_email_sender_before/after_template_part` actions.

PSR-4 autoload (`composer.json`): `WeLabs\WpChangeEmailSender\` → `includes/`. All PHP classes live under this namespace.

### Constants defined at boot

`WP_CHANGE_EMAIL_SENDER_FILE`, `_BASENAME`, `_PLUGIN_VERSION`, `_DIR`, `_INC_DIR`, `_TEMPLATE_DIR`, `_PLUGIN_ASSET`, `_PLUGIN_ADMIN_ASSET`, `_PLUGIN_PUBLIC_ASSET`, `_LOAD_STYLE`, `_LOAD_SCRIPTS`. Use these rather than recomputing paths.

### React admin bundle

`webpack.config.js` extends `@wordpress/scripts` defaults with a single entry `src/admin.js` → `assets/build/admin/script.js` (plus `script.asset.php` consumed by `Admin\Settings::enqueue_admin_settings_scripts` for dependency/version detection). Styling uses plain CSS (`src/Components/LayoutStyles.css` and component-level CSS). The enqueue is gated by `$screen->id === 'toplevel_page_wp_change_email_sender-settings'`.

### React component map

```
src/
├── admin.js                        # App root — mounts HashRouter, 3 Routes
├── public-path.js                  # Sets __webpack_public_path__ for lazy chunks
├── context/
│   └── SettingsContext.js          # Global settings state & REST API calls
└── Components/
    ├── Layout.js                   # Shell: header, tab nav, <Outlet />, snackbar list
    ├── LayoutStyles.css            # All shared CSS (nav, skeleton, cards, snackbars…)
    ├── SettingsHeader.js           # Top banner: title, subtitle, action buttons
    ├── GeneralSettings.js          # Route "/" — sender name/email + force toggles
    ├── SendTestEmail.js            # Route "/send-test-email" — test email form
    ├── ImportExport.js             # Route "/import-export" — JSON backup & restore
    └── icons.js                    # Re-exports from @heroicons/react/24/outline
```

### SettingsContext API

`src/context/SettingsContext.js` exposes a single context via the `useSettings()` hook:

| Value | Type | Description |
|---|---|---|
| `settings` | `object` | Live copy of `wp_change_email_sender_settings` |
| `isLoading` | `bool` | `true` while the initial GET is in flight |
| `isSaving` | `bool` | `true` while a POST save is in flight |
| `saveSettings(data, customMessage?)` | `async fn` | POSTs `data` to REST API; shows a success or error snackbar. Pass `customMessage` to override the default "Settings saved successfully!" string. |

The import flow calls `saveSettings(payload, "Settings imported successfully.")` so the same REST endpoint and snackbar system is reused.

### Import / Export feature

`src/Components/ImportExport.js` implements client-side JSON backup and restore:

- **`ALLOWED_KEYS`** — strict whitelist of the four permitted setting keys. Any key outside this list is silently dropped.
- **`pickAllowed(obj)`** — pure filter function that strips disallowed keys from any object.
- **Export** (`handleExport`): calls `pickAllowed(settings)`, serialises to pretty-printed JSON, triggers a `<a download>` click, then fires a success/error snackbar.
- **Import** (`handleImport`): reads the file via `FileReader`, `JSON.parse`s it, validates it is a non-null object, passes it through `pickAllowed`, and rejects with a user-facing i18n error if no allowed keys survive. On success it calls `saveSettings(payload, customMessage)`.
- The hidden `<input type="file" accept=".json">` is triggered programmatically via `fileInputRef`; its value is reset after each import so the same file can be re-imported.

### Loading state & skeleton UI

While `isLoading` is `true`, `Layout.js` renders a skeleton in place of the real nav tabs and a Card with a `<Spinner />` in place of the page content.

The skeleton nav uses `.wpces-skeleton-tab` elements (widths matching the three real tabs) animated by a CSS `@keyframes wpces-pulse` opacity loop defined in `LayoutStyles.css`.

### Icons

`src/Components/icons.js` re-exports all icons from `@heroicons/react/24/outline`:

| Export | Used in |
|---|---|
| `CheckBadgeIcon` | SettingsContext (save success), ImportExport (export success) |
| `ExclamationCircleIcon` | SettingsContext (save error), ImportExport (export/import error) |
| `EnvelopeIcon` | Layout → SettingsHeader |
| `ArrowDownTrayIcon` | ImportExport — Export button |
| `ArrowUpTrayIcon` | ImportExport — Import button |
| `GearIcon` (alias `Cog6ToothIcon`) | Available for future use |

Always import icons from `./icons` (or `../Components/icons`) — never directly from `@heroicons/react` — so the single re-export file stays the source of truth.

### Snackbar notifications

Notices are dispatched through `@wordpress/notices` (`store as noticesStore`) and rendered by a `<SnackbarList>` mounted at the bottom of `Layout.js`. The list is filtered to `type === "snackbar"` only.

- **Success:** `createSuccessNotice(message, { type: "snackbar", icon: <CheckBadgeIcon … /> })`
- **Error:** `createErrorNotice(message, { type: "snackbar", icon: <ExclamationCircleIcon … /> })`

`SettingsContext.saveSettings` fires the appropriate notice automatically. Components that need standalone notices (e.g. `ImportExport`, `SendTestEmail`) call `useDispatch(noticesStore)` directly.

### Versioning

The plugin version lives in **three** places and must stay in sync:
- Plugin header `Version:` in `wp-change-email-sender.php` (this is what `bin/build.sh` extracts for the ZIP filename)
- `$version` property in `includes/WpChangeEmailSender.php`
- `Stable tag:` in `readme.txt`

### Text domain

All i18n strings use the `wp-change-email-sender` text domain (enforced by `phpcs.xml`). The text domain is loaded explicitly in `WpChangeEmailSender::load_textdomain()` from the `/languages` directory. All dynamically thrown `Error` messages that surface to the user must also be wrapped with `__( …, 'wp-change-email-sender' )`.
