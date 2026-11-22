<?php
/**
 * Title: Accueil Atelier Pivoine
 * Slug: atelier-pivoine-blocs/accueil
 * Categories: featured
 * Description: Bannière, engagements, bouquets du moment, univers et histoire de l'atelier.
 */

$img  = get_stylesheet_directory_uri() . '/images/';
$shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/boutique/');
?>
<!-- wp:cover {"url":"<?php echo esc_url($img . 'hero.jpg'); ?>","dimRatio":40,"overlayColor":"vert","isUserOverlayColor":true,"minHeight":620,"align":"full","className":"ap-hero","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull ap-hero" style="min-height:620px"><span aria-hidden="true" class="wp-block-cover__background has-vert-background-color has-background-dim-40 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url($img . 'hero.jpg'); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"className":"ap-eyebrow"} -->
<p class="ap-eyebrow">Fleuriste de quartier · Paris 11e</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"ap-hero-title"} -->
<h1 class="wp-block-heading ap-hero-title">Cueilli à l'aube, offert le jour même.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ap-hero-text"} -->
<p class="ap-hero-text">Des bouquets de saison composés à la main chaque matin par Camille, livrés partout dans Paris.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($shop); ?>">Voir les bouquets</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->

<!-- wp:columns {"align":"wide","className":"ap-reassurance"} -->
<div class="wp-block-columns alignwide ap-reassurance"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Livré le jour même</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Pour toute commande passée avant 14 h, partout dans Paris.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Livraison offerte dès 60 €</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ou retrait gratuit à l'atelier, 27 rue Imaginaire.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Paiement sécurisé</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Connexion chiffrée, vos coordonnées restent protégées.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"ap-section","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ap-section"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Les bouquets du moment</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"ap-lead"} -->
<p class="has-text-align-center ap-lead">Composés à la main, au rythme des saisons.</p>
<!-- /wp:paragraph -->

<!-- wp:woocommerce/product-collection {"queryId":1,"query":{"perPage":4,"pages":1,"offset":0,"postType":"product","order":"desc","orderBy":"date","search":"","exclude":[],"inherit":false,"taxQuery":{},"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[]},"tagName":"div","displayLayout":{"type":"flex","columns":4,"shrinkColumns":true},"queryContextIncludes":["collection"],"className":"ap-products"} -->
<div class="wp-block-woocommerce-product-collection ap-products"><!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true} /-->

<!-- wp:post-title {"textAlign":"center","level":3,"isLink":true,"__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"center"} /-->

<!-- wp:woocommerce/product-button {"textAlign":"center","isDescendentOfQueryLoop":true} /-->
<!-- /wp:woocommerce/product-template --></div>
<!-- /wp:woocommerce/product-collection --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"ap-section ap-univers","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ap-section ap-univers"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Nos univers</h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-categories {"hasCount":false,"isHierarchical":false,"className":"ap-categories"} /--></div>
<!-- /wp:group -->

<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"ap-story"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center ap-story"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"className":"ap-rounded"} -->
<figure class="wp-block-image ap-rounded"><img src="<?php echo esc_url($img . 'story.jpg'); ?>" alt="Camille compose un bouquet sur le plan de travail en bois de l'atelier"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"ap-eyebrow"} -->
<p class="ap-eyebrow">L'atelier</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Une boutique de quartier, quelques rues après Oberkampf</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Le rideau se lève à 7 h, les seaux sont déjà pleins : renoncules, eucalyptus, branches de saison arrivées à l'aube. Chaque bouquet est composé à la main puis emballé dans un papier kraft noué d'un ruban.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/atelier/')); ?>">Découvrir l'atelier</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
