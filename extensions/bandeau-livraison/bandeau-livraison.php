<?php
/**
 * Plugin Name: Bandeau livraison offerte
 * Description: Bandeau en haut de chaque page : « Livraison offerte dès 60 € », avec ce qu'il manque dans le panier.
 * Version: 1.0.0
 * Author: Atelier Pivoine
 * Requires Plugins: woocommerce
 * Text Domain: bandeau-livraison
 */

defined('ABSPATH') || exit;

// Seuil de gratuité, modifiable sans toucher au code : add_filter('bandeau_livraison_seuil', fn() => 50);
function bandeau_livraison_seuil() {
    return (float) apply_filters('bandeau_livraison_seuil', 60);
}

add_action('wp_body_open', function () {
    if (is_admin() || !function_exists('WC') || !WC()->cart) {
        return;
    }
    $seuil = bandeau_livraison_seuil();
    $total = (float) WC()->cart->get_displayed_subtotal();
    $reste = $seuil - $total;

    if ($total > 0 && $reste <= 0) {
        $texte = 'La livraison vous est offerte !';
    } elseif ($total > 0) {
        $texte = sprintf('Plus que <strong>%s</strong> pour la livraison offerte', wc_price($reste));
    } else {
        $texte = sprintf('Livraison offerte dès <strong>%s</strong> · Composé le matin, livré le jour même à Paris', wc_price($seuil, ['decimals' => 0]));
    }
    printf('<div class="bandeau-livraison" role="note">%s</div>', wp_kses_post($texte));
});

add_action('wp_head', function () {
    echo '<style>.bandeau-livraison{background:var(--wp--preset--color--vert,#2F4A3A);color:var(--wp--preset--color--blanc,#fff);'
        . 'text-align:center;font-size:.875rem;padding:.5rem 1rem;line-height:1.4}'
        . '.bandeau-livraison strong{color:var(--wp--preset--color--petale,#F6D6D9)}'
        . '.bandeau-livraison .woocommerce-Price-amount{font-weight:700}</style>';
});
