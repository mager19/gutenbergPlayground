<?php
/**
 * Demo programas classified with taxonomies, plus the "Buscador de programas" page.
 *
 * Idempotent: terms are looked up by name, posts by slug. Included by
 * bin/seed.php, and can run on its own without touching other demo content:
 *   wp eval-file wp-content/themes/externado/bin/seed-programas.php
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'externado_seed_log' ) ) {
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
}

if ( ! function_exists( 'externado_seed_upsert' ) ) {
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

		$post_id = wp_insert_post( wp_slash( $postarr ), true );

		if ( is_wp_error( $post_id ) ) {
			externado_seed_log( 'ERROR ' . $postarr['post_name'] . ': ' . $post_id->get_error_message() );
			return 0;
		}

		externado_seed_log( ( $existing ? 'Updated ' : 'Created ' ) . $postarr['post_type'] . ' ' . $postarr['post_name'] . ' (#' . $post_id . ')' );

		return (int) $post_id;
	}
}

// Run as the first administrator so content keeps its full markup (re-inits kses for that user).
if ( ! current_user_can( 'unfiltered_html' ) ) {
	$externado_admins = get_users(
		array(
			'role'    => 'administrator',
			'number'  => 1,
			'orderby' => 'ID',
			'fields'  => 'ID',
		)
	);
	wp_set_current_user( $externado_admins ? (int) $externado_admins[0] : 1 );
}

/*
 * 1. Taxonomy terms.
 */
foreach ( externado_get_programa_taxonomies() as $externado_taxonomy => $externado_settings ) {
	foreach ( $externado_settings['terms'] as $externado_term_name ) {
		if ( ! term_exists( $externado_term_name, $externado_taxonomy ) ) {
			$externado_result = wp_insert_term( $externado_term_name, $externado_taxonomy );
			externado_seed_log( is_wp_error( $externado_result ) ? 'ERROR term ' . $externado_term_name . ': ' . $externado_result->get_error_message() : 'Created term ' . $externado_taxonomy . ' ' . $externado_term_name );
		}
	}
}

/**
 * Assign the classification terms (by name) to a programa.
 *
 * @param int   $post_id Post ID.
 * @param array $terms   Taxonomy => term name or list of names.
 */
function externado_seed_programa_terms( $post_id, $terms ) {
	foreach ( $terms as $taxonomy => $names ) {
		$ids = array();
		foreach ( (array) $names as $name ) {
			$term = get_term_by( 'name', $name, $taxonomy );
			if ( $term ) {
				$ids[] = (int) $term->term_id;
			}
		}
		wp_set_object_terms( $post_id, $ids, $taxonomy );
	}
}

/*
 * 2. Existing "Derecho" programa: classification only (its content stays as it is).
 */
$externado_derecho_id = externado_get_post_id_by_slug( 'derecho', 'programa' );
if ( $externado_derecho_id ) {
	externado_seed_programa_terms(
		$externado_derecho_id,
		array(
			'externado_nivel'     => 'Pregrado',
			'externado_modalidad' => 'Presencial',
			'externado_jornada'   => 'Diurna',
			'externado_facultad'  => 'Derecho',
		)
	);
	externado_seed_log( 'Classified programa derecho (#' . $externado_derecho_id . ')' );
}

/*
 * 3. Demo programas. Facts follow the taxonomy terms; SNIES numbers are not real.
 *
 * Keys: title, excerpt, nivel, modalidad, jornada (string or list), facultad, titulo, foco.
 */
