<?php
/**
 * Mon compte : mes commandes — Atelier Pivoine.
 *
 * Adapté du modèle WooCommerce myaccount/orders.php (version 9.5.0) :
 * mêmes données, mêmes actions et mêmes hooks, présentés en cartes
 * (photo, statut, date de livraison) au lieu d'un tableau.
 *
 * @version 9.5.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders); ?>

<header class="account-section-head">
    <p class="section-eyebrow">Mon compte</p>
    <h2>Mes <em>commandes</em></h2>
</header>

<?php if ($has_orders) : ?>

    <ul class="order-cards">
        <?php
        foreach ($customer_orders->orders as $customer_order) :
            $order      = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            $item_count = $order->get_item_count() - $order->get_item_count_refunded();
            $items      = $order->get_items();
            $first_item = $items ? reset($items) : null;
            $product    = $first_item ? $first_item->get_product() : null;
            $extras     = function_exists('atelier_pivoine_order_extras') ? atelier_pivoine_order_extras($order) : [];
            $names      = array_map(function ($item) {
                return $item->get_quantity() . ' × ' . $item->get_name();
            }, $items);
            ?>
            <li class="order-card woocommerce-orders-table__row--status-<?php echo esc_attr($order->get_status()); ?>">
                <a class="order-card-photo" href="<?php echo esc_url($order->get_view_order_url()); ?>" tabindex="-1" aria-hidden="true">
                    <?php if ($product && function_exists('atelier_pivoine_product_image')) {
                        atelier_pivoine_product_image(wc_get_product($product->get_parent_id() ?: $product->get_id()), 240);
                    } ?>
                </a>
                <div class="order-card-body">
                    <div class="order-card-top">
                        <h3>
                            <a href="<?php echo esc_url($order->get_view_order_url()); ?>">Commande n° <?php echo esc_html($order->get_order_number()); ?></a>
                        </h3>
                        <span class="order-status status-<?php echo esc_attr($order->get_status()); ?>"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span>
                    </div>
                    <p class="order-card-items"><?php echo esc_html(implode(', ', $names)); ?></p>
                    <dl class="order-card-meta">
                        <div><dt>Passée le</dt><dd><time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></time></dd></div>
                        <?php if (!empty($extras['Livraison / retrait'])) : ?>
                            <div><dt>Livraison / retrait</dt><dd><?php echo esc_html($extras['Livraison / retrait']); ?></dd></div>
                        <?php endif; ?>
                        <div><dt>Total</dt><dd><strong><?php echo wp_kses_post($order->get_formatted_order_total()); ?></strong> · <?php echo esc_html($item_count . ' article' . ($item_count > 1 ? 's' : '')); ?></dd></div>
                    </dl>
                    <div class="order-card-actions">
                        <?php
                        foreach (wc_get_account_orders_actions($order) as $key => $action) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                            $label = $action['aria-label'] ?? sprintf('%1$s la commande n° %2$s', $action['name'], $order->get_order_number());
                            printf(
                                '<a href="%s" class="%s" aria-label="%s">%s</a>',
                                esc_url($action['url']),
                                esc_attr('view' === $key ? 'btn-secondary order-btn' : 'btn-ghost order-btn ' . sanitize_html_class($key)),
                                esc_attr($label),
                                esc_html('view' === $key ? 'Voir le détail' : $action['name'])
                            );
                        }
                        ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php do_action('woocommerce_before_account_orders_pagination'); ?>

    <?php if (1 < $customer_orders->max_num_pages) : ?>
        <div class="woocommerce-pagination woocommerce-Pagination order-pagination">
            <?php if (1 !== $current_page) : ?>
                <a class="btn-ghost" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>">← Commandes plus récentes</a>
            <?php endif; ?>
            <?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
                <a class="btn-ghost" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>">Commandes plus anciennes →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php else : ?>

    <div class="account-empty">
        <p class="account-empty-title">Pas encore de commande</p>
        <p>Les bouquets du jour n'attendent que vous.</p>
        <a class="btn-primary" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">Découvrir les bouquets</a>
    </div>

<?php endif; ?>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>
