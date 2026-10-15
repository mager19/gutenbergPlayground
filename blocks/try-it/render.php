<?php
/**
 * Render callback for externado/try-it.
 *
 * The edit link is resolved by slug at render time and only shown to users
 * who can edit the target content.
 *
 * @package Externado
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$externado_feature_id = isset( $attributes['feature'] ) ? (string) $attributes['feature'] : '';
$externado_url        = externado_get_feature_try_url( $externado_feature_id );

if ( '' === $externado_url ) {
	return;
}

// Opens in a new tab so the presentation stays where the viewer left it.
printf(
	'<div %1$s><a class="externado-try-it__link wp-element-button" href="%2$s" target="_blank" rel="noopener">%3$s<span class="screen-reader-text"> %4$s</span></a></div>',
	get_block_wrapper_attributes(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_url( $externado_url ),
	esc_html__( 'Pruébalo tú', 'externado' ),
	esc_html__( '(se abre en una pestaña nueva)', 'externado' )
);
