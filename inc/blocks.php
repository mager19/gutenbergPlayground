<?php
/**
 * Custom blocks (no build step): assets and registration.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the shared editor script and the shared view script module.
 *
 * Runs before block registration so block.json can reference the handles.
 */
function externado_register_block_assets() {
	// File modification times bust browser caches while iterating without a build step.
	$editor_file = get_theme_file_path( 'blocks/editor.js' );
	$view_file   = get_theme_file_path( 'blocks/votes-view.js' );

	wp_register_script(
		'externado-blocks-editor',
		get_theme_file_uri( 'blocks/editor.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		(string) filemtime( $editor_file ),
		true
	);

	$features = array();
	foreach ( externado_get_features() as $id => $feature ) {
		$features[] = array(
			'id'    => $id,
			'title' => $feature['title'],
		);
	}

	wp_add_inline_script(
		'externado-blocks-editor',
		'window.externadoFeatures = ' . wp_json_encode( $features ) . ';',
		'before'
	);

	wp_register_script_module(
		'externado-votes-view',
		get_theme_file_uri( 'blocks/votes-view.js' ),
		array( '@wordpress/interactivity' ),
		(string) filemtime( $view_file )
	);

	$finder_file = get_theme_file_path( 'blocks/finder-view.js' );

	wp_register_script_module(
		'externado-finder-view',
		get_theme_file_uri( 'blocks/finder-view.js' ),
		array( '@wordpress/interactivity' ),
		(string) filemtime( $finder_file )
	);
}
add_action( 'init', 'externado_register_block_assets', 5 );

/**
 * Register the theme blocks from their block.json files.
 */
function externado_register_blocks() {
	foreach ( array( 'feature-vote', 'vote-summary', 'try-it', 'program-finder' ) as $block ) {
		register_block_type( get_theme_file_path( 'blocks/' . $block ) );
	}
}
add_action( 'init', 'externado_register_blocks' );

/**
 * URL to come back to after logging in.
 *
 * @param string $fragment Optional fragment (without #).
 * @return string
 */
function externado_current_url( $fragment = '' ) {
	$url = is_singular() ? get_permalink( get_queried_object_id() ) : home_url( '/' );

	if ( ! $url ) {
		$url = home_url( '/' );
	}

	return $fragment ? $url . '#' . $fragment : $url;
}