$externado_programas = array(
	'especializacion-derecho-tributario'        => array( 'Especialización en Derecho Tributario', 'Profundice en el régimen de impuestos colombiano y en la planeación tributaria de empresas y personas.', 'Especialización', 'Presencial', 'Nocturna', 'Derecho', 'Especialista en Derecho Tributario', 'el régimen tributario, el procedimiento ante la administración de impuestos y la planeación fiscal' ),
	'maestria-finanzas'                         => array( 'Maestría en Finanzas', 'Formación avanzada en valoración, mercados de capitales y gestión del riesgo para quienes trabajan en el sector financiero.', 'Maestría', 'Híbrida', 'Nocturna', 'Finanzas, Gobierno y Relaciones Internacionales', 'Magíster en Finanzas', 'la valoración de empresas, los mercados de capitales, las inversiones y la gestión del riesgo' ),
	'pregrado-contaduria-publica'               => array( 'Pregrado en Contaduría Pública', 'Forma contadores públicos con criterio ético para la información financiera, la auditoría y la tributación.', 'Pregrado', 'Presencial', 'Nocturna', 'Contaduría Pública', 'Contador(a) público(a)', 'la contabilidad financiera, la auditoría, la revisoría fiscal y los impuestos' ),
	'diplomado-inteligencia-artificial-derecho' => array( 'Diplomado en Inteligencia Artificial para el Derecho', 'Curso corto sobre el uso responsable de la inteligencia artificial en la práctica jurídica y su regulación.', 'Educación continua', 'Virtual', 'Nocturna', 'Derecho', 'Certificado de participación', 'las herramientas de inteligencia artificial para abogados, la protección de datos y la regulación de la tecnología' ),
	'maestria-periodismo'                       => array( 'Maestría en Periodismo', 'Investigación periodística, narrativas digitales y ética de los medios para periodistas en ejercicio.', 'Maestría', 'Presencial', 'Diurna', 'Comunicación Social y Periodismo', 'Magíster en Periodismo', 'el periodismo de investigación, las narrativas digitales y la ética de los medios' ),
	'doctorado-ciencias-sociales-humanas'       => array( 'Doctorado en Ciencias Sociales y Humanas', 'Programa de investigación para formar investigadores en sociología, antropología, historia y filosofía.', 'Doctorado', 'Presencial', 'Diurna', 'Ciencias Sociales y Humanas', 'Doctor(a) en Ciencias Sociales y Humanas', 'la investigación en sociología, antropología, historia y filosofía' ),
	'pregrado-economia'                         => array( 'Pregrado en Economía', 'Economistas con formación en teoría, métodos cuantitativos y política económica.', 'Pregrado', 'Presencial', 'Diurna', 'Economía', 'Economista', 'la microeconomía, la macroeconomía, la econometría y la política económica' ),
	'pregrado-administracion-empresas'          => array( 'Pregrado en Administración de Empresas', 'Gestión de organizaciones, emprendimiento y estrategia, con opción de cursarlo de día o de noche.', 'Pregrado', 'Presencial', array( 'Diurna', 'Nocturna' ), 'Administración de Empresas', 'Administrador(a) de empresas', 'la gerencia, el emprendimiento, el mercadeo y la estrategia de organizaciones' ),
	'pregrado-comunicacion-social-periodismo'   => array( 'Pregrado en Comunicación Social y Periodismo', 'Forma comunicadores y periodistas para medios, organizaciones y proyectos digitales.', 'Pregrado', 'Presencial', 'Diurna', 'Comunicación Social y Periodismo', 'Comunicador(a) social y periodista', 'el periodismo, la comunicación organizacional, la producción audiovisual y los medios digitales' ),
	'maestria-derecho-constitucional'           => array( 'Maestría en Derecho Constitucional', 'Estudio avanzado de la Constitución, los derechos fundamentales y la justicia constitucional.', 'Maestría', 'Híbrida', 'Fin de semana', 'Derecho', 'Magíster en Derecho Constitucional', 'la teoría constitucional, los derechos fundamentales y la jurisprudencia de la Corte Constitucional' ),
	'especializacion-gerencia-proyectos'        => array( 'Especialización en Gerencia de Proyectos', 'Planeación, ejecución y control de proyectos con metodologías ágiles y tradicionales.', 'Especialización', 'Virtual', 'Fin de semana', 'Administración de Empresas', 'Especialista en Gerencia de Proyectos', 'la planeación, la gestión de equipos, los costos y el control de proyectos' ),
	'maestria-gobierno-politicas-publicas'      => array( 'Maestría en Gobierno y Políticas Públicas', 'Diseño, implementación y evaluación de políticas públicas para el sector público y las organizaciones sociales.', 'Maestría', 'Presencial', 'Fin de semana', 'Finanzas, Gobierno y Relaciones Internacionales', 'Magíster en Gobierno y Políticas Públicas', 'el diseño y la evaluación de políticas públicas y la gestión del Estado' ),
	'pregrado-relaciones-internacionales'       => array( 'Pregrado en Finanzas, Gobierno y Relaciones Internacionales', 'Una carrera que une la economía global, la política internacional y las finanzas públicas.', 'Pregrado', 'Presencial', 'Diurna', 'Finanzas, Gobierno y Relaciones Internacionales', 'Profesional en Finanzas, Gobierno y Relaciones Internacionales', 'las relaciones internacionales, el gobierno y las finanzas públicas' ),
	'especializacion-finanzas-corporativas'     => array( 'Especialización en Finanzas Corporativas', 'Decisiones de inversión y financiación en la empresa, en un formato virtual pensado para quienes trabajan.', 'Especialización', 'Virtual', 'Nocturna', 'Finanzas, Gobierno y Relaciones Internacionales', 'Especialista en Finanzas Corporativas', 'las decisiones de inversión, la estructura de capital y la valoración financiera' ),
	'maestria-educacion'                        => array( 'Maestría en Educación', 'Investigación pedagógica e innovación educativa para docentes y directivos.', 'Maestría', 'Virtual', 'Fin de semana', 'Educación', 'Magíster en Educación', 'la investigación pedagógica, el currículo y la innovación educativa' ),
	'especializacion-museologia-patrimonio'     => array( 'Especialización en Museología y Gestión del Patrimonio', 'Gestión de museos, colecciones y patrimonio cultural material e inmaterial.', 'Especialización', 'Híbrida', 'Fin de semana', 'Estudios del Patrimonio Cultural', 'Especialista en Museología y Gestión del Patrimonio', 'la gestión de museos y colecciones, la conservación y el patrimonio cultural' ),
	'diplomado-periodismo-datos'                => array( 'Diplomado en Periodismo de Datos', 'Curso corto para encontrar, analizar y visualizar datos en historias periodísticas.', 'Educación continua', 'Virtual', 'Nocturna', 'Comunicación Social y Periodismo', 'Certificado de participación', 'la búsqueda, el análisis y la visualización de datos para el periodismo' ),
	'doctorado-derecho'                         => array( 'Doctorado en Derecho', 'Formación de investigadores en derecho con tesis doctoral y estancias de investigación.', 'Doctorado', 'Presencial', 'Diurna', 'Derecho', 'Doctor(a) en Derecho', 'la investigación jurídica y la tesis doctoral' ),
	'diplomado-niif'                            => array( 'Diplomado en Normas Internacionales de Información Financiera (NIIF)', 'Curso corto y práctico sobre la aplicación de las NIIF en la contabilidad de las empresas.', 'Educación continua', 'Virtual', 'Fin de semana', 'Contaduría Pública', 'Certificado de participación', 'la aplicación de las NIIF y la preparación de estados financieros' ),
);

