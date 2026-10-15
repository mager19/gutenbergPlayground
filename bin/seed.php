<?php
/**
 * Demo content for the "Novedades Gutenberg" showcase.
 *
 * Idempotent: every item is looked up by slug and updated instead of duplicated.
 *
 * Usage (from the WordPress root):
 *   wp eval-file wp-content/themes/externado/bin/seed.php
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';

// Run as the first administrator so content keeps its full markup (unfiltered_html).
$externado_admins = get_users(
	array(
		'role'    => 'administrator',
		'number'  => 1,
		'orderby' => 'ID',
		'fields'  => 'ID',
	)
);
wp_set_current_user( $externado_admins ? (int) $externado_admins[0] : 1 );

/**
 * Print a log line.
 *
 * @param string $message Message.
 */
function externado_seed_log( $message ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::log( $message );
	} else {
		echo esc_html( $message ) . "\n";
	}
}

/**
 * Create or update a post identified by slug and post type.
 *
 * @param array $postarr Post data; must include post_name and post_type.
 * @return int Post ID.
 */
function externado_seed_upsert( $postarr ) {
	$existing = externado_get_post_id_by_slug( $postarr['post_name'], $postarr['post_type'] );

	if ( $existing ) {
		$postarr['ID'] = $existing;
	}

	if ( ! isset( $postarr['post_status'] ) ) {
		$postarr['post_status'] = 'publish';
	}

	// wp_insert_post() unslashes its input; slash it so JSON escapes in block attributes survive.
	$post_id = wp_insert_post( wp_slash( $postarr ), true );

	if ( is_wp_error( $post_id ) ) {
		externado_seed_log( 'ERROR ' . $postarr['post_name'] . ': ' . $post_id->get_error_message() );
		return 0;
	}

	externado_seed_log( ( $existing ? 'Updated ' : 'Created ' ) . $postarr['post_type'] . ' ' . $postarr['post_name'] . ' (#' . $post_id . ')' );

	return (int) $post_id;
}

/**
 * Generate a short mono 8-bit WAV file with a simple tone sequence.
 *
 * @param string $path  Destination path.
 * @param array  $notes Frequencies in Hz.
 */
