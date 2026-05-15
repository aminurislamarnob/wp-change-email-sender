<?php

namespace WeLabs\WpChangeEmailSender;

/**
 * Plugin data upgrader.
 *
 * Runs one-shot, idempotent data migrations when the stored DB version
 * is behind the plugin version. The DB version is stamped only after a
 * successful run, so a failure mid-migration can be retried on the next
 * load.
 */
class Upgrader {

    /**
     * Option name used to record the DB schema version already migrated to.
     *
     * @var string
     */
    const DB_VERSION_OPTION = 'wp_change_email_sender_db_version';

    /**
     * Option name holding the unified React-surface settings.
     *
     * @var string
     */
    const SETTINGS_OPTION = 'wp_change_email_sender_settings';

    /**
     * Compare the stored DB version against the running plugin version and
     * dispatch any outstanding migrations.
     *
     * @return void
     */
    public function maybe_upgrade() {
        $stored  = get_option( self::DB_VERSION_OPTION, '0' );
        $current = WP_CHANGE_EMAIL_SENDER_PLUGIN_VERSION;

        if ( version_compare( $stored, $current, '>=' ) ) {
            return;
        }

        if ( version_compare( $stored, '3.3', '<' ) ) {
            $this->migrate_to_3_3();
        }

        if ( version_compare( $stored, '3.3.1', '<' ) ) {
            $this->migrate_to_3_3_1();
        }

        update_option( self::DB_VERSION_OPTION, $current );
    }

    /**
     * Copy the legacy standalone sender options into the unified settings
     * option introduced in 3.3, and restore v3.2 "always override From"
     * behaviour by enabling both force flags.
     *
     * Idempotent: reruns are safe because the outer version gate prevents
     * re-entry.
     *
     * The legacy options are intentionally left in place as a safety net.
     * They can be removed in a later release once the migration has had
     * time to settle across installs.
     *
     * @return void
     */
    private function migrate_to_3_3() {
        $settings = get_option( self::SETTINGS_OPTION, array() );

        if ( ! is_array( $settings ) ) {
            $settings = array();
        }

        // Preserve v3.2 behaviour: it always overrode From for every wp_mail() call.
        $settings['force_from_name']  = true;
        $settings['force_from_email'] = true;

        $legacy_name  = get_option( 'wpces_email_sender_name', '' );
        $legacy_email = get_option( 'wpces_sender_email_address', '' );

        if ( '' !== $legacy_name && empty( $settings['wp_change_email_sender_name'] ) ) {
            $settings['wp_change_email_sender_name'] = sanitize_text_field( $legacy_name );
        }

        if ( '' !== $legacy_email && empty( $settings['wp_change_email_sender_email_address'] ) ) {
            $settings['wp_change_email_sender_email_address'] = sanitize_email( $legacy_email );
        }

        update_option( self::SETTINGS_OPTION, $settings );
    }

    /**
     * Force-enable both From overrides on upgrade to 3.3.1.
     *
     * Covers v3.3.0 → v3.3.1 installs that already migrated away from the
     * legacy options but landed with force_* defaulting to false, causing
     * SMTP relays in strict-sender mode to reject mail from other plugins.
     *
     * Idempotent via the outer version gate in maybe_upgrade().
     *
     * @return void
     */
    private function migrate_to_3_3_1() {
        $settings = get_option( self::SETTINGS_OPTION, array() );

        if ( ! is_array( $settings ) ) {
            $settings = array();
        }

        $settings['force_from_name']  = true;
        $settings['force_from_email'] = true;

        update_option( self::SETTINGS_OPTION, $settings );
    }
}
