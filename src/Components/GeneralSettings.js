/**
 * WordPress dependencies
 */
import { useState, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Button,
	Card,
	CardBody,
	Spinner,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useSettings } from '../context/SettingsContext';
import SettingsHeader from './SettingsHeader';
import { GearIcon } from './icons';

const GeneralSettings = () => {
	const { settings, isSaving, saveSettings } = useSettings();

	const [ senderName, setSenderName ] = useState(
		settings.wp_change_email_sender_name ?? ''
	);
	const [ senderEmail, setSenderEmail ] = useState(
		settings.wp_change_email_sender_email_address ?? ''
	);
	const [ forceFromName, setForceFromName ] = useState(
		settings.force_from_name ?? false
	);
	const [ forceFromEmail, setForceFromEmail ] = useState(
		settings.force_from_email ?? false
	);

	const handleSubmit = useCallback(
		async ( event ) => {
			event.preventDefault();
			await saveSettings( {
				wp_change_email_sender_name: senderName,
				wp_change_email_sender_email_address: senderEmail,
				force_from_name: forceFromName,
				force_from_email: forceFromEmail,
			} );
		},
		[ senderName, senderEmail, forceFromName, forceFromEmail, saveSettings ]
	);

	return (
		<div>
			<SettingsHeader
				icon={ GearIcon }
				title={ __( 'General Settings', 'wp-change-email-sender' ) }
			/>
			<form onSubmit={ handleSubmit }>
				<Card>
					<CardBody>
						<div className="wpces-settings-group">
							<TextControl
								label={ __(
									'Email Sender Name',
									'wp-change-email-sender'
								) }
								help={ __(
									'The "From" name used for outgoing WordPress emails.',
									'wp-change-email-sender'
								) }
								value={ senderName }
								onChange={ setSenderName }
								placeholder={ __(
									'Mail Sender Name',
									'wp-change-email-sender'
								) }
							/>
						</div>
						<div className="wpces-settings-group">
							<TextControl
								label={ __(
									'Sender Email Address',
									'wp-change-email-sender'
								) }
								help={ __(
									'The "From" address used for outgoing WordPress emails.',
									'wp-change-email-sender'
								) }
								type="email"
								value={ senderEmail }
								onChange={ setSenderEmail }
								placeholder="info@yourdomain.com"
							/>
						</div>
						<div className="wpces-settings-group">
							<ToggleControl
								label={ __(
									'Force From Name',
									'wp-change-email-sender'
								) }
								help={ __(
									'Overrides the From Name set by WooCommerce, Contact Form 7, and other plugins.',
									'wp-change-email-sender'
								) }
								checked={ forceFromName }
								onChange={ setForceFromName }
							/>
						</div>
						<div className="wpces-settings-group">
							<ToggleControl
								label={ __(
									'Force From Email',
									'wp-change-email-sender'
								) }
								help={ __(
									'Overrides the From Email Address set by WooCommerce, Contact Form 7, and other plugins.',
									'wp-change-email-sender'
								) }
								checked={ forceFromEmail }
								onChange={ setForceFromEmail }
							/>
						</div>
						<Button
							variant="primary"
							type="submit"
							isBusy={ isSaving }
							disabled={ isSaving }
						>
							{ isSaving && <Spinner /> }
							{ __( 'Save Changes', 'wp-change-email-sender' ) }
						</Button>
					</CardBody>
				</Card>
			</form>
		</div>
	);
};

export default GeneralSettings;
