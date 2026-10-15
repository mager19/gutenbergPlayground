<?php
/**
 * Title: Novedades Gutenberg: capítulo 7, Redactar con más herramientas
 * Slug: externado/novedades-redactar
 * Categories: externado-novedades
 * Inserter: no
 * Description: Nuevas opciones de bloque, Interactivity API, tiempo de lectura, fórmulas matemáticas y Data Views.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * New block options. The block markup was produced by the editor's own serializer
 * (wp.blocks.serialize) so it validates exactly: fitText, textColumns, textIndent
 * and background.gradient.
 */
$externado_option = static function ( $label, $markup, $modifier ) {
	$out  = '<!-- wp:group {"className":"externado-option externado-option--' . $modifier . '","layout":{"type":"default"}} -->' . "\n";
	$out .= '<div class="wp-block-group externado-option externado-option--' . $modifier . '">';
	$out .= externado_paragraph_markup( esc_html( $label ), 'externado-feature__sublabel' );
	$out .= $markup . "\n";
	$out .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
	return $out;
};

$externado_options  = '<!-- wp:group {"className":"externado-options","layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->' . "\n";
$externado_options .= '<div class="wp-block-group externado-options">';
$externado_options .= $externado_option(
	__( 'Título que se ajusta al ancho', 'externado' ),
	'<!-- wp:heading {"level":3,"fitText":true} -->' . "\n" . '<h3 class="wp-block-heading has-fit-text">' . esc_html__( 'Inscripciones 2027', 'externado' ) . '</h3>' . "\n" . '<!-- /wp:heading -->',
	'fit'
);
$externado_options .= $externado_option(
	__( 'Degradado de fondo', 'externado' ),
	'<!-- wp:group {"style":{"background":{"gradient":"linear-gradient(135deg, #00522C 0%, #4D7B19 100%)"}},"layout":{"type":"constrained"}} -->' . "\n" . '<div class="wp-block-group"><!-- wp:paragraph -->' . "\n" . '<p>' . esc_html__( 'Abren las inscripciones. Conozca fechas y requisitos.', 'externado' ) . '</p>' . "\n" . '<!-- /wp:paragraph --></div>' . "\n" . '<!-- /wp:group -->',
	'gradient'
);
$externado_options .= $externado_option(
	__( 'Párrafo en dos columnas', 'externado' ),
	'<!-- wp:paragraph {"style":{"typography":{"textColumns":"2"}}} -->' . "\n" . '<p style="column-count:2">' . esc_html__( 'El Externado abre su convocatoria 2027 para pregrado y posgrado. Las inscripciones se reciben en línea hasta el 30 de noviembre y el proceso incluye prueba escrita y entrevista con la facultad.', 'externado' ) . '</p>' . "\n" . '<!-- /wp:paragraph -->',
	'columns'
);
$externado_options .= $externado_option(
	__( 'Sangría de primera línea', 'externado' ),
	'<!-- wp:paragraph {"style":{"typography":{"textIndent":"1.5em"}}} -->' . "\n" . '<p style="text-indent:1.5em">' . esc_html__( 'La universidad conserva su tradición de formación crítica e independiente, y abre cada año nuevas rutas de estudio para quienes llegan a sus aulas.', 'externado' ) . '</p>' . "\n" . '<!-- /wp:paragraph -->',
	'indent'
);
$externado_options .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

// Time to read: one faculty news item (not WordPress' sample post), shown as a time range and as a word count.
$externado_time_query = array(
	'perPage'  => 1,
	'pages'    => 0,
	'offset'   => 0,
	'postType' => 'post',
	'order'    => 'desc',
	'orderBy'  => 'date',
	'author'   => '',
	'search'   => '',
	'exclude'  => array(),
	'sticky'   => '',
	'inherit'  => false,
);
$externado_faculty     = get_term_by( 'slug', 'facultad-derecho', 'category' );
if ( $externado_faculty ) {
	$externado_time_query['taxQuery'] = array( 'include' => array( 'category' => array( (int) $externado_faculty->term_id ) ) );
}
$externado_time  = '<!-- wp:query ' . wp_json_encode(
	array(
		'queryId'   => 60,
		'query'     => $externado_time_query,
		'className' => 'externado-time-demo',
	)
) . ' -->' . "\n";
$externado_time .= '<div class="wp-block-query externado-time-demo"><!-- wp:post-template -->' . "\n";
$externado_time .= '<!-- wp:post-title {"level":4,"isLink":true,"className":"externado-time-demo__title"} /-->' . "\n";
$externado_time .= '<!-- wp:group {"className":"externado-time-demo__row","layout":{"type":"flex","flexWrap":"wrap"}} -->' . "\n";
$externado_time .= '<div class="wp-block-group externado-time-demo__row"><!-- wp:post-time-to-read /-->' . "\n\n";
$externado_time .= '<!-- wp:post-time-to-read {"displayMode":"words"} /--></div>' . "\n" . '<!-- /wp:group -->' . "\n";
$externado_time .= '<!-- /wp:post-template --></div>' . "\n" . '<!-- /wp:query -->' . "\n";
$externado_time .= externado_paragraph_markup( esc_html__( 'Es el mismo bloque con dos modos: tiempo de lectura y número de palabras. Se calcula solo a partir del contenido de la noticia.', 'externado' ), 'externado-note' );

