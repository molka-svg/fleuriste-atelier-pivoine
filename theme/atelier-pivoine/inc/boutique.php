<?php
/**
 * Atelier Pivoine — boutique, panier latéral, commande et compte client.
 *
 * Tout ce qui concerne WooCommerce est regroupé ici. Le fichier est chargé
 * par functions.php uniquement si WooCommerce est actif.
 */

if (!defined('ABSPATH')) {
    exit;
}

const ATELIER_PIVOINE_LIVRAISON_OFFERTE = 60;   // € : livraison offerte à partir de ce montant
const ATELIER_PIVOINE_HEURE_LIMITE      = 14;   // h : commande le jour même avant cette heure

/* ==========================================================================
 * Images des bouquets
 *
 * Si un bouquet n'a pas d'image dans WordPress, on utilise la photo déclarée
 * dans atelier_pivoine_image_map() sous la clé « bouquet-{slug} ».
 * ======================================================================== */
function atelier_pivoine_product_image_url($product, $width = 800) {
    if (!$product instanceof WC_Product) {
        return '';
    }
    $image_id = $product->get_image_id();
    if ($image_id) {
        $src = wp_get_attachment_image_url($image_id, 'woocommerce_single');
        if ($src) {
            return $src;
        }
    }
    return atelier_pivoine_image_url('bouquet-' . $product->get_slug(), $width);
}

function atelier_pivoine_product_image($product, $width = 800, $eager = false) {
    $url = atelier_pivoine_product_image_url($product, $width);
    if (!$url) {
        echo '<div class="product-placeholder"><span>' . esc_html($product->get_name()) . '</span></div>';
        return;
    }
    printf(
        '<img src="%s" alt="%s" width="%d" loading="%s" decoding="async" onerror="this.style.display=\'none\'">',
        esc_url($url),
        esc_attr($product->get_name()),
        (int) $width,
        $eager ? 'eager' : 'lazy'
    );
}

// Panier, mini-panier, e-mails : la photo remplace l'image grise par défaut
add_filter('woocommerce_product_get_image', function ($html, $product, $size, $attr, $placeholder) {
    if ($product->get_image_id()) {
        return $html;
    }
    $url = atelier_pivoine_product_image_url($product, 600);
    if (!$url) {
        return $html;
    }
    return sprintf(
        '<img src="%s" alt="%s" class="attachment-woocommerce_thumbnail" loading="lazy" decoding="async">',
        esc_url($url),
        esc_attr($product->get_name())
    );
}, 10, 5);

// Fiche produit sans image : on montre la photo du bouquet
add_filter('woocommerce_placeholder_img_src', function ($src) {
    global $product;
    if ($product instanceof WC_Product && !$product->get_image_id()) {
        $url = atelier_pivoine_product_image_url($product, 1200);
        if ($url) {
            return $url;
        }
    }
    return $src;
});

/* ==========================================================================
 * Ajout rapide : bouton « Ajouter » puis sélecteur − / +
 * ======================================================================== */
function atelier_pivoine_cart_qty($product_id) {
    if (!WC()->cart) {
        return 0;
    }
    $qty = 0;
    foreach (WC()->cart->get_cart() as $item) {
        if ((int) $item['product_id'] === (int) $product_id) {
            $qty += (int) $item['quantity'];
        }
    }
    return $qty;
}

function atelier_pivoine_qty_controls($product) {
    $id   = $product->get_id();
    $name = $product->get_name();

    if (!$product->is_purchasable() || !$product->is_in_stock() || !$product->is_type('simple')) {
        return sprintf('<a class="add-btn add-btn--ghost" href="%s">Voir</a>', esc_url($product->get_permalink()));
    }

    $qty  = atelier_pivoine_cart_qty($id);
    $html = '<div class="qty-controls" data-ap-ctrl="' . (int) $id . '" data-ap-name="' . esc_attr($name) . '">';
    if (!$qty) {
        // Sans JavaScript, le lien ajoute quand même au panier
        $html .= sprintf(
            '<a class="add-btn" href="%s" data-ap-add="%d" rel="nofollow">Ajouter</a>',
            esc_url(add_query_arg('add-to-cart', $id, wc_get_page_permalink('shop'))),
            (int) $id
        );
    } else {
        $html .= atelier_pivoine_stepper($id, $qty, $name);
    }
    return $html . '</div>';
}

