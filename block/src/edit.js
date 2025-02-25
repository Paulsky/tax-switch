/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { __ } from '@wordpress/i18n';

import {
	InspectorControls,
	PanelColorSettings,
	useBlockProps,
} from '@wordpress/block-editor';

import { TextControl, PanelBody } from '@wordpress/components';

import SwitchComponent from './components/SwitchComponent';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		switchColor,
		switchColorChecked,
		switchBackgroundColor,
		switchBackgroundColorChecked,
		switchTextColor,
		switchLabelIncl,
		switchLabelExcl,
	} = attributes;

	const { originalTaxDisplay } = window.wtsEditorObject || {
		originalTaxDisplay: 'incl',
	};

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelColorSettings
					title={ __( 'Switch colors', 'wdevs-tax-switch' ) }
					initialOpen={ true }
					colorSettings={ [
						{
							value: switchColor,
							onChange: ( color ) =>
								setAttributes( { switchColor: color } ),
							label: __( 'Switch color', 'wdevs-tax-switch' ),
						},
						{
							value: switchColorChecked,
							onChange: ( color ) =>
								setAttributes( {
									switchColorChecked: color,
								} ),
							label: __(
								'Switch color checked',
								'wdevs-tax-switch'
							),
						},
						{
							value: switchBackgroundColor,
							onChange: ( color ) =>
								setAttributes( {
									switchBackgroundColor: color,
								} ),
							label: __( 'Background color', 'wdevs-tax-switch' ),
						},
						{
							value: switchBackgroundColorChecked,
							onChange: ( color ) =>
								setAttributes( {
									switchBackgroundColorChecked: color,
								} ),
							label: __(
								'Background color checked',
								'wdevs-tax-switch'
							),
						},
						{
							value: switchTextColor,
							onChange: ( color ) =>
								setAttributes( {
									switchTextColor: color,
								} ),
							label: __( 'Text color', 'wdevs-tax-switch' ),
						},
					] }
				/>
				<PanelBody
					title={ __( 'Switch Labels', 'wdevs-tax-switch' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __(
							'Including VAT Label',
							'wdevs-tax-switch'
						) }
						value={ switchLabelIncl }
						onChange={ ( value ) =>
							setAttributes( { switchLabelIncl: value } )
						}
					/>
					<TextControl
						label={ __(
							'Excluding VAT Label',
							'wdevs-tax-switch'
						) }
						value={ switchLabelExcl }
						onChange={ ( value ) =>
							setAttributes( { switchLabelExcl: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<SwitchComponent
				{ ...attributes }
				readOnly={ true }
				originalTaxDisplay={ originalTaxDisplay }
			/>
		</div>
	);
}
