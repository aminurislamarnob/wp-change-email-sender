# CLAUDE.md

## Commands

```bash
npm run start        # webpack watch
npm run build        # production build → assets/build/admin/
npm run lint:js      # wp-scripts lint-js
npm run lint:css     # wp-scripts lint-style
composer phpcs       # PHP lint
composer phpcbf      # PHP auto-fix
bin/build.sh         # release ZIP
```

## Architecture

Single unified DB option: `wp_change_email_sender_settings`

Keys: `wp_change_email_sender_name`, `wp_change_email_sender_email_address`, `force_from_name`, `force_from_email`

**PHP entry:** `wp-change-email-sender.php` → `WpChangeEmailSender` (singleton) → boots `OverrideEmailSender`, `Admin\Settings`, `Admin\REST\SettingsController`, `Admin\REST\TestEmailController`, `Assets`.

**REST API:** `wp-change-email-sender/v1/settings` (GET/POST) · `wp-change-email-sender/v1/send-test-email` (POST)

**Override priority:** `wp_mail_from` / `wp_mail_from_name` at `PHP_INT_MAX`. In force mode, original sender email is preserved as `Reply-To`.

**Upgrader:** Runs on every `plugins_loaded`; migrates legacy standalone options (`wpces_email_sender_name`, `wpces_sender_email_address`) into the unified option.

## React Admin

`src/admin.js` → `HashRouter` with 3 routes, all inside `<Layout>`:
- `/` → `GeneralSettings`
- `/send-test-email` → `SendTestEmail`
- `/import-export` → `ImportExport`

**State:** `SettingsContext` (`useSettings()`) — exposes `settings`, `isLoading`, `isSaving`, `saveSettings(data, customMessage?)`.

**Icons:** Always import from `./icons` (re-exports `@heroicons/react/24/outline`), never directly from heroicons.

**Snackbars:** Dispatched via `@wordpress/notices`. `saveSettings` fires them automatically. Other components call `useDispatch(noticesStore)` directly.

**Loading state:** `isLoading === true` renders skeleton tab placeholders (`.wpces-skeleton-tab` + `wpces-pulse` animation) and a `<Spinner>` card instead of the real content.

**Import/Export:** `ALLOWED_KEYS` whitelist + `pickAllowed()` filter strips any non-permitted keys on both export and import. Import calls `saveSettings(payload, customMessage)` through the existing REST flow.

## Versioning

Keep in sync across **three** files: plugin header `Version:` · `$version` in `WpChangeEmailSender.php` · `Stable tag:` in `readme.txt`.

## Text domain

`wp-change-email-sender` — all user-facing strings, including dynamically thrown errors, must use `__( '…', 'wp-change-email-sender' )`.
