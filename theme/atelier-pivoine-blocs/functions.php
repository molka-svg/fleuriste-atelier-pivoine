<?php
/**
 * Atelier Pivoine (blocs) — thème enfant de Twenty Twenty-Five.
 *
 * L'apparence se règle dans Apparence > Éditeur :
 *   - couleurs et polices : theme.json (Styles) ;
 *   - en-tête et pied de page : parts/header.html, parts/footer.html ;
 *   - accueil : templates/front-page.html + patterns/accueil.php ;
 *   - fiche produit et boutique : templates/single-product.html, archive-product.html.
 *
 * Ce fichier ne garde que ce qui ne se fait pas en blocs : chargement des polices,
 * panier latéral, sélecteur − / +, champs de commande (inc/boutique.php).
 */

if (!defined('ABSPATH')) {
    exit;
}

const ATELIER_PIVOINE_FONTS = 'https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap';

add_action('after_setup_theme', function () {
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    // Les polices de la charte aussi dans l'éditeur
    add_editor_style(ATELIER_PIVOINE_FONTS);
});

add_action('wp_enqueue_scripts', function () {
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();

    wp_enqueue_style('atelier-pivoine-fonts', ATELIER_PIVOINE_FONTS, [], null);
    wp_enqueue_style('atelier-pivoine-boutique', $uri . '/assets/css/boutique.css', [], filemtime($dir . '/assets/css/boutique.css'));
    wp_enqueue_style('atelier-pivoine-blocs', get_stylesheet_uri(), ['atelier-pivoine-boutique'], filemtime($dir . '/style.css'));
    wp_enqueue_script(
        'atelier-pivoine-main',
        $uri . '/assets/js/main.js',
        [],
        filemtime($dir . '/assets/js/main.js'),
        ['in_footer' => true, 'strategy' => 'defer']
    );
});

// Accélère le chargement des polices
add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = ['href' => 'https://fonts.googleapis.com'];
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous'];
    }
    return $urls;
}, 10, 2);

// Classe « js » : certaines animations ne s'activent que si le JavaScript fonctionne
add_action('wp_head', function () {
    echo "<script>document.documentElement.className+=' js';</script>\n";
}, 1);

/* --------------------------------------------------------------------------
 * Photos du thème (dossier images/)
 * Utilisées par inc/boutique.php (bouquet sans image) et la page de connexion.
 * ------------------------------------------------------------------------ */
function atelier_pivoine_image_url($key, $width = 1400) {
    foreach (['jpg', 'jpeg', 'webp', 'png'] as $ext) {
        if (file_exists(get_stylesheet_directory() . "/images/{$key}.{$ext}")) {
            return get_stylesheet_directory_uri() . "/images/{$key}.{$ext}";
        }
    }
    return '';
}

function atelier_pivoine_img($key, $alt = '', $width = 1000, $eager = false) {
    $url = atelier_pivoine_image_url($key, $width);
    if (!$url) {
        return;
    }
    printf(
        '<img src="%s" alt="%s" width="%d" loading="%s" decoding="async">',
        esc_url($url),
        esc_attr($alt),
        (int) $width,
        $eager ? 'eager' : 'lazy'
    );
}

// Boutique : panier latéral, sélecteur − / +, panier, commande, compte client
if (class_exists('WooCommerce')) {
    require_once get_stylesheet_directory() . '/inc/boutique.php';
    require_once get_stylesheet_directory() . '/inc/design.php';
}
