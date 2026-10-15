<?php
/**
 * Feature catalog for the "Novedades Gutenberg" showcase.
 *
 * Single source of truth used by the vote block, the summary block,
 * the "Pruébalo tú" block, the chapter patterns and the REST endpoint.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Effort levels (rows of the value-vs-effort matrix).
 *
 * @return array<string, array{label: string, description: string}>
 */
function externado_get_effort_levels() {
	return array(
		'A' => array(
			'label'       => __( 'Listo para usar', 'externado' ),
			'description' => __( 'Viene con WordPress; solo hay que empezar a usarlo.', 'externado' ),
		),
		'B' => array(
			'label'       => __( 'Ajuste del tema', 'externado' ),
			'description' => __( 'Requiere configurar o ampliar el tema.', 'externado' ),
		),
		'C' => array(
			'label'       => __( 'Desarrollo', 'externado' ),
			'description' => __( 'Requiere desarrollo a la medida.', 'externado' ),
		),
	);
}

/**
 * Story chapters, in display order.
 *
 * @return array<string, array{number: int, title: string, intro: string}>
 */
function externado_get_chapters() {
	return array(
		'preparar'  => array(
			'number' => 1,
			'title'  => __( 'Preparar la campaña', 'externado' ),
			'intro'  => __( 'Antes de abrir inscripciones, el equipo arma la página de la convocatoria con secciones aprobadas y estilos institucionales.', 'externado' ),
		),
		'activar'   => array(
			'number' => 2,
			'title'  => __( 'Activar el anuncio', 'externado' ),
			'intro'  => __( 'El banner de inscripciones queda listo pero oculto, con versiones distintas para celular y escritorio.', 'externado' ),
		),
		'revisar'   => array(
			'number' => 3,
			'title'  => __( 'Revisar la convocatoria', 'externado' ),
			'intro'  => __( 'Admisiones, Comunicaciones y la facultad revisan el texto dentro del editor, sin cadenas de correo.', 'externado' ),
		),
		'publicar'  => array(
			'number' => 4,
			'title'  => __( 'Publicar la ficha del programa', 'externado' ),
			'intro'  => __( 'La ficha de cada programa toma sus datos de campos únicos y reutiliza componentes sincronizados.', 'externado' ),
		),
		'difundir'  => array(
			'number' => 5,
			'title'  => __( 'Difundir', 'externado' ),
			'intro'  => __( 'Noticias por facultad y audio de Externado Radio que se publican sin herramientas externas.', 'externado' ),
		),
		'orientar'  => array(
			'number' => 6,
			'title'  => __( 'Inteligencia artificial y práctica', 'externado' ),
			'intro'  => __( 'El equipo redacta noticias desde Claude y las encuentra como borrador en WordPress; cualquiera practica en una copia desechable.', 'externado' ),
		),
		'redactar'  => array(
			'number' => 7,
			'title'  => __( 'Redactar con más herramientas', 'externado' ),
			'intro'  => __( 'Noticias, material académico, experiencias interactivas y la biblioteca de componentes se trabajan con recursos nuevos del editor, sin plugins.', 'externado' ),
		),
		'resultado' => array(
			'number' => 8,
			'title'  => __( 'Resultado', 'externado' ),
			'intro'  => __( 'Sus votos se convierten en una matriz de valor contra esfuerzo: una hoja de ruta priorizada.', 'externado' ),
		),
	);
}

/**
 * Feature catalog.
 *
 * Keys of each item:
 * - title, chapter, effort (A|B|C), what, value.
 * - demo: 'front' (live demo on the page) or 'editor' (step-by-step instructions).
 * - try: target of the "Pruébalo tú" button, or null.
 *     array( 'type' => 'post', 'post_type' => ..., 'slug' => ... ) or
 *     array( 'type' => 'admin', 'path' => ... ).
 * - steps: editor instructions.
 *
 * @return array<string, array<string, mixed>>
 */
