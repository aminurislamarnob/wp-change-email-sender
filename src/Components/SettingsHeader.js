/**
 * WordPress dependencies
 */
import { Notice } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useSettings } from '../context/SettingsContext';

const SettingsHeader = ( { icon: Icon, title } ) => {
	const { message, error, setMessage, setError } = useSettings();

	return (
		<>
			<div className="settings-header">
				<div className="settings-header-icon">
					<Icon />
				</div>
				<h2>{ title }</h2>
			</div>
			{ message && (
				<Notice
					className="wpces-notice"
					status="success"
					isDismissible
					onDismiss={ () => setMessage( '' ) }
				>
					{ message }
				</Notice>
			) }
			{ error && (
				<Notice
					className="wpces-notice"
					status="error"
					isDismissible
					onDismiss={ () => setError( '' ) }
				>
					{ error }
				</Notice>
			) }
		</>
	);
};

export default SettingsHeader;
