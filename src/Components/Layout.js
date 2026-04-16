/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Spinner } from '@wordpress/components';

/**
 * External dependencies
 */
import { Link, Outlet, useLocation } from 'react-router-dom';

/**
 * Internal dependencies
 */
import { useSettings } from '../context/SettingsContext';
import { GearIcon, EnvelopeIcon } from './icons';
import logo from '../../assets/images/settings-logo.svg';

const Layout = () => {
	const location = useLocation();
	const { isLoading } = useSettings();
	const isActive = ( path ) => location.pathname === path;

	return (
		<div className="wpces-setting-wrapper">
			<section className="wpces-sidebar-nav">
				<div className="sidebar-logo">
					<img
						src={ logo }
						alt={ __(
							'WP Change Email Sender',
							'wp-change-email-sender'
						) }
						className="layout-logo"
						width="163"
						height="44"
					/>
				</div>
				<nav>
					<ul>
						<li className={ isActive( '/' ) ? 'active' : '' }>
							<Link to="/">
								<div className="menu-title">
									{ __(
										'General',
										'wp-change-email-sender'
									) }
								</div>
								<div className="menu-title-description">
									{ __(
										'Set email sender name and address',
										'wp-change-email-sender'
									) }
								</div>
								<div className="menu-icon">
									<GearIcon />
								</div>
							</Link>
						</li>
						<li
							className={
								isActive( '/test-email' ) ? 'active' : ''
							}
						>
							<Link to="/test-email">
								<div className="menu-title">
									{ __(
										'Test Email',
										'wp-change-email-sender'
									) }
								</div>
								<div className="menu-title-description">
									{ __(
										'Verify your email settings',
										'wp-change-email-sender'
									) }
								</div>
								<div className="menu-icon">
									<EnvelopeIcon />
								</div>
							</Link>
						</li>
					</ul>
				</nav>
			</section>
			<main>
				{ isLoading ? (
					<div className="wpces-loading">
						<Spinner />
					</div>
				) : (
					<Outlet />
				) }
			</main>
		</div>
	);
};

export default Layout;
