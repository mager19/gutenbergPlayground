<?php
/**
 * Title: Ficha de programa
 * Slug: externado/programa-ficha
 * Categories: externado
 * Block Types: core/post-content
 * Post Types: programa
 * Description: Estructura inicial de un programa: datos clave, pestañas (Perfil, Plan de estudios, Costos) y preguntas frecuentes, todo editable con bloques.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo externado_programa_content_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	array(
		'facts'  => array(
			__( 'Duración', 'externado' )        => __( 'Ej.: 10 semestres', 'externado' ),
			__( 'Modalidad', 'externado' )       => __( 'Ej.: Presencial', 'externado' ),
			__( 'Título otorgado', 'externado' ) => __( 'Ej.: Abogado(a)', 'externado' ),
			__( 'Registro SNIES', 'externado' )  => __( 'Número de registro', 'externado' ),
		),
		'perfil' => array( __( 'Describa el perfil del egresado: qué sabe hacer y dónde puede trabajar.', 'externado' ) ),
		'plan'   => array(
			__( 'Ciclo de fundamentación: semestres 1 a 4.', 'externado' ),
			__( 'Ciclo de profundización: semestres 5 a 8.', 'externado' ),
			__( 'Prácticas y trabajo de grado.', 'externado' ),
		),
		'costos' => array( __( 'Valor de la matrícula por semestre, opciones de financiación y becas.', 'externado' ) ),
		'faq'    => array(
			__( '¿Cuándo cierran las inscripciones?', 'externado' ) => __( 'Escriba la fecha de cierre.', 'externado' ),
			__( '¿Hay examen de admisión?', 'externado' )           => __( 'Describa el proceso de admisión.', 'externado' ),
		),
	)
);
