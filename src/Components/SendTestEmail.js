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
  TextareaControl,
  TextControl,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

import { useDispatch } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import { CheckBadgeIcon, ExclamationCircleIcon } from './icons';

const SEND_TEST_EMAIL_PATH = "/wp-change-email-sender/v1/send-test-email";

const SendTestEmail = () => {
  const [recipientEmail, setRecipientEmail] = useState(
    window.__wpcesCurrentUserEmail ?? "",
  );
  const [emailBody, setEmailBody] = useState("");
  const [isSending, setIsSending] = useState(false);
  const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore);

  const handleSendTestEmail = useCallback(
    async (event) => {
      event.preventDefault();
      setIsSending(true);

      try {
        const response = await apiFetch({
          path: SEND_TEST_EMAIL_PATH,
          method: "POST",
          data: { recipient: recipientEmail, message: emailBody },
        });

        createSuccessNotice(
          response.message || __("Test email sent!", "wp-change-email-sender"),
          { 
            type: 'snackbar', 
            id: 'wpces-test-success',
            icon: <CheckBadgeIcon style={{ width: '24px', height: '24px', color: 'rgb(16 185 129)' }} />
          }
        );
      } catch (err) {
        createErrorNotice(
          err.message || __("Failed to send test email.", "wp-change-email-sender"),
          { 
            type: 'snackbar', 
            id: 'wpces-test-error',
            icon: <ExclamationCircleIcon style={{ width: '24px', height: '24px', color: 'rgb(244 63 94)' }} />
          }
        );
      } finally {
        setIsSending(false);
      }
    },
    [recipientEmail, emailBody, createSuccessNotice, createErrorNotice],
  );

  return (
    <div className="wpces-section" id="wpces-test-email">
      <form onSubmit={handleSendTestEmail}>
        <Card className="wpces-form-header-card">
          <CardBody className="wpces-form-section-header">
            <h3 className="wpces-section-title">
              {__("Send Test Email", "wp-change-email-sender")}
            </h3>
            <p className="wpces-section-description">
              {__(
                "Send a test email to ensure your configuration is successfully overriding the default sender.",
                "wp-change-email-sender",
              )}
            </p>
          </CardBody>
        </Card>
        <Card>
          <CardBody className="wpces-form-section-body">
            <div className="wpces-settings-group">
              <TextControl
                label={__("Recipient Email Address", "wp-change-email-sender")}
                help={__(
                  "Send a test email to verify your sender settings are working correctly.",
                  "wp-change-email-sender",
                )}
                type="email"
                value={recipientEmail}
                onChange={setRecipientEmail}
                placeholder="you@example.com"
              />
            </div>
            <div className="wpces-settings-group">
              <TextareaControl
                label={__("Email Body", "wp-change-email-sender")}
                help={__(
                  "Optional custom message to include in the test email.",
                  "wp-change-email-sender",
                )}
                value={emailBody}
                onChange={setEmailBody}
                rows={4}
              />
            </div>
            <Button
              variant="primary"
              type="submit"
              isBusy={isSending}
              disabled={isSending || !recipientEmail}
            >
              {isSending && <Spinner />}
              {__("Send Test Email", "wp-change-email-sender")}
            </Button>
          </CardBody>
        </Card>
      </form>
    </div>
  );
};

export default SendTestEmail;
