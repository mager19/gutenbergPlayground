<?php
/**
 * Title: Novedades Gutenberg: capítulo 8, Resultado
 * Slug: externado/novedades-resultado
 * Categories: externado-novedades
 * Inserter: no
 * Description: Resumen de votos: matriz de valor contra esfuerzo y ranking.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo externado_chapter_open_markup( 'resultado', 'section-verde' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<!-- wp:externado/vote-summary {"align":"wide"} /-->
<?php
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