function atelier_pivoine_stepper($id, $qty, $name, $small = false) {
    return sprintf(
        '<div class="stepper%5$s" role="group" aria-label="Quantité de %3$s">'
        . '<button type="button" data-ap-dec="%1$d" aria-label="Retirer un %3$s">−</button>'
        . '<output aria-live="polite">%2$d</output>'
        . '<button type="button" data-ap-inc="%1$d" aria-label="Ajouter un %3$s">+</button>'
        . '</div>',
        (int) $id,
        (int) $qty,
        esc_attr($name),
        '',
        $small ? ' stepper--sm' : ''
    );
}

// Point d'entrée AJAX : fixe la quantité d'un bouquet (0 = retirer)
function atelier_pivoine_ajax_set_qty() {
    check_ajax_referer('ap_cart', 'nonce');

    $product_id = absint($_POST['product_id'] ?? 0);
    $qty        = max(0, min(20, (int) ($_POST['qty'] ?? 0)));
    $product    = wc_get_product($product_id);

    if (!$product || !$product->is_purchasable()) {
        wp_send_json_error(['message' => 'Ce bouquet n\'est plus disponible.'], 400);
    }

    $cart = WC()->cart;
    $key  = null;
    foreach ($cart->get_cart() as $cart_key => $item) {
        if ((int) $item['product_id'] === $product_id) {
            $key = $cart_key;
            break;
        }
    }

    if ($key) {
        $qty ? $cart->set_quantity($key, $qty, false) : $cart->remove_cart_item($key);
    } elseif ($qty) {
        if (!$cart->add_to_cart($product_id, $qty)) {
            $errors = wc_get_notices('error');
            wc_clear_notices();
            $message = $errors ? wp_strip_all_tags($errors[0]['notice']) : 'Impossible d\'ajouter ce bouquet.';
            wp_send_json_error(['message' => $message], 400);
        }
    }

    $cart->calculate_totals();
    wc_clear_notices();

    if (WC()->session && !WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }

    wp_send_json_success(atelier_pivoine_cart_payload());
}
add_action('wc_ajax_ap_set_qty', 'atelier_pivoine_ajax_set_qty');

function atelier_pivoine_cart_payload() {
    ob_start();
    atelier_pivoine_drawer_content();
    $drawer = ob_get_clean();

    $qtys = [];
    foreach (WC()->cart->get_cart() as $item) {
        $pid        = (int) $item['product_id'];
        $qtys[$pid] = ($qtys[$pid] ?? 0) + (int) $item['quantity'];
    }

    return [
        'count'  => WC()->cart->get_cart_contents_count(),
        'drawer' => $drawer,
        'qtys'   => (object) $qtys,
    ];
}

// Données pour main.js
add_action('wp_enqueue_scripts', function () {
    wp_localize_script('atelier-pivoine-main', 'atelierPivoine', [
        'ajaxUrl' => WC_AJAX::get_endpoint('ap_set_qty'),
        'nonce'   => wp_create_nonce('ap_cart'),
    ]);
}, 20);

/* ==========================================================================
 * Panier latéral (tiroir)
 * ======================================================================== */
function atelier_pivoine_checkout_entry_url() {
    // Le parcours validé : sans compte, on passe d'abord par la connexion
    if (is_user_logged_in()) {
        return wc_get_checkout_url();
    }
    return add_query_arg('suite', 'commande', wc_get_page_permalink('myaccount'));
}

