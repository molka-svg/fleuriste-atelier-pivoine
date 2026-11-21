<?php
/**
 * Modèle WooCommerce (boutique, fiche produit, catégories).
 *
 * La liste des bouquets (boutique et rayons) a son propre gabarit :
 * template-parts/boutique.php. Les fiches produit gardent l'affichage WooCommerce.
 */
get_header();

if (is_shop() || is_product_taxonomy()) :
    get_template_part('template-parts/boutique');
else : ?>

<main class="site-main">
    <div class="page-container">
        <?php woocommerce_content(); ?>
    </div>
</main>

<?php endif;

get_footer();
