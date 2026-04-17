/**
 * WordPress dependencies
 */
import { useState, useEffect } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Spinner, Button } from "@wordpress/components";
import { Link, Outlet, useLocation } from "react-router-dom";
import { SnackbarList } from "@wordpress/components";
import { useSelect, useDispatch } from "@wordpress/data";
import { store as noticesStore } from "@wordpress/notices";

/**
 * Internal dependencies
 */
import { useSettings } from "../context/SettingsContext";
import { EnvelopeIcon } from "./icons";
import SettingsHeader from "./SettingsHeader";

const Layout = () => {
  const { isLoading } = useSettings();
  const location = useLocation();
  const isActive = (path) => location.pathname === path;

  const notices = useSelect((select) => select(noticesStore).getNotices());
  const { removeNotice } = useDispatch(noticesStore);

  // Filter only our snackbar notices
  const snackbarNotices = notices.filter(
    (notice) => notice.type === "snackbar",
  );

  return (
    <div className="wpces-admin-app">
      <SettingsHeader
        icon={EnvelopeIcon}
        title={__("WP Change Email Sender", "wp-change-email-sender")}
        subTitle={__(
          "Configure your global email sender details and test your outgoing email functionality.",
          "wp-change-email-sender",
        )}
        actions={
          <>
            <Button
              variant="secondary"
              href="https://github.com/aminurislamarnob/wp-change-email-sender"
              target="_blank"
            >
              {__("Documentation", "wp-change-email-sender")}
            </Button>
            <Button
              variant="primary"
              href="https://buymeacoffee.com/aiarnob"
              target="_blank"
            >
              {__("Donate", "wp-change-email-sender")}
            </Button>
          </>
        }
      />

      <main className="wpces-main-content wpces-setting-wrapper">
        {isLoading ? (
          <div className="wpces-loading">
            <Spinner />
          </div>
        ) : (
          <div className="wpces-content-body">
            <div className="wpces-hash-nav">
              <Link to="/" className={isActive("/") ? "is-active" : ""}>
                {__("General Settings", "wp-change-email-sender")}
              </Link>
              <Link
                to="/send-test-email"
                className={isActive("/send-test-email") ? "is-active" : ""}
              >
                {__("Send Test Email", "wp-change-email-sender")}
              </Link>
            </div>

            <Outlet />
          </div>
        )}
      </main>

      <SnackbarList
        notices={snackbarNotices}
        className="components-editor-notices__snackbar"
        onRemove={removeNotice}
      />
    </div>
  );
};

export default Layout;
