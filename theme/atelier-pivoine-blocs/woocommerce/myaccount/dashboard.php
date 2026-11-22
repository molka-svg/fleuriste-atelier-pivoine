<?php
/**
 * Mon compte : accueil — Atelier Pivoine.
 *
 * Adapté du modèle WooCommerce myaccount/dashboard.php (version 4.4.0) :
 * mêmes hooks, avec un bonjour, la dernière commande et des raccourcis.
 *
 * @version 4.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$first  = $current_user->first_name ?: $current_user->display_name;
$orders = wc_get_orders([
    'customer_id' => get_current_user_id(),
    'limit'       => 1,
    'orderby'     => 'date',
    'order'       => 'DESC',
]);
$last = $orders ? $orders[0] : null;
?>

<div class="account-hello">
    <p class="section-eyebrow">Mon compte</p>
    <h2>Bonjour <em><?php echo esc_html($first); ?></em></h2>
    <p>Ce n'est pas vous ? <a href="<?php echo esc_url(wc_logout_url()); ?>">Se déconnecter</a></p>
</div>

<?php if ($last) :
    $extras = function_exists('atelier_pivoine_order_extras') ? atelier_pivoine_order_extras($last) : [];
    ?>
    <article class="account-last">
        <div class="account-last-top">
            <p class="section-eyebrow">Dernière commande</p>
            <span class="order-status status-<?php echo esc_attr($last->get_status()); ?>"><?php echo esc_html(wc_get_order_status_name($last->get_status())); ?></span>
        </div>
        <h3>Commande n° <?php echo esc_html($last->get_order_number()); ?></h3>
        <p class="account-last-meta">
            Passée le <?php echo esc_html(wc_format_datetime($last->get_date_created())); ?> ·
            <strong><?php echo wp_kses_post($last->get_formatted_order_total()); ?></strong>
        </p>
        <?php if (!empty($extras['Livraison / retrait'])) : ?>
            <p class="account-last-meta">Livraison ou retrait : <?php echo esc_html($extras['Livraison / retrait']); ?></p>
        <?php endif; ?>
        <ul class="account-last-items">
            <?php foreach ($last->get_items() as $item) : ?>
                <li><?php echo (int) $item->get_quantity(); ?> × <?php echo esc_html($item->get_name()); ?></li>
            <?php endforeach; ?>
        </ul>
        <a class="section-link" href="<?php echo esc_url($last->get_view_order_url()); ?>">Voir le détail ↗</a>
    </article>
<?php else : ?>
    <article class="account-last account-last--empty">
        <p>Pas encore de commande. Les bouquets du jour vous attendent.</p>
        <a class="btn-primary" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Découvrir les bouquets</a>
    </article>
<?php endif; ?>

<div class="account-shortcuts">
    <a href="<?php echo esc_url(wc_get_endpoint_url('orders')); ?>"><strong>Mes commandes</strong><span>Suivre et retrouver vos bouquets</span></a>
    <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address')); ?>"><strong>Mes adresses</strong><span>Livraison et facturation</span></a>
    <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>"><strong>Mes informations</strong><span>Nom, e-mail, mot de passe</span></a>
</div>

<?php
do_action('woocommerce_account_dashboard');
do_action('woocommerce_before_my_account'); // phpcs:ignore -- hook historique conservé
do_action('woocommerce_after_my_account');  // phpcs:ignore -- hook historique conservé
