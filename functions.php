<?php
/**
 * Externado theme setup.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Public Playground link: a disposable copy of this demo built from playground/blueprint.json on GitHub.
const EXTERNADO_PLAYGROUND_URL = 'https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/mager19/gutenbergPlayground/main/playground/blueprint.json';

require_once get_theme_file_path( 'inc/features.php' );
require_once get_theme_file_path( 'inc/markup.php' );
require_once get_theme_file_path( 'inc/votes.php' );
require_once get_theme_file_path( 'inc/programa.php' );
require_once get_theme_file_path( 'inc/finder.php' );
require_once get_theme_file_path( 'inc/blocks.php' );
require_once get_theme_file_path( 'inc/icons.php' );
require_once get_theme_file_path( 'inc/abilities.php' );

/**
 * Enqueue front-end and editor styles.
 */
function externado_setup() {
	add_editor_style( 'assets/css/theme.css' );
}
add_action( 'after_setup_theme', 'externado_setup' );

/**
 * Enqueue front-end assets. The file modification time busts caches while iterating.
 */
function externado_enqueue_assets() {
	$css_file = get_theme_file_path( 'assets/css/theme.css' );
	$version  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'externado-theme', get_theme_file_uri( 'assets/css/theme.css' ), array(), $version );
}
add_action( 'wp_enqueue_scripts', 'externado_enqueue_assets' );

/**
 * Register the pattern categories used by theme patterns.
 */
function externado_register_pattern_category() {
	register_block_pattern_category( 'externado', array( 'label' => __( 'Externado', 'externado' ) ) );
	register_block_pattern_category( 'externado-novedades', array( 'label' => __( 'Novedades Gutenberg', 'externado' ) ) );
}
add_action( 'init', 'externado_register_pattern_category' );
