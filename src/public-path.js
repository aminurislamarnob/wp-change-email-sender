/* eslint-disable camelcase, no-unused-vars */
/* global __webpack_public_path__:writable */

// Set at runtime by wp_add_inline_script in Settings.php.
// Falls back to auto-detection if the global is missing.
if ( typeof window.__wpcesBuildURL === 'string' ) {
	__webpack_public_path__ = window.__wpcesBuildURL;
}