function externado_get_features() {
	$convocatoria = array(
		'type'      => 'post',
		'post_type' => 'page',
		'slug'      => 'convocatoria-inscripciones-2027',
	);
	$programa     = array(
		'type'      => 'post',
		'post_type' => 'programa',
		'slug'      => 'derecho',
	);

	return array(
		'zoom-out'          => array(
			'title'   => __( 'Zoom Out', 'externado' ),
			'chapter' => 'preparar',
			'effort'  => 'A',
			'what'    => __( 'Vista alejada del editor que muestra la página como una secuencia de secciones para insertar, mover o reemplazar patrones completos.', 'externado' ),
			'value'   => __( 'Armar una página de campaña en minutos ensamblando secciones aprobadas, sin perderse en bloques individuales.', 'externado' ),
			'demo'    => 'editor',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Abra la página de la convocatoria en el editor, en una pantalla de al menos 960 px de ancho.', 'externado' ),
				__( 'En la barra lateral, pestaña Página, pulse el nombre de la plantilla («Páginas») y active «Mostrar plantilla»: Zoom Out trabaja sobre las secciones de la plantilla.', 'externado' ),
				__( 'Pulse el botón Zoom Out (el ícono de cuadrado junto a la vista previa) o use Cmd/Ctrl + Mayús + 0.', 'externado' ),
				__( 'Arrastre una sección para cambiar su orden o inserte un patrón entre dos secciones.', 'externado' ),
				__( 'Vuelva a la vista normal para editar los textos.', 'externado' ),
			),
		),
		'section-styles'    => array(
			'title'   => __( 'Estilos de sección', 'externado' ),
			'chapter' => 'preparar',
			'effort'  => 'B',
			'what'    => __( 'Variaciones de estilo para Grupos y Columnas que cambian fondo, textos, enlaces y botones de toda una sección con un clic.', 'externado' ),
			'value'   => __( 'Las secciones de campaña conservan la identidad institucional (verde, pizarra, gris) sin que cada editor elija colores a mano.', 'externado' ),
			'demo'    => 'front',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Seleccione un bloque Grupo de la página.', 'externado' ),
				__( 'En la barra lateral, abra la pestaña Estilos.', 'externado' ),
				__( 'Elija Sección verde, Sección pizarra o Sección gris.', 'externado' ),
			),
		),
		'style-book'        => array(
			'title'   => __( 'Style Book', 'externado' ),
			'chapter' => 'preparar',
			'effort'  => 'B',
			'what'    => __( 'Vista del editor del sitio que muestra todos los bloques, colores y tipografías del tema en una sola pantalla.', 'externado' ),
			'value'   => __( 'Revisar de un vistazo la identidad visual antes de lanzar la campaña y detectar inconsistencias.', 'externado' ),
			'demo'    => 'editor',
			// Target site is a classic theme: Style Book only shows there as a read-only view (Apariencia → Diseño).
			'not_applicable' => __( 'No aplica a profundidad: el sitio del Externado usa un tema clásico. Allí el Style Book solo sirve para consultar los estilos (Apariencia → Diseño), no para editarlos.', 'externado' ),
			'try'     => array(
				'type' => 'admin',
				'path' => 'site-editor.php?p=%2Fstyles',
			),
			'steps'   => array(
				__( 'Vaya a Apariencia → Editor → Estilos.', 'externado' ),
				__( 'Pulse el ícono de ojo «Libro de estilos», arriba del panel Estilos.', 'externado' ),
				__( 'Recorra las pestañas: Resumen, Texto, Colores, Tema, Medios.', 'externado' ),
				__( 'Cambie un color o una tipografía en el panel Estilos y vea cómo se actualiza todo el libro.', 'externado' ),
			),
		),
		'visibility'        => array(
			'title'   => __( 'Visibilidad por dispositivo', 'externado' ),
			'chapter' => 'activar',
			'effort'  => 'A',
			'what'    => __( 'Permite ocultar un bloque por completo, o solo en móvil, tableta o escritorio.', 'externado' ),
			'value'   => __( 'Dejar el banner de inscripciones listo pero oculto hasta el día de apertura, y mostrar versiones distintas para celular y escritorio.', 'externado' ),
			'demo'    => 'front',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Seleccione el banner de la convocatoria.', 'externado' ),
				__( 'En la barra de herramientas, abra Opciones → Ocultar.', 'externado' ),
				__( 'Elija ocultarlo en todos los dispositivos o solo en algunos.', 'externado' ),
			),
		),
		'icon'              => array(
			'title'   => __( 'Bloque Ícono', 'externado' ),
			'chapter' => 'activar',
			'effort'  => 'A',
			'what'    => __( 'Inserta íconos SVG con color, tamaño y rotación. Además de la biblioteca de WordPress, desde la 7.1 el tema puede registrar una colección propia que aparece en el mismo selector.', 'externado' ),
			'value'   => __( 'Íconos de marca (birrete, balanza, diploma) disponibles para todo el equipo, sin subir imágenes ni pedir diseños cada vez.', 'externado' ),
			'demo'    => 'front',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Inserte el bloque Ícono.', 'externado' ),
				__( 'Abra la biblioteca de íconos y elija la colección «Externado» en la columna izquierda.', 'externado' ),
				__( 'Ajuste color y tamaño en la barra lateral.', 'externado' ),
			),
		),
		'notes'             => array(
			'title'   => __( 'Notas en bloques', 'externado' ),
			'chapter' => 'revisar',
			'effort'  => 'A',
			'what'    => __( 'Comentarios internos anclados a un bloque, con respuestas y estado resuelto. No se publican.', 'externado' ),
			'value'   => __( 'La revisión de la convocatoria entre Admisiones, Comunicaciones y la facultad ocurre dentro del contenido, no por correo.', 'externado' ),
			'demo'    => 'editor',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Abra la página de la convocatoria en el editor.', 'externado' ),
				__( 'Abra el panel de Notas desde la barra superior: verá la nota sobre el párrafo de fechas.', 'externado' ),
				__( 'Responda la nota o márquela como resuelta.', 'externado' ),
				__( 'Para crear una nueva, seleccione un bloque, abra el menú ⋮ de su barra de herramientas y elija «Añadir nota» (atajo: Cmd/Ctrl + Alt + M).', 'externado' ),
			),
		),
		'revisions'         => array(
			'title'   => __( 'Revisiones visuales', 'externado' ),
			'chapter' => 'revisar',
			'effort'  => 'A',
			'what'    => __( 'Comparación visual entre versiones de una página, que resalta los bloques añadidos, eliminados y modificados.', 'externado' ),
			'value'   => __( 'Ver qué cambió entre la versión aprobada y la actual antes de publicar, y restaurar si hace falta.', 'externado' ),
			'demo'    => 'editor',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Abra la página de la convocatoria y haga un cambio; guarde.', 'externado' ),
				__( 'En la barra lateral de la página, abra Revisiones.', 'externado' ),
				__( 'Desplácese entre versiones y observe los cambios resaltados.', 'externado' ),
			),
		),
		'block-bindings'    => array(
			'title'   => __( 'Block Bindings', 'externado' ),
			'chapter' => 'publicar',
			'effort'  => 'C',
			'what'    => __( 'Conecta párrafos, títulos, imágenes y botones a datos, como los campos personalizados del programa, en lugar de texto fijo.', 'externado' ),
			'value'   => __( 'La ficha de cada programa muestra duración, modalidad, título y SNIES desde campos únicos que se actualizan en un solo lugar.', 'externado' ),
			'demo'    => 'front',
			// Content strategy: move content out of custom fields into blocks, so binding blocks to fields goes against the goal.
			'not_applicable' => __( 'No aplica a la estrategia: el objetivo es dejar de manejar el contenido con campos y editarlo directamente con bloques.', 'externado' ),
			'try'     => $programa,
			'steps'   => array(
				__( 'Abra el programa Derecho en el editor.', 'externado' ),
				__( 'En ⋮ Opciones → Preferencias, active el panel «Campos personalizados»: aparece debajo del contenido con duracion, modalidad, titulo_otorgado y snies.', 'externado' ),
				__( 'Cambie un valor (por ejemplo, duracion a «9 semestres») y pulse Guardar.', 'externado' ),
				__( 'Vea la ficha publicada: la plantilla toma los valores automáticamente.', 'externado' ),
			),
		),
		'pattern-overrides' => array(
			'title'   => __( 'Pattern Overrides', 'externado' ),
			'chapter' => 'publicar',
			'effort'  => 'C',
			'what'    => __( 'Patrones sincronizados en los que algunos campos se pueden cambiar en cada uso, manteniendo el diseño bloqueado.', 'externado' ),
			'value'   => __( 'Tarjetas de docentes y llamados a inscripción idénticos en todo el sitio, con datos propios en cada página.', 'externado' ),
			'demo'    => 'front',
			'try'     => array(
				'type'      => 'post',
				'post_type' => 'wp_block',
				'slug'      => 'tarjeta-docente',
			),
			'steps'   => array(
				__( 'Abra el patrón sincronizado Tarjeta docente.', 'externado' ),
				__( 'Observe los bloques marcados como editables por instancia.', 'externado' ),
				__( 'Inserte el patrón en una página y cambie solo nombre, cargo y área.', 'externado' ),
			),
		),
		'breadcrumbs'       => array(
			'title'   => __( 'Migas de pan', 'externado' ),
			'chapter' => 'publicar',
			'effort'  => 'A',
			'what'    => __( 'Bloque que muestra la ruta de navegación de la página actual.', 'externado' ),
			'value'   => __( 'Orientar al aspirante dentro del sitio sin mantener menús manuales.', 'externado' ),
			'demo'    => 'front',
			'try'     => array(
				'type'      => 'view',
				'post_type' => 'programa',
				'slug'      => 'derecho',
			),
			'steps'   => array(
				__( 'Abra la ficha publicada de Derecho: arriba se ve Inicio › Programas › Derecho.', 'externado' ),
				__( 'Para insertarlo en otra página, agregue el bloque «Migas de pan» y elija el separador.', 'externado' ),
			),
		),
		'tabs'              => array(
			'title'   => __( 'Pestañas', 'externado' ),
			'chapter' => 'publicar',
			'effort'  => 'A',
			'what'    => __( 'Bloque nativo de pestañas, accesible con teclado y lectores de pantalla.', 'externado' ),
			'value'   => __( 'Organizar Perfil, Plan de estudios y Costos en la ficha de programa sin páginas interminables.', 'externado' ),
			'demo'    => 'front',
			'try'     => $programa,
			'steps'   => array(
				__( 'Inserte el bloque Pestañas.', 'externado' ),
				__( 'Escriba el nombre de cada pestaña y su contenido.', 'externado' ),
			),
		),
		'accordion'         => array(
			'title'   => __( 'Acordeón', 'externado' ),
			'chapter' => 'publicar',
			'effort'  => 'A',
			'what'    => __( 'Bloque nativo de contenido plegable con encabezados accesibles.', 'externado' ),
			'value'   => __( 'Preguntas frecuentes de admisiones legibles y ordenadas, también en celular.', 'externado' ),
			'demo'    => 'front',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Inserte el bloque Acordeón.', 'externado' ),
				__( 'Escriba cada pregunta como encabezado y la respuesta en el panel.', 'externado' ),
			),
		),
		'query-grid'        => array(
			'title'   => __( 'Cuadrícula adaptable, Query Loop y Terms Query', 'externado' ),
			'chapter' => 'difundir',
			'effort'  => 'B',
			'what'    => __( 'Listados de entradas en cuadrícula que se adapta al ancho de pantalla, y un bloque nuevo para listar categorías como facultades.', 'externado' ),
			'value'   => __( 'Noticias por facultad y directorios de facultades que se actualizan solos al publicar.', 'externado' ),
			'demo'    => 'front',
			'try'     => array(
				'type'      => 'post',
				'post_type' => 'page',
				'slug'      => 'noticias-por-facultad',
			),
			'steps'   => array(
				__( 'Abra la página «Noticias por facultad» en el editor.', 'externado' ),
				__( 'En la Vista de lista, seleccione «Plantilla de entrada» dentro del bloque Consulta: en la barra lateral cambie las columnas o el ancho mínimo y vea cómo se reacomoda.', 'externado' ),
				__( 'Seleccione el bloque de términos (facultades): en la barra lateral elija qué categorías mostrar y el orden.', 'externado' ),
				__( 'Publique una noticia nueva en una facultad: aparece sola en la cuadrícula y sube el número de esa facultad.', 'externado' ),
			),
		),
		'playlist'          => array(
			'title'   => __( 'Lista de reproducción', 'externado' ),
			'chapter' => 'difundir',
			'effort'  => 'A',
			'what'    => __( 'Bloque que agrupa audios en una lista con reproductor y forma de onda.', 'externado' ),
			'value'   => __( 'Publicar episodios de Externado Radio o pódcast de facultades sin reproductores externos.', 'externado' ),
			'demo'    => 'front',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Inserte el bloque Lista de reproducción.', 'externado' ),
				__( 'Seleccione audios de la biblioteca de medios.', 'externado' ),
			),
		),
		'mcp-claude'        => array(
			'title'      => __( 'Redactar con Claude (MCP)', 'externado' ),
			'chapter'    => 'orientar',
			'effort'     => 'C',
			'what'       => __( 'El sitio declara «habilidades» con la Abilities API de WordPress y el plugin oficial MCP Adapter las expone a asistentes como Claude Desktop. Aquí, la principal: redactar una noticia y dejarla como borrador en WordPress.', 'externado' ),
			'value'      => __( 'Comunicaciones le pide a Claude la noticia de la convocatoria de Derecho y recibe un borrador con título, resumen y párrafos, ya en su categoría. El equipo solo revisa y ajusta: Claude nunca publica y respeta los permisos del usuario.', 'externado' ),
			'demo'       => 'editor',
			'demo_label' => __( 'Así se ve en Claude Desktop (ilustración)', 'externado' ),
			/* translators: %d: number of steps. */
			'steps_label' => __( 'Cómo conectarlo (%d pasos)', 'externado' ),
			'try'        => array(
				'type' => 'admin',
				'path' => 'edit.php?post_status=draft&post_type=post',
			),
			'steps'      => array(
				__( 'Instale el plugin MCP Adapter (oficial de WordPress.org) y registre las habilidades del sitio en el tema o en un plugin.', 'externado' ),
				__( 'En Claude Desktop, agregue el conector del sitio en la configuración y reinicie la aplicación.', 'externado' ),
				__( 'Pídale la noticia con el contexto que tenga, por ejemplo: «Redacta una noticia sobre la apertura de inscripciones 2027 en Derecho y déjala como borrador».', 'externado' ),
				__( 'Revise el borrador en Entradas → Borradores: la publicación siempre la decide una persona.', 'externado' ),
			),
		),
		'playground'        => array(
			'title'   => __( 'WordPress Playground: práctica sin riesgo', 'externado' ),
			'chapter' => 'orientar',
			'effort'  => 'B',
			'what'    => __( 'WordPress completo que corre en el navegador, sin servidor. Un enlace instala este tema y el contenido de demostración, y abre la presentación con sesión iniciada.', 'externado' ),
			'value'   => __( 'Capacitar al equipo editorial y mostrar propuestas al cliente: cada persona tiene su propia copia para probar y equivocarse, que se borra al cerrar la pestaña.', 'externado' ),
			'demo'    => 'front',
			'demo_label' => __( 'Qué incluye el enlace', 'externado' ),
			/* translators: %d: number of steps. */
			'steps_label' => __( 'Cómo usarlo (%d pasos)', 'externado' ),
			'try'     => array(
				'type' => 'url',
				'url'  => EXTERNADO_PLAYGROUND_URL,
			),
			'steps'   => array(
				__( 'Abra el enlace: la primera carga tarda entre 30 y 60 segundos mientras instala WordPress, el tema y el contenido.', 'externado' ),
				__( 'Pruebe cualquier novedad de esta página: edite, borre o rompa lo que quiera; nada afecta al sitio real.', 'externado' ),
				__( 'Para actualizar la copia, se publica el tema en GitHub: el próximo Playground que se abra ya trae los cambios.', 'externado' ),
			),
		),
		'interactivity'     => array(
			'title'   => __( 'Interactivity API', 'externado' ),
			'chapter' => 'redactar',
			'effort'  => 'C',
			'what'    => __( 'Estándar de WordPress para agregar interactividad a los bloques (filtros, votaciones, buscadores) sin recargar la página.', 'externado' ),
			'value'   => __( 'Buscadores de programas, filtros de agenda o votaciones como esta, rápidos y compatibles con el editor.', 'externado' ),
			'demo'    => 'front',
			'try'     => null,
			'steps'   => array(),
		),
		'block-options'     => array(
			'title'   => __( 'Nuevas opciones de bloque', 'externado' ),
			'chapter' => 'redactar',
			'effort'  => 'A',
			'what'    => __( 'Opciones nuevas en párrafos, títulos y grupos: texto que se estira al ancho del contenedor, columnas de texto, sangría de párrafo y degradado de fondo.', 'externado' ),
			'value'   => __( 'Titulares de campaña y textos con aire editorial hechos por el propio equipo, sin pedir CSS ni bloques a la medida.', 'externado' ),
			'demo'    => 'front',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Seleccione un título: en Tipografía active «Ajustar texto al ancho» y cambie el ancho del contenedor.', 'externado' ),
				__( 'Seleccione un párrafo: en Tipografía elija 2 columnas o una sangría de primera línea.', 'externado' ),
				__( 'Seleccione un grupo: en Fondo elija un degradado; puede combinarse con una imagen.', 'externado' ),
			),
		),
		'time-to-read'      => array(
			'title'   => __( 'Tiempo de lectura', 'externado' ),
			'chapter' => 'redactar',
			'effort'  => 'A',
			'what'    => __( 'Bloque que calcula solo el tiempo de lectura de una entrada (como rango o fijo) o su número de palabras.', 'externado' ),
			'value'   => __( 'Las noticias largas avisan cuánto toman y el equipo editorial controla la extensión sin contar a mano.', 'externado' ),
			'demo'    => 'front',
			'try'     => array(
				'type'      => 'post',
				'post_type' => 'page',
				'slug'      => 'noticias-por-facultad',
			),
			'steps'   => array(
				__( 'Abra «Noticias por facultad» y seleccione «1–2 minutos» dentro de la cuadrícula.', 'externado' ),
				__( 'En la barra lateral, cambie entre tiempo de lectura y número de palabras, o entre rango y valor fijo.', 'externado' ),
			),
		),
		'math'              => array(
			'title'   => __( 'Fórmulas matemáticas', 'externado' ),
			'chapter' => 'redactar',
			'effort'  => 'A',
			'what'    => __( 'Bloque Math y formato en línea que escriben fórmulas en LaTeX y las muestran como notación matemática accesible.', 'externado' ),
			'value'   => __( 'Economía, Finanzas y Contaduría publican fórmulas de clase y de investigación como texto, no como imágenes que no se pueden editar ni leer en voz alta.', 'externado' ),
			'demo'    => 'front',
			'try'     => $convocatoria,
			'steps'   => array(
				__( 'Inserte el bloque «Math» y escriba la fórmula en LaTeX, por ejemplo \frac{a}{b}.', 'externado' ),
				__( 'Dentro de un párrafo, seleccione un texto y aplique el formato «Math» desde la barra de herramientas.', 'externado' ),
			),
		),
		'dataviews'         => array(
			'title'   => __( 'Data Views: biblioteca de patrones', 'externado' ),
			'chapter' => 'redactar',
			'effort'  => 'A',
			'what'    => __( 'Pantallas de administración renovadas con vista en cuadrícula, tabla o lista, búsqueda, filtros y acciones en lote. En un tema clásico se ven en Apariencia → Patrones.', 'externado' ),
			'value'   => __( 'Encontrar en segundos el patrón aprobado (CTA, tarjeta docente, banner) entre muchos, filtrando por categoría o estado de sincronización.', 'externado' ),
			'demo'    => 'editor',
			'try'     => array(
				'type' => 'admin',
				'path' => 'site-editor.php?p=%2Fpattern',
			),
			'steps'   => array(
				__( 'Abra la biblioteca de patrones: en esta demo, Apariencia → Editor → Patrones; en un tema clásico, Apariencia → Diseño → Patrones (o Apariencia → Patrones).', 'externado' ),
				__( 'Cambie la vista entre cuadrícula y tabla con el botón de vista, arriba a la derecha.', 'externado' ),
				__( 'Busque «tarjeta» o filtre por «Sincronizado» para ver solo los componentes compartidos.', 'externado' ),
			),
		),
	);
}

