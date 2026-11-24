<?php
/**
 * Title: Page L'atelier
 * Slug: atelier-pivoine-blocs/atelier
 * Categories: featured
 * Description: Histoire, engagements, savoir-faire, horaires et adresse.
 */

$img  = get_stylesheet_directory_uri() . '/images/';
$shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/boutique/');
?>
<!-- wp:group {"tagName":"section","className":"atelier-hero","layout":{"type":"default"}} -->
<section class="wp-block-group atelier-hero"><!-- wp:image {"className":"atelier-hero-img"} -->
<figure class="wp-block-image atelier-hero-img"><img src="<?php echo esc_url($img . 'atelier.jpg'); ?>" alt="Fleuriste composant un bouquet sur son plan de travail"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"atelier-hero-text","layout":{"type":"default"}} -->
<div class="wp-block-group atelier-hero-text"><!-- wp:paragraph {"className":"section-eyebrow reveal"} -->
<p class="section-eyebrow reveal">Notre histoire</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"reveal"} -->
<h1 class="wp-block-heading reveal">Un carnet d'herbier <em>sur le comptoir.</em></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"reveal"} -->
<p class="reveal">Une petite boutique du 11e, quelques rues après Oberkampf. Les seaux sont déjà pleins à l'ouverture : renoncules, eucalyptus, branches de saison arrivées à l'aube.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"reveal"} -->
<p class="reveal">Camille Rose compose chaque bouquet à la main, sur un grand plan de travail en bois, et l'emballe dans un papier kraft noué d'un ruban.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($shop); ?>">Voir les bouquets du jour</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"className":"atelier-values","layout":{"type":"default"}} -->
<div class="wp-block-group atelier-values"><!-- wp:group {"className":"value-card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group value-card reveal"><!-- wp:paragraph {"className":"value-num"} -->
<p class="value-num">01</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">De saison</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nous travaillons les fleurs au rythme des saisons, sans fleurs qui ont fait le tour du monde.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"value-card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group value-card reveal"><!-- wp:paragraph {"className":"value-num"} -->
<p class="value-num">02</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Artisanal</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Chaque bouquet est unique, composé à la main au moment de votre commande.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"value-card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group value-card reveal"><!-- wp:paragraph {"className":"value-num"} -->
<p class="value-num">03</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Proche</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Livraison par coursier dans Paris, le jour même pour une commande passée avant 14 h.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"atelier-craft","layout":{"type":"default"}} -->
<section class="wp-block-group atelier-craft"><!-- wp:group {"className":"atelier-craft-text reveal","layout":{"type":"default"}} -->
<div class="wp-block-group atelier-craft-text reveal"><!-- wp:paragraph {"className":"section-eyebrow"} -->
<p class="section-eyebrow">Le savoir-faire</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Des mains, <em>un ruban, une carte.</em></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Avant de partir, chaque bouquet est vérifié tige par tige, emballé dans un papier kraft et accompagné de votre message, écrit à la main.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:image {"className":"atelier-craft-img"} -->
<figure class="wp-block-image atelier-craft-img"><img src="<?php echo esc_url($img . 'atelier-mains.jpg'); ?>" alt="Mains de la fleuriste assemblant un petit bouquet"/></figure>
<!-- /wp:image --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"atelier-visit","layout":{"type":"default"}} -->
<section class="wp-block-group atelier-visit"><!-- wp:group {"className":"visit-card reveal","layout":{"type":"default"}} -->
<div class="wp-block-group visit-card reveal"><!-- wp:paragraph {"className":"section-eyebrow"} -->
<p class="section-eyebrow">Venir à l'atelier</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Horaires</h2>
<!-- /wp:heading -->

<!-- wp:table {"className":"visit-hours"} -->
<figure class="wp-block-table visit-hours"><table><tbody><tr><td>Lundi</td><td>Fermé</td></tr><tr><td>Mardi – samedi</td><td>9 h – 19 h</td></tr><tr><td>Dimanche</td><td>Fermé</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:paragraph {"className":"visit-address"} -->
<p class="visit-address">27 rue Imaginaire, 75011 Paris<br>01 00 00 00 00 · bonjour@atelier-pivoine.test</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"visit-map reveal","layout":{"type":"default"}} -->
<div class="wp-block-group visit-map reveal"><!-- wp:paragraph {"className":"visit-map-pin"} -->
<p class="visit-map-pin">✿</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Paris 11e</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Métro Oberkampf · 4 min à pied</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
