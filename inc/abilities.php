<?php
/**
 * Site abilities for AI agents (WordPress Abilities API, core since 6.9).
 *
 * Exposed to MCP clients such as Claude through the MCP Adapter plugin
 * (meta.mcp.public = true). Every call still runs the permission callback
 * against the WordPress user the client is authenticated as.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ability category for the Externado site.
 */
function externado_register_ability_category() {
	wp_register_ability_category(
		'externado',
		array(
			'label'       => __( 'Universidad Externado', 'externado' ),
			'description' => __( 'Programas, noticias y resultados del sitio del Externado.', 'externado' ),
		)
	);
}
add_action( 'wp_abilities_api_categories_init', 'externado_register_ability_category' );

/**
 * Register the abilities.
 */
function externado_register_abilities() {
	$taxonomy_filter = static function ( $label ) {
		return array(
			'type'        => 'string',
			'description' => $label,
		);
	};

	// 1. Search programas: read-only, same data as the program finder.
	wp_register_ability(
		'externado/buscar-programas',
		array(
			'label'               => __( 'Buscar programas', 'externado' ),
			'description'         => __( 'Busca programas académicos del Externado por texto libre y/o por nivel, modalidad, jornada o facultad. Devuelve título, enlace y clasificación de cada programa.', 'externado' ),
			'category'            => 'externado',
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'texto'     => array(
						'type'        => 'string',
						'description' => __( 'Texto libre, por ejemplo "finanzas de noche". Opcional.', 'externado' ),
						'maxLength'   => 200,
					),
					'nivel'     => $taxonomy_filter( __( 'Pregrado, Especialización, Maestría, Doctorado o Educación continua. Opcional.', 'externado' ) ),
					'modalidad' => $taxonomy_filter( __( 'Presencial, Virtual o Híbrida. Opcional.', 'externado' ) ),
					'jornada'   => $taxonomy_filter( __( 'Diurna, Nocturna o Fin de semana. Opcional.', 'externado' ) ),
					'facultad'  => $taxonomy_filter( __( 'Nombre o parte del nombre de la facultad. Opcional.', 'externado' ) ),
				),
				'additionalProperties' => false,
			),
			'output_schema'       => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'id'        => array( 'type' => 'integer' ),
						'title'     => array( 'type' => 'string' ),
						'url'       => array( 'type' => 'string' ),
						'nivel'     => array( 'type' => 'string' ),
						'modalidad' => array( 'type' => 'string' ),
						'jornada'   => array( 'type' => 'string' ),
						'facultad'  => array( 'type' => 'string' ),
					),
				),
			),
			'execute_callback'    => 'externado_ability_search_programas',
			'permission_callback' => static function () {
				return current_user_can( 'read' );
			},
			'meta'                => array(
				'annotations' => array(
					'readonly'    => true,
					'destructive' => false,
					'idempotent'  => true,
				),
				'mcp'         => array( 'public' => true ),
			),
		)
	);

	// 2. Create a news draft: never published, a person reviews it in the editor.
	wp_register_ability(
		'externado/crear-borrador-noticia',
		array(
			'label'               => __( 'Crear borrador de noticia', 'externado' ),
			'description'         => __( 'Crea una noticia como BORRADOR (nunca la publica) para que el equipo editorial la revise. El contenido se guarda como bloques de párrafo; puede asignarse a una facultad.', 'externado' ),
			'category'            => 'externado',
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'titulo'    => array(
						'type'      => 'string',
						'minLength' => 5,
						'maxLength' => 160,
					),
					'parrafos'  => array(
						'type'        => 'array',
						'description' => __( 'Párrafos del cuerpo de la noticia, en orden.', 'externado' ),
						'minItems'    => 1,
						'maxItems'    => 12,
						'items'       => array(
							'type'      => 'string',
							'maxLength' => 1200,
						),
					),
					'resumen'   => array(
						'type'      => 'string',
						'maxLength' => 300,
					),
					'facultad'  => array(
						'type'        => 'string',
						'description' => __( 'Categoría de la facultad, por ejemplo "Facultad de Derecho". Opcional.', 'externado' ),
					),
				),
				'required'             => array( 'titulo', 'parrafos' ),
				'additionalProperties' => false,
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'id'          => array( 'type' => 'integer' ),
					'estado'      => array( 'type' => 'string' ),
					'editar'      => array( 'type' => 'string' ),
					'vista_previa' => array( 'type' => 'string' ),
				),
			),
			'execute_callback'    => 'externado_ability_create_news_draft',
			'permission_callback' => static function () {
				return current_user_can( 'edit_posts' );
			},
			'meta'                => array(
				'annotations' => array(
					'readonly'    => false,
					'destructive' => false,
					'idempotent'  => false,
				),
				'mcp'         => array( 'public' => true ),
			),
		)
	);

	// 3. Voting results: read-only snapshot of the Novedades vote.
	wp_register_ability(
		'externado/resultados-votacion',
		array(
			'label'               => __( 'Resultados de la votación', 'externado' ),
			'description'         => __( 'Devuelve el ranking de las novedades de Gutenberg votadas por el equipo editorial, con votos a favor, en contra y nivel de esfuerzo (A listo para usar, B ajuste del tema, C desarrollo).', 'externado' ),
			'category'            => 'externado',
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(),
				'additionalProperties' => false,
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'total_votos' => array( 'type' => 'integer' ),
					'ranking'     => array( 'type' => 'array' ),
				),
			),
			'execute_callback'    => 'externado_ability_vote_results',
			'permission_callback' => static function () {
				return current_user_can( 'read' );
			},
			'meta'                => array(
				'annotations' => array(
					'readonly'    => true,
					'destructive' => false,
					'idempotent'  => true,
				),
				'mcp'         => array( 'public' => true ),
			),
		)
	);
}
add_action( 'wp_abilities_api_init', 'externado_register_abilities' );

