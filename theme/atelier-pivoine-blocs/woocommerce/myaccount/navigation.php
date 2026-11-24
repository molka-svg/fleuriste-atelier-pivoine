<?php
/**
 * Mon compte : menu latéral — Atelier Pivoine.
 *
 * Adapté du modèle WooCommerce myaccount/navigation.php (version 9.3.0) :
 * mêmes liens et mêmes hooks, avec la carte de la cliente (initiales, prénom)
 * et une icône par rubrique.
 *
 * @version 9.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$user     = wp_get_current_user();
$first    = $user->first_name ?: $user->display_name;
$initials = mb_strtoupper(mb_substr($first, 0, 1) . mb_substr((string) $user->last_name, 0, 1));
$since    = $user->user_registered ? wp_date('F Y', strtotime($user->user_registered)) : '';

// Icônes au trait (même style que l'icône « compte » de l'en-tête)
$icons = [
    'dashboard'       => '<path d="M4 11.5 12 5l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1z"/>',
    'orders'          => '<path d="M6 7h12l-1 13H7z"/><path d="M9 7a3 3 0 0 1 6 0"/>',
    'edit-address'    => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
    'edit-account'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>',
    'customer-logout' => '<path d="M14 4h5v16h-5"/><path d="M10 8l-4 4 4 4"/><path d="M6 12h10"/>',
];

do_action('woocommerce_before_account_navigation');
?>

<nav class="woocommerce-MyAccount-navigation account-nav" aria-label="Pages du compte">
    <div class="account-card">
        <span class="account-card-avatar" aria-hidden="true"><?php echo esc_html($initials); ?></span>
        <div>
            <p class="account-card-name"><?php echo esc_html(trim($first . ' ' . $user->last_name)); ?></p>
            <?php if ($since) : ?><p class="account-card-since">Cliente depuis <?php echo esc_html($since); ?></p><?php endif; ?>
        </div>
    </div>
    <ul>
        <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
            <li class="<?php echo wc_get_account_menu_item_classes($endpoint); // phpcs:ignore ?>">
                <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" <?php echo wc_is_current_account_menu_item($endpoint) ? 'aria-current="page"' : ''; ?>>
                    <svg class="account-nav-icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?php echo $icons[$endpoint] ?? '<circle cx="12" cy="12" r="3"/>'; // phpcs:ignore ?></svg>
                    <span><?php echo esc_html($label); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<?php do_action('woocommerce_after_account_navigation'); ?>
