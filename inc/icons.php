<?php
/**
 * "Externado" icon collection for the Icon block (WordPress 7.1 Icons API).
 *
 * Icons come from Google Material Symbols (Apache License 2.0), outlined style.
 * They are single filled paths, which is what the core icon sanitizer accepts
 * (only svg, path and polygon survive; stroke-based icons would render empty).
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icons in the collection: file name in assets/icons => label.
 *
 * @return array<string, string>
 */
function externado_get_icons() {
	return array(
		'birrete'       => __( 'Birrete', 'externado' ),
		'libro'         => __( 'Libro', 'externado' ),
		'balanza'       => __( 'Balanza', 'externado' ),
		'mazo'          => __( 'Mazo', 'externado' ),
		'diploma'       => __( 'Diploma', 'externado' ),
		'campus'        => __( 'Campus', 'externado' ),
		'calendario'    => __( 'Calendario académico', 'externado' ),
		'investigacion' => __( 'Investigación', 'externado' ),
	);
}

/**
 * Register the collection and its icons.
 */
function externado_register_icons() {
	if ( ! function_exists( 'wp_register_icon_collection' ) ) {
		return; // WordPress < 7.1.
	}

	wp_register_icon_collection(
		'externado',
		array(
			'label'       => __( 'Externado', 'externado' ),
			'description' => __( 'Íconos académicos de la Universidad Externado de Colombia.', 'externado' ),
		)
	);

	foreach ( externado_get_icons() as $slug => $label ) {
		wp_register_icon(
			'externado/' . $slug,
			array(
				'label'     => $label,
				'file_path' => get_theme_file_path( 'assets/icons/' . $slug . '.svg' ),
			)
		);
	}
}
add_action( 'init', 'externado_register_icons' );

/**
 * Show the collection in brand green inside the editor's "Icon library" modal.
 *
 * The modal is editor UI, outside the content iframe, so theme.json does not reach it.
 * Items carry no collection data, so the SVG files carry the `externado-icon` class
 * (the core sanitizer keeps `class` on <svg>). The selected item keeps the white
 * foreground of the primary button.
 */
function externado_icon_library_styles() {
	$green = '#00522c';
	$css   = '.wp-block-icon__inserter-grid-icons-list-item:not(.is-primary) svg.externado-icon { fill: ' . $green . '; }'
		. '.wp-block-icon__inserter-grid-icons-list-item.is-primary svg.externado-icon { fill: currentColor; }';

	wp_register_style( 'externado-editor-ui', false, array(), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_style( 'externado-editor-ui' );
	wp_add_inline_style( 'externado-editor-ui', $css );
}
add_action( 'enqueue_block_editor_assets', 'externado_icon_library_styles' );
