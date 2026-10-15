<?php
/**
 * Title: Agenda
 * Slug: externado/agenda
 * Categories: externado
 * Description: Event list with date badges.
 */

$externado_events = array(
	array( '24', 'Sep', 'accent', 'Finanzas, Gobierno y Relaciones Internacionales', 'Club de lectura: Contra la Revolución', '1:00 pm', 'Museo Lux non Occidat' ),
	array( '14', 'Oct', 'accent-dark', 'Finanzas, Gobierno y Relaciones Internacionales', 'Handbook of South American Regionalism', '4:00 pm', 'I 501' ),
	array( '15', 'Oct', 'primary', 'Derecho, Comunicaciones y Tecnologías de la Información', 'Foro: De la calidad normativa a la calidad de las decisiones regulatorias', '8:30 am', 'Edificio H - Salón 403 / Online' ),
	array( '15', 'Oct', 'danger', 'Biblioteca', 'Conoce los gestores bibliográficos', '10:00 am', 'Teams' ),
);
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"base","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"className":"externado-section-title","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group externado-section-title"><!-- wp:heading -->
<h2 class="wp-block-heading">Agenda</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}}} -->
<p class="has-link-color"><a href="#">Ver todos ›</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"grid","columnCount":2,"minimumColumnWidth":"300px"}} -->
<div class="wp-block-group"><?php foreach ( $externado_events as $externado_event ) : list( $day, $month, $color, $unit, $title, $time, $place ) = $externado_event; ?><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:group {"style":{"dimensions":{"minHeight":"56px"},"layout":{"selfStretch":"fixed","flexSize":"56px"},"border":{"radius":"999px"},"spacing":{"blockGap":"0","padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}},"backgroundColor":"<?php echo esc_attr( $color ); ?>","textColor":"base","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group has-base-color has-<?php echo esc_attr( $color ); ?>-background-color has-text-color has-background" style="border-radius:999px;min-height:56px;padding-top:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"900","lineHeight":"1"}},"fontSize":"large"} -->
<p class="has-large-font-size" style="font-weight:900;line-height:1"><?php echo esc_html( $day ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1"}},"fontSize":"x-small"} -->
<p class="has-x-small-font-size" style="line-height:1"><?php echo esc_html( $month ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"700"},"color":{"text":"#555555"}},"fontSize":"x-small"} -->
<p class="has-text-color has-x-small-font-size" style="color:#555555;font-weight:700"><?php echo esc_html( $unit ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"style":{"color":{"text":"#222222"}},"fontSize":"medium"} -->
<h3 class="wp-block-heading has-text-color has-medium-font-size" style="color:#222222"><?php echo esc_html( $title ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic"}}} -->
<p style="font-style:italic">Hora: <?php echo esc_html( $time ); ?><br>Lugar: <?php echo esc_html( $place ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<?php endforeach; ?></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