// Math: compound interest, a formula an Economics course would publish.
$externado_mathml  = '<mrow><mi>A</mi><mo>=</mo><mi>P</mi><msup><mrow><mo>(</mo><mn>1</mn><mo>+</mo><mfrac><mi>r</mi><mi>n</mi></mfrac><mo>)</mo></mrow><mrow><mi>n</mi><mi>t</mi></mrow></msup></mrow>';
$externado_math    = '<!-- wp:math ' . serialize_block_attributes( array( 'latex' => 'A = P\left(1 + \frac{r}{n}\right)^{nt}' ) ) . ' -->' . "\n";
$externado_math   .= '<div class="wp-block-math"><math display="block">' . $externado_mathml . '</math></div>' . "\n" . '<!-- /wp:math -->' . "\n";
$externado_math   .= externado_paragraph_markup( esc_html__( 'Interés compuesto, escrito en LaTeX:', 'externado' ) . ' <code>A = P\left(1 + \frac{r}{n}\right)^{nt}</code>', 'externado-note' );

// Data Views: static illustration of the pattern library in table view.
$externado_p     = static function ( $html, $class_name ) {
	return externado_paragraph_markup( $html, $class_name );
};
$externado_views  = '<!-- wp:group {"className":"externado-mock externado-mock--dataviews","layout":{"type":"default"}} -->' . "\n";
$externado_views .= '<div class="wp-block-group externado-mock externado-mock--dataviews">';
$externado_views .= $externado_p( '<span class="externado-dv__search">' . esc_html__( 'tarjeta', 'externado' ) . '</span> <span class="externado-dv__filter">' . esc_html__( 'Sincronizado', 'externado' ) . '</span> <span class="externado-dv__views"><span>' . esc_html__( 'Cuadrícula', 'externado' ) . '</span><span class="is-active">' . esc_html__( 'Tabla', 'externado' ) . '</span></span>', 'externado-dv__toolbar' );
foreach (
	array(
		array( __( 'Tarjeta docente', 'externado' ), __( 'Sincronizado', 'externado' ), __( 'Externado', 'externado' ) ),
		array( __( 'Tarjeta de programa', 'externado' ), __( 'Sincronizado', 'externado' ), __( 'Externado', 'externado' ) ),
		array( __( 'Tarjeta de evento', 'externado' ), __( 'Sincronizado', 'externado' ), __( 'Agenda', 'externado' ) ),
	) as $externado_row
) {
	$externado_views .= $externado_p( '<span class="externado-dv__name">' . esc_html( $externado_row[0] ) . '</span><span>' . esc_html( $externado_row[1] ) . '</span><span>' . esc_html( $externado_row[2] ) . '</span>', 'externado-dv__row' );
}
$externado_views .= $externado_p( esc_html__( '3 resultados · filtrado por «Sincronizado»', 'externado' ), 'externado-dv__footer' );
$externado_views .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

// Interactivity API: a live counter bound to the shared `externado/votes` store.
// It is an HTML block because directives on a paragraph would not survive the editor.
$externado_total = 0;
foreach ( externado_get_vote_counts() as $externado_count ) {
	$externado_total += $externado_count['yes'] + $externado_count['no'];
}
$externado_interactivity  = '<!-- wp:html -->' . "\n";
$externado_interactivity .= '<div class="externado-live-counter" data-wp-interactive="externado/votes" data-wp-class--is-live="state.isLive">';
$externado_interactivity .= '<span class="externado-live-counter__number" data-wp-text="state.totalVotes">' . (int) $externado_total . '</span>';
$externado_interactivity .= '<span class="externado-live-counter__label">' . esc_html__( 'votos registrados en esta página', 'externado' ) . '</span>';
$externado_interactivity .= '</div>' . "\n" . '<!-- /wp:html -->' . "\n";
$externado_interactivity .= externado_paragraph_markup( esc_html__( 'Así funciona el resumen que sigue. Cada voto se guarda sin recargar la página y este número sube en todos los navegadores abiertos. Los botones de voto, este contador y la matriz de resultados leen el mismo estado: cuando uno cambia, cambian todos.', 'externado' ), 'externado-note' );
$externado_interactivity .= externado_paragraph_markup( '<a href="#capitulo-resultado">' . esc_html__( 'Ver la matriz de resultados', 'externado' ) . '</a>', 'externado-note' );

// Order: block options open the chapter, Interactivity API closes it as the bridge to the result.
$externado_units  = externado_feature_unit_markup( 'block-options', $externado_options );
$externado_units .= externado_feature_unit_markup( 'time-to-read', $externado_time );
$externado_units .= externado_feature_unit_markup( 'math', $externado_math );
$externado_units .= externado_feature_unit_markup( 'dataviews', $externado_views );
$externado_units .= externado_feature_unit_markup( 'interactivity', $externado_interactivity );

// No section style: chapters alternate white/gray and 'orientar' (gray) now precedes this one.
echo externado_chapter_open_markup( 'redactar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_feature_grid_markup( $externado_units, 'bookends' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
