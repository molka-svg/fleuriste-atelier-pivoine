<?php
/**
 * Title: Bandeau de la boutique
 * Slug: atelier-pivoine-blocs/boutique-bandeau
 * Categories: featured
 * Description: Photo, titre du rayon et fil d’Ariane.
 */

$img  = get_stylesheet_directory_uri() . '/images/';
$shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/boutique/');
?>
<!-- wp:group {"tagName":"section","className":"page-hero","layout":{"type":"default"}} -->
<section class="wp-block-group page-hero"><!-- wp:group {"className":"page-hero-bg","layout":{"type":"default"}} -->
<div class="wp-block-group page-hero-bg"><!-- wp:image {"className":"page-hero-photo"} -->
<figure class="wp-block-image page-hero-photo"><img src="<?php echo esc_url($img . 'boutique.jpg'); ?>" alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"page-hero-content","layout":{"type":"default"}} -->
<div class="wp-block-group page-hero-content"><!-- wp:woocommerce/breadcrumbs /-->

<!-- wp:paragraph {"className":"section-eyebrow"} -->
<p class="section-eyebrow">La boutique</p>
<!-- /wp:paragraph -->

<!-- wp:query-title {"type":"archive","showPrefix":false} /-->

<!-- wp:term-description /-->

<!-- wp:paragraph {"className":"page-hero-text"} -->
<p class="page-hero-text">Composés chaque matin à l'atelier avec les fleurs de saison. Livraison le jour même dans Paris pour toute commande avant 14 h, offerte dès 60 €.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
