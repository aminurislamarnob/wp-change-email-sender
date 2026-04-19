# Skill: Add a New Settings Page

Follow every step in order. Do not skip any.

---

## 1. Create the component — `src/Components/<PageName>.js`

Use this exact structure. Replace `<PageName>`, `route-slug`, `my_setting_key`, and label strings.

```jsx
/**
 * WordPress dependencies
 */
import { useState, useCallback } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Card, CardBody, Spinner, TextControl, ToggleControl } from "@wordpress/components";
import { useSettings } from "../context/SettingsContext";

const <PageName> = () => {
  const { settings, isSaving, saveSettings } = useSettings();

  // Mirror each setting key into local state
  const [myValue, setMyValue] = useState(settings.my_setting_key ?? "");

  const handleSubmit = useCallback(
    async (event) => {
      event.preventDefault();
      await saveSettings({ my_setting_key: myValue });
    },
    [myValue, saveSettings],
  );

  return (
    <div className="wpces-section" id="wpces-<page-name>">
      <form onSubmit={handleSubmit}>

        {/* Header card — title + description */}
        <Card className="wpces-form-header-card">
          <CardBody className="wpces-form-section-header">
            <h3 className="wpces-section-title">
              {__("<Page Title>", "wp-change-email-sender")}
            </h3>
            <p className="wpces-section-description">
              {__("<Short description>", "wp-change-email-sender")}
            </p>
          </CardBody>
        </Card>

        {/* Body card — controls */}
        <Card>
          <CardBody className="wpces-form-section-body">
            <div className="wpces-settings-group">
              <TextControl
                label={__("<Label>", "wp-change-email-sender")}
                help={__("<Help text>", "wp-change-email-sender")}
                value={myValue}
                onChange={setMyValue}
              />
            </div>

            {/* Save button — always last inside the body card */}
            <Button variant="primary" type="submit" isBusy={isSaving} disabled={isSaving}>
              {isSaving && <Spinner />}
              {__("Save Changes", "wp-change-email-sender")}
            </Button>
          </CardBody>
        </Card>

      </form>
    </div>
  );
};

export default <PageName>;
```

**Rules:**
- Use `TextControl`, `ToggleControl`, `SelectControl` from `@wordpress/components` — never raw `<input>`.
- Wrap every control in `<div className="wpces-settings-group">`.
- Every user-facing string must use `__( '…', 'wp-change-email-sender' )`.
- No inline styles unless matching an existing pattern in `LayoutStyles.css`.
- Do not add new top-level CSS classes without adding them to `LayoutStyles.css`.

---

## 2. Register the allowed settings keys in the REST controller

Open `includes/Admin/REST/SettingsController.php` and add each new key to the `get_item_schema()` `properties` array and ensure the `update_item()` sanitisation handles it.

For string keys — use `sanitize_text_field()` (or `sanitize_email()` for emails).  
For bool keys — cast with `(bool)`.

---

## 3. Register the route — `src/admin.js`

```js
import <PageName> from './Components/<PageName>';

// Inside <Routes>:
<Route path="<route-slug>" element={ <<PageName> /> } />
```

---

## 4. Add the nav link — `src/Components/Layout.js`

Inside the `<div className="wpces-hash-nav">` block, append:

```jsx
<Link
  to="/<route-slug>"
  className={isActive("/<route-slug>") ? "is-active" : ""}
>
  {__("<Nav Label>", "wp-change-email-sender")}
</Link>
```

Also add a matching skeleton tab in the `isLoading` branch directly above:

```jsx
<div className="wpces-skeleton-tab" style={{ width: "<Npx>" }}></div>
```

Estimate width ≈ `character_count × 8px`.

---

## 5. Update `CLAUDE.md`

- Add the new route to the 3-route list under **React Admin**.
- Add new setting keys to the **Settings and data flow** key list if they touch the DB option.

---

## 6. Update `readme.txt`

- Add a bullet under `= Plugin Features =`.
- Add a bullet under the current version in `== Changelog ==`.

---

## Checklist before committing

- [ ] Component renders without console errors
- [ ] Save button fires `saveSettings` and shows snackbar
- [ ] Nav tab highlights correctly on the active route
- [ ] Skeleton tab width roughly matches real tab width
- [ ] All strings translated (`__()` with correct text domain)
- [ ] New DB keys handled in `SettingsController.php`
- [ ] `CLAUDE.md` and `readme.txt` updated
- [ ] `git commit -m "Feat: add <PageName> settings page"`
