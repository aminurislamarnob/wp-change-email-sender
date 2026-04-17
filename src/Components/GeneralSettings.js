/**
 * WordPress dependencies
 */
import { useState, useCallback } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import {
  Button,
  Card,
  CardBody,
  Spinner,
  TextControl,
  ToggleControl,
} from "@wordpress/components";

import { useSettings } from "../context/SettingsContext";

const GeneralSettings = () => {
  const { settings, isSaving, saveSettings } = useSettings();

  const [senderName, setSenderName] = useState(
    settings.wp_change_email_sender_name ?? "",
  );
  const [senderEmail, setSenderEmail] = useState(
    settings.wp_change_email_sender_email_address ?? "",
  );
  const [forceFromName, setForceFromName] = useState(
    settings.force_from_name ?? false,
  );
  const [forceFromEmail, setForceFromEmail] = useState(
    settings.force_from_email ?? false,
  );

  const handleSubmit = useCallback(
    async (event) => {
      event.preventDefault();
      await saveSettings({
        wp_change_email_sender_name: senderName,
        wp_change_email_sender_email_address: senderEmail,
        force_from_name: forceFromName,
        force_from_email: forceFromEmail,
      });
    },
    [senderName, senderEmail, forceFromName, forceFromEmail, saveSettings],
  );

  return (
    <div className="wpces-section" id="wpces-general-settings">
      <form onSubmit={handleSubmit}>
        <Card className="wpces-form-header-card">
          <CardBody className="wpces-form-section-header">
            <h3 className="wpces-section-title">
              {__("General Settings", "wp-change-email-sender")}
            </h3>
            <p className="wpces-section-description">
              {__(
                "Control core email overrides such as sender name and email address.",
                "wp-change-email-sender",
              )}
            </p>
          </CardBody>
        </Card>
        <Card>
          <CardBody className="wpces-form-section-body">
            <div className="wpces-settings-group">
              <TextControl
                label={__("Email Sender Name", "wp-change-email-sender")}
                help={__(
                  'The "From" name used for outgoing WordPress emails.',
                  "wp-change-email-sender",
                )}
                value={senderName}
                onChange={setSenderName}
                placeholder={__("Mail Sender Name", "wp-change-email-sender")}
              />
            </div>
            <div className="wpces-settings-group">
              <TextControl
                label={__("Sender Email Address", "wp-change-email-sender")}
                help={__(
                  'The "From" address used for outgoing WordPress emails.',
                  "wp-change-email-sender",
                )}
                type="email"
                value={senderEmail}
                onChange={setSenderEmail}
                placeholder="info@yourdomain.com"
              />
            </div>
            <div className="wpces-settings-group">
              <ToggleControl
                label={__("Force From Name", "wp-change-email-sender")}
                help={__(
                  "Overrides the From Name set by WooCommerce, Contact Form 7, and other plugins.",
                  "wp-change-email-sender",
                )}
                checked={forceFromName}
                onChange={setForceFromName}
              />
            </div>
            <div className="wpces-settings-group">
              <ToggleControl
                label={__("Force From Email", "wp-change-email-sender")}
                help={__(
                  "Overrides the From Email Address set by WooCommerce, Contact Form 7, and other plugins.",
                  "wp-change-email-sender",
                )}
                checked={forceFromEmail}
                onChange={setForceFromEmail}
              />
            </div>
            <Button
              variant="primary"
              type="submit"
              isBusy={isSaving}
              disabled={isSaving}
            >
              {isSaving && <Spinner />}
              {__("Save Changes", "wp-change-email-sender")}
            </Button>
          </CardBody>
        </Card>
      </form>
    </div>
  );
};

export default GeneralSettings;
