<?php
/**
 * Title: Novedades Gutenberg: unidad de funcionalidad
 * Slug: externado/novedades-feature-unit
 * Categories: externado-novedades
 * Description: Tarjeta de una funcionalidad: qué es, valor para el Externado, instrucciones, voto y botón «Pruébalo tú». Cambie la funcionalidad de los bloques de voto y «Pruébalo tú» en la barra lateral.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo externado_feature_unit_markup( 'tabs' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
