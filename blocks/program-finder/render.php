<?php
/**
 * Render callback for externado/program-finder.
 *
 * @package Externado
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

externado_finder_init_state();

$externado_finder_id = wp_unique_id( 'externado-finder-' );
$externado_examples  = array(
	__( 'Algo de finanzas que pueda estudiar de noche', 'externado' ),
	__( 'Derecho virtual', 'externado' ),
	__( 'Un posgrado los fines de semana', 'externado' ),
	__( 'Un diplomado corto', 'externado' ),
);
$externado_context   = array(
	'query'        => '',
	'isIdle'       => true,
	'isLoading'    => false,
	'isResults'    => false,
	'isEmpty'      => false,
	'isError'      => false,
	'hasAnswer'    => false,
	'isAi'         => false,
	'showAiHint'   => false,
	'showAiFailed' => false,
	'modeLabel'    => '',
	'countLabel'   => '',
	'errorMessage' => '',
	'canRetry'     => false,
	'results'      => array(),
);
?>
<div
	<?php echo get_block_wrapper_attributes( array( 'class' => 'externado-finder' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-wp-interactive="externado/finder"
	<?php echo wp_interactivity_data_wp_context( $externado_context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
	<form class="externado-finder__form" role="search" action="#" data-wp-on--submit="actions.submit">
		<label class="externado-finder__label" for="<?php echo esc_attr( $externado_finder_id ); ?>"><?php esc_html_e( '¿Qué le gustaría estudiar?', 'externado' ); ?></label>
		<div class="externado-finder__row">
			<input
				id="<?php echo esc_attr( $externado_finder_id ); ?>"
				class="externado-finder__input"
				type="search"
				name="q"
				maxlength="200"
				autocomplete="off"
				placeholder="<?php esc_attr_e( 'Ej.: algo de finanzas que pueda estudiar de noche', 'externado' ); ?>"
				aria-describedby="<?php echo esc_attr( $externado_finder_id ); ?>-privacy"
				data-wp-bind--value="context.query"
				data-wp-on--input="actions.setQuery"
			>
			<button type="submit" class="externado-finder__submit" aria-disabled="false" data-wp-bind--aria-disabled="context.isLoading"><?php esc_html_e( 'Buscar', 'externado' ); ?></button>
		</div>
		<p class="externado-finder__privacy" id="<?php echo esc_attr( $externado_finder_id ); ?>-privacy"><?php esc_html_e( 'No guardamos lo que escribe.', 'externado' ); ?></p>
	</form>

	<div class="externado-finder__examples">
		<p class="externado-finder__examples-label" id="<?php echo esc_attr( $externado_finder_id ); ?>-examples"><?php esc_html_e( 'Pruebe con:', 'externado' ); ?></p>
		<ul class="externado-finder__chips" aria-labelledby="<?php echo esc_attr( $externado_finder_id ); ?>-examples">
			<?php foreach ( $externado_examples as $externado_example ) : ?>
				<li><button type="button" class="externado-finder__chip" data-query="<?php echo esc_attr( $externado_example ); ?>" data-wp-on--click="actions.useExample"><?php echo esc_html( $externado_example ); ?></button></li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="externado-finder__results" aria-live="polite" data-wp-bind--aria-busy="context.isLoading">
		<p class="externado-finder__status" data-wp-bind--hidden="!context.isLoading" hidden><?php esc_html_e( 'Buscando programas…', 'externado' ); ?></p>

		<div class="externado-finder__answer" data-wp-bind--hidden="!context.hasAnswer" hidden>
			<p class="externado-finder__mode">
				<span class="externado-finder__badge" data-wp-class--is-ai="context.isAi" data-wp-text="context.modeLabel"></span>
				<span class="externado-finder__hint" data-wp-bind--hidden="!context.showAiHint" hidden><?php esc_html_e( 'La IA no está configurada; se usó la búsqueda por palabras.', 'externado' ); ?></span>
			<span class="externado-finder__hint" data-wp-bind--hidden="!context.showAiFailed" hidden><?php esc_html_e( 'La IA no respondió esta vez; se usó la búsqueda por palabras.', 'externado' ); ?></span>
			</p>
			<p class="externado-finder__count" data-wp-bind--hidden="!context.isResults" data-wp-text="context.countLabel" hidden></p>
			<p class="externado-finder__empty" data-wp-bind--hidden="!context.isEmpty" hidden><?php esc_html_e( 'No encontramos programas para esa búsqueda. Pruebe con otras palabras.', 'externado' ); ?></p>
			<ul class="externado-finder__list" data-wp-bind--hidden="!context.isResults" hidden>
				<template data-wp-each--item="context.results" data-wp-each-key="context.item.id">
					<li class="externado-finder__card">
						<h3 class="externado-finder__title"><a data-wp-bind--href="context.item.url" data-wp-text="context.item.title"></a></h3>
						<ul class="externado-finder__pills">
							<li class="externado-finder__pill" data-wp-bind--hidden="!context.item.nivel" data-wp-text="context.item.nivel"></li>
							<li class="externado-finder__pill" data-wp-bind--hidden="!context.item.modalidad" data-wp-text="context.item.modalidad"></li>
							<li class="externado-finder__pill" data-wp-bind--hidden="!context.item.jornada" data-wp-text="context.item.jornada"></li>
						</ul>
						<p class="externado-finder__faculty" data-wp-bind--hidden="!context.item.facultad"><?php esc_html_e( 'Facultad:', 'externado' ); ?> <span data-wp-text="context.item.facultad"></span></p>
						<p class="externado-finder__excerpt" data-wp-text="context.item.excerpt"></p>
						<p class="externado-finder__reason" data-wp-bind--hidden="!context.item.reason"><strong><?php esc_html_e( 'Por qué:', 'externado' ); ?></strong> <span data-wp-text="context.item.reason"></span></p>
					</li>
				</template>
			</ul>
		</div>

		<div class="externado-finder__error" data-wp-bind--hidden="!context.isError" hidden>
			<p data-wp-text="context.errorMessage"></p>
			<button type="button" class="externado-finder__retry" data-wp-bind--hidden="!context.canRetry" data-wp-on--click="actions.retry"><?php esc_html_e( 'Reintentar', 'externado' ); ?></button>
		</div>
	</div>
</div>
