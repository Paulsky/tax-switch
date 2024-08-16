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
	const { switchColor, switchBackgroundColor, switchBackgroundColorChecked } =
		attributes;

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
					] }
				/>
			</InspectorControls>
			{ ( attributes.readOnly = true ) }
			<SwitchComponent { ...attributes } />
		</div>
	);
}
