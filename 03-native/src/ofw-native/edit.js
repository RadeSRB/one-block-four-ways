/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
} from '@wordpress/block-editor';

import { Button, PanelBody, SelectControl } from '@wordpress/components';

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
 * @param {Object}   props               Block properties.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Block attributes updater.
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { quote, author, role, photoId, photoUrl, variant } = attributes;

	const blockProps = useBlockProps( {
		className: `testimonial is-${ variant }`,
	} );

	const onSelectPhoto = ( media ) => {
		setAttributes( {
			photoId: media.id,
			photoUrl: media.sizes?.thumbnail?.url || media.url,
		} );
	};

	const onSelectVariant = ( newVariant ) => {
		setAttributes( { variant: newVariant } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'ofw' ) }>
					<SelectControl
						label={ __( 'Variant', 'ofw' ) }
						value={ variant }
						options={ [
							{ value: 'light', label: __( 'Light', 'ofw' ) },
							{ value: 'dark', label: __( 'Dark', 'ofw' ) },
						] }
						onChange={ onSelectVariant }
					/>
				</PanelBody>
			</InspectorControls>

			<figure { ...blockProps }>
				<div className="testimonial__photo">
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ onSelectPhoto }
							allowedTypes={ [ 'image' ] }
							value={ photoId }
							render={ ( { open } ) =>
								photoUrl ? (
									<button
										type="button"
										className="testimonial__photo-button"
										onClick={ open }
										aria-label={ __(
											'Change photo',
											'ofw'
										) }
									>
										<img src={ photoUrl } alt="" />
									</button>
								) : (
									<Button
										variant="secondary"
										onClick={ open }
									>
										{ __( 'Choose photo', 'ofw' ) }
									</Button>
								)
							}
						/>
					</MediaUploadCheck>
				</div>

				<blockquote>
					<RichText
						tagName="p"
						value={ quote }
						onChange={ ( value ) =>
							setAttributes( { quote: value } )
						}
						placeholder={ __( 'Quote', 'ofw' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
				</blockquote>

				<figcaption>
					<RichText
						tagName="strong"
						value={ author }
						onChange={ ( value ) =>
							setAttributes( { author: value } )
						}
						placeholder={ __( 'Author', 'ofw' ) }
						allowedFormats={ [] }
					/>
					{ ', ' }
					<RichText
						tagName="span"
						value={ role }
						onChange={ ( value ) =>
							setAttributes( { role: value } )
						}
						placeholder={ __( 'Role', 'ofw' ) }
						allowedFormats={ [] }
					/>
				</figcaption>
			</figure>
		</>
	);
}
