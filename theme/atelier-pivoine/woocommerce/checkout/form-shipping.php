<?php
/**
 * Commande — « C'est pour offrir ? » et étape ③ Votre bouquet
 *
 * Remplace le bloc « Expédier à une adresse différente » de WooCommerce.
 * La date, le créneau et le message de la carte sont déclarés dans inc/boutique.php.
 *
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;
?>
<?php if (true === WC()->cart->needs_shipping_address()) : ?>
    <section class="ap-step ap-step--gift woocommerce-shipping-fields">
        <h3 id="ship-to-different-address">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked(apply_filters('woocommerce_ship_to_different_address_checked', 'shipping' === get_option('woocommerce_ship_to_destination') ? 1 : 0), 1); ?> type="checkbox" name="ship_to_different_address" value="1" />
                <span>C'est pour offrir&nbsp;? Livrer à une autre adresse</span>
            </label>
        </h3>

        <div class="shipping_address">
            <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>
            <div class="woocommerce-shipping-fields__field-wrapper">
                <?php
                foreach ($checkout->get_checkout_fields('shipping') as $key => $field) {
                    woocommerce_form_field($key, $field, $checkout->get_value($key));
                }
                ?>
            </div>
            <?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>
        </div>
    </section>
<?php endif; ?>

<section class="ap-step woocommerce-additional-fields">
    <h3 class="ap-step-title"><span class="ap-step-num">3</span> Votre bouquet</h3>
    <?php do_action('woocommerce_before_order_notes', $checkout); ?>
    <div class="woocommerce-additional-fields__field-wrapper">
        <?php foreach ($checkout->get_checkout_fields('order') as $key => $field) : ?>
            <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
        <?php endforeach; ?>
    </div>
    <?php do_action('woocommerce_after_order_notes', $checkout); ?>
</section>
