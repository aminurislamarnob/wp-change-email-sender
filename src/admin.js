/**
 * Must be first — sets __webpack_public_path__ for lazy-loaded chunks.
 */
import './public-path';

/**
 * WordPress dependencies
 */
import { lazy, Suspense, createRoot } from '@wordpress/element';
import { Spinner } from '@wordpress/components';

/**
 * External dependencies
 */
import { HashRouter as Router, Routes, Route } from 'react-router-dom';

/**
 * Internal dependencies
 */
import './styles/styles.css';
import './styles/index.css';
import './Components/LayoutStyles.css';
import { SettingsProvider } from './context/SettingsContext';
import Layout from './Components/Layout';
import GeneralSettings from './Components/GeneralSettings';

const ProductSettings = lazy( () =>
	import(
		/* webpackChunkName: "product-settings" */ './Components/ProductSettings'
	)
);

const LazyFallback = () => (
	<div className="wpces-loading">
		<Spinner />
	</div>
);

const App = () => (
	<Router>
		<SettingsProvider>
			<Suspense fallback={ <LazyFallback /> }>
				<Routes>
					<Route path="/" element={ <Layout /> }>
						<Route index element={ <GeneralSettings /> } />
						<Route
							path="product-settings"
							element={ <ProductSettings /> }
						/>
					</Route>
				</Routes>
			</Suspense>
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
