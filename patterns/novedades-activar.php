<?php
/**
 * Title: Novedades Gutenberg: capítulo 2, Activar el anuncio
 * Slug: externado/novedades-activar
 * Categories: externado-novedades
 * Inserter: no
 * Description: Visibilidad de bloques por dispositivo y bloque Ícono.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Visibility map: which banner shows on which device. Static explanation of the live blocks below it.
$externado_on  = '<span class="externado-vis externado-vis--on">' . esc_html__( 'Se ve', 'externado' ) . '</span>';
$externado_off = '<span class="externado-vis externado-vis--off">' . esc_html__( 'Oculto', 'externado' ) . '</span>';
$externado_map_rows = array(
	array( __( 'Aviso escritorio', 'externado' ), '', array( $externado_on, $externado_on, $externado_off ) ),
	array( __( 'Aviso celular', 'externado' ), '', array( $externado_off, $externado_off, $externado_on ) ),
	array( __( 'Banner día de apertura', 'externado' ), __( 'Se activa el 1 de septiembre', 'externado' ), array( $externado_off, $externado_off, $externado_off ) ),
);

$externado_visibility  = '<!-- wp:table {"className":"externado-visibility-map"} -->' . "\n";
$externado_visibility .= '<figure class="wp-block-table externado-visibility-map"><table class="has-fixed-layout"><thead><tr>';
$externado_visibility .= '<th>' . esc_html__( 'Bloque', 'externado' ) . '</th><th>' . esc_html__( 'Escritorio', 'externado' ) . '</th><th>' . esc_html__( 'Tableta', 'externado' ) . '</th><th>' . esc_html__( 'Celular', 'externado' ) . '</th>';
$externado_visibility .= '</tr></thead><tbody>';
foreach ( $externado_map_rows as $externado_row ) {
	$externado_visibility .= '<tr><td><strong>' . esc_html( $externado_row[0] ) . '</strong>';
	if ( '' !== $externado_row[1] ) {
		$externado_visibility .= '<br><span class="externado-vis-note">' . esc_html( $externado_row[1] ) . '</span>';
	}
	$externado_visibility .= '</td><td>' . implode( '</td><td>', $externado_row[2] ) . '</td></tr>';
}
$externado_visibility .= '</tbody></table></figure>' . "\n" . '<!-- /wp:table -->' . "\n";

$externado_visibility .= externado_paragraph_markup( esc_html__( 'En su pantalla ahora se ve:', 'externado' ), 'externado-feature__sublabel' );

$externado_visibility .= '<!-- wp:group {"metadata":{"name":"Banner escritorio","blockVisibility":{"viewport":{"mobile":false}}},"className":"externado-device externado-device--wide","layout":{"type":"default"}} -->' . "\n";
$externado_visibility .= '<div class="wp-block-group externado-device externado-device--wide">';
$externado_visibility .= externado_paragraph_markup( '<strong>' . esc_html__( 'Versión para tableta y escritorio.', 'externado' ) . '</strong> ' . esc_html__( 'Inscripciones abiertas para pregrado y posgrado 2027. Consulte fechas, requisitos y costos de cada programa.', 'externado' ) );
$externado_visibility .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

$externado_visibility .= '<!-- wp:group {"metadata":{"name":"Banner celular","blockVisibility":{"viewport":{"tablet":false,"desktop":false}}},"className":"externado-device externado-device--mobile","layout":{"type":"default"}} -->' . "\n";
$externado_visibility .= '<div class="wp-block-group externado-device externado-device--mobile">';
$externado_visibility .= externado_paragraph_markup( '<strong>' . esc_html__( 'Versión para celular.', 'externado' ) . '</strong> ' . esc_html__( 'Inscripciones 2027 abiertas.', 'externado' ) );
$externado_visibility .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

$externado_visibility .= '<!-- wp:group {"metadata":{"name":"Banner día de apertura (oculto)","blockVisibility":false},"className":"externado-device externado-device--hidden","layout":{"type":"default"}} -->' . "\n";
$externado_visibility .= '<div class="wp-block-group externado-device externado-device--hidden">';
$externado_visibility .= externado_paragraph_markup( esc_html__( 'Hoy abren las inscripciones 2027.', 'externado' ) );
$externado_visibility .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

$externado_visibility .= externado_paragraph_markup( esc_html__( 'Achique la ventana o ábrala en el celular para ver la otra versión. El banner del día de apertura no aparece en ninguna: solo se ve en el editor.', 'externado' ), 'externado-note' );

$externado_icons  = externado_paragraph_markup( esc_html__( 'Biblioteca de WordPress', 'externado' ), 'externado-feature__sublabel' );
$externado_icons .= '<!-- wp:group {"className":"externado-icon-row","layout":{"type":"grid","minimumColumnWidth":"8rem"}} -->' . "\n";
$externado_icons .= '<div class="wp-block-group externado-icon-row">';
foreach (
	array(
		'core/calendar'   => __( 'Fechas', 'externado' ),
		'core/people'     => __( 'Requisitos', 'externado' ),
		'core/map-marker' => __( 'Sede', 'externado' ),
		'core/envelope'   => __( 'Contacto', 'externado' ),
	) as $externado_icon => $externado_label
) {
	$externado_icons .= '<!-- wp:group {"className":"externado-icon-item","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->' . "\n";
	$externado_icons .= '<div class="wp-block-group externado-icon-item">';
	$externado_icons .= '<!-- wp:icon {"icon":"' . esc_attr( $externado_icon ) . '","textColor":"primary","style":{"dimensions":{"width":"32px"}}} /-->' . "\n";
	$externado_icons .= externado_paragraph_markup( esc_html( $externado_label ) );
	$externado_icons .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
}
$externado_icons .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

// Own collection registered by the theme (inc/icons.php): same block, brand icons.
$externado_icons .= externado_paragraph_markup( esc_html__( 'Colección propia «Externado», registrada desde el tema', 'externado' ), 'externado-feature__sublabel' );
$externado_icons .= '<!-- wp:group {"className":"externado-icon-collection","layout":{"type":"grid","minimumColumnWidth":"7rem"}} -->' . "\n";
$externado_icons .= '<div class="wp-block-group externado-icon-collection">';
$externado_icon_index = 0;
foreach ( externado_get_icons() as $externado_icon => $externado_label ) {
	// Alternate the two brand colors to show the icons take any palette color.
	$externado_icon_color = 0 === $externado_icon_index++ % 2 ? 'primary' : 'accent-dark';
	$externado_icons     .= '<!-- wp:group {"className":"externado-icon-tile","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->' . "\n";
	$externado_icons     .= '<div class="wp-block-group externado-icon-tile">';
	$externado_icons     .= '<!-- wp:icon {"icon":"externado/' . esc_attr( $externado_icon ) . '","textColor":"' . $externado_icon_color . '","style":{"dimensions":{"width":"36px"}}} /-->' . "\n";
	$externado_icons .= externado_paragraph_markup( esc_html( $externado_label ) );
	$externado_icons .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
}
$externado_icons .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

$externado_units  = externado_feature_unit_markup( 'visibility', $externado_visibility );
$externado_units .= externado_feature_unit_markup( 'icon', $externado_icons );

echo externado_chapter_open_markup( 'activar', 'section-gris' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_feature_grid_markup( $externado_units ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
