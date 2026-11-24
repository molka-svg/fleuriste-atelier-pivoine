<?php
/**
 * Design de l'atelier dans le thème en blocs : onglets et filtres de la boutique,
 * ajout rapide − / + dans les cartes, bouton « compte » de l'en-tête.
 * Les blocs restent dans les modèles : on ne change ici que leur affichage.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    $file = '/assets/css/design.css';
    wp_enqueue_style('atelier-pivoine-design', get_stylesheet_directory_uri() . $file, ['atelier-pivoine-blocs'], filemtime(get_stylesheet_directory() . $file));
    $js = '/assets/js/design.js';
    wp_enqueue_script('atelier-pivoine-design', get_stylesheet_directory_uri() . $js, [], filemtime(get_stylesheet_directory() . $js), ['in_footer' => true, 'strategy' => 'defer']);
}, 20);

// [ap_categories] : onglets Tous / catégories, avec le nombre de bouquets
add_shortcode('ap_categories', function () {
    $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
    if (is_wp_error($terms)) {
        return '';
    }
    $actuel = is_product_taxonomy() ? (int) get_queried_object_id() : 0;
    $lien   = function ($url, $nom, $nb, $actif) {
        return sprintf('<a class="shop-cat" href="%s"%s>%s <span class="shop-cat-count">%d</span></a>', esc_url($url), $actif ? ' aria-current="page"' : '', esc_html($nom), $nb);
    };
    $html = $lien(wc_get_page_permalink('shop'), 'Tous', (int) wp_count_posts('product')->publish, !$actuel);
    foreach ($terms as $t) {
        $html .= $lien(get_term_link($t), $t->name, (int) $t->count, $actuel === (int) $t->term_id);
    }
    return '<nav class="shop-cats" aria-label="Catégories de bouquets">' . $html . '</nav>';
});

// [ap_filtres] : filtres par prix (le tri choisi est conservé)
add_shortcode('ap_filtres', function () {
    $base    = is_product_taxonomy() ? get_term_link(get_queried_object()) : wc_get_page_permalink('shop');
    $current = sanitize_key($_GET['prix'] ?? '');
    $html    = '';
    foreach (atelier_pivoine_price_filters() as $key => [$label]) {
        $url = $key ? add_query_arg('prix', $key, $base) : $base;
        if (!empty($_GET['orderby'])) {
            $url = add_query_arg('orderby', sanitize_key($_GET['orderby']), $url);
        }
        $html .= sprintf('<a class="chip" href="%s"%s>%s</a>', esc_url($url), $current === (string) $key ? ' aria-current="true"' : '', esc_html($label));
    }
    return '<div class="chips" role="group" aria-label="Filtrer par prix">' . $html . '</div>';
});

// Cartes : le bouton « Ajouter au panier » devient « Ajouter » puis − / +
add_filter('render_block_woocommerce/product-button', function ($html, $block, $instance) {
    $id      = $instance->context['postId'] ?? 0;
    $product = $id ? wc_get_product($id) : null;
    if (!$product || is_admin() || wp_is_json_request()) {
        return $html;
    }
    if (!$product->is_in_stock()) {
        return '<div class="ap-card-action"><span class="ap-rupture">En rupture</span></div>';
    }
    if ($product->is_type('variable')) {
        return '<div class="ap-card-action"><a class="add-btn add-btn--ghost" href="' . esc_url($product->get_permalink()) . '">Choisir la taille</a></div>';
    }
    return '<div class="ap-card-action">' . atelier_pivoine_qty_controls($product) . '</div>';
}, 10, 3);

// Sur une catégorie, sa description remplace le texte général du bandeau
add_filter('body_class', function ($c) {
    if (is_product_taxonomy() && term_description()) {
        $c[] = 'ap-avec-description';
    }
    return $c;
});