function atelier_pivoine_drawer_content() {
    $cart = WC()->cart;

    if (!$cart || $cart->is_empty()) {
        ?>
        <div class="drawer-empty">
            <p class="drawer-empty-title">Votre panier est vide</p>
            <p>Les bouquets du jour n'attendent que vous.</p>
        </div>
        <div class="drawer-foot">
            <a class="btn-secondary btn-block" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Voir les bouquets</a>
        </div>
        <?php
        return;
    }

    $subtotal = (float) $cart->get_subtotal();
    $left     = ATELIER_PIVOINE_LIVRAISON_OFFERTE - $subtotal;
    $percent  = min(100, round($subtotal / ATELIER_PIVOINE_LIVRAISON_OFFERTE * 100));
    ?>
    <ul class="drawer-lines">
        <?php foreach ($cart->get_cart() as $item) :
            $product = $item['data'];
            if (!$product) {
                continue;
            }
            $pid = (int) $item['product_id'];
            ?>
            <li class="drawer-line">
                <a class="drawer-thumb" href="<?php echo esc_url($product->get_permalink()); ?>"><?php atelier_pivoine_product_image(wc_get_product($pid), 200); ?></a>
                <div>
                    <p class="drawer-name"><?php echo esc_html($product->get_name()); ?></p>
                    <div class="drawer-actions">
                        <?php echo atelier_pivoine_stepper($pid, $item['quantity'], $product->get_name(), true); // phpcs:ignore ?>
                        <button type="button" class="drawer-remove" data-ap-remove="<?php echo (int) $pid; ?>">Retirer</button>
                    </div>
                </div>
                <p class="drawer-price"><?php echo wp_kses_post($cart->get_product_subtotal($product, $item['quantity'])); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="drawer-foot">
        <div class="free-ship">
            <?php if ($left > 0) : ?>
                Plus que <strong><?php echo wp_kses_post(wc_price($left)); ?></strong> pour la livraison offerte à Paris
            <?php else : ?>
                La livraison à Paris vous est offerte ✿
            <?php endif; ?>
            <span class="free-ship-bar" aria-hidden="true"><span style="width:<?php echo (int) $percent; ?>%"></span></span>
        </div>
        <div class="drawer-total">
            <span>Sous-total</span>
            <strong><?php echo wp_kses_post(wc_price($subtotal)); ?></strong>
        </div>
        <p class="drawer-note">Livraison 8,90 € à Paris, ou retrait gratuit à l'atelier.</p>
        <a class="btn-primary btn-block" href="<?php echo esc_url(atelier_pivoine_checkout_entry_url()); ?>">Commander</a>
        <a class="drawer-link" href="<?php echo esc_url(wc_get_cart_url()); ?>">Voir le panier détaillé</a>
    </div>
    <?php
}

add_action('wp_footer', function () {
    if (is_checkout()) {
        return;
    }
    ?>
    <div class="drawer-overlay" data-ap-close></div>
    <aside class="cart-drawer" id="cart-drawer" role="dialog" aria-modal="true" aria-labelledby="cart-drawer-title" aria-hidden="true" inert>
        <div class="drawer-head">
            <h2 id="cart-drawer-title">Mon panier</h2>
            <button type="button" class="drawer-close" data-ap-close aria-label="Fermer le panier">✕</button>
        </div>
        <div class="drawer-inner" id="cart-drawer-inner"><?php atelier_pivoine_drawer_content(); ?></div>
    </aside>
    <div class="ap-toast" id="ap-toast" role="status" aria-live="polite"></div>
    <?php
}, 5);

/* ==========================================================================
 * Boutique : filtres par prix
 * ======================================================================== */
function atelier_pivoine_price_filters() {
    return [
        ''        => ['Tous', null, null],
        'moins40' => ['Moins de 40 €', 0, 39.99],
        '40a60'   => ['40 à 60 €', 40, 60],
        'plus60'  => ['Plus de 60 €', 60.01, 100000],
    ];
}

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if (!($query->is_post_type_archive('product') || $query->is_tax('product_cat'))) {
        return;
    }
    $key     = sanitize_key($_GET['prix'] ?? '');
    $filters = atelier_pivoine_price_filters();
    if (!$key || !isset($filters[$key])) {
        return;
    }
    $meta   = (array) $query->get('meta_query');
    $meta[] = [
        'key'     => '_price',
        'value'   => [$filters[$key][1], $filters[$key][2]],
        'compare' => 'BETWEEN',
        'type'    => 'DECIMAL(10,2)',
    ];
    $query->set('meta_query', $meta);
});

add_filter('loop_shop_per_page', function () {
    return 12;
});

/* ==========================================================================
 * Connexion et inscription
 * ======================================================================== */

// Après connexion ou inscription depuis le panier : on revient à la commande
function atelier_pivoine_redirect_after_auth($redirect) {
    if (($_POST['ap_suite'] ?? '') === 'commande' && WC()->cart && !WC()->cart->is_empty()) {
        return wc_get_checkout_url();
    }
    return $redirect;
}
add_filter('woocommerce_login_redirect', 'atelier_pivoine_redirect_after_auth', 20);
add_filter('woocommerce_registration_redirect', 'atelier_pivoine_redirect_after_auth', 20);

