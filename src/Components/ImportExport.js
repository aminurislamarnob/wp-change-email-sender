/**
 * WordPress dependencies
 */
import { useRef } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Card, CardBody } from "@wordpress/components";
import { useDispatch } from "@wordpress/data";
import { store as noticesStore } from "@wordpress/notices";

/**
 * Internal dependencies
 */
import { ArrowDownTrayIcon, ArrowUpTrayIcon, CheckBadgeIcon, ExclamationCircleIcon } from "./icons";
import { useSettings } from "../context/SettingsContext";

const ALLOWED_KEYS = [
  "wp_change_email_sender_name",
  "wp_change_email_sender_email_address",
  "force_from_name",
  "force_from_email",
];

const pickAllowed = (obj) =>
  Object.fromEntries(
    Object.entries(obj).filter(([key]) => ALLOWED_KEYS.includes(key))
  );

const ImportExport = () => {
  const { settings, saveSettings, isSaving } = useSettings();
  const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore);
  const fileInputRef = useRef(null);

  const handleExport = () => {
    try {
      const exportData = pickAllowed(settings);

      const dataStr = JSON.stringify(exportData, null, 2);
      const blob = new Blob([dataStr], { type: "application/json" });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = "wp-change-email-sender-settings.json";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);

      createSuccessNotice(
        __("Settings exported successfully.", "wp-change-email-sender"),
        {
          type: "snackbar",
          id: "wpces-export-success",
          icon: <CheckBadgeIcon style={{ width: '24px', height: '24px', color: 'rgb(16 185 129)' }} />,
        },
      );
    } catch (error) {
      createErrorNotice(
        __("Failed to export settings.", "wp-change-email-sender"),
        {
          type: "snackbar",
          id: "wpces-export-error",
          icon: <ExclamationCircleIcon style={{ width: '24px', height: '24px', color: 'rgb(244 63 94)' }} />,
        },
      );
    }
  };

  const handleImport = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async (e) => {
      try {
        const importedSettings = JSON.parse(e.target.result);

        // Basic validation to ensure it's an object
        if (typeof importedSettings !== "object" || importedSettings === null) {
          throw new Error("Invalid format");
        }

        const payload = pickAllowed(importedSettings);

        if (Object.keys(payload).length === 0) {
          throw new Error("No valid settings found");
        }

        await saveSettings(
          payload,
          __("Settings imported successfully.", "wp-change-email-sender")
        );
      } catch (error) {
        createErrorNotice(
          __(
            "Invalid settings file. Please upload a valid JSON file.",
            "wp-change-email-sender",
          ),
          {
            type: "snackbar",
            id: "wpces-import-error",
            icon: <ExclamationCircleIcon style={{ width: '24px', height: '24px', color: 'rgb(244 63 94)' }} />,
          },
        );
      }

      // Reset input
      if (fileInputRef.current) {
        fileInputRef.current.value = "";
      }
    };
    reader.readAsText(file);
  };

  return (
    <div className="wpces-section">
      <Card className="wpces-form-header-card">
        <CardBody className="wpces-form-section-header">
          <h3 className="wpces-section-title">
            {__("Import / Export", "wp-change-email-sender")}
          </h3>
          <p className="wpces-section-description">
            {__(
              "Download your current configurations as a JSON backup or restore previously saved settings.",
              "wp-change-email-sender",
            )}
          </p>
        </CardBody>
      </Card>

      <Card>
        <CardBody className="wpces-form-section-body">
          <div className="wpces-settings-group" style={{ marginBottom: "32px" }}>
            <h4 style={{ marginTop: 0, marginBottom: "8px" }}>{__("Export Settings", "wp-change-email-sender")}</h4>
            <p className="wpces-setting-description" style={{ marginBottom: "16px" }}>
              {__(
                "Download your current email sender configurations as a JSON file. This is useful for backups or transferring settings to another site.",
                "wp-change-email-sender",
              )}
            </p>
            <Button
              variant="primary"
              icon={
                <ArrowDownTrayIcon style={{ width: "18px", marginRight: "6px" }} />
              }
              onClick={handleExport}
            >
              {__("Export Settings", "wp-change-email-sender")}
            </Button>
          </div>

          <div className="wpces-settings-group" style={{ borderTop: "1px solid #e2e8f0", paddingTop: "32px", marginBottom: 0 }}>
            <h4 style={{ marginTop: 0, marginBottom: "8px" }}>{__("Import Settings", "wp-change-email-sender")}</h4>
            <p className="wpces-setting-description" style={{ marginBottom: "16px" }}>
              {__(
                "Restore your settings by uploading a previously exported JSON file. Note: This will overwrite your current configurations.",
                "wp-change-email-sender",
              )}
            </p>

            <input
              type="file"
              accept=".json,application/json"
              style={{ display: "none" }}
              ref={fileInputRef}
              onChange={handleImport}
            />

            <Button
              variant="secondary"
              isBusy={isSaving}
              disabled={isSaving}
              icon={<ArrowUpTrayIcon style={{ width: "18px", marginRight: "6px" }} />}
              onClick={() => fileInputRef.current?.click()}
            >
              {__("Import Settings", "wp-change-email-sender")}
            </Button>
          </div>
        </CardBody>
      </Card>
    </div>
  );
};

export default ImportExport;
