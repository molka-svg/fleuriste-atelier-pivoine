<?php
/**
 * Atelier Pivoine — functions.php
 */

if (!defined('ABSPATH')) {
    exit;
}

/* --------------------------------------------------------------------------
 * Configuration du thème
 * ------------------------------------------------------------------------ */
function atelier_pivoine_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'menu_principal' => 'Menu principal',
    ]);
}
add_action('after_setup_theme', 'atelier_pivoine_setup');

/* --------------------------------------------------------------------------
 * Styles & scripts
 * ------------------------------------------------------------------------ */
function atelier_pivoine_assets() {
    // Polices : DM Serif Display (titres) + DM Sans (texte)
    wp_enqueue_style(
        'atelier-pivoine-fonts',
        'https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap',
        [],
        null
    );

    $style_path = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style(
        'atelier-pivoine-style',
        get_stylesheet_uri(),
        ['atelier-pivoine-fonts'],
        file_exists($style_path) ? filemtime($style_path) : '2.0'
    );

    $js_path = get_template_directory() . '/assets/js/main.js';
    if (file_exists($js_path)) {
        wp_enqueue_script(
            'atelier-pivoine-main',
            get_template_directory_uri() . '/assets/js/main.js',
            [],
            filemtime($js_path),
            ['in_footer' => true, 'strategy' => 'defer']
        );
    }
}
add_action('wp_enqueue_scripts', 'atelier_pivoine_assets');

// Accélère le chargement des polices
function atelier_pivoine_resource_hints($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = ['href' => 'https://fonts.googleapis.com'];
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous'];
    }
    return $urls;
}
add_filter('wp_resource_hints', 'atelier_pivoine_resource_hints', 10, 2);