/**
 * Execute: search programas.
 *
 * @param array<string, mixed>|null $input Input.
 * @return array<int, array<string, mixed>>
 */
function externado_ability_search_programas( $input = null ) {
	$input   = is_array( $input ) ? $input : array();
	$text    = isset( $input['texto'] ) ? sanitize_text_field( $input['texto'] ) : '';
	$filters = array(
		'externado_nivel'     => $input['nivel'] ?? '',
		'externado_modalidad' => $input['modalidad'] ?? '',
		'externado_jornada'   => $input['jornada'] ?? '',
		'externado_facultad'  => $input['facultad'] ?? '',
	);

	// Free text uses the finder's keyword search (synonyms included); otherwise start from the full catalog.
	$candidates = '' !== $text ? externado_finder_keyword_search( $text ) : array_map(
		static function ( $entry ) {
			return externado_finder_result( $entry, '' );
		},
		array_values( externado_finder_catalog() )
	);

	$map     = array(
		'externado_nivel'     => 'nivel',
		'externado_modalidad' => 'modalidad',
		'externado_jornada'   => 'jornada',
		'externado_facultad'  => 'facultad',
	);
	$results = array();
	foreach ( $candidates as $item ) {
		foreach ( $filters as $taxonomy => $wanted ) {
			$wanted = externado_finder_normalize( sanitize_text_field( (string) $wanted ) );
			if ( '' !== $wanted && false === strpos( externado_finder_normalize( $item[ $map[ $taxonomy ] ] ), $wanted ) ) {
				continue 2;
			}
		}
		$results[] = array(
			'id'        => (int) $item['id'],
			'title'     => $item['title'],
			'url'       => $item['url'],
			'nivel'     => $item['nivel'],
			'modalidad' => $item['modalidad'],
			'jornada'   => $item['jornada'],
			'facultad'  => $item['facultad'],
		);
	}

	return $results;
}

/**
 * Execute: create a news draft.
 *
 * @param array<string, mixed> $input Input.
 * @return array<string, mixed>|WP_Error
 */
function externado_ability_create_news_draft( $input ) {
	$content = '';
	foreach ( (array) $input['parrafos'] as $paragraph ) {
		$content .= externado_paragraph_markup( esc_html( sanitize_textarea_field( (string) $paragraph ) ) ) . "\n";
	}

	$categories = array();
	if ( ! empty( $input['facultad'] ) ) {
		$term = get_term_by( 'name', sanitize_text_field( $input['facultad'] ), 'category' );
		if ( $term ) {
			$categories[] = (int) $term->term_id;
		}
	}

	$post_id = wp_insert_post(
		array(
			'post_type'     => 'post',
			'post_status'   => 'draft', // Always a draft: publishing stays a human decision.
			'post_title'    => sanitize_text_field( $input['titulo'] ),
			'post_excerpt'  => isset( $input['resumen'] ) ? sanitize_text_field( $input['resumen'] ) : '',
			'post_content'  => $content,
			'post_author'   => get_current_user_id(),
			'post_category' => $categories,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	return array(
		'id'           => (int) $post_id,
		'estado'       => 'borrador',
		'editar'       => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
		'vista_previa' => (string) get_preview_post_link( $post_id ),
	);
}

/**
 * Execute: voting results.
 *
 * @return array<string, mixed>
 */
function externado_ability_vote_results() {
	$ranking = externado_get_feature_ranking();
	$total   = 0;
	$rows    = array();
	foreach ( $ranking as $item ) {
		$total += $item['yes'] + $item['no'];
		$rows[] = array(
			'novedad'   => $item['title'],
			'esfuerzo'  => $item['effort'],
			'a_favor'   => $item['yes'],
			'en_contra' => $item['no'],
		);
	}

	return array(
		'total_votos' => $total,
		'ranking'     => $rows,
	);
}
