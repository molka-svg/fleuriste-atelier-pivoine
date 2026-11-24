<?php
/**
 * Title: Accueil Atelier Pivoine
 * Slug: atelier-pivoine-blocs/accueil
 * Categories: featured
 * Description: Grande photo, engagements, univers, bouquets du moment, atelier et signature.
 */

$img  = get_stylesheet_directory_uri() . '/images/';
$shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/boutique/');
$cat  = function ($slug) use ($shop) {
    $lien = taxonomy_exists('product_cat') ? get_term_link($slug, 'product_cat') : '';
    return is_string($lien) && $lien ? $lien : $shop;
};
?>
<!-- wp:group {"tagName":"section","className":"hero","layout":{"type":"default"}} -->
<section class="wp-block-group hero"><!-- wp:group {"className":"hero-bg","layout":{"type":"default"}} -->
<div class="wp-block-group hero-bg"><!-- wp:image {"className":"hero-photo"} -->
<figure class="wp-block-image hero-photo"><img src="<?php echo esc_url($img . 'hero.jpg'); ?>" alt="Pivoines roses dans un vase en verre"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"hero-content","layout":{"type":"default"}} -->
<div class="wp-block-group hero-content"><!-- wp:paragraph {"className":"hero-eyebrow reveal"} -->
<p class="hero-eyebrow reveal">Fleuriste · Paris 11e</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"reveal"} -->
<h1 class="wp-block-heading reveal">Un bouquet.<br><em>Une émotion.</em></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"hero-description reveal"} -->
<p class="hero-description reveal">Des fleurs de saison, choisies avec délicatesse et assemblées à la main pour faire fleurir vos moments précieux.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"hero-buttons reveal"} -->
<div class="wp-block-buttons hero-buttons reveal"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($shop); ?>">Découvrir les bouquets</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"btn-verre"} -->
<div class="wp-block-button btn-verre"><a class="wp-block-button__link wp-element-button" href="#univers">Notre univers ↓</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"className":"reassurance","layout":{"type":"default"}} -->
<div class="wp-block-group reassurance"><!-- wp:paragraph {"className":"reassurance-item"} -->
<p class="reassurance-item"><span class="reassurance-icon">✳</span> Fleurs de saison</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"reassurance-item"} -->
<p class="reassurance-item"><span class="reassurance-icon">♡</span> Compositions artisanales</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"reassurance-item"} -->
<p class="reassurance-item"><span class="reassurance-icon">⌁</span> Livré le jour même à Paris</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"reassurance-item"} -->
<p class="reassurance-item"><span class="reassurance-icon">✿</span> Livraison offerte dès 60 €</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"reassurance-item"} -->
<p class="reassurance-item"><span class="reassurance-icon">✓</span> Paiement sécurisé</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","anchor":"univers","className":"home-categories","layout":{"type":"default"}} -->
<section id="univers" class="wp-block-group home-categories"><!-- wp:group {"className":"section-heading reveal","layout":{"type":"default"}} -->
<div class="wp-block-group section-heading reveal"><!-- wp:paragraph {"className":"section-eyebrow"} -->
<p class="section-eyebrow">Laissez parler les fleurs</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Des fleurs pour <em>tous vos moments.</em></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"categories-grid","layout":{"type":"default"}} -->
<div class="wp-block-group categories-grid"><!-- wp:group {"className":"category-card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group category-card reveal"><!-- wp:image {"className":"category-photo"} -->
<figure class="wp-block-image category-photo"><img src="<?php echo esc_url($img . 'cat-1.jpg'); ?>" alt="Pivoines roses emballées dans du papier blanc"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"category-label","layout":{"type":"default"}} -->
<div class="wp-block-group category-label"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url($cat('bouquets-de-saison')); ?>">Bouquets de saison</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Le charme du naturel</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"category-arrow"} -->
<p class="category-arrow">↗</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"category-card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group category-card reveal"><!-- wp:image {"className":"category-photo"} -->
<figure class="wp-block-image category-photo"><img src="<?php echo esc_url($img . 'cat-2.jpg'); ?>" alt="Pivoines roses dans un vase"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"category-label","layout":{"type":"default"}} -->
<div class="wp-block-group category-label"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url($cat('petites-attentions')); ?>">Petites attentions</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Pour faire plaisir simplement</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"category-arrow"} -->
<p class="category-arrow">↗</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"category-card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group category-card reveal"><!-- wp:image {"className":"category-photo"} -->
<figure class="wp-block-image category-photo"><img src="<?php echo esc_url($img . 'cat-3.jpg'); ?>" alt="Mur de roses pêche et crème"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"category-label","layout":{"type":"default"}} -->
<div class="wp-block-group category-label"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url($cat('grandes-occasions')); ?>">Grandes occasions</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Les moments à célébrer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"category-arrow"} -->
<p class="category-arrow">↗</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"home-products","layout":{"type":"default"}} -->
<section class="wp-block-group home-products"><!-- wp:group {"className":"section-heading reveal","layout":{"type":"default"}} -->
<div class="wp-block-group section-heading reveal"><!-- wp:paragraph {"className":"section-eyebrow"} -->
<p class="section-eyebrow">Les coups de cœur de l'atelier</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Nos bouquets <em>du moment.</em></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:woocommerce/product-collection {"queryId":1,"query":{"perPage":8,"pages":1,"offset":0,"postType":"product","order":"desc","orderBy":"date","search":"","exclude":[],"inherit":false,"taxQuery":{},"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[]},"tagName":"div","displayLayout":{"type":"flex","columns":4,"shrinkColumns":true},"queryContextIncludes":["collection"],"className":"ap-products ap-rail"} -->
<div class="wp-block-woocommerce-product-collection ap-products ap-rail"><!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"imageSizing":"single","isDescendentOfQueryLoop":true} /-->