// Prénom et nom à l'inscription
add_filter('woocommerce_process_registration_errors', function ($errors) {
    if (empty(trim($_POST['ap_prenom'] ?? ''))) {
        $errors->add('ap_prenom', 'Indiquez votre prénom.');
    }
    if (empty(trim($_POST['ap_nom'] ?? ''))) {
        $errors->add('ap_nom', 'Indiquez votre nom.');
    }
    return $errors;
});

add_action('woocommerce_created_customer', function ($customer_id) {
    $prenom = sanitize_text_field(wp_unslash($_POST['ap_prenom'] ?? ''));
    $nom    = sanitize_text_field(wp_unslash($_POST['ap_nom'] ?? ''));
    if ($prenom) {
        update_user_meta($customer_id, 'first_name', $prenom);
        update_user_meta($customer_id, 'billing_first_name', $prenom);
        update_user_meta($customer_id, 'shipping_first_name', $prenom);
    }
    if ($nom) {
        update_user_meta($customer_id, 'last_name', $nom);
        update_user_meta($customer_id, 'billing_last_name', $nom);
        update_user_meta($customer_id, 'shipping_last_name', $nom);
    }
    if ($prenom) {
        wp_update_user(['ID' => $customer_id, 'display_name' => trim($prenom . ' ' . $nom)]);
    }
});

/* ==========================================================================
 * Mon compte : menu en français et plus court
 * ======================================================================== */
add_filter('woocommerce_account_menu_items', function ($items) {
    return [
        'dashboard'       => 'Accueil',
        'orders'          => 'Mes commandes',
        'edit-address'    => 'Mes adresses',
        'edit-account'    => 'Mes informations',
        'customer-logout' => 'Se déconnecter',
    ];
});

/* ==========================================================================
 * Commande : date, créneau et message sur la carte
 * ======================================================================== */
function atelier_pivoine_creneaux() {
    return [
        'matin'      => 'Matin · 9 h – 13 h',
        'apres-midi' => 'Après-midi · 14 h – 19 h',
    ];
}

function atelier_pivoine_now() {
    return new DateTimeImmutable('now', wp_timezone());
}

add_filter('woocommerce_checkout_fields', function ($fields) {
    $today = atelier_pivoine_now();
    $min   = (int) $today->format('G') >= ATELIER_PIVOINE_HEURE_LIMITE ? $today->modify('+1 day') : $today;

    // Champs inutiles pour un fleuriste de quartier
    unset($fields['billing']['billing_company'], $fields['shipping']['shipping_company']);

    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_phone']['label']    = 'Téléphone';
    $fields['billing']['billing_phone']['description'] = 'Le livreur vous appelle s\'il ne trouve personne.';

    $fields['order'] = [
        'ap_date' => [
            'type'              => 'date',
            'label'             => 'Date de livraison ou de retrait',
            'required'          => true,
            'class'             => ['form-row-first'],
            'default'           => $min->format('Y-m-d'),
            'custom_attributes' => ['min' => $min->format('Y-m-d')],
            'priority'          => 10,
        ],
        'ap_creneau' => [
            'type'     => 'select',
            'label'    => 'Créneau',
            'required' => true,
            'class'    => ['form-row-last'],
            'options'  => atelier_pivoine_creneaux(),
            'priority' => 20,
        ],
        'ap_message' => [
            'type'              => 'textarea',
            'label'             => 'Un petit mot sur la carte',
            'required'          => false,
            'placeholder'       => 'Joyeux anniversaire ! Avec tout mon amour…',
            'description'       => 'Écrit à la main par Camille. 200 caractères maximum.',
            'custom_attributes' => ['maxlength' => 200],
            'class'             => ['form-row-wide', 'ap-card-message'],
            'priority'          => 30,
        ],
        'order_comments' => [
            'type'        => 'textarea',
            'label'       => 'Indications pour le livreur',
            'required'    => false,
            'placeholder' => 'Code de la porte, étage…',
            'class'       => ['form-row-wide'],
            'priority'    => 40,
        ],
    ];
    return $fields;
});

