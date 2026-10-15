<?php
/**
 * "Programa" custom post type and its meta (Block Bindings demo).
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta keys exposed to the block editor and Block Bindings.
 *
 * @return array<string, string> Meta key => label.
 */
function externado_get_programa_meta_keys() {
	return array(
		'duracion'        => __( 'Duración', 'externado' ),
		'modalidad'       => __( 'Modalidad', 'externado' ),
		'titulo_otorgado' => __( 'Título otorgado', 'externado' ),
		'snies'           => __( 'Registro SNIES', 'externado' ),
	);
}

/**
 * Register the `programa` post type and its meta.
 */
function externado_register_programa() {
	register_post_type(
		'programa',
		array(
			'labels'       => array(
				'name'               => __( 'Programas', 'externado' ),
				'singular_name'      => __( 'Programa', 'externado' ),
				'add_new_item'       => __( 'Añadir programa', 'externado' ),
				'edit_item'          => __( 'Editar programa', 'externado' ),
				'new_item'           => __( 'Nuevo programa', 'externado' ),
				'view_item'          => __( 'Ver programa', 'externado' ),
				'search_items'       => __( 'Buscar programas', 'externado' ),
				'not_found'          => __( 'No se encontraron programas.', 'externado' ),
				'all_items'          => __( 'Todos los programas', 'externado' ),
				'archives'           => __( 'Programas', 'externado' ),
				'menu_name'          => __( 'Programas', 'externado' ),
				'not_found_in_trash' => __( 'No hay programas en la papelera.', 'externado' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'has_archive'  => 'programas',
			'rewrite'      => array( 'slug' => 'programas' ),
			'menu_icon'    => 'dashicons-welcome-learn-more',
			'supports'     => array(
				'title',
				'editor' => array( 'notes' => true ),
				'thumbnail',
				'excerpt',
				'custom-fields',
			),
		)
	);

	foreach ( externado_get_programa_meta_keys() as $key => $label ) {
		register_post_meta(
			'programa',
			$key,
			array(
				'label'             => $label,
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}
}
add_action( 'init', 'externado_register_programa' );

/**
 * Classification taxonomies of `programa` (content strategy: taxonomies, not fields).
 *
 * @return array<string, array{rewrite: string, name: string, singular: string, terms: string[]}> Taxonomy key => settings.
 */
function externado_get_programa_taxonomies() {
	return array(
		'externado_nivel'     => array(
			'rewrite'  => 'nivel',
			'name'     => __( 'Niveles', 'externado' ),
			'singular' => __( 'Nivel', 'externado' ),
			'terms'    => array( 'Pregrado', 'Especialización', 'Maestría', 'Doctorado', 'Educación continua' ),
		),
		'externado_modalidad' => array(
			'rewrite'  => 'modalidad',
			'name'     => __( 'Modalidades', 'externado' ),
			'singular' => __( 'Modalidad', 'externado' ),
			'terms'    => array( 'Presencial', 'Virtual', 'Híbrida' ),
		),
		'externado_jornada'   => array(
			'rewrite'  => 'jornada',
			'name'     => __( 'Jornadas', 'externado' ),
			'singular' => __( 'Jornada', 'externado' ),
			'terms'    => array( 'Diurna', 'Nocturna', 'Fin de semana' ),
		),
		'externado_facultad'  => array(
			'rewrite'  => 'facultad',
			'name'     => __( 'Facultades', 'externado' ),
			'singular' => __( 'Facultad', 'externado' ),
			'terms'    => array(
				'Derecho',
				'Economía',
				'Administración de Empresas',
				'Comunicación Social y Periodismo',
				'Ciencias Sociales y Humanas',
				'Finanzas, Gobierno y Relaciones Internacionales',
				'Contaduría Pública',
				'Educación',
				'Estudios del Patrimonio Cultural',
			),
		),
	);
}

/**
 * Register the programa taxonomies.
 */
function externado_register_programa_taxonomies() {
	foreach ( externado_get_programa_taxonomies() as $taxonomy => $settings ) {
		register_taxonomy(
			$taxonomy,
			'programa',
			array(
				'labels'            => array(
					'name'          => $settings['name'],
					'singular_name' => $settings['singular'],
					'menu_name'     => $settings['name'],
					'all_items'     => $settings['name'],
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => $settings['rewrite'] ),
			)
		);
	}
}
add_action( 'init', 'externado_register_programa_taxonomies' );

/**
 * Content of a programa page as editable blocks: key facts, tabs and FAQ.
 *
 * Content strategy: everything lives in the post content (no fields), so
 * editors change it directly with blocks. Used by the "Ficha de programa"
 * starter pattern (placeholders) and by the demo seed (real Derecho data).
 *
 * @param array{facts: array<string, string>, perfil: string[], plan: string[], costos: string[], faq: array<string, string>} $data Content.
 * @return string Serialized block markup.
 */
function externado_programa_content_markup( $data ) {
	$paragraphs = static function ( $items ) {
		$out = '';
		foreach ( $items as $text ) {
			$out .= externado_paragraph_markup( esc_html( $text ) );
		}
		return $out;
	};

	// Key facts as plain, editable text.
	$out  = '<!-- wp:group {"metadata":{"name":"Datos clave"},"align":"wide","className":"externado-facts","layout":{"type":"grid","minimumColumnWidth":"10rem"}} -->' . "\n";
	$out .= '<div class="wp-block-group alignwide externado-facts">';
	foreach ( $data['facts'] as $label => $value ) {
		$out .= '<!-- wp:group {"className":"externado-facts__item","layout":{"type":"default"}} -->' . "\n";
		$out .= '<div class="wp-block-group externado-facts__item">';
		$out .= externado_paragraph_markup( esc_html( $label ), 'externado-facts__label' );
		$out .= externado_paragraph_markup( esc_html( $value ), 'externado-facts__value' );
		$out .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
	}
	$out .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n\n";

	$plan  = '<!-- wp:list -->' . "\n" . '<ul class="wp-block-list">';
	foreach ( $data['plan'] as $item ) {
		$plan .= '<!-- wp:list-item -->' . "\n" . '<li>' . esc_html( $item ) . '</li>' . "\n" . '<!-- /wp:list-item -->' . "\n";
	}
	$plan .= '</ul>' . "\n" . '<!-- /wp:list -->' . "\n";

	$out .= '<!-- wp:heading -->' . "\n" . '<h2 class="wp-block-heading">' . esc_html__( 'Información del programa', 'externado' ) . '</h2>' . "\n" . '<!-- /wp:heading -->' . "\n\n";
	$out .= externado_tabs_markup(
		array(
			__( 'Perfil', 'externado' )           => $paragraphs( $data['perfil'] ),
			__( 'Plan de estudios', 'externado' ) => $plan,
			__( 'Costos', 'externado' )           => $paragraphs( $data['costos'] ),
		)
	) . "\n";

	$faq = array();
	foreach ( $data['faq'] as $question => $answer ) {
		$faq[ $question ] = externado_paragraph_markup( esc_html( $answer ) );
	}
	$out .= '<!-- wp:heading -->' . "\n" . '<h2 class="wp-block-heading">' . esc_html__( 'Preguntas frecuentes', 'externado' ) . '</h2>' . "\n" . '<!-- /wp:heading -->' . "\n\n";
	$out .= externado_accordion_markup( $faq );

	return $out;
}
