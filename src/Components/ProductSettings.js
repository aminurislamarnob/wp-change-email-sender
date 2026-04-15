import { __ } from '@wordpress/i18n';
import { useState, useEffect } from 'react';
import {
	Button,
	Card,
	CardBody,
	Notice,
	TextControl,
	Spinner,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const PRODUCT_FIELDS = [
	{
		key: 'productPerPage',
		apiKey: 'wp_change_email_sender_product_per_page',
		label: __( 'Products per page', 'wp-change-email-sender' ),
		help: __( 'Default: 10', 'wp-change-email-sender' ),
	},
];

const ProductSettings = () => {
	const [ product, setProduct ] = useState( () =>
		Object.fromEntries(
			PRODUCT_FIELDS.map( ( { key } ) => [ key, '' ] )
		)
	);
	const [ isLoading, setIsLoading ] = useState( false );
	const [ message, setMessage ] = useState( '' );
	const [ error, setError ] = useState( '' );

	// Fetch plugin settings.
	useEffect( () => {
		setIsLoading( true );
		const fetchSettings = async () => {
			try {
				const response = await apiFetch( {
					path: '/wp-change-email-sender/v1/settings',
				} );

				const productData = {};
				PRODUCT_FIELDS.forEach( ( { key, apiKey } ) => {
					productData[ key ] =
						response[ apiKey ] !== undefined
							? String( response[ apiKey ] )
							: '';
				} );
				setProduct( productData );

				setError( null );
				setIsLoading( false );
			} catch ( err ) {
				setError( err.message );
				setIsLoading( false );
			}
		};

		fetchSettings();
	}, [] );

	// Handle submit to save product settings.
	const handleSubmit = async ( event ) => {
		event.preventDefault();
		setIsLoading( true );
		try {
			const data = {};
			PRODUCT_FIELDS.forEach( ( { key, apiKey } ) => {
				data[ apiKey ] = product[ key ] ?? '';
			} );

			const response = await apiFetch( {
				path: '/wp-change-email-sender/v1/settings',
				method: 'POST',
				data,
			} );

			const productData = {};
			PRODUCT_FIELDS.forEach( ( { key, apiKey } ) => {
				productData[ key ] =
					response[ apiKey ] !== undefined
						? String( response[ apiKey ] )
						: '';
			} );
			setProduct( productData );

			setMessage(
				__( 'Settings saved successfully!', 'wp-change-email-sender' )
			);
			setError( '' );
			setIsLoading( false );
		} catch ( error ) {
			setError( error.message );
			setMessage( '' );
			setIsLoading( false );
		}
	};

	return (
		<div>
			<div className="settings-header">
				<div className="settings-header-icon">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="16"
						height="16"
						fill="currentColor"
						className="bi bi-collection"
						viewBox="0 0 16 16"
					>
						<path d="M2.5 3.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1zm2-2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1zM0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6zm1.5.5A.5.5 0 0 1 1 13V6a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5z" />
					</svg>
				</div>
				<h2>{ __( 'Product Settings', 'wp-change-email-sender' ) }</h2>
			</div>
			{ message && (
				<Notice
					className="w-full mb-4"
					status="success"
					isDismissible
					onDismiss={ () => setMessage( '' ) }
				>
					{ message }
				</Notice>
			) }
			{ error && (
				<Notice
					className="w-full mb-4"
					status="error"
					isDismissible
					onDismiss={ () => setError( '' ) }
				>
					{ error }
				</Notice>
			) }
			<form onSubmit={ handleSubmit }>
				<Card>
					<CardBody>
						{ PRODUCT_FIELDS.map( ( { key, label, help } ) => (
							<div
								key={ key }
								className="wpces-settings-group"
							>
								<TextControl
									label={ label }
									help={ help }
									value={ product[ key ] }
									onChange={ ( value ) =>
										setProduct( ( prev ) => ( {
											...prev,
											[ key ]: value,
										} ) )
									}
								/>
							</div>
						) ) }
						<Button
							variant="primary"
							type="submit"
							disabled={ isLoading }
						>
							{ isLoading && <Spinner /> }
							{ __( 'Save Changes', 'wp-change-email-sender' ) }
						</Button>
					</CardBody>
				</Card>
			</form>
		</div>
	);
};

export default ProductSettings;
