<?php
/**
 * Title: Novedades Gutenberg: capítulo 4, Publicar la ficha del programa
 * Slug: externado/novedades-publicar
 * Categories: externado-novedades
 * Inserter: no
 * Description: Block Bindings, Pattern Overrides, migas de pan, pestañas y acordeón.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Block Bindings: a Query Loop provides the programa as context, so the bound paragraphs show its real meta.
$externado_bindings  = '<!-- wp:query {"queryId":40,"query":{"perPage":1,"pages":0,"offset":0,"postType":"programa","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"externado-bindings-demo"} -->' . "\n";
$externado_bindings .= '<div class="wp-block-query externado-bindings-demo"><!-- wp:post-template -->' . "\n";
$externado_bindings .= '<!-- wp:post-title {"level":4,"isLink":true} /-->' . "\n";
$externado_bindings .= externado_programa_facts_markup( true );
$externado_bindings .= '<!-- /wp:post-template -->' . "\n\n";
$externado_bindings .= '<!-- wp:query-no-results -->' . "\n";
$externado_bindings .= externado_paragraph_markup( esc_html__( 'Aún no hay programas publicados. Ejecute el script de contenido de demostración.', 'externado' ) );
$externado_bindings .= '<!-- /wp:query-no-results --></div>' . "\n" . '<!-- /wp:query -->' . "\n";
$externado_bindings .= externado_paragraph_markup( esc_html__( 'Ningún valor está escrito en la página: cada uno se lee del campo indicado debajo. Cambie el campo en el programa y se actualiza en todas partes.', 'externado' ), 'externado-note' );

// Pattern Overrides: two instances of the same synced pattern with their own data.
$externado_card_a = externado_synced_pattern_markup(
	'tarjeta-docente',
	array(
		'nombre' => array( 'content' => 'Dra. Mariana Restrepo' ),
		'cargo'  => array( 'content' => __( 'Decana de la Facultad de Derecho', 'externado' ) ),
		'area'   => array( 'content' => __( 'Derecho constitucional', 'externado' ) ),
	)
);
$externado_card_b = externado_synced_pattern_markup(
	'tarjeta-docente',
	array(
		'nombre' => array( 'content' => 'Dr. Andrés Villamizar' ),
		'cargo'  => array( 'content' => __( 'Director del programa de Economía', 'externado' ) ),
		'area'   => array( 'content' => __( 'Economía pública', 'externado' ) ),
	)
);

if ( $externado_card_a && $externado_card_b ) {
	$externado_overrides  = '<!-- wp:group {"className":"externado-cards externado-cards--overrides","layout":{"type":"grid","minimumColumnWidth":"12rem"}} -->' . "\n";
	$externado_overrides .= '<div class="wp-block-group externado-cards externado-cards--overrides">' . $externado_card_a . "\n" . $externado_card_b . '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
	$externado_overrides .= externado_paragraph_markup( '<span class="externado-override-key externado-override-key--edit">' . esc_html__( 'Editable en cada uso: nombre, cargo y área', 'externado' ) . '</span> <span class="externado-override-key externado-override-key--fixed">' . esc_html__( 'Fijo para todos: diseño, colores y orden', 'externado' ) . '</span>', 'externado-override-legend' );
	$externado_overrides .= externado_paragraph_markup( esc_html__( 'Es el mismo patrón sincronizado usado dos veces. Si se cambia su diseño, cambian las dos tarjetas; sus datos se quedan.', 'externado' ), 'externado-note' );
} else {
	$externado_overrides = externado_paragraph_markup( esc_html__( 'El patrón «Tarjeta docente» aún no existe. Ejecute el script de contenido de demostración.', 'externado' ), 'externado-note' );
}

// Live trail for this page, then the trail the same block produces on the programa page.
$externado_breadcrumbs  = externado_paragraph_markup( esc_html__( 'En esta página', 'externado' ), 'externado-feature__sublabel' );
$externado_breadcrumbs .= '<!-- wp:breadcrumbs {"separator":"›","className":"externado-breadcrumbs-demo"} /-->' . "\n";
$externado_breadcrumbs .= externado_paragraph_markup( esc_html__( 'En la ficha del programa', 'externado' ), 'externado-feature__sublabel' );
$externado_breadcrumbs .= externado_paragraph_markup(
	'<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Inicio', 'externado' ) . '</a> <span aria-hidden="true">›</span> <a href="' . esc_url( get_post_type_archive_link( 'programa' ) ? get_post_type_archive_link( 'programa' ) : home_url( '/programas/' ) ) . '">' . esc_html__( 'Programas', 'externado' ) . '</a> <span aria-hidden="true">›</span> <span aria-current="page">' . esc_html__( 'Derecho', 'externado' ) . '</span>',
	'externado-breadcrumbs-sample'
);
$externado_breadcrumbs .= externado_paragraph_markup( esc_html__( 'Es el mismo bloque: la ruta se calcula sola según dónde está el contenido, sin menús que mantener a mano.', 'externado' ), 'externado-note' );

$externado_tabs = externado_tabs_markup(
	array(
		__( 'Perfil', 'externado' )           => externado_paragraph_markup( esc_html__( 'Profesionales con formación humanista y crítica, capaces de asesorar, litigar y diseñar políticas públicas.', 'externado' ) ),
		__( 'Plan de estudios', 'externado' ) => externado_paragraph_markup( esc_html__( 'Diez semestres: fundamentación, profundización y prácticas en el Consultorio Jurídico.', 'externado' ) ),
		__( 'Costos', 'externado' )           => externado_paragraph_markup( esc_html__( 'Valor de matrícula por semestre, opciones de financiación y becas por mérito.', 'externado' ) ),
	)
);

$externado_accordion = externado_accordion_markup(
	array(
		__( '¿Cuándo cierran las inscripciones?', 'externado' ) => externado_paragraph_markup( esc_html__( 'El 30 de noviembre para el primer período académico de 2027.', 'externado' ) ),
		__( '¿Hay examen de admisión?', 'externado' )           => externado_paragraph_markup( esc_html__( 'Sí: prueba escrita y entrevista con la facultad.', 'externado' ) ),
		__( '¿Puedo pagar en cuotas?', 'externado' )            => externado_paragraph_markup( esc_html__( 'Sí, con las entidades de financiación aliadas.', 'externado' ) ),
	),
	true
);

$externado_units  = externado_feature_unit_markup( 'block-bindings', $externado_bindings );
$externado_units .= externado_feature_unit_markup( 'pattern-overrides', $externado_overrides );
$externado_units .= externado_feature_unit_markup( 'breadcrumbs', $externado_breadcrumbs );
$externado_units .= externado_feature_unit_markup( 'tabs', $externado_tabs );
$externado_units .= externado_feature_unit_markup( 'accordion', $externado_accordion );

echo externado_chapter_open_markup( 'publicar', 'section-gris' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_feature_grid_markup( $externado_units ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
