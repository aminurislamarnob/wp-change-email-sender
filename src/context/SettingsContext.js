/**
 * WordPress dependencies
 */
import {
  createContext,
  useContext,
  useState,
  useEffect,
  useCallback,
  useMemo,
} from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";
import { __ } from "@wordpress/i18n";
import { useDispatch } from "@wordpress/data";
import { store as noticesStore } from "@wordpress/notices";
import { CheckBadgeIcon, ExclamationCircleIcon } from '../Components/icons';

const SETTINGS_PATH = "/wp-change-email-sender/v1/settings";

const SettingsContext = createContext();

export const SettingsProvider = ({ children }) => {
  const [settings, setSettings] = useState({});
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore);

  useEffect(() => {
    let cancelled = false;

    const fetchSettings = async () => {
      try {
        const response = await apiFetch({ path: SETTINGS_PATH });

        if (!cancelled) {
          setSettings(response ?? {});
        }
      } catch (err) {
        if (!cancelled) {
          createErrorNotice(err.message, {
            type: "snackbar",
            id: "wpces-fetch-error",
          });
        }
      } finally {
        if (!cancelled) {
          setIsLoading(false);
        }
      }
    };

    fetchSettings();

    return () => {
      cancelled = true;
    };
  }, [createErrorNotice]);

  const saveSettings = useCallback(
    async (data, customMessage) => {
      setIsSaving(true);

      try {
        const response = await apiFetch({
          path: SETTINGS_PATH,
          method: "POST",
          data,
        });

        setSettings(response ?? {});
        createSuccessNotice(
          customMessage ?? __("Settings saved successfully!", "wp-change-email-sender"),
          {
            type: "snackbar",
            id: "wpces-save-success",
            isDismissible: false,
            icon: <CheckBadgeIcon style={{ width: '24px', height: '24px', color: 'rgb(16 185 129)' }} />,
          },
        );
      } catch (err) {
        createErrorNotice(err.message, {
          type: "snackbar",
          id: "wpces-save-error",
          icon: <ExclamationCircleIcon style={{ width: '24px', height: '24px', color: 'rgb(244 63 94)' }} />,
        });
      } finally {
        setIsSaving(false);
      }
    },
    [createSuccessNotice, createErrorNotice],
  );

  const value = useMemo(
    () => ({
      settings,
      isLoading,
      isSaving,
      saveSettings,
    }),
    [settings, isLoading, isSaving, saveSettings],
  );

  return (
    <SettingsContext.Provider value={value}>
      {children}
    </SettingsContext.Provider>
  );
};

export const useSettings = () => {
  const context = useContext(SettingsContext);

  if (!context) {
    throw new Error("useSettings must be used within a SettingsProvider");
  }

  return context;
};