$externado_duraciones = array(
	'Pregrado'           => '9 semestres',
	'Especialización'    => '2 semestres',
	'Maestría'           => '4 semestres',
	'Doctorado'          => '8 semestres',
	'Educación continua' => '120 horas',
);
$externado_horarios   = array(
	'Diurna'        => 'de lunes a viernes, entre las 7:00 a. m. y las 4:00 p. m.',
	'Nocturna'      => 'de lunes a viernes, de 6:00 p. m. a 10:00 p. m.',
	'Fin de semana' => 'viernes de 6:00 p. m. a 10:00 p. m. y sábados de 8:00 a. m. a 5:00 p. m.',
);
$externado_planes     = array(
	'Pregrado'           => array( 'Ciclo de fundamentación: semestres 1 a 4.', 'Ciclo de profundización: semestres 5 a 8.', 'Práctica y trabajo de grado: semestre 9.' ),
	'Especialización'    => array( 'Semestre 1: fundamentos y seminarios.', 'Semestre 2: profundización y electivas.', 'Trabajo final aplicado.' ),
	'Maestría'           => array( 'Semestres 1 y 2: cursos de fundamentación.', 'Semestres 3 y 4: profundización y electivas.', 'Trabajo de grado o investigación.' ),
	'Doctorado'          => array( 'Seminarios doctorales y examen de candidatura.', 'Estancia de investigación.', 'Tesis doctoral y sustentación.' ),
	'Educación continua' => array( 'Módulo 1: conceptos básicos.', 'Módulo 2: casos y práctica.', 'Módulo 3: proyecto final.' ),
);

