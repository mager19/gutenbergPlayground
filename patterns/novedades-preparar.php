<?php
/**
 * Title: Novedades Gutenberg: capítulo 1, Preparar la campaña
 * Slug: externado/novedades-preparar
 * Categories: externado-novedades
 * Inserter: no
 * Description: Zoom Out, estilos de sección y Style Book.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$externado_sections = '';
foreach (
	array(
		'section-verde'   => __( 'Sección verde: llamados a la acción.', 'externado' ),
		'section-pizarra' => __( 'Sección pizarra: portadas y anuncios.', 'externado' ),
		'section-gris'    => __( 'Sección gris: contenido de apoyo.', 'externado' ),
	) as $externado_style => $externado_text
) {
	$externado_sections .= '<!-- wp:group {"className":"is-style-' . $externado_style . ' externado-mini-section","layout":{"type":"constrained"}} -->' . "\n";
	$externado_sections .= '<div class="wp-block-group is-style-' . $externado_style . ' externado-mini-section">';
	$externado_sections .= externado_paragraph_markup( '<strong>' . esc_html__( 'Inscripciones 2027', 'externado' ) . '</strong> · ' . esc_html( $externado_text ) . ' <a href="#capitulo-preparar">' . esc_html__( 'Enlace de ejemplo', 'externado' ) . '</a>' );
	$externado_sections .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
}

$externado_units  = externado_feature_unit_markup( 'zoom-out' );
$externado_units .= externado_feature_unit_markup( 'section-styles', $externado_sections );
$externado_units .= externado_feature_unit_markup( 'style-book' );

echo externado_chapter_open_markup( 'preparar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_feature_grid_markup( $externado_units ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