// Icône de l'onglet : la fleur du logo (images/favicon.svg + versions PNG).
// Si une « Icône du site » est choisie dans Apparence > Personnaliser, c'est elle qui l'emporte.
function atelier_pivoine_favicon() {
    if (has_site_icon()) {
        return;
    }
    $dir = get_template_directory_uri() . '/images/';
    printf('<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url($dir . 'favicon.svg'));
    printf('<link rel="icon" href="%s" sizes="32x32" type="image/png">' . "\n", esc_url($dir . 'favicon-32.png'));
    printf('<link rel="icon" href="%s" sizes="512x512" type="image/png">' . "\n", esc_url($dir . 'icon-512.png'));
    printf('<link rel="apple-touch-icon" href="%s">' . "\n", esc_url($dir . 'apple-touch-icon.png'));
    echo '<meta name="theme-color" content="#2F4A3A">' . "\n";
}
add_action('wp_head', 'atelier_pivoine_favicon', 2);
add_action('login_head', 'atelier_pivoine_favicon');
add_action('admin_head', 'atelier_pivoine_favicon');

// Classe "js" : les animations d'apparition ne s'activent que si le JavaScript fonctionne
function atelier_pivoine_js_class() {
    echo "<script>document.documentElement.className+=' js';</script>\n";
}
add_action('wp_head', 'atelier_pivoine_js_class', 1);

/* --------------------------------------------------------------------------
 * Images du site
 *
 * Toutes les photos de la page d'accueil sont déclarées ICI, à un seul endroit.
 *
 * Pour utiliser vos propres photos : déposez simplement un fichier dans
 * le dossier /images/ du thème avec le nom de la clé (ex. hero.jpg, story.webp,
 * cat-1.jpg…). Il remplacera automatiquement la photo Unsplash.
 * ------------------------------------------------------------------------ */
function atelier_pivoine_image_map() {
    return [
        // Bannière plein écran
        'hero'   => '1519657603751-38e2d6e37f09', // roses roses et blanches

        // Univers (3 colonnes)
        'cat-1'  => '1484231398360-515f8b80b1a8', // bouquet rose et dahlia
        'cat-2'  => '1598961111901-e64e25bc0a4c', // bouquet rose et blanc
        'cat-3'  => '1503513883989-25ef8b2f1a53', // bouquet de roses

        // Bouquets de démonstration (affichés tant que WooCommerce n'a pas de produits)
        'prod-1' => '1487530811176-3780de880c2d', // bouquet généreux
        'prod-2' => '1552174965-c6616f62fc4f',    // roses roses et rouges
        'prod-3' => '1676470932768-cf78e791290d', // vase de fleurs roses
        'prod-4' => '1600368037440-96975dc2079c', // roses à la rosée
        'prod-5' => '1598961111901-e64e25bc0a4c', // bouquet rose et blanc

        // L'atelier
        'story'  => '1552174965-c6616f62fc4f',

        // Bandeau final
        'cta'    => '1487530811176-3780de880c2d',

        // Bandeau de la page Boutique
        'boutique'       => '1782038522299-c1568390810f', // roses roses et giroflées

        // Page « L'atelier » : grande photo, puis le savoir-faire
        'atelier'        => '1552174965-c6616f62fc4f',
        'atelier-mains'  => '1598961111901-e64e25bc0a4c',

        // Connexion / Mon compte (colonne photo)
        'connexion'      => '1503513883989-25ef8b2f1a53',

        // Bouquets de la boutique (clé = « bouquet- » + adresse du produit)
        // Utilisées seulement si le produit n'a pas d'image dans WordPress.
        'bouquet-le-bouquet-de-camille' => '1519657603751-38e2d6e37f09',
        'bouquet-jardin-dete'           => '1484231398360-515f8b80b1a8',
        'bouquet-douceur-dautomne'      => '1503513883989-25ef8b2f1a53',
        'bouquet-neige-de-decembre'     => '1598961111901-e64e25bc0a4c',
        'bouquet-eveil-de-printemps'    => '1487530811176-3780de880c2d',
        'bouquet-petit-mot'             => '1676470932768-cf78e791290d',
        'bouquet-champetre'             => '1600368037440-96975dc2079c',
        'bouquet-grand-jour'            => '1552174965-c6616f62fc4f',
    ];
}

function atelier_pivoine_image_url($key, $width = 1400) {
    // 1) Une photo déposée dans /images/ a la priorité
    foreach (['jpg', 'jpeg', 'webp', 'png'] as $ext) {
        $file = get_template_directory() . "/images/{$key}.{$ext}";
        if (file_exists($file)) {
            return get_template_directory_uri() . "/images/{$key}.{$ext}";
        }
    }

    // 2) Sinon, photo Unsplash
    $map = atelier_pivoine_image_map();
    if (!isset($map[$key])) {
        return '';
    }
    return 'https://images.unsplash.com/photo-' . $map[$key] . '?auto=format&fit=crop&w=' . (int) $width . '&q=80';
}

/**
 * Affiche une balise <img> avec chargement différé et repli propre
 * (si la photo ne charge pas, le fond rose du conteneur reste visible).
 */
function atelier_pivoine_img($key, $alt = '', $width = 1000, $eager = false) {
    $url = atelier_pivoine_image_url($key, $width);
    if (!$url) {
        return;
    }
    printf(
        '<img src="%s" alt="%s" width="%d" loading="%s" decoding="async" onerror="this.style.display=\'none\'">',
        esc_url($url),
        esc_attr($alt),
        (int) $width,
        $eager ? 'eager' : 'lazy'
    );
}

/* --------------------------------------------------------------------------
 * WooCommerce
 * ------------------------------------------------------------------------ */

// Compteur du panier mis à jour sans recharger la page
function atelier_pivoine_cart_fragment($fragments) {
    $count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
    $fragments['span.cart-count'] = '<span class="cart-count' . ($count ? '' : ' is-empty') . '">' . (int) $count . '</span>';
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'atelier_pivoine_cart_fragment');

// Nombre de produits par ligne dans la boutique
add_filter('loop_shop_columns', function () {
    return 3;
});

// Boutique, panier latéral, commande et compte client
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/boutique.php';
}
