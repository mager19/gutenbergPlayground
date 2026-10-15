<?php
/**
 * Title: Novedades Gutenberg: capítulo 6, Orientar al aspirante con IA
 * Slug: externado/novedades-orientar
 * Categories: externado-novedades
 * Inserter: no
 * Description: Buscador de programas en lenguaje natural con el cliente de IA de WordPress y búsqueda por palabras como respaldo.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Live demo: the program finder block over the demo programas classified with taxonomies.
$externado_demo  = '<!-- wp:externado/program-finder /-->' . "\n";
$externado_demo .= externado_paragraph_markup( esc_html__( 'Los programas son de ejemplo. Sin IA configurada en este sitio, responde la búsqueda por palabras; con un proveedor conectado, el modelo elige entre los mismos programas.', 'externado' ), 'externado-note' );

$externado_units = externado_feature_unit_markup( 'ai-finder', $externado_demo );

echo externado_chapter_open_markup( 'orientar', 'section-gris' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_feature_grid_markup( $externado_units, 'lead' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
