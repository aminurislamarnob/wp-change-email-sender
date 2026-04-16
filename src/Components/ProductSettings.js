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
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useSettings } from '../context/SettingsContext';
import SettingsHeader from './SettingsHeader';
import { CollectionIcon } from './icons';

const PRODUCT_FIELDS = [
	{
		key: 'productPerPage',
		apiKey: 'wp_change_email_sender_product_per_page',
		label: __( 'Products per page', 'wp-change-email-sender' ),
		help: __( 'Default: 10', 'wp-change-email-sender' ),
	},
];

const ProductSettings = () => {
	const { settings, isSaving, saveSettings } = useSettings();

	const [ product, setProduct ] = useState( () => {
		const initial = {};
		PRODUCT_FIELDS.forEach( ( { key, apiKey } ) => {
			initial[ key ] =
				settings[ apiKey ] !== undefined
					? String( settings[ apiKey ] )
					: '';
		} );
		return initial;
	} );

	const handleSubmit = useCallback(
		async ( event ) => {
			event.preventDefault();
			const data = {};
			PRODUCT_FIELDS.forEach( ( { key, apiKey } ) => {
				data[ apiKey ] = product[ key ] ?? '';
			} );
			await saveSettings( data );
		},
		[ product, saveSettings ]
	);

	return (
		<div>
			<SettingsHeader
				icon={ CollectionIcon }
				title={ __( 'Product Settings', 'wp-change-email-sender' ) }
			/>
			<form onSubmit={ handleSubmit }>
				<Card>
					<CardBody>
						{ PRODUCT_FIELDS.map( ( { key, label, help } ) => (
							<div key={ key } className="wpces-settings-group">
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

export default ProductSettings;
