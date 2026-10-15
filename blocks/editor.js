/**
 * Editor registration for the Externado theme blocks (no build step).
 *
 * Block metadata comes from block.json (registered in PHP); this file only
 * provides the edit UI. Rendering is dynamic, previewed with ServerSideRender.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl } = wp.components;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;

	const features = window.externadoFeatures || [];
	const featureOptions = [ { label: __( 'Seleccione…', 'externado' ), value: '' } ].concat(
		features.map( ( feature ) => ( { label: feature.title, value: feature.id } ) )
	);

	/**
	 * Build an edit component.
	 *
	 * @param {string}  name       Block name.
	 * @param {boolean} withPicker Whether the block has a `feature` attribute.
	 * @return {Function} Edit component.
	 */
	function makeEdit( name, withPicker ) {
		return function Edit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps();

			return el(
				Fragment,
				null,
				withPicker &&
					el(
						InspectorControls,
						null,
						el(
							PanelBody,
							{ title: __( 'Funcionalidad', 'externado' ) },
							el( SelectControl, {
								label: __( 'Funcionalidad del catálogo', 'externado' ),
								value: attributes.feature,
								options: featureOptions,
								onChange: ( feature ) => setAttributes( { feature } ),
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
							} )
						)
					),
				el(
					'div',
					blockProps,
					el( ServerSideRender, { block: name, attributes } )
				)
			);
		};
	}

	registerBlockType( 'externado/feature-vote', {
		edit: makeEdit( 'externado/feature-vote', true ),
		save: () => null,
	} );

	registerBlockType( 'externado/try-it', {
		edit: makeEdit( 'externado/try-it', true ),
		save: () => null,
	} );

	registerBlockType( 'externado/vote-summary', {
		edit: makeEdit( 'externado/vote-summary', false ),
		save: () => null,
	} );

	registerBlockType( 'externado/program-finder', {
		edit: makeEdit( 'externado/program-finder', false ),
		save: () => null,
	} );
} )( window.wp );
