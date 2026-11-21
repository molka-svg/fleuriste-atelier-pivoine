<?php
/**
 * Configure la boutique Atelier Pivoine dans le WordPress local (Docker).
 *
 * Prérequis : WooCommerce 10.7.0 installé et activé (la 11.x exige WordPress 7.0,
 * notre image Docker est en 6.8).
 *
 * Utilisation :
 *   docker cp scripts/configurer-boutique.php <conteneur-wordpress>:/tmp/
 *   docker exec -u www-data <conteneur-wordpress> php /tmp/configurer-boutique.php
 *
 * Le script peut être relancé : il met à jour sans créer de doublons.
 * Données fictives uniquement (commerce, adresse, produits).
 */
require '/var/www/html/wp-load.php';

// Nom du site (affiché dans l'onglet du navigateur)
update_option('blogname', 'Atelier Pivoine');
update_option('blogdescription', 'Fleuriste de quartier, Paris 11e');

// Adresses propres : /boutique/, /panier/…
update_option('permalink_structure', '/%postname%/');

// Boutique : France, euros, Paris
update_option('woocommerce_default_country', 'FR:75');
update_option('woocommerce_store_address', '27 rue Imaginaire');
update_option('woocommerce_store_city', 'Paris');
update_option('woocommerce_store_postcode', '75011');
update_option('woocommerce_currency', 'EUR');
update_option('woocommerce_currency_pos', 'right_space');
update_option('woocommerce_price_thousand_sep', ' ');
update_option('woocommerce_price_decimal_sep', ',');
update_option('woocommerce_price_num_decimals', 2);
update_option('woocommerce_allowed_countries', 'specific');
update_option('woocommerce_specific_allowed_countries', ['FR']);
update_option('woocommerce_ship_to_countries', '');
update_option('woocommerce_calc_taxes', 'no');
update_option('woocommerce_coming_soon', 'no');
update_option('woocommerce_store_pages_only', 'no');
update_option('woocommerce_onboarding_profile', ['skipped' => true]);
update_option('woocommerce_task_list_hidden', 'yes');

// Comptes clients : commande sans compte possible, création de compte au moment de commander
update_option('woocommerce_enable_guest_checkout', 'yes');
update_option('woocommerce_enable_checkout_login_reminder', 'yes');
update_option('woocommerce_enable_signup_and_login_from_checkout', 'yes');
update_option('woocommerce_enable_myaccount_registration', 'yes');
update_option('woocommerce_registration_generate_password', 'no');
update_option('woocommerce_registration_generate_username', 'yes');

// Pages : noms et adresses en français, versions « classiques » (shortcodes) pour que le thème les habille
$pages = [
    'shop'      => ['woocommerce_shop_page_id',      'Nos bouquets', 'boutique',   ''],
    'cart'      => ['woocommerce_cart_page_id',      'Panier',       'panier',     '[woocommerce_cart]'],
    'checkout'  => ['woocommerce_checkout_page_id',  'Commande',     'commande',   '[woocommerce_checkout]'],
    'myaccount' => ['woocommerce_myaccount_page_id', 'Mon compte',   'mon-compte', '[woocommerce_my_account]'],
];
foreach ($pages as $key => [$option, $title, $slug, $content]) {
    $id = (int) get_option($option);
    if (!$id) { echo "Page $key introuvable\n"; continue; }
    $data = ['ID' => $id, 'post_title' => $title, 'post_name' => $slug, 'post_status' => 'publish'];
    if ($content) $data['post_content'] = $content;
    wp_update_post($data);
    echo "Page $title → /$slug/\n";
}

// Page « L'atelier » avec son gabarit
$atelier = get_page_by_path('atelier');
$atelier_id = $atelier ? $atelier->ID : wp_insert_post([
    'post_type' => 'page', 'post_status' => 'publish', 'post_title' => "L'atelier", 'post_name' => 'atelier',
]);
update_post_meta($atelier_id, '_wp_page_template', 'page-atelier.php');
echo "Page L'atelier → /atelier/\n";

// Page d'accueil : on garde front-page.php du thème (affichage « derniers articles »)
update_option('show_on_front', 'posts');