/**
 * Get one feature by ID.
 *
 * @param string $id Feature ID.
 * @return array<string, mixed>|null
 */
function externado_get_feature( $id ) {
	$features = externado_get_features();

	return isset( $features[ $id ] ) ? $features[ $id ] : null;
}

/**
 * Whether a feature ID exists in the catalog.
 *
 * @param mixed $id Feature ID.
 * @return bool
 */
function externado_is_valid_feature( $id ) {
	return is_string( $id ) && array_key_exists( $id, externado_get_features() );
}

/**
 * Find a post ID by slug and post type (any status).
 *
 * @param string $slug      Post slug.
 * @param string $post_type Post type.
 * @return int Post ID, or 0 when not found.
 */
function externado_get_post_id_by_slug( $slug, $post_type = 'page' ) {
	$ids = get_posts(
		array(
			'name'                   => $slug,
			'post_type'              => $post_type,
			'post_status'            => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'suppress_filters'       => true,
		)
	);

	return $ids ? (int) $ids[0] : 0;
}

/**
 * Resolve the "Pruébalo tú" URL for a feature for the current user.
 *
 * @param string $id Feature ID.
 * @return string Admin URL, or empty string when there is no target or the user cannot edit it.
 */
function externado_get_feature_try_url( $id ) {
	$feature = externado_get_feature( $id );

	if ( ! $feature || empty( $feature['try'] ) ) {
		return '';
	}

	$target = $feature['try'];

	if ( 'admin' === $target['type'] ) {
		return current_user_can( 'edit_theme_options' ) ? admin_url( $target['path'] ) : '';
	}

	// External link open to everyone (e.g. the public Playground copy).
	if ( 'url' === $target['type'] ) {
		return (string) $target['url'];
	}

	$post_id = externado_get_post_id_by_slug( $target['slug'], $target['post_type'] );

	// Front-end features (e.g. breadcrumbs) are best tried on the published page itself.
	if ( 'view' === $target['type'] ) {
		return $post_id ? (string) get_permalink( $post_id ) : '';
	}

	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		return '';
	}

	return admin_url( 'post.php?post=' . $post_id . '&action=edit' );
}

