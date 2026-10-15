<?php
/**
 * Render callback for externado/vote-summary.
 *
 * Value-vs-effort matrix (rows = effort A/B/C, columns = interest) and a
 * ranked list. Shares the `externado/votes` store with the vote blocks, so
 * it updates live after voting on the same page.
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

// Demo mode: results are public so several browsers can watch them update live.
externado_votes_init_state();

$externado_features = externado_get_features();
$externado_efforts  = externado_get_effort_levels();
$externado_by_level = array();
foreach ( $externado_features as $externado_id => $externado_feature ) {
	$externado_by_level[ $externado_feature['effort'] ][ $externado_id ] = $externado_feature;
}

/**
 * Print the chips of one matrix cell.
 *
 * @param array  $features Features of the row.
 * @param string $hidden   Directive used to hide the chip in this column.
 */
$externado_print_chips = static function ( $features, $hidden ) {
	// data-empty is shown by CSS when every chip in the cell is hidden.
	echo '<ul class="externado-matrix__chips" data-empty="' . esc_attr__( 'Ninguna por ahora', 'externado' ) . '">';
	foreach ( $features as $id => $feature ) {
		printf(
			'<li class="externado-chip" %1$s data-wp-bind--hidden="%2$s"><span class="externado-chip__title">%3$s</span> <span class="externado-chip__count"><span data-wp-text="state.yesCount"></span> %4$s · <span data-wp-text="state.noCount"></span> %5$s</span></li>' . "\n",
			wp_interactivity_data_wp_context( array( 'feature' => $id ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_attr( $hidden ),
			esc_html( $feature['title'] ),
			esc_html__( 'a favor', 'externado' ),
			esc_html__( 'en contra', 'externado' )
		);
	}
	echo '</ul>';
};
?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-wp-interactive="externado/votes" data-wp-init="callbacks.startLive">
	<div class="externado-summary__status">
		<p class="externado-live" data-wp-class--is-live="state.isLive" role="status">
			<span class="externado-live__dot" aria-hidden="true"></span>
			<span data-wp-bind--hidden="!state.isLive" hidden><?php esc_html_e( 'En vivo: los votos de todos los navegadores se suman cada 3 segundos.', 'externado' ); ?></span>
			<span data-wp-bind--hidden="state.isLive"><?php esc_html_e( 'Conectando con los votos en vivo…', 'externado' ); ?></span>
		</p>
		<?php if ( current_user_can( 'manage_options' ) ) : ?>
			<?php // Reset control: inline confirmation, working and done states, all driven by the shared store. ?>
			<div class="externado-reset" role="group" aria-label="<?php esc_attr_e( 'Reiniciar la votación', 'externado' ); ?>">
				<button type="button" class="externado-reset__start" data-wp-on--click="actions.askReset" data-wp-bind--hidden="!state.isResetIdle">
					<?php esc_html_e( 'Reiniciar votación', 'externado' ); ?>
				</button>
				<span class="externado-reset__confirm" data-wp-bind--hidden="!state.isResetConfirm" hidden>
					<span class="externado-reset__question">
						<?php esc_html_e( '¿Borrar los', 'externado' ); ?> <strong data-wp-text="state.totalVotes"></strong> <?php esc_html_e( 'votos de todos los navegadores?', 'externado' ); ?>
					</span>
					<button type="button" class="externado-reset__yes" data-wp-on--click="actions.confirmReset"><?php esc_html_e( 'Sí, reiniciar', 'externado' ); ?></button>
					<button type="button" class="externado-reset__no" data-wp-on--click="actions.cancelReset"><?php esc_html_e( 'Cancelar', 'externado' ); ?></button>
				</span>
				<span class="externado-reset__working" data-wp-bind--hidden="!state.isResetWorking" hidden><?php esc_html_e( 'Reiniciando…', 'externado' ); ?></span>
				<span class="externado-reset__done" role="status" data-wp-bind--hidden="!state.isResetDone" hidden><?php esc_html_e( 'Listo: la votación quedó en cero en todos los navegadores.', 'externado' ); ?></span>
				<span class="externado-reset__error" role="alert" data-wp-bind--hidden="!state.resetError" hidden><?php esc_html_e( 'No se pudo reiniciar. Intente de nuevo.', 'externado' ); ?></span>
			</div>
		<?php endif; ?>
	</div>
	<div class="externado-summary__panel">
		<h3 class="externado-summary__heading"><?php esc_html_e( 'Matriz de valor contra esfuerzo', 'externado' ); ?></h3>
		<p class="externado-summary__help"><?php esc_html_e( 'Una novedad pasa a «El equipo lo quiere» cuando tiene más votos «Me interesa» que «No por ahora». Las filas ordenan por esfuerzo: arriba lo que viene listo, abajo lo que requiere desarrollo.', 'externado' ); ?></p>
		<table class="externado-matrix">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Esfuerzo', 'externado' ); ?></th>
					<th scope="col"><?php esc_html_e( 'El equipo lo quiere', 'externado' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Por ahora: sin mayoría a favor', 'externado' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $externado_efforts as $externado_level => $externado_effort ) : ?>
					<tr class="externado-matrix__row externado-matrix__row--<?php echo esc_attr( strtolower( $externado_level ) ); ?>">
						<th scope="row">
							<span class="externado-badge externado-badge--<?php echo esc_attr( strtolower( $externado_level ) ); ?>"><?php echo esc_html( $externado_level ); ?></span>
							<span class="externado-matrix__level"><?php echo esc_html( $externado_effort['label'] ); ?></span>
							<span class="externado-matrix__desc"><?php echo esc_html( $externado_effort['description'] ); ?></span>
						</th>
						<td class="externado-matrix__cell externado-matrix__cell--priority<?php echo 'A' === $externado_level ? ' is-start' : ''; ?>" data-label="<?php esc_attr_e( 'El equipo lo quiere', 'externado' ); ?>">
							<?php if ( 'A' === $externado_level ) : ?>
								<p class="externado-matrix__start"><?php esc_html_e( 'Empiece aquí: mucho valor, poco esfuerzo', 'externado' ); ?></p>
							<?php endif; ?>
							<?php $externado_print_chips( $externado_by_level[ $externado_level ] ?? array(), 'state.isNotPriority' ); ?>
						</td>
						<td class="externado-matrix__cell" data-label="<?php esc_attr_e( 'Por ahora: sin mayoría a favor', 'externado' ); ?>">
							<?php $externado_print_chips( $externado_by_level[ $externado_level ] ?? array(), 'state.isPriority' ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="externado-summary__panel">
		<h3 class="externado-summary__heading"><?php esc_html_e( 'Ranking del equipo', 'externado' ); ?></h3>
		<ol class="externado-ranking">
			<template data-wp-each--item="state.ranking" data-wp-each-key="context.item.id">
				<li class="externado-ranking__item">
					<span class="externado-ranking__title" data-wp-text="context.item.title"></span>
					<span class="externado-ranking__meta">
						<span class="externado-badge" data-wp-class--externado-badge--a="context.item.isA" data-wp-class--externado-badge--b="context.item.isB" data-wp-class--externado-badge--c="context.item.isC" data-wp-text="context.item.effort"></span>
						<span><span data-wp-text="context.item.yes"></span> <?php esc_html_e( 'a favor', 'externado' ); ?></span>
						<span><span data-wp-text="context.item.no"></span> <?php esc_html_e( 'en contra', 'externado' ); ?></span>
					</span>
				</li>
			</template>
		</ol>
	</div>
</div>
