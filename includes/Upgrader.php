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

        update_option( self::DB_VERSION_OPTION, $current );
    }

    /**
     * Copy the legacy standalone sender options into the unified settings
     * option introduced in 3.3.
     *
     * Idempotent: reruns are safe because the outer version gate prevents
     * re-entry, and values already present in the new option are never
     * overwritten.
     *
     * The legacy options are intentionally left in place as a safety net.
     * They can be removed in a later release once the migration has had
     * time to settle across installs.
     *
     * @return void
     */
    private function migrate_to_3_3() {
        $legacy_name  = get_option( 'wpces_email_sender_name', '' );
        $legacy_email = get_option( 'wpces_sender_email_address', '' );

        if ( '' === $legacy_name && '' === $legacy_email ) {
            return;
        }

        $settings = get_option( self::SETTINGS_OPTION, array() );

        if ( ! is_array( $settings ) ) {
            $settings = array();
        }

        if ( empty( $settings['wp_change_email_sender_name'] ) && '' !== $legacy_name ) {
            $settings['wp_change_email_sender_name'] = $legacy_name;
        }

        if ( empty( $settings['wp_change_email_sender_email_address'] ) && '' !== $legacy_email ) {
            $settings['wp_change_email_sender_email_address'] = $legacy_email;
        }

        update_option( self::SETTINGS_OPTION, $settings );
    }
}
