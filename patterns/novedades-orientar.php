<?php
/**
 * Title: Novedades Gutenberg: capítulo 6, Inteligencia artificial y práctica
 * Slug: externado/novedades-orientar
 * Categories: externado-novedades
 * Inserter: no
 * Description: Conector para Claude (MCP) que redacta borradores de noticias y WordPress Playground para practicar sin riesgo.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// MCP connector: illustration of a Claude Desktop conversation focused on drafting news with the site's abilities.
$externado_p     = static function ( $html, $class_name ) {
	return externado_paragraph_markup( $html, $class_name );
};
$externado_chat  = '<!-- wp:group {"className":"externado-mock externado-mock--chat","layout":{"type":"default"}} -->' . "\n";
$externado_chat .= '<div class="wp-block-group externado-mock externado-mock--chat">';
$externado_chat .= $externado_p( esc_html__( 'Redacta una noticia sobre la apertura de inscripciones 2027 en la Facultad de Derecho: tono institucional, tres párrafos y un resumen corto. Déjala como borrador.', 'externado' ), 'externado-chat__user' );
$externado_chat .= $externado_p( '<span class="externado-chat__tool">' . esc_html__( 'Usó: Crear borrador de noticia', 'externado' ) . '</span>', 'externado-chat__meta' );
$externado_chat .= $externado_p( esc_html__( 'Listo. Dejé el borrador en la categoría Facultad de Derecho, con título, resumen y tres párrafos:', 'externado' ), 'externado-chat__claude' );
$externado_chat .= $externado_p(
	'<span class="externado-chat__draft-label">' . esc_html__( 'Borrador', 'externado' ) . '</span>'
	. '<span class="externado-chat__draft-title">' . esc_html__( 'Abren las inscripciones 2027 en la Facultad de Derecho', 'externado' ) . '</span>'
	. esc_html__( 'Los aspirantes ya pueden postularse al pregrado en Derecho para el periodo 2027. El proceso es en línea y la Facultad acompaña cada paso.', 'externado' ),
	'externado-chat__draft'
);
$externado_chat .= $externado_p( esc_html__( 'No lo publiqué: queda en Entradas → Borradores para que el equipo lo revise y ajuste.', 'externado' ), 'externado-chat__claude' );
$externado_chat .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

// Playground: what the link gives, with the same call to action as the hero.
$externado_pg  = '<!-- wp:list {"className":"externado-pg-list"} -->' . "\n" . '<ul class="wp-block-list externado-pg-list">';
foreach (
	array(
		__( 'WordPress 7.1 con este tema y el contenido de demostración', 'externado' ),
		__( 'Sesión iniciada como administrador, en esta misma página', 'externado' ),
		__( 'Una copia por persona: se borra al cerrar la pestaña', 'externado' ),
	) as $externado_item
) {
	$externado_pg .= '<!-- wp:list-item -->' . "\n" . '<li>' . esc_html( $externado_item ) . '</li>' . "\n" . '<!-- /wp:list-item -->' . "\n";
}
$externado_pg .= '</ul>' . "\n" . '<!-- /wp:list -->' . "\n";
$externado_pg .= externado_paragraph_markup( esc_html__( 'No incluye la votación compartida entre navegadores ni la IA: esas se muestran en el sitio de la presentación.', 'externado' ), 'externado-note' );

$externado_units  = externado_feature_unit_markup( 'mcp-claude', $externado_chat );
$externado_units .= externado_feature_unit_markup( 'playground', $externado_pg );

echo externado_chapter_open_markup( 'orientar', 'section-gris' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_feature_grid_markup( $externado_units, 'pair' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
