<?php
/**
 * Helpers that build serialized block markup for core blocks.
 *
 * The markup mirrors each block's save() output so it validates in the editor.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Paragraph block.
 *
 * @param string $html      Inner HTML (already escaped / trusted).
 * @param string $class_name Optional class name.
 * @return string
 */
function externado_paragraph_markup( $html, $class_name = '' ) {
	if ( $class_name ) {
		return '<!-- wp:paragraph {"className":"' . esc_attr( $class_name ) . '"} -->' . "\n" . '<p class="' . esc_attr( $class_name ) . '">' . $html . '</p>' . "\n" . '<!-- /wp:paragraph -->' . "\n";
	}

	return '<!-- wp:paragraph -->' . "\n" . '<p>' . $html . '</p>' . "\n" . '<!-- /wp:paragraph -->' . "\n";
}

/**
 * Core Tabs block (tabs > tab-list + tab-panels > tab-panel).
 *
 * @param array<string, string> $tabs Tab label => inner block markup.
 * @return string
 */
function externado_tabs_markup( $tabs ) {
	$out  = '<!-- wp:tabs -->' . "\n" . '<div class="wp-block-tabs">';
	$out .= '<!-- wp:tab-list -->' . "\n" . '<div role="tablist" class="wp-block-tab-list">';
	foreach ( array_keys( $tabs ) as $label ) {
		$out .= '<button type="button" role="tab">' . esc_html( $label ) . '</button>';
	}
	$out .= '</div>' . "\n" . '<!-- /wp:tab-list -->' . "\n\n";
	$out .= '<!-- wp:tab-panels -->' . "\n" . '<div class="wp-block-tab-panels">';
	foreach ( $tabs as $label => $inner ) {
		$out .= '<!-- wp:tab-panel ' . wp_json_encode( array( 'label' => $label ), JSON_UNESCAPED_UNICODE ) . ' -->' . "\n";
		$out .= '<section role="tabpanel" tabindex="0" class="wp-block-tab-panel">' . $inner . '</section>' . "\n";
		$out .= '<!-- /wp:tab-panel -->' . "\n";
	}
	$out .= '</div>' . "\n" . '<!-- /wp:tab-panels --></div>' . "\n" . '<!-- /wp:tabs -->' . "\n";

	return $out;
}

/**
 * Core Accordion block.
 *
 * @param array<string, string> $items Question => answer markup (inner blocks).
 * @return string
 */
function externado_accordion_markup( $items, $open_first = false ) {
	$out   = '<!-- wp:accordion -->' . "\n" . '<div role="group" class="wp-block-accordion">';
	$first = true;
	foreach ( $items as $question => $answer ) {
		// openByDefault is a native accordion-item attribute; the server renders the open state.
		$out  .= ( $open_first && $first ) ? '<!-- wp:accordion-item {"openByDefault":true} -->' : '<!-- wp:accordion-item -->';
		$out  .= "\n" . '<div class="wp-block-accordion-item">';
		$first = false;
		$out .= '<!-- wp:accordion-heading -->' . "\n";
		$out .= '<h3 class="wp-block-accordion-heading has-icon has-icon-right"><button type="button" class="wp-block-accordion-heading__toggle"><span class="wp-block-accordion-heading__toggle-title">' . esc_html( $question ) . '</span><span class="wp-block-accordion-heading__toggle-icon" aria-hidden="true">+</span></button></h3>' . "\n";
		$out .= '<!-- /wp:accordion-heading -->' . "\n\n";
		$out .= '<!-- wp:accordion-panel -->' . "\n" . '<div role="region" class="wp-block-accordion-panel">' . $answer . '</div>' . "\n" . '<!-- /wp:accordion-panel --></div>' . "\n";
		$out .= '<!-- /wp:accordion-item -->' . "\n";
	}
	$out .= '</div>' . "\n" . '<!-- /wp:accordion -->' . "\n";

	return $out;
}

/**
 * Faculty news: a Query Loop in a responsive grid plus a Terms Query of the faculty categories.
 *
 * Used by the Novedades page (chapter 5) and by the "Noticias por facultad" demo page,
 * where both blocks can be edited in the post editor.
 *
 * @param bool $with_label Show the sub-label above the Terms Query.
 * @return string Serialized block markup.
 */
