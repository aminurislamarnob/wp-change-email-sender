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
	TextareaControl,
	TextControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { useSettings } from '../context/SettingsContext';
import SettingsHeader from './SettingsHeader';
import { EnvelopeIcon } from './icons';

const SEND_TEST_EMAIL_PATH = '/wp-change-email-sender/v1/send-test-email';

const SendTestEmail = () => {
	const { setMessage, setError } = useSettings();
	const [ recipientEmail, setRecipientEmail ] = useState(
		window.__wpcesCurrentUserEmail ?? ''
	);
	const [ emailBody, setEmailBody ] = useState( '' );
	const [ isSending, setIsSending ] = useState( false );

	const handleSendTestEmail = useCallback(
		async ( event ) => {
			event.preventDefault();
			setIsSending( true );
			setMessage( '' );
			setError( '' );

			try {
				const response = await apiFetch( {
					path: SEND_TEST_EMAIL_PATH,
					method: 'POST',
					data: { recipient: recipientEmail, message: emailBody },
				} );

				setMessage( response.message );
			} catch ( err ) {
				setError( err.message );
			} finally {
				setIsSending( false );
			}
		},
		[ recipientEmail, emailBody, setMessage, setError ]
	);

	return (
		<div>
			<SettingsHeader
				icon={ EnvelopeIcon }
				title={ __( 'Send Test Email', 'wp-change-email-sender' ) }
			/>
			<form onSubmit={ handleSendTestEmail }>
				<Card>
					<CardBody>
						<div className="wpces-settings-group">
							<TextControl
								label={ __(
									'Recipient Email Address',
									'wp-change-email-sender'
								) }
								help={ __(
									'Send a test email to verify your sender settings are working correctly.',
									'wp-change-email-sender'
								) }
								type="email"
								value={ recipientEmail }
								onChange={ setRecipientEmail }
								placeholder="you@example.com"
							/>
						</div>
						<div className="wpces-settings-group">
							<TextareaControl
								label={ __(
									'Email Body',
									'wp-change-email-sender'
								) }
								help={ __(
									'Optional custom message to include in the test email.',
									'wp-change-email-sender'
								) }
								value={ emailBody }
								onChange={ setEmailBody }
								rows={ 4 }
							/>
						</div>
						<Button
							variant="primary"
							type="submit"
							isBusy={ isSending }
							disabled={ isSending || ! recipientEmail }
						>
							{ isSending && <Spinner /> }
							{ __(
								'Send Test Email',
								'wp-change-email-sender'
							) }
						</Button>
					</CardBody>
				</Card>
			</form>
		</div>
	);
};

export default SendTestEmail;
