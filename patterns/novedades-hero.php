<?php
/**
 * Title: Novedades Gutenberg: portada
 * Slug: externado/novedades-hero
 * Categories: externado-novedades
 * Inserter: no
 * Description: Portada de la página Novedades Gutenberg: propósito, cómo funciona, niveles de esfuerzo y la ruta de capítulos.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$externado_features = externado_get_features();
$externado_counts   = array_count_values( wp_list_pluck( $externado_features, 'chapter' ) );

$externado_route = '';
foreach ( externado_get_chapters() as $externado_chapter_id => $externado_chapter ) {
	$externado_count = isset( $externado_counts[ $externado_chapter_id ] ) ? (int) $externado_counts[ $externado_chapter_id ] : 0;
	$externado_meta  = $externado_count
		? sprintf(
			/* translators: %d: number of features in the chapter. */
			_n( '%d novedad', '%d novedades', $externado_count, 'externado' ),
			$externado_count
		)
		: __( 'Su hoja de ruta', 'externado' );

	$externado_route .= '<!-- wp:list-item -->' . "\n"
		. '<li><a href="#capitulo-' . esc_attr( $externado_chapter_id ) . '">'
		. '<span class="externado-route__title">' . esc_html( $externado_chapter['title'] ) . '</span>'
		. '<span class="externado-route__meta">' . esc_html( $externado_meta ) . '</span>'
		. '</a></li>' . "\n"
		. '<!-- /wp:list-item -->' . "\n";
}

$externado_steps = array(
	array( __( 'Lea y vea la demo.', 'externado' ), __( 'Cada novedad explica qué es y qué resuelve para el Externado.', 'externado' ) ),
	array( __( 'Pruébela.', 'externado' ), __( '«Pruébalo tú» abre el editor sobre contenido de ejemplo.', 'externado' ) ),
	array( __( 'Vote.', 'externado' ), __( '«Me interesa» o «No por ahora». Se guarda al instante y puede cambiarlo.', 'externado' ) ),
);

$externado_steps_markup = '';
foreach ( $externado_steps as $externado_step ) {
	$externado_steps_markup .= '<!-- wp:list-item -->' . "\n"
		. '<li><strong>' . esc_html( $externado_step[0] ) . '</strong> ' . esc_html( $externado_step[1] ) . '</li>' . "\n"
		. '<!-- /wp:list-item -->' . "\n";
}

$externado_efforts = '';
foreach ( externado_get_effort_levels() as $externado_effort_id => $externado_effort ) {
	$externado_efforts .= '<!-- wp:list-item -->' . "\n"
		. '<li><span class="externado-badge externado-badge--' . esc_attr( strtolower( $externado_effort_id ) ) . '">' . esc_html( $externado_effort_id ) . '</span> ' . esc_html( $externado_effort['label'] ) . '</li>' . "\n"
		. '<!-- /wp:list-item -->' . "\n";
}
?>
<!-- wp:group {"tagName":"header","metadata":{"name":"Portada"},"align":"full","className":"is-style-section-pizarra externado-hero","layout":{"type":"constrained"}} -->
<header class="wp-block-group alignfull is-style-section-pizarra externado-hero"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:breadcrumbs /--></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"externado-hero__intro"} -->
<div class="wp-block-columns alignwide externado-hero__intro"><!-- wp:column {"width":"58%"} -->
<div class="wp-block-column" style="flex-basis:58%"><!-- wp:heading {"level":1,"className":"externado-hero__title"} -->
<h1 class="wp-block-heading externado-hero__title"><?php esc_html_e( 'Novedades de Gutenberg', 'externado' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"externado-hero__lead"} -->
<p class="externado-hero__lead"><?php echo esc_html( sprintf( /* translators: %d: number of features. */ __( '%d funcionalidades nuevas del editor, contadas como si abriéramos juntos las inscripciones 2027. Vote las que le sirven: al final, sus votos se convierten en la hoja de ruta del sitio.', 'externado' ), count( $externado_features ) ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"42%","className":"externado-hero__howto"} -->
<div class="wp-block-column externado-hero__howto" style="flex-basis:42%"><!-- wp:heading {"level":2,"className":"externado-hero__subtitle"} -->
<h2 class="wp-block-heading externado-hero__subtitle"><?php esc_html_e( 'Cómo funciona', 'externado' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"externado-steps"} -->
<ol class="wp-block-list externado-steps"><?php echo $externado_steps_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ol>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"externado-hero__legend-label"} -->
<p class="externado-hero__legend-label"><?php esc_html_e( 'Cada novedad indica cuánto cuesta adoptarla:', 'externado' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"externado-effort-legend"} -->
<ul class="wp-block-list externado-effort-legend"><?php echo $externado_efforts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"tagName":"nav","align":"wide","className":"externado-route","layout":{"type":"default"},"ariaLabel":"<?php esc_attr_e( 'Capítulos de la campaña', 'externado' ); ?>"} -->
<nav class="wp-block-group alignwide externado-route" aria-label="<?php esc_attr_e( 'Capítulos de la campaña', 'externado' ); ?>"><!-- wp:heading {"level":2,"className":"externado-hero__subtitle"} -->
<h2 class="wp-block-heading externado-hero__subtitle"><?php esc_html_e( 'La ruta de la campaña', 'externado' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"externado-route__list"} -->
<ol class="wp-block-list externado-route__list"><?php echo $externado_route; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ol>
<!-- /wp:list --></nav>
<!-- /wp:group --></header>
<!-- /wp:group -->