function externado_faculty_news_markup( $with_label = true ) {
	$faculty_ids = array();
	foreach ( array( 'facultad-derecho', 'facultad-economia', 'facultad-administracion', 'facultad-comunicacion', 'facultad-ciencias-sociales' ) as $slug ) {
		$term = get_term_by( 'slug', $slug, 'category' );
		if ( $term ) {
			$faculty_ids[] = (int) $term->term_id;
		}
	}

	$query_args = array(
		'perPage'  => 4,
		'pages'    => 0,
		'offset'   => 0,
		'postType' => 'post',
		'order'    => 'desc',
		'orderBy'  => 'date',
		'author'   => '',
		'search'   => '',
		'exclude'  => array(),
		'sticky'   => '',
		'inherit'  => false,
	);
	if ( $faculty_ids ) {
		// Only faculty news.
		$query_args['taxQuery'] = array( 'include' => array( 'category' => $faculty_ids ) );
	}

	$out  = '<!-- wp:query ' . wp_json_encode(
		array(
			'queryId'   => 50,
			'query'     => $query_args,
			'className' => 'externado-news-grid',
		)
	) . ' -->' . "\n";
	$out .= '<div class="wp-block-query externado-news-grid"><!-- wp:post-template {"layout":{"type":"grid","minimumColumnWidth":"11rem"}} -->' . "\n";
	$out .= '<!-- wp:post-terms {"term":"category","className":"externado-news-grid__terms"} /-->' . "\n\n";
	$out .= '<!-- wp:post-title {"level":4,"isLink":true,"className":"externado-news-grid__title"} /-->' . "\n\n";
	$out .= '<!-- wp:post-time-to-read {"className":"externado-news-grid__time"} /-->' . "\n";
	$out .= '<!-- /wp:post-template --></div>' . "\n" . '<!-- /wp:query -->' . "\n";

	if ( ! $faculty_ids ) {
		return $out;
	}

	$term_query = array(
		'perPage'    => 10,
		'taxonomy'   => 'category',
		'order'      => 'asc',
		'orderBy'    => 'name',
		'include'    => $faculty_ids,
		'hideEmpty'  => true,
		'showNested' => false,
		'inherit'    => false,
	);

	if ( $with_label ) {
		$out .= externado_paragraph_markup( esc_html__( 'Facultades (Terms Query), con su número de noticias', 'externado' ), 'externado-feature__sublabel externado-terms__label' );
	}
	$out .= '<!-- wp:terms-query ' . wp_json_encode( array( 'termQuery' => $term_query, 'className' => 'externado-terms' ) ) . ' -->' . "\n";
	$out .= '<div class="wp-block-terms-query externado-terms"><!-- wp:term-template {"layout":{"type":"flex","flexWrap":"wrap"}} -->' . "\n";
	$out .= '<!-- wp:group {"className":"externado-terms__item","layout":{"type":"flex","flexWrap":"nowrap"}} -->' . "\n";
	$out .= '<div class="wp-block-group externado-terms__item"><!-- wp:term-name {"isLink":true} /-->' . "\n\n" . '<!-- wp:term-count /--></div>' . "\n" . '<!-- /wp:group -->' . "\n";
	$out .= '<!-- /wp:term-template --></div>' . "\n" . '<!-- /wp:terms-query -->' . "\n";

	return $out;
}

/**
 * Key data rows of a programa, each value bound to post meta (Block Bindings).
 *
 * @param bool $show_source Show the meta key each value comes from (used by the Novedades demo).
 * @return string
 */
function externado_programa_facts_markup( $show_source = false ) {
	$out  = '<!-- wp:group {"className":"externado-facts","layout":{"type":"grid","minimumColumnWidth":"10rem"}} -->' . "\n";
	$out .= '<div class="wp-block-group externado-facts">';
	foreach ( externado_get_programa_meta_keys() as $key => $label ) {
		$out .= '<!-- wp:group {"className":"externado-facts__item","layout":{"type":"default"}} -->' . "\n";
		$out .= '<div class="wp-block-group externado-facts__item">';
		$out .= externado_paragraph_markup( esc_html( $label ), 'externado-facts__label' );
		$out .= '<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"' . esc_attr( $key ) . '"}}}},"className":"externado-facts__value"} -->' . "\n";
		$out .= '<p class="externado-facts__value"></p>' . "\n";
		$out .= '<!-- /wp:paragraph -->' . "\n";
		if ( $show_source ) {
			$out .= externado_paragraph_markup( esc_html__( 'campo', 'externado' ) . ' <code>' . esc_html( $key ) . '</code>', 'externado-facts__source' );
		}
		$out .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";
	}
	$out .= '</div>' . "\n" . '<!-- /wp:group -->' . "\n";

	return $out;
}
