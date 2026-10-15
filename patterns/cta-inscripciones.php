<?php
/**
 * Title: CTA Inscripciones (sincronizado)
 * Slug: externado/cta-inscripciones
 * Categories: externado
 * Inserter: no
 * Description: Inserta el patrón sincronizado «CTA Inscripciones», con un respaldo estático si aún no existe.
 *
 * @package Externado
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$externado_cta = externado_synced_pattern_markup( 'cta-inscripciones' );

if ( $externado_cta ) {
	echo $externado_cta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>
<!-- wp:group {"align":"full","className":"is-style-section-verde","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-section-verde"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Inscripciones abiertas 2027', 'externado' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->