/**
 * Build the block markup for one feature unit.
 *
 * @param string $id   Feature ID.
 * @param string $demo Block markup for the live demo (front-end features).
 * @return string Serialized block markup.
 */
function externado_feature_unit_markup( $id, $demo = '' ) {
	$feature = externado_get_feature( $id );

	if ( ! $feature ) {
		return '';
	}

	$efforts = externado_get_effort_levels();
	$effort  = $feature['effort'];
	$classes = empty( $feature['not_applicable'] ) ? 'externado-feature' : 'externado-feature is-not-applicable';
	$attrs   = wp_json_encode(
		array(
			'tagName'   => 'article',
			'anchor'    => 'funcionalidad-' . $id,
			'metadata'  => array( 'name' => $feature['title'] ),
			'className' => $classes,
			'layout'    => array( 'type' => 'default' ),
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);

	$out  = '<!-- wp:group ' . $attrs . ' -->' . "\n";
	$out .= '<article id="funcionalidad-' . esc_attr( $id ) . '" class="wp-block-group ' . esc_attr( $classes ) . '">';

	if ( ! empty( $feature['not_applicable'] ) ) {
		$out .= '<!-- wp:paragraph {"className":"externado-feature__na"} -->' . "\n";
		$out .= '<p class="externado-feature__na">' . esc_html( $feature['not_applicable'] ) . '</p>' . "\n";
		$out .= '<!-- /wp:paragraph -->' . "\n";
	}

	// Reading order follows the decision: name, why it matters, what it is, see it, then cost and vote.
	$out .= '<!-- wp:heading {"level":3,"className":"externado-feature__title"} -->' . "\n";
	$out .= '<h3 class="wp-block-heading externado-feature__title">' . esc_html( $feature['title'] ) . '</h3>' . "\n";
	$out .= '<!-- /wp:heading -->' . "\n";

	$out .= '<!-- wp:paragraph {"className":"externado-feature__value"} -->' . "\n";
	$out .= '<p class="externado-feature__value">' . esc_html( $feature['value'] ) . '</p>' . "\n";
	$out .= '<!-- /wp:paragraph -->' . "\n";

	$out .= '<!-- wp:paragraph {"className":"externado-feature__what"} -->' . "\n";
	$out .= '<p class="externado-feature__what"><strong>' . esc_html__( 'Qué es:', 'externado' ) . '</strong> ' . esc_html( $feature['what'] ) . '</p>' . "\n";
	$out .= '<!-- /wp:paragraph -->' . "\n";

	$try_block = '<!-- wp:externado/try-it {"feature":"' . esc_attr( $id ) . '"} /-->' . "\n";

	$is_front = 'front' === $feature['demo'];

	// Demo stage: a live demo (front-end features) or a static illustration of the editor UI.
	if ( '' !== $demo ) {
		$stage_class = $is_front ? 'externado-feature__demo' : 'externado-feature__demo externado-feature__demo--mock';
		$label_class = $is_front ? 'externado-feature__label' : 'externado-feature__label is-mock';
		$label       = $is_front ? __( 'En vivo', 'externado' ) : __( 'Así se ve en el editor (ilustración)', 'externado' );
		// Illustrations of tools outside the editor (e.g. Claude Desktop) name their own surface.
		if ( ! empty( $feature['demo_label'] ) ) {
			$label = $feature['demo_label'];
		}

		$out .= '<!-- wp:group {"className":"' . $stage_class . '","layout":{"type":"default"}} -->' . "\n";
		$out .= '<div class="wp-block-group ' . $stage_class . '">';
		$out .= '<!-- wp:paragraph {"className":"' . $label_class . '"} -->' . "\n";
		$out .= '<p class="' . $label_class . '">' . esc_html( $label ) . '</p>' . "\n";
		$out .= '<!-- /wp:paragraph -->' . "\n";
		$out .= $demo . "\n";
		$out .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
	}

	// "Pruébalo tú" always sits at the end of the steps it belongs to, so its destination is explained.
	if ( ! empty( $feature['steps'] ) ) {
		$summary = ! empty( $feature['steps_label'] )
			? sprintf( $feature['steps_label'], count( $feature['steps'] ) )
			: ( $is_front
			? sprintf(
				/* translators: %d: number of steps. */
				_n( 'Cómo probarla en el editor (%d paso)', 'Cómo probarla en el editor (%d pasos)', count( $feature['steps'] ), 'externado' ),
				count( $feature['steps'] )
			)
			: sprintf(
				/* translators: %d: number of steps. */
				_n( 'Se ve en el editor · cómo probarla (%d paso)', 'Se ve en el editor · cómo probarla (%d pasos)', count( $feature['steps'] ), 'externado' ),
				count( $feature['steps'] )
			) );
		$out .= '<!-- wp:details {"summary":"' . esc_attr( $summary ) . '","className":"externado-feature__howto"} -->' . "\n";
		$out .= '<details class="wp-block-details externado-feature__howto"><summary>' . esc_html( $summary ) . '</summary>';
		$out .= '<!-- wp:list {"ordered":true} -->' . "\n" . '<ol class="wp-block-list">';
		foreach ( $feature['steps'] as $step ) {
			$out .= '<!-- wp:list-item -->' . "\n" . '<li>' . esc_html( $step ) . '</li>' . "\n" . '<!-- /wp:list-item -->' . "\n";
		}
		$out .= '</ol>' . "\n" . '<!-- /wp:list -->' . "\n";
		$out .= $try_block;
		$out .= '</details>' . "\n" . '<!-- /wp:details -->' . "\n";
	} else {
		$out .= $try_block;
	}

	$out .= '<!-- wp:group {"className":"externado-feature__decision","layout":{"type":"default"}} -->' . "\n";
	$out .= '<div class="wp-block-group externado-feature__decision">';
	$out .= '<!-- wp:paragraph {"className":"externado-feature__effort"} -->' . "\n";
	$out .= '<p class="externado-feature__effort"><span class="externado-badge externado-badge--' . esc_attr( strtolower( $effort ) ) . '">' . esc_html( $effort ) . '</span> <strong>' . esc_html( $efforts[ $effort ]['label'] ) . '.</strong> ' . esc_html( $efforts[ $effort ]['description'] ) . '</p>' . "\n";
	$out .= '<!-- /wp:paragraph -->' . "\n";
	$out .= '<!-- wp:externado/feature-vote {"feature":"' . esc_attr( $id ) . '"} /-->' . "\n";
	$out .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

	$out .= '</article>' . "\n" . '<!-- /wp:group -->' . "\n";

	return $out;
}

/**
 * Build the opening markup of a chapter section.
 *
 * @param string $chapter_id Chapter ID.
 * @param string $style      Section style slug (empty for none).
 * @return string Serialized block markup (opening part, includes heading and intro).
 */
function externado_chapter_open_markup( $chapter_id, $style = '' ) {
	$chapters = externado_get_chapters();

	if ( ! isset( $chapters[ $chapter_id ] ) ) {
		return '';
	}

	$chapter = $chapters[ $chapter_id ];
	$classes = trim( 'externado-chapter ' . ( $style ? 'is-style-' . $style : '' ) );
	$attrs   = wp_json_encode(
		array(
			'tagName'   => 'section',
			'anchor'    => 'capitulo-' . $chapter_id,
			'metadata'  => array( 'name' => $chapter['title'] ),
			'align'     => 'full',
			'className' => $classes,
			'layout'    => array( 'type' => 'constrained' ),
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);

	$out  = '<!-- wp:group ' . $attrs . ' -->' . "\n";
	$out .= '<section id="capitulo-' . esc_attr( $chapter_id ) . '" class="wp-block-group alignfull ' . esc_attr( $classes ) . '">';
	// The number matches the stop in the hero route, so it lives inside the heading instead of a label above it.
	$out .= '<!-- wp:heading {"className":"externado-chapter__title"} -->' . "\n";
	$out .= '<h2 class="wp-block-heading externado-chapter__title"><span class="externado-chapter__num"><span class="screen-reader-text">' . esc_html__( 'Capítulo', 'externado' ) . ' </span>' . (int) $chapter['number'] . '</span> ' . esc_html( $chapter['title'] ) . '</h2>' . "\n";
	$out .= '<!-- /wp:heading -->' . "\n";
	$out .= '<!-- wp:paragraph {"className":"externado-chapter__intro"} -->' . "\n";
	$out .= '<p class="externado-chapter__intro">' . esc_html( $chapter['intro'] ) . '</p>' . "\n";
	$out .= '<!-- /wp:paragraph -->' . "\n";

	return $out;
}

/**
 * Closing markup for a chapter section.
 *
 * @return string
 */
function externado_chapter_close_markup() {
	return '</section>' . "\n" . '<!-- /wp:group -->' . "\n";
}

/**
 * Wrap feature units in a responsive grid group.
 *
 * @param string $inner Inner markup.
 * @return string
 */
function externado_feature_grid_markup( $inner, $variant = '' ) {
	// Two features share the row in halves; five go 2 + 3 so no column is left empty.
	// 'lead': the first feature takes the full row (its demo needs the width), the rest go in halves.
	$count   = substr_count( $inner, '<article ' );
	$classes = 'externado-feature-grid';
	if ( 'bookends' === $variant ) {
		// First and last features span the row; the ones in between share it in thirds.
		$classes .= ' externado-feature-grid--bookends';
	} elseif ( 'lead' === $variant ) {
		// The features after the lead share the next row: halves for two, thirds for three.
		$classes .= 4 === $count ? ' externado-feature-grid--lead externado-feature-grid--lead-three' : ' externado-feature-grid--lead';
	} elseif ( 2 === $count ) {
		$classes .= ' externado-feature-grid--pair';
	} elseif ( 5 === $count ) {
		$classes .= ' externado-feature-grid--five';
	}

	$out  = '<!-- wp:group {"align":"wide","className":"' . $classes . '","layout":{"type":"grid","minimumColumnWidth":"20rem"}} -->' . "\n";
	$out .= '<div class="wp-block-group alignwide ' . $classes . '">';
	$out .= $inner;
	$out .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

	return $out;
}

/**
 * Serialized core/block reference to a synced pattern found by slug.
 *
 * @param string $slug    Synced pattern slug.
 * @param array  $content Pattern overrides keyed by block name.
 * @return string Block markup, or empty string if the pattern does not exist.
 */
function externado_synced_pattern_markup( $slug, $content = array() ) {
	$ref = externado_get_post_id_by_slug( $slug, 'wp_block' );

	if ( ! $ref ) {
		return '';
	}

	$attrs = array( 'ref' => $ref );

	if ( $content ) {
		$attrs['content'] = $content;
	}

	return '<!-- wp:block ' . wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ' /-->';
}