add_action('woocommerce_after_checkout_validation', function ($data, $errors) {
    $date = sanitize_text_field($_POST['ap_date'] ?? '');
    $d    = DateTimeImmutable::createFromFormat('!Y-m-d', $date, wp_timezone());
    $now  = atelier_pivoine_now();

    if ($date === '') {
        // WooCommerce signale déjà le champ obligatoire vide
    } elseif (!$d) {
        $errors->add('ap_date', 'Choisissez une date de livraison ou de retrait.');
    } elseif ($d < $now->setTime(0, 0)) {
        $errors->add('ap_date', 'La date choisie est déjà passée.');
    } elseif ($d->format('Y-m-d') === $now->format('Y-m-d') && (int) $now->format('G') >= ATELIER_PIVOINE_HEURE_LIMITE) {
        $errors->add('ap_date', 'Pour être livré aujourd\'hui, il faut commander avant ' . ATELIER_PIVOINE_HEURE_LIMITE . ' h. Choisissez demain ou plus tard.');
    }

    if (!isset(atelier_pivoine_creneaux()[$_POST['ap_creneau'] ?? ''])) {
        $errors->add('ap_creneau', 'Choisissez un créneau.');
    }

    if (mb_strlen(wp_unslash($_POST['ap_message'] ?? '')) > 200) {
        $errors->add('ap_message', 'Le message de la carte doit faire 200 caractères maximum.');
    }
}, 10, 2);

add_action('woocommerce_checkout_create_order', function ($order) {
    $order->update_meta_data('_ap_date', sanitize_text_field($_POST['ap_date'] ?? ''));
    $order->update_meta_data('_ap_creneau', sanitize_key($_POST['ap_creneau'] ?? ''));
    $order->update_meta_data('_ap_message', sanitize_textarea_field(wp_unslash($_POST['ap_message'] ?? '')));
});

function atelier_pivoine_order_extras($order) {
    $date = $order->get_meta('_ap_date');
    if (!$date) {
        return [];
    }
    $d       = DateTimeImmutable::createFromFormat('!Y-m-d', $date, wp_timezone());
    $creneau = atelier_pivoine_creneaux()[$order->get_meta('_ap_creneau')] ?? '';
    $message = $order->get_meta('_ap_message');

    return [
        'Livraison / retrait' => ($d ? wp_date('l j F Y', $d->getTimestamp()) : $date) . ($creneau ? ' · ' . $creneau : ''),
        'Message de la carte' => $message !== '' ? '« ' . $message . ' »' : 'Sans carte',
    ];
}

function atelier_pivoine_print_order_extras($order) {
    $extras = atelier_pivoine_order_extras($order);
    if (!$extras) {
        return;
    }
    echo '<section class="order-extras"><h2>Votre bouquet</h2><dl>';
    foreach ($extras as $label => $value) {
        echo '<dt>' . esc_html($label) . '</dt><dd>' . esc_html($value) . '</dd>';
    }
    echo '</dl></section>';
}
add_action('woocommerce_order_details_after_order_table', 'atelier_pivoine_print_order_extras');

add_action('woocommerce_admin_order_data_after_shipping_address', function ($order) {
    foreach (atelier_pivoine_order_extras($order) as $label => $value) {
        echo '<p><strong>' . esc_html($label) . ' :</strong><br>' . esc_html($value) . '</p>';
    }
});

add_action('woocommerce_email_order_meta', function ($order, $sent_to_admin, $plain_text) {
    foreach (atelier_pivoine_order_extras($order) as $label => $value) {
        echo $plain_text
            ? esc_html($label) . ' : ' . esc_html($value) . "\n"
            : '<p><strong>' . esc_html($label) . ' :</strong> ' . esc_html($value) . '</p>';
    }
}, 10, 3);

/* ==========================================================================
 * Étapes du parcours (commande et confirmation)
 * ======================================================================== */
function atelier_pivoine_steps($current) {
    $steps = [
        1 => 'Panier',
        2 => is_user_logged_in() ? 'Connexion' : 'Vos coordonnées',
        3 => 'Livraison et paiement',
        4 => 'Confirmation',
    ];
    echo '<ol class="checkout-steps" aria-label="Étapes de la commande">';
    foreach ($steps as $n => $label) {
        $state = $n < $current ? 'done' : ($n === $current ? 'current' : '');
        printf(
            '<li class="%s"%s><span class="step-dot">%s</span><span class="step-label">%s</span></li>',
            esc_attr($state),
            $n === $current ? ' aria-current="step"' : '',
            $n < $current ? '✓' : (int) $n,
            esc_html($label)
        );
    }
    echo '</ol>';
}

add_action('woocommerce_before_checkout_form', function () {
    atelier_pivoine_steps(3);
}, 1);

add_action('woocommerce_before_thankyou', function () {
    atelier_pivoine_steps(5);
}, 1);
