<?php
/**
 * Title: Novedades Gutenberg: capítulo 5, Difundir
 * Slug: externado/novedades-difundir
 * Categories: externado-novedades
 * Inserter: no
 * Description: Query Loop en cuadrícula, Terms Query y lista de reproducción.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Query Loop with a responsive grid + Terms Query with the faculty categories (shared with the demo page).
$externado_query = externado_faculty_news_markup();

// Playlist with the demo audio attachments created by bin/seed.php.
$externado_tracks = '';
$externado_names  = array(
	'externado-radio-1' => array( __( 'Abren las inscripciones 2027', 'externado' ), '0:06' ),
	'externado-radio-2' => array( __( 'Así es la vida en el campus', 'externado' ), '0:06' ),
	'externado-radio-3' => array( __( 'Consejos para la entrevista', 'externado' ), '0:06' ),
);
foreach ( $externado_names as $externado_slug => $externado_track ) {
	$externado_ids = get_posts(
		array(
			'name'           => $externado_slug,
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( ! $externado_ids ) {
		continue;
	}
	$externado_tracks .= '<!-- wp:playlist-track ' . wp_json_encode(
		array(
			'id'     => (int) $externado_ids[0],
			'src'    => wp_get_attachment_url( $externado_ids[0] ),
			'title'  => $externado_track[0],
			'artist' => 'Externado Radio',
			'album'  => __( 'Inscripciones 2027', 'externado' ),
			'length' => $externado_track[1],
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	) . ' /-->' . "\n";
}

if ( $externado_tracks ) {
	$externado_playlist = '<!-- wp:playlist -->' . "\n" . '<figure class="wp-block-playlist"><ol class="wp-block-playlist__tracklist wp-block-playlist__tracklist-show-numbers">' . $externado_tracks . '</ol></figure>' . "\n" . '<!-- /wp:playlist -->' . "\n";
} else {
	$externado_playlist = externado_paragraph_markup( esc_html__( 'Los audios de demostración aún no existen. Ejecute el script de contenido de demostración.', 'externado' ), 'externado-note' );
}

$externado_units  = externado_feature_unit_markup( 'query-grid', $externado_query );
$externado_units .= externado_feature_unit_markup( 'playlist', $externado_playlist );

echo externado_chapter_open_markup( 'difundir' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_feature_grid_markup( $externado_units, 'lead' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo externado_chapter_close_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