foreach ( $externado_programas as $externado_slug => $externado_p ) {
	list( $externado_title, $externado_excerpt, $externado_nivel, $externado_modalidad, $externado_jornada, $externado_facultad, $externado_titulo, $externado_foco ) = $externado_p;

	$externado_jornadas = (array) $externado_jornada;
	$externado_horario  = array();
	foreach ( $externado_jornadas as $externado_j ) {
		$externado_horario[] = 'jornada ' . mb_strtolower( $externado_j ) . ', ' . $externado_horarios[ $externado_j ];
	}

	$externado_content  = externado_programa_content_markup(
		array(
			'facts'  => array(
				'Nivel'           => $externado_nivel,
				'Modalidad'       => $externado_modalidad,
				'Jornada'         => implode( ' y ', $externado_jornadas ),
				'Duración'        => $externado_duraciones[ $externado_nivel ],
				'Título otorgado' => $externado_titulo,
				'Registro SNIES'  => 'Dato de ejemplo',
			),
			'perfil' => array(
				$externado_excerpt,
				'El programa se centra en ' . $externado_foco . '.',
			),
			'plan'   => $externado_planes[ $externado_nivel ],
			'costos' => array( 'Consulte el valor de la matrícula, las opciones de financiación y las becas con la Oficina de Admisiones.' ),
			'faq'    => array(
				'¿Cuándo son las clases?' => 'En ' . implode( '; o en ', $externado_horario ) . '.',
				'¿Cómo me inscribo?'      => 'Con el formulario de la convocatoria de admisiones 2027, que pide documento de identidad y certificados de estudios.',
			),
		)
	);
	$externado_content .= "\n" . externado_paragraph_markup( 'Programa de ejemplo para la demostración.', 'externado-note' );

	$externado_id = externado_seed_upsert(
		array(
			'post_type'    => 'programa',
			'post_name'    => $externado_slug,
			'post_title'   => $externado_title,
			'post_excerpt' => $externado_excerpt,
			'post_content' => $externado_content,
		)
	);

	if ( $externado_id ) {
		externado_seed_programa_terms(
			$externado_id,
			array(
				'externado_nivel'     => $externado_nivel,
				'externado_modalidad' => $externado_modalidad,
				'externado_jornada'   => $externado_jornadas,
				'externado_facultad'  => $externado_facultad,
			)
		);
	}
}

/*
 * 4. "Buscador de programas" page (target of the ai-finder "Pruébalo tú" button).
 */
externado_seed_upsert(
	array(
		'post_type'    => 'page',
		'post_name'    => 'buscador-de-programas',
		'post_title'   => 'Buscador de programas',
		'post_content' => externado_paragraph_markup( 'Escriba con sus palabras qué le gustaría estudiar y le sugerimos hasta cuatro programas.' )
			. "\n" . '<!-- wp:externado/program-finder {"align":"wide"} /-->' . "\n\n"
			. externado_paragraph_markup( 'Los programas de esta demostración son de ejemplo. Si la IA de WordPress está configurada (Ajustes → Conectores), la respuesta la elige un modelo de lenguaje entre los programas publicados; si no, se usa la búsqueda por palabras.', 'externado-note' ),
	)
);

if ( function_exists( 'externado_finder_bump_rev' ) ) {
	externado_finder_bump_rev();
}
