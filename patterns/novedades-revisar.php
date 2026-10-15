<?php
/**
 * Title: Novedades Gutenberg: capítulo 3, Revisar la convocatoria
 * Slug: externado/novedades-revisar
 * Categories: externado-novedades
 * Inserter: no
 * Description: Notas en bloques y revisiones visuales.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Static illustrations of the editor UI, built from the real demo content of the convocatoria page.
$externado_p = static function ( $html, $class_name ) {
	return externado_paragraph_markup( $html, $class_name );
};

// Notes: the dates paragraph with a resolved thread anchored to it.
$externado_notes  = '<!-- wp:group {"className":"externado-mock externado-mock--notes","layout":{"type":"default"}} -->' . "\n";
$externado_notes .= '<div class="wp-block-group externado-mock externado-mock--notes">';
$externado_notes .= $externado_p( esc_html__( 'Las inscripciones para el primer período académico de 2027 van del 1 de septiembre al', 'externado' ) . ' <mark>' . esc_html__( '30 de noviembre', 'externado' ) . '</mark>.', 'externado-mock__block' );
$externado_notes .= '<!-- wp:group {"className":"externado-mock__thread","layout":{"type":"default"}} -->' . "\n";
$externado_notes .= '<div class="wp-block-group externado-mock__thread">';
$externado_notes .= $externado_p( '<strong>' . esc_html__( 'Admisiones', 'externado' ) . '</strong> <span>' . esc_html__( 'hace 2 h', 'externado' ) . '</span>', 'externado-mock__author' );
$externado_notes .= $externado_p( esc_html__( 'Confirmar con la facultad si la fecha de cierre se mantiene el 30 de noviembre.', 'externado' ), 'externado-mock__comment' );
$externado_notes .= $externado_p( '<strong>' . esc_html__( 'Facultad de Derecho', 'externado' ) . '</strong> <span>' . esc_html__( 'hace 1 h', 'externado' ) . '</span>', 'externado-mock__author is-reply' );
$externado_notes .= $externado_p( esc_html__( 'Se mantiene. @Comunicaciones, ya pueden publicar.', 'externado' ), 'externado-mock__comment is-reply' );
$externado_notes .= $externado_p( esc_html__( 'Resuelta', 'externado' ), 'externado-mock__status' );
$externado_notes .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
$externado_notes .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

// Visual revisions: timeline slider plus highlighted changes.
$externado_revisions  = '<!-- wp:group {"className":"externado-mock externado-mock--revisions","layout":{"type":"default"}} -->' . "\n";
$externado_revisions .= '<div class="wp-block-group externado-mock externado-mock--revisions">';
$externado_revisions .= $externado_p( '<strong>' . esc_html__( 'Revisiones', 'externado' ) . '</strong> <span>' . esc_html__( 'Versión 3 de 4 · ayer, 4:12 p. m.', 'externado' ) . '</span>', 'externado-mock__timeline' );
$externado_revisions .= $externado_p( esc_html__( 'Las inscripciones van del 1 de septiembre al', 'externado' ) . ' <del>' . esc_html__( '15', 'externado' ) . '</del> <ins>' . esc_html__( '30', 'externado' ) . '</ins> ' . esc_html__( 'de noviembre.', 'externado' ), 'externado-diff externado-diff--mod' );
$externado_revisions .= $externado_p( esc_html__( 'Requisitos: prueba escrita y entrevista con la facultad.', 'externado' ), 'externado-diff externado-diff--add' );
$externado_revisions .= $externado_p( esc_html__( 'La inscripción solo se recibe en forma presencial.', 'externado' ), 'externado-diff externado-diff--del' );
$externado_revisions .= $externado_p( '<span class="externado-diff-key externado-diff-key--add">' . esc_html__( 'Añadido', 'externado' ) . '</span> <span class="externado-diff-key externado-diff-key--del">' . esc_html__( 'Eliminado', 'externado' ) . '</span> <span class="externado-diff-key externado-diff-key--mod">' . esc_html__( 'Modificado', 'externado' ) . '</span> <span class="externado-mock__restore">' . esc_html__( 'Restaurar', 'externado' ) . '</span>', 'externado-mock__legend' );
$externado_revisions .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

$externado_units  = externado_feature_unit_markup( 'notes', $externado_notes );
$externado_units .= externado_feature_unit_markup( 'revisions', $externado_revisions );

echo externado_chapter_open_markup( 'revisar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_feature_grid_markup( $externado_units ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
