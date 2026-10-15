<?php
/**
 * Render callback for externado/feature-vote.
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

$externado_feature_id = isset( $attributes['feature'] ) ? (string) $attributes['feature'] : '';
$externado_feature    = externado_get_feature( $externado_feature_id );

if ( ! $externado_feature ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		printf(
			'<div %s><p>%s</p></div>',
			get_block_wrapper_attributes(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html__( 'Seleccione una funcionalidad en la barra lateral.', 'externado' )
		);
	}
	return;
}

// Demo mode: visitors without a session vote with an anonymous browser token (see inc/votes.php).
externado_votes_init_state();

$externado_group_label = sprintf(
	/* translators: %s: feature title. */
	__( '¿Le interesa %s?', 'externado' ),
	$externado_feature['title']
);
$externado_context     = array(
	'feature' => $externado_feature_id,
	'error'   => false,
);
?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-wp-interactive="externado/votes"
	data-wp-init="callbacks.startLive"
	<?php echo wp_interactivity_data_wp_context( $externado_context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
	<p class="externado-vote__question" aria-hidden="true"><?php esc_html_e( '¿Le interesa?', 'externado' ); ?></p>
	<div class="externado-vote__buttons" role="group" aria-label="<?php echo esc_attr( $externado_group_label ); ?>">
		<button
			type="button"
			class="externado-vote__button externado-vote__button--yes"
			data-vote="yes"
			data-wp-on--click="actions.vote"
			data-wp-class--is-selected="state.isYes"
			data-wp-bind--aria-pressed="state.isYes"
		>
			<?php esc_html_e( 'Me interesa', 'externado' ); ?>
			<span class="externado-vote__count" data-wp-text="state.yesCount"></span>
		</button>
		<button
			type="button"
			class="externado-vote__button externado-vote__button--no"
			data-vote="no"
			data-wp-on--click="actions.vote"
			data-wp-class--is-selected="state.isNo"
			data-wp-bind--aria-pressed="state.isNo"
		>
			<?php esc_html_e( 'No por ahora', 'externado' ); ?>
			<span class="externado-vote__count" data-wp-text="state.noCount"></span>
		</button>
	</div>
	<p class="externado-vote__error" role="status" data-wp-bind--hidden="!context.error" data-wp-text="state.errorMessage" hidden></p>
</div>
