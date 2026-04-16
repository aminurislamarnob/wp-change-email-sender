/**
 * Must be first — sets __webpack_public_path__ for lazy-loaded chunks.
 */
import './public-path';

/**
 * WordPress dependencies
 */
import { createRoot } from '@wordpress/element';

/**
 * External dependencies
 */
import { HashRouter as Router, Routes, Route } from 'react-router-dom';

/**
 * Internal dependencies
 */
import './Components/LayoutStyles.css';
import { SettingsProvider } from './context/SettingsContext';
import Layout from './Components/Layout';
import GeneralSettings from './Components/GeneralSettings';
import SendTestEmail from './Components/SendTestEmail';

const App = () => (
	<Router>
		<SettingsProvider>
			<Routes>
				<Route path="/" element={ <Layout /> }>
					<Route index element={ <GeneralSettings /> } />
					<Route path="test-email" element={ <SendTestEmail /> } />
				</Route>
			</Routes>
		</SettingsProvider>
	</Router>
);

document.addEventListener( 'DOMContentLoaded', () => {
	const container = document.getElementById( 'WpChangeEmailSenderSettings' );

	if ( container ) {
		const root = createRoot( container );
		root.render( <App /> );
	}
} );