// Rayon « Bouquets de saison »
$term = term_exists('bouquets-de-saison', 'product_cat');
if (!$term) $term = wp_insert_term('Bouquets de saison', 'product_cat', [
    'slug' => 'bouquets-de-saison',
    'description' => "Composés chaque matin avec les fleurs arrivées à l'aube.",
]);
$cat_id = (int) (is_array($term) ? $term['term_id'] : $term);
update_option('default_product_cat', $cat_id);
// Supprime la catégorie « Non classé » créée par WooCommerce si elle est vide
$unc = get_term_by('slug', 'uncategorized', 'product_cat') ?: get_term_by('slug', 'non-classe', 'product_cat');
if ($unc && (int) $unc->term_id !== $cat_id && !$unc->count) wp_delete_term($unc->term_id, 'product_cat');

// 8 bouquets de test (mêmes que la maquette validée)
$bouquets = [
    ['Le bouquet de Camille', "Roses anciennes poudrées et gypsophile, le classique de l'atelier", 62, true],
    ["Jardin d'été",          "Pivoines roses nouées d'un ruban de soie",                         45, false],
    ["Douceur d'automne",     'Roses cuivrées aux reflets de feu',                                 49, false],
    ['Neige de décembre',     'Renoncules blanches, tulipes et eucalyptus',                        55, false],
    ['Éveil de printemps',    'Pivoines roses et blanches dans leur papier kraft',                 39, false],
    ['Petit mot',             'Petites roses de jardin mélangées, pour dire merci',                25, false],
    ['Champêtre',             'Pivoine, seringat et feuillages cueillis dans les champs',          35, false],
    ['Grand jour',            'Roses pêche et jacinthes pour les tables de fête',                  89, false],
];
foreach ($bouquets as $i => [$name, $desc, $price, $featured]) {
    $existing = get_page_by_title($name, OBJECT, 'product');
    $p = $existing ? wc_get_product($existing->ID) : new WC_Product_Simple();
    $p->set_name($name);
    $p->set_short_description($desc);
    $p->set_description($desc . '. Composé à la main le jour de la livraison, emballé dans un papier kraft noué d\'un ruban.');
    $p->set_regular_price((string) $price);
    $p->set_category_ids([$cat_id]);
    $p->set_featured($featured);
    $p->set_menu_order($i);
    $p->set_status('publish');
    $p->set_manage_stock(true);
    $p->set_stock_quantity(20);
    $p->save();
    echo "Bouquet : $name ($price €)\n";
}

// Livraison : zone Paris (75001 à 75020)
$zone = null;
foreach (WC_Shipping_Zones::get_zones() as $z) if ($z['zone_name'] === 'Paris') $zone = new WC_Shipping_Zone($z['id']);
if (!$zone) {
    $zone = new WC_Shipping_Zone();
    $zone->set_zone_name('Paris');
    $zone->add_location('FR', 'country');
    $zone->set_locations([['code' => '75*', 'type' => 'postcode'], ['code' => 'FR', 'type' => 'country']]);
    $zone->save();
    $flat = $zone->add_shipping_method('flat_rate');
    update_option("woocommerce_flat_rate_{$flat}_settings", ['title' => 'Livraison à Paris', 'tax_status' => 'none', 'cost' => '8.90']);
    $free = $zone->add_shipping_method('free_shipping');
    update_option("woocommerce_free_shipping_{$free}_settings", ['title' => 'Livraison offerte', 'requires' => 'min_amount', 'min_amount' => '60', 'ignore_discounts' => 'no']);
    $pick = $zone->add_shipping_method('local_pickup');
    update_option("woocommerce_local_pickup_{$pick}_settings", ['title' => "Retrait à l'atelier", 'tax_status' => 'none', 'cost' => '']);
    echo "Zone de livraison Paris créée\n";
}

// Paiement de test : paiement au retrait / à la livraison
update_option('woocommerce_cod_settings', [
    'enabled' => 'yes',
    'title' => 'Paiement à la livraison ou au retrait',
    'description' => "Environnement de test : aucun paiement en ligne n'est demandé.",
    'instructions' => 'Vous réglerez à la réception de vos fleurs.',
    'enable_for_methods' => [],
    'enable_for_virtual' => 'yes',
]);

flush_rewrite_rules();
echo "Configuration terminée.\n";
echo "Si /boutique/ renvoie une erreur 404 : Réglages > Permaliens > Enregistrer (écrit le .htaccess).\n";
