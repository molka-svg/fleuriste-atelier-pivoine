<?php
/**
 * Mon compte : mes adresses — Atelier Pivoine.
 *
 * Adapté du modèle WooCommerce myaccount/my-address.php (version 9.3.0) :
 * mêmes adresses, mêmes liens et mêmes hooks, en deux cartes avec un bouton court.
 *
 * @version 9.3.0
 */

defined('ABSPATH') || exit;

$customer_id = get_current_user_id();

if (!wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
    $get_addresses = apply_filters('woocommerce_my_account_get_addresses', [
        'billing'  => 'Adresse de facturation',
        'shipping' => 'Adresse de livraison',
    ], $customer_id);
} else {
    $get_addresses = apply_filters('woocommerce_my_account_get_addresses', [
        'billing' => 'Adresse de facturation',
    ], $customer_id);
}
?>

<header class="account-section-head">
    <p class="section-eyebrow">Mon compte</p>
    <h2>Mes <em>adresses</em></h2>
    <p><?php echo apply_filters('woocommerce_my_account_my_address_description', 'Elles sont préremplies quand vous passez commande. Pour offrir des fleurs, vous pourrez toujours indiquer une autre adresse.'); // phpcs:ignore ?></p>
</header>

<div class="woocommerce-Addresses address-cards">
    <?php foreach ($get_addresses as $name => $address_title) :
        $address = wc_get_account_formatted_address($name);
        ?>
        <article class="woocommerce-Address address-card">
            <header class="woocommerce-Address-title title">
                <h3><?php echo esc_html($address_title); ?></h3>
            </header>
            <address>
                <?php
                echo $address ? wp_kses_post($address) : '<span class="address-empty">Pas encore renseignée.</span>'; // phpcs:ignore
                do_action('woocommerce_my_account_after_my_address', $name);
                ?>
            </address>
            <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', $name)); ?>" class="edit btn-ghost" aria-label="<?php echo esc_attr(($address ? 'Modifier ' : 'Ajouter ') . mb_strtolower($address_title)); ?>">
                <?php echo $address ? 'Modifier' : 'Ajouter'; ?>
            </a>
        </article>
    <?php endforeach; ?>
</div>