<!-- wp:post-title {"level":3,"isLink":true,"__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true} /-->

<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true} /-->
<!-- /wp:woocommerce/product-template --></div>
<!-- /wp:woocommerce/product-collection -->

<!-- wp:buttons {"className":"rail-more"} -->
<div class="wp-block-buttons rail-more"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($shop); ?>">Voir toute la boutique ↗</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"home-story","layout":{"type":"default"}} -->
<section class="wp-block-group home-story"><!-- wp:image {"className":"story-image"} -->
<figure class="wp-block-image story-image"><img src="<?php echo esc_url($img . 'story.jpg'); ?>" alt="Main tenant un bouquet de pivoines blanches et d'hortensias roses"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"story-content reveal","layout":{"type":"default"}} -->
<div class="wp-block-group story-content reveal"><!-- wp:paragraph {"className":"section-eyebrow"} -->
<p class="section-eyebrow">Au cœur de Paris 11e</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La passion des fleurs. <em>Le goût du détail.</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Chaque fleur a son caractère, chaque composition son histoire. Nous imaginons des bouquets spontanés, généreux et vivants, inspirés par la beauté des saisons.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"btn-vert"} -->
<div class="wp-block-button btn-vert"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/atelier/')); ?>">Découvrir l'atelier</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"home-message","layout":{"type":"default"}} -->
<section class="wp-block-group home-message"><!-- wp:image {"className":"home-message-photo"} -->
<figure class="wp-block-image home-message-photo"><img src="<?php echo esc_url($img . 'cta.jpg'); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"home-message-content reveal","layout":{"type":"default"}} -->
<div class="wp-block-group home-message-content reveal"><!-- wp:paragraph {"className":"section-eyebrow"} -->
<p class="section-eyebrow">Atelier Pivoine</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Cueilli à l'aube,<br><em>offert le jour même.</em></h2>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($shop); ?>">Trouver mon bouquet</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
