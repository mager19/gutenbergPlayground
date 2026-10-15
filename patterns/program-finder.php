<?php
/**
 * Title: Buscador de programas
 * Slug: externado/program-finder
 * Categories: externado
 * Description: Program search bar with academic level shortcuts.
 */

$externado_levels = array( 'Pregrados', 'Especializaciones', 'Maestrías', 'Doctorados', 'Becas', 'Regiones', 'Edu. Continuada' );
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"backgroundColor":"slate","textColor":"base","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull has-base-color has-slate-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"30%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:30%"><!-- wp:heading {"style":{"typography":{"fontStyle":"italic","fontWeight":"700"},"color":{"text":"#ffffff"}},"fontSize":"large"} -->
<h2 class="wp-block-heading has-text-color has-large-font-size" style="color:#ffffff;font-style:italic;font-weight:700">Encuentre el programa de su interés:</h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%"><!-- wp:search {"label":"Buscar programa","showLabel":false,"placeholder":"Ej. Derecho","buttonText":"Buscar","buttonPosition":"button-inside","buttonUseIcon":true} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"15%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:15%"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|accent"}}}},"fontSize":"small"} -->
<p class="has-link-color has-small-font-size"><a href="#">Inscripciones abiertas ›</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"grid","minimumColumnWidth":"140px"}} -->
<div class="wp-block-group"><?php foreach ( $externado_levels as $externado_level ) : ?><!-- wp:group {"className":"externado-card","style":{"spacing":{"blockGap":"0"}},"backgroundColor":"base","layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group externado-card has-base-background-color has-background"><!-- wp:cover {"dimRatio":100,"overlayColor":"primary","minHeight":110,"isDark":true} -->
<div class="wp-block-cover is-dark" style="min-height:110px"><span aria-hidden="true" class="wp-block-cover__background has-primary-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"></div></div>
<!-- /wp:cover -->

<!-- wp:paragraph {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20","right":"var:preset|spacing|20"}},"typography":{"fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"fontSize":"small"} -->
<p class="has-link-color has-small-font-size" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20);font-weight:700"><a href="#"><?php echo esc_html( $externado_level ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<?php endforeach; ?></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
