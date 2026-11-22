<?php
/**
 * Panier vide — Atelier Pivoine
 *
 * Remplace le modèle WooCommerce : message chaleureux et bouton vers la boutique.
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

do_action('woocommerce_cart_is_empty');
?>
<div class="cart-empty-box">
    <span class="cart-empty-icon" aria-hidden="true">✿</span>
    <h2 class="cart-empty-title">Votre panier est vide</h2>
    <p>Les bouquets du jour n'attendent que vous&nbsp;: composés à la main le matin même, livrés dans Paris.</p>
    <a class="btn-primary" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">Voir les bouquets</a>
</div>
