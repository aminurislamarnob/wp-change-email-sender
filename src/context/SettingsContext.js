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
} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

const SETTINGS_PATH = '/wp-change-email-sender/v1/settings';

const SettingsContext = createContext();

export const SettingsProvider = ( { children } ) => {
	const [ settings, setSettings ] = useState( {} );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ message, setMessage ] = useState( '' );
	const [ error, setError ] = useState( '' );

	useEffect( () => {
		let cancelled = false;

		const fetchSettings = async () => {
			try {
				const response = await apiFetch( { path: SETTINGS_PATH } );

				if ( ! cancelled ) {
					setSettings( response ?? {} );
					setError( '' );
				}
			} catch ( err ) {
				if ( ! cancelled ) {
					setError( err.message );
				}
			} finally {
				if ( ! cancelled ) {
					setIsLoading( false );
				}
			}
		};

		fetchSettings();

		return () => {
			cancelled = true;
		};
	}, [] );

	const saveSettings = useCallback( async ( data ) => {
		setIsSaving( true );
		setMessage( '' );
		setError( '' );

		try {
			const response = await apiFetch( {
				path: SETTINGS_PATH,
				method: 'POST',
				data,
			} );

			setSettings( response ?? {} );
			setMessage(
				__( 'Settings saved successfully!', 'wp-change-email-sender' )
			);
		} catch ( err ) {
			setError( err.message );
		} finally {
			setIsSaving( false );
		}
	}, [] );

	const value = useMemo(
		() => ( {
			settings,
			isLoading,
			isSaving,
			message,
			error,
			setMessage,
			setError,
			saveSettings,
		} ),
		[ settings, isLoading, isSaving, message, error, saveSettings ]
	);

	return (
		<SettingsContext.Provider value={ value }>
			{ children }
		</SettingsContext.Provider>
	);
};

export const useSettings = () => {
	const context = useContext( SettingsContext );

	if ( ! context ) {
		throw new Error( 'useSettings must be used within a SettingsProvider' );
	}

	return context;
};