function externado_seed_write_wav( $path, $notes ) {
	$rate    = 8000;
	$seconds = 6;
	$total   = $rate * $seconds;
	$per     = (int) floor( $total / count( $notes ) );
	$data    = '';

	for ( $i = 0; $i < $total; $i++ ) {
		$note_index = min( count( $notes ) - 1, (int) floor( $i / $per ) );
		$position   = $i - ( $note_index * $per );
		$envelope   = min( 1, $position / 400 ) * min( 1, ( $per - $position ) / 800 );
		$sample     = sin( 2 * M_PI * $notes[ $note_index ] * $i / $rate ) * 0.5 * $envelope;
		$data      .= chr( (int) round( 128 + $sample * 127 ) );
	}

	$header = 'RIFF' . pack( 'V', 36 + strlen( $data ) ) . 'WAVE'
		. 'fmt ' . pack( 'V', 16 ) . pack( 'v', 1 ) . pack( 'v', 1 ) . pack( 'V', $rate ) . pack( 'V', $rate ) . pack( 'v', 1 ) . pack( 'v', 8 )
		. 'data' . pack( 'V', strlen( $data ) );

	file_put_contents( $path, $header . $data ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
}

// Make sure the CPT permalinks work and theme pattern files added since the last cache are picked up.
flush_rewrite_rules( false );
wp_get_theme()->delete_pattern_cache();

/*
 * 1. Faculty categories and news posts.
 */
$externado_faculties = array(
	'facultad-derecho'           => 'Facultad de Derecho',
	'facultad-economia'          => 'Facultad de Economía',
	'facultad-administracion'    => 'Facultad de Administración de Empresas',
	'facultad-comunicacion'      => 'Facultad de Comunicación Social',
	'facultad-ciencias-sociales' => 'Facultad de Ciencias Sociales y Humanas',
);
$externado_term_ids  = array();
foreach ( $externado_faculties as $externado_slug => $externado_name ) {
	$externado_term = get_term_by( 'slug', $externado_slug, 'category' );
	if ( ! $externado_term ) {
		$externado_result = wp_insert_term( $externado_name, 'category', array( 'slug' => $externado_slug ) );
		$externado_term_ids[ $externado_slug ] = is_wp_error( $externado_result ) ? 0 : (int) $externado_result['term_id'];
		externado_seed_log( 'Created category ' . $externado_slug );
	} else {
		$externado_term_ids[ $externado_slug ] = (int) $externado_term->term_id;
	}
}

$externado_news = array(
	'derecho-abre-convocatoria-2027'           => array( 'La Facultad de Derecho abre su convocatoria 2027', 'facultad-derecho', 1 ),
	'economia-observatorio-fiscal'             => array( 'Economía presenta su nuevo observatorio fiscal', 'facultad-economia', 3 ),
	'administracion-programa-emprendimiento'   => array( 'Administración de Empresas lanza un programa de emprendimiento', 'facultad-administracion', 5 ),
	'comunicacion-laboratorio-podcast'         => array( 'Comunicación Social estrena laboratorio de pódcast', 'facultad-comunicacion', 7 ),
	'ciencias-sociales-seminario-memoria'      => array( 'Seminario de memoria histórica en Ciencias Sociales y Humanas', 'facultad-ciencias-sociales', 9 ),
	'derecho-conferencias-inteligencia-artificial' => array( 'Ciclo de conferencias sobre inteligencia artificial y regulación', 'facultad-derecho', 11 ),
);
foreach ( $externado_news as $externado_slug => $externado_item ) {
	$externado_content  = "<!-- wp:paragraph -->\n<p>" . esc_html( $externado_item[0] ) . '. Esta es una noticia de demostración para el recorrido Novedades Gutenberg; su contenido no corresponde a información oficial.</p>' . "\n<!-- /wp:paragraph -->\n\n";
	$externado_content .= "<!-- wp:paragraph -->\n<p>La comunidad académica podrá consultar más detalles en la página de la facultad y en los canales oficiales de la Universidad. Las inscripciones y actividades se anuncian con anticipación.</p>\n<!-- /wp:paragraph -->\n\n";
	$externado_content .= "<!-- wp:paragraph -->\n<p>Este texto adicional existe para que el bloque de tiempo de lectura tenga algo que medir y para que el listado en cuadrícula muestre tarjetas de distinta longitud.</p>\n<!-- /wp:paragraph -->";

	externado_seed_upsert(
		array(
			'post_type'     => 'post',
			'post_name'     => $externado_slug,
			'post_title'    => $externado_item[0],
			'post_content'  => $externado_content,
			'post_excerpt'  => __( 'Noticia de demostración para el recorrido Novedades Gutenberg.', 'externado' ),
			'post_date'     => gmdate( 'Y-m-d H:i:s', strtotime( '-' . $externado_item[2] . ' days' ) ),
			'post_category' => array( $externado_term_ids[ $externado_item[1] ] ),
		)
	);
}

/*
 * 2. Programa "Derecho" with meta (Block Bindings).
 */
$externado_derecho = externado_seed_upsert(
	array(
		'post_type'    => 'programa',
		'post_name'    => 'derecho',
		'post_title'   => 'Derecho',
		'post_excerpt' => 'Pregrado en Derecho con más de un siglo de tradición: formación humanista, crítica y práctica.',
		// Content strategy: the whole ficha (facts, tabs, FAQ) is editable blocks in the post content.
		'post_content' => externado_programa_content_markup(
			array(
				'facts'  => array(
					'Duración'        => '10 semestres',
					'Modalidad'       => 'Presencial',
					'Título otorgado' => 'Abogado(a)',
					'Registro SNIES'  => '0000 (dato de ejemplo)',
				),
				'perfil' => array(
					'El programa forma profesionales capaces de asesorar, litigar, conciliar y diseñar políticas públicas, con una sólida base en teoría jurídica y en derecho público y privado.',
					'Los estudiantes realizan prácticas en el Consultorio Jurídico y participan en semilleros de investigación desde los primeros semestres.',
				),
				'plan'   => array(
					'Ciclo de fundamentación: semestres 1 a 4.',
					'Ciclo de profundización: semestres 5 a 8.',
					'Prácticas y trabajo de grado: semestres 9 y 10.',
				),
				'costos' => array( 'Consulte el valor de la matrícula por semestre, las opciones de financiación y las becas por mérito con la Oficina de Admisiones.' ),
				'faq'    => array(
					'¿Cuándo cierran las inscripciones?' => 'El 30 de noviembre para el primer período académico de 2027.',
					'¿Hay examen de admisión?'           => 'Sí: prueba escrita y entrevista con la facultad.',
					'¿Puedo pagar en cuotas?'            => 'Sí, con las entidades de financiación aliadas.',
				),
			)
		),
		'meta_input'   => array(
			'duracion'        => '10 semestres',
			'modalidad'       => 'Presencial',
			'titulo_otorgado' => 'Abogado(a)',
			'snies'           => '0000 (dato de ejemplo)',
		),
	)
);

/*
 * 3. Synced patterns with pattern overrides.
 */
$externado_card = <<<'HTML'
<!-- wp:group {"metadata":{"name":"Tarjeta docente"},"className":"externado-teacher-card","layout":{"type":"default"}} -->
<div class="wp-block-group externado-teacher-card"><!-- wp:heading {"level":3,"metadata":{"name":"nombre","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<h3 class="wp-block-heading">Nombre del docente</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"metadata":{"name":"cargo","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<p>Cargo</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"name":"area","bindings":{"__default":{"source":"core/pattern-overrides"}}},"className":"externado-teacher-card__area"} -->
<p class="externado-teacher-card__area">Área de conocimiento</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML;

$externado_cta = <<<'HTML'
<!-- wp:group {"metadata":{"name":"CTA Inscripciones"},"align":"full","className":"is-style-section-verde externado-cta","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-section-verde externado-cta"><!-- wp:heading {"metadata":{"name":"titulo_cta","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<h2 class="wp-block-heading">Inscripciones abiertas 2027</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"metadata":{"name":"texto_cta","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<p>Conozca fechas, requisitos y costos, y comience su proceso de admisión.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"metadata":{"name":"boton_cta","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/convocatoria-inscripciones-2027/">Inscríbase</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
HTML;

externado_seed_upsert(
	array(
		'post_type'    => 'wp_block',
		'post_name'    => 'tarjeta-docente',
		'post_title'   => 'Tarjeta docente',
		'post_content' => $externado_card,
	)
);
externado_seed_upsert(
	array(
		'post_type'    => 'wp_block',
		'post_name'    => 'cta-inscripciones',
		'post_title'   => 'CTA Inscripciones',
		'post_content' => $externado_cta,
	)
);

/*
 * 4. Demo audio for the Playlist block.
 */
$externado_uploads = wp_upload_dir();
$externado_audio   = array(
	'externado-radio-1' => array( 'Abren las inscripciones 2027', array( 392, 523, 659, 784 ) ),
	'externado-radio-2' => array( 'Así es la vida en el campus', array( 440, 554, 659, 554 ) ),
	'externado-radio-3' => array( 'Consejos para la entrevista', array( 330, 392, 494, 392 ) ),
);
foreach ( $externado_audio as $externado_slug => $externado_track ) {
	$externado_ids = get_posts(
		array(
			'name'           => $externado_slug,
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $externado_ids ) {
		externado_seed_log( 'Exists attachment ' . $externado_slug . ' (#' . $externado_ids[0] . ')' );
		continue;
	}

	$externado_dir = trailingslashit( $externado_uploads['basedir'] ) . 'externado-demo';
	wp_mkdir_p( $externado_dir );
	$externado_file = $externado_dir . '/' . $externado_slug . '.wav';
	externado_seed_write_wav( $externado_file, $externado_track[1] );

	$externado_attachment_id = wp_insert_attachment(
		array(
			'post_title'     => $externado_track[0],
			'post_name'      => $externado_slug,
			'post_mime_type' => 'audio/wav',
			'post_status'    => 'inherit',
			'post_content'   => '',
		),
		$externado_file
	);
	if ( $externado_attachment_id && ! is_wp_error( $externado_attachment_id ) ) {
		wp_update_attachment_metadata( $externado_attachment_id, wp_generate_attachment_metadata( $externado_attachment_id, $externado_file ) );
		externado_seed_log( 'Created attachment ' . $externado_slug . ' (#' . $externado_attachment_id . ')' );
	}
}

/*
 * 5. "Convocatoria inscripciones 2027" page (Notes, revisions, visibility, icons, accordion, custom CSS).
 */

/**
 * Build the convocatoria page content.
 *
 * @param int $note_id Note (comment) ID to attach to the dates paragraph, or 0.
 * @return string
 */
function externado_seed_convocatoria_content( $note_id ) {
	$note_attrs = $note_id ? ' ' . wp_json_encode( array( 'metadata' => array( 'noteId' => array( $note_id ) ) ) ) : '';

	$content  = '<!-- wp:group {"metadata":{"name":"Portada de la convocatoria"},"align":"full","className":"is-style-section-verde","layout":{"type":"constrained"}} -->' . "\n";
	$content .= '<div class="wp-block-group alignfull is-style-section-verde"><!-- wp:heading {"level":2} -->' . "\n" . '<h2 class="wp-block-heading">Inscripciones abiertas 2027</h2>' . "\n" . '<!-- /wp:heading -->' . "\n\n";
	$content .= '<!-- wp:paragraph' . $note_attrs . ' -->' . "\n" . '<p>Las inscripciones para el primer período académico de 2027 van del 1 de septiembre al 30 de noviembre.</p>' . "\n" . '<!-- /wp:paragraph -->' . "\n\n";
	$content .= '<!-- wp:buttons -->' . "\n" . '<div class="wp-block-buttons"><!-- wp:button -->' . "\n" . '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#requisitos">Ver requisitos</a></div>' . "\n" . '<!-- /wp:button --></div>' . "\n" . '<!-- /wp:buttons --></div>' . "\n" . '<!-- /wp:group -->' . "\n\n";

	$content .= '<!-- wp:group {"metadata":{"name":"Banner día de apertura (oculto)","blockVisibility":false},"className":"is-style-section-pizarra","layout":{"type":"constrained"}} -->' . "\n";
	$content .= '<div class="wp-block-group is-style-section-pizarra"><!-- wp:paragraph -->' . "\n" . '<p><strong>Hoy abren las inscripciones 2027.</strong> Este banner está oculto: actívelo el día de apertura.</p>' . "\n" . '<!-- /wp:paragraph --></div>' . "\n" . '<!-- /wp:group -->' . "\n\n";

	$content .= '<!-- wp:group {"metadata":{"name":"Aviso escritorio","blockVisibility":{"viewport":{"mobile":false}}},"className":"is-style-section-gris","layout":{"type":"constrained"}} -->' . "\n";
	$content .= '<div class="wp-block-group is-style-section-gris"><!-- wp:paragraph -->' . "\n" . '<p>Versión para tableta y escritorio: consulte fechas, requisitos y costos de cada programa.</p>' . "\n" . '<!-- /wp:paragraph --></div>' . "\n" . '<!-- /wp:group -->' . "\n\n";

	$content .= '<!-- wp:group {"metadata":{"name":"Aviso celular","blockVisibility":{"viewport":{"tablet":false,"desktop":false}}},"className":"is-style-section-gris","layout":{"type":"constrained"}} -->' . "\n";
	$content .= '<div class="wp-block-group is-style-section-gris"><!-- wp:paragraph -->' . "\n" . '<p>Versión para celular: inscripciones abiertas.</p>' . "\n" . '<!-- /wp:paragraph --></div>' . "\n" . '<!-- /wp:group -->' . "\n\n";

	$content .= '<!-- wp:group {"layout":{"type":"grid","minimumColumnWidth":"10rem"}} -->' . "\n" . '<div class="wp-block-group">';
	foreach (
		array(
			'core/calendar'   => 'Fechas',
			'core/people'     => 'Requisitos',
			'core/map-marker' => 'Sede',
			'core/envelope'   => 'Contacto',
		) as $icon => $label
	) {
		$content .= '<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->' . "\n" . '<div class="wp-block-group"><!-- wp:icon {"icon":"' . $icon . '","textColor":"primary","style":{"dimensions":{"width":"32px"}}} /-->' . "\n\n" . '<!-- wp:paragraph -->' . "\n" . '<p>' . $label . '</p>' . "\n" . '<!-- /wp:paragraph --></div>' . "\n" . '<!-- /wp:group -->' . "\n\n";
	}
	$content .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n\n";

	$content .= '<!-- wp:heading {"anchor":"requisitos"} -->' . "\n" . '<h2 class="wp-block-heading" id="requisitos">Requisitos</h2>' . "\n" . '<!-- /wp:heading -->' . "\n\n";
	$content .= externado_accordion_markup(
		array(
			'¿Qué documentos necesito?' => externado_paragraph_markup( 'Documento de identidad, diploma o acta de grado de bachiller y resultados de la prueba Saber 11.' ),
			'¿Hay examen de admisión?'  => externado_paragraph_markup( 'Sí: prueba escrita y entrevista con la facultad.' ),
		)
	);

	return $content;
}

$externado_convocatoria = externado_seed_upsert(
	array(
		'post_type'    => 'page',
		'post_name'    => 'convocatoria-inscripciones-2027',
		'post_title'   => 'Convocatoria inscripciones 2027',
		'post_content' => externado_seed_convocatoria_content( 0 ),
	)
);

// Note on the dates paragraph: a 'note' comment linked from the block metadata (noteId).
if ( $externado_convocatoria ) {
	$externado_notes = get_comments(
		array(
			'post_id' => $externado_convocatoria,
			'type'    => 'note',
			'parent'  => 0,
			'number'  => 1,
			'status'  => 'all',
			'orderby' => 'comment_ID',
			'order'   => 'ASC',
		)
	);

	if ( $externado_notes ) {
		$externado_note_id = (int) $externado_notes[0]->comment_ID;
		externado_seed_log( 'Exists note #' . $externado_note_id );
	} else {
		$externado_request = new WP_REST_Request( 'POST', '/wp/v2/comments' );
		$externado_request->set_body_params(
			array(
				'post'    => $externado_convocatoria,
				'type'    => 'note',
				'content' => 'Admisiones: confirmar con la facultad si la fecha de cierre se mantiene el 30 de noviembre.',
			)
		);
		$externado_response = rest_do_request( $externado_request );
		$externado_note_id  = 0;
		if ( $externado_response->is_error() ) {
			externado_seed_log( 'WARNING: note not created: ' . $externado_response->as_error()->get_error_message() );
		} else {
			$externado_data    = $externado_response->get_data();
			$externado_note_id = (int) $externado_data['id'];
			externado_seed_log( 'Created note #' . $externado_note_id );
		}
	}

	if ( $externado_note_id ) {
		externado_seed_upsert(
			array(
				'post_type'    => 'page',
				'post_name'    => 'convocatoria-inscripciones-2027',
				'post_title'   => 'Convocatoria inscripciones 2027',
				'post_content' => externado_seed_convocatoria_content( $externado_note_id ),
			)
		);
	}
}

/*
 * 5b. "Noticias por facultad" page: Query Loop grid + Terms Query as editable blocks
 * (target of the query-grid "Pruébalo tú" button).
 */
$externado_faculty_news = externado_seed_upsert(
	array(
		'post_type'    => 'page',
		'post_name'    => 'noticias-por-facultad',
		'post_title'   => 'Noticias por facultad',
		'post_content' => "<!-- wp:paragraph -->\n<p>Las últimas noticias de las facultades y el directorio de facultades. Ambos listados se actualizan solos al publicar.</p>\n<!-- /wp:paragraph -->\n\n" . externado_faculty_news_markup( false ),
	)
);

/*
 * 5c. Programas classified with taxonomies + "Buscador de programas" page (program finder demo).
 */
require __DIR__ . '/seed-programas.php';

/*
 * 6. "Novedades Gutenberg" page with its template.
 */
$externado_novedades = externado_seed_upsert(
	array(
		'post_type'     => 'page',
		'post_name'     => 'novedades-gutenberg',
		'post_title'    => 'Novedades Gutenberg',
		'post_content'  => "<!-- wp:paragraph -->\n<p>El contenido de esta página lo genera la plantilla «Novedades Gutenberg» a partir de patrones por capítulo.</p>\n<!-- /wp:paragraph -->",
		'page_template' => 'page-novedades',
	)
);

externado_seed_log( '' );
externado_seed_log( 'Novedades: ' . get_permalink( $externado_novedades ) );
externado_seed_log( 'Programa:  ' . get_permalink( $externado_derecho ) );
externado_seed_log( 'Convocatoria: ' . get_permalink( $externado_convocatoria ) );
