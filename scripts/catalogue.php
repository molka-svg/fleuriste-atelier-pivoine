<?php
/**
 * Catalogue Atelier Pivoine (partie 3 du sujet).
 *
 *   - 12 bouquets répartis dans 3 catégories ;
 *   - une photo principale au même format (900 x 1125) avec texte alternatif ;
 *   - une galerie sur 3 bouquets ;
 *   - « Le bouquet de Camille » en 3 tailles (produit variable, prix et stock par taille) ;
 *   - « Neige de décembre » en promotion du 20 novembre au 24 décembre 2026 ;
 *   - une UGS sur chaque bouquet et chaque taille, stock géré partout ;
 *   - « Douceur d'automne » volontairement en rupture (fin de saison) ;
 *   - le logo et l'icône du site.
 *
 * À lancer APRÈS scripts/configurer-boutique.php, avec le thème « Atelier Pivoine (blocs) »
 * installé (les photos sont dans son dossier images/, sources dans images/CREDITS.md) :
 *
 *   docker cp scripts/catalogue.php <conteneur-wordpress>:/tmp/
 *   docker exec -u www-data <conteneur-wordpress> php /tmp/catalogue.php
 *
 * Relançable sans doublon. Commerce, produits et prix fictifs.
 */
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

const AP_PHOTOS = WP_CONTENT_DIR . '/themes/atelier-pivoine-blocs/images/';

/* --------------------------------------------------------------------------
 * 1. Les 3 catégories
 * ------------------------------------------------------------------------ */
$categories = [
    'bouquets-de-saison' => ['Bouquets de saison', "Composés chaque matin avec les fleurs arrivées à l'aube."],
    'grandes-occasions'  => ['Grandes occasions', 'Mariages, anniversaires, naissances : des bouquets généreux pour les jours qui comptent.'],
    'petites-attentions' => ['Petites attentions', 'De petits bouquets à moins de 40 € pour dire merci, pardon ou « je pense à vous ».'],
];
$cat = [];
foreach ($categories as $slug => [$nom, $desc]) {
    $terme = get_term_by('slug', $slug, 'product_cat');
    if ($terme) {
        $id = (int) $terme->term_id;
        wp_update_term($id, 'product_cat', ['name' => $nom, 'description' => $desc]);
    } else {
        $nouveau = wp_insert_term($nom, 'product_cat', ['slug' => $slug, 'description' => $desc]);
        if (is_wp_error($nouveau)) {
            exit("Erreur catégorie $nom : " . $nouveau->get_error_message() . "\n");
        }
        $id = (int) $nouveau['term_id'];
    }
    $cat[$slug] = $id;
    echo "Catégorie : $nom\n";
}

/* --------------------------------------------------------------------------
 * 2. Photos : importées une seule fois dans la médiathèque
 *    (réimportées si le fichier a changé)
 * ------------------------------------------------------------------------ */
function ap_photo($fichier, $alt) {
    $chemin = AP_PHOTOS . $fichier;
    if (!file_exists($chemin)) {
        echo "  Photo manquante : $fichier\n";
        return 0;
    }
    $md5  = md5_file($chemin);
    $deja = get_posts([
        'post_type'   => 'attachment',
        'post_status' => 'inherit',
        'meta_key'    => '_ap_source',
        'meta_value'  => $fichier,
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);
    if ($deja && get_post_meta($deja[0], '_ap_md5', true) === $md5) {
        update_post_meta($deja[0], '_wp_attachment_image_alt', $alt);
        return (int) $deja[0];
    }
    if ($deja) {
        wp_delete_attachment($deja[0], true); // la photo a changé : on la remplace
    }
    $envoi = wp_upload_bits($fichier, null, file_get_contents($chemin));
    if ($envoi['error']) {
        echo "  Erreur photo $fichier : {$envoi['error']}\n";
        return 0;
    }
    $id = wp_insert_attachment([
        'post_mime_type' => wp_check_filetype($envoi['file'])['type'],
        'post_title'     => preg_replace('/\.\w+$/', '', $fichier),
        'post_status'    => 'inherit',
    ], $envoi['file']);
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $envoi['file']));
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    update_post_meta($id, '_ap_source', $fichier);
    update_post_meta($id, '_ap_md5', $md5);
    return (int) $id;
}

/* --------------------------------------------------------------------------
 * 3. Les 12 bouquets
 *    UGS, nom, catégorie, prix TTC (null = variable), stock, photos, description courte, description longue
 * ------------------------------------------------------------------------ */
$bouquets = [
    ['AP-BQ-001', 'Le bouquet de Camille', 'grandes-occasions', null, null,
        ['bouquet-le-bouquet-de-camille.jpg'],
        "Roses anciennes poudrées et gypsophile : le classique de l'atelier, en trois tailles.",
        "C'est le bouquet que Camille compose depuis l'ouverture de l'atelier : des roses anciennes aux tons poudrés, un nuage de gypsophile et quelques tiges d'eucalyptus. Choisissez sa taille selon l'occasion : le petit pour un dîner, le moyen pour un anniversaire, le généreux pour un grand jour. Coupez les tiges en biseau et changez l'eau tous les deux jours : il vous accompagnera une bonne semaine."],
    ['AP-BQ-002', "Jardin d'été", 'bouquets-de-saison', 45, 15,
        ['bouquet-jardin-dete.jpg'],
        "Pivoines roses nouées d'un ruban de soie, le parfum des beaux jours.",
        "Des pivoines roses à peine ouvertes, accompagnées de feuillages tendres et nouées d'un ruban de soie. Elles s'épanouissent dans les deux jours qui suivent la livraison : c'est tout leur charme. Hauteur : environ 40 cm. Placez-les au frais, loin des fruits et du soleil direct."],
    ['AP-BQ-003', "Douceur d'automne", 'bouquets-de-saison', 49, 0,
        ['bouquet-douceur-dautomne.jpg'],
        'Roses cuivrées aux reflets de feu, pour les premières soirées fraîches.',
        "Des roses aux teintes cuivrées, orangées et bordeaux, associées à des branches de saison. Un bouquet chaleureux qui rappelle les feuilles d'octobre. Hauteur : environ 40 cm. Ce bouquet suit la saison des roses d'automne : il est en rupture jusqu'à leur retour."],
    ['AP-BQ-004', 'Neige de décembre', 'bouquets-de-saison', 55, 15,
        ['bouquet-neige-de-decembre.jpg'],
        "Renoncules blanches, tulipes et eucalyptus : la douceur de l'hiver.",
        "Un bouquet tout en blanc et vert tendre : renoncules, tulipes et eucalyptus argenté. Idéal pour décorer une table de fête ou à offrir pendant les vacances de Noël. Hauteur : environ 45 cm. Prix doux jusqu'au 24 décembre."],
    ['AP-BQ-005', 'Éveil de printemps', 'bouquets-de-saison', 39, 15,
        ['bouquet-eveil-de-printemps.jpg', 'bouquet-eveil-de-printemps-2.jpg', 'bouquet-eveil-de-printemps-3.jpg'],
        'Pivoines roses et blanches dans leur papier kraft.',
        "Des pivoines roses et blanches, simplement emballées dans notre papier kraft noué d'un ruban. Un bouquet qui sent le retour des beaux jours. Hauteur : environ 35 cm. Les boutons s'ouvrent en un à deux jours : posez-les dans un vase rempli à moitié d'eau fraîche."],
    ['AP-BQ-006', 'Petit mot', 'petites-attentions', 25, 20,
        ['bouquet-petit-mot.jpg'],
        'Petites roses de jardin mélangées, pour dire merci.',
        "Quelques roses de jardin de toutes les couleurs, assemblées en un petit bouquet rond. Parfait pour dire merci, s'excuser ou souhaiter une belle journée. Hauteur : environ 25 cm. Pensez à ajouter un petit mot sur la carte au moment de la commande."],
    ['AP-BQ-007', 'Champêtre', 'petites-attentions', 35, 15,
        ['bouquet-champetre.jpg'],
        'Pivoine, seringat et feuillages des champs, comme une cueillette.',
        "Un bouquet libre et léger, comme une cueillette au bord d'un chemin : pivoine, seringat, fleurs sauvages et feuillages. Chaque composition est unique, selon les arrivages du matin. Hauteur : environ 35 cm."],
    ['AP-BQ-008', 'Grand jour', 'grandes-occasions', 89, 6,
        ['bouquet-grand-jour.jpg'],
        'Roses pêche et jacinthes pour les tables de fête.',
        "Notre plus grand bouquet : des roses couleur pêche, des jacinthes et des feuillages généreux pour un mariage, un anniversaire ou une réception. Il trône au centre d'une table. Hauteur : environ 60 cm. Pour un événement, appelez-nous : nous adaptons les couleurs à votre décoration."],
    ['AP-BQ-009', 'Tendresse', 'grandes-occasions', 52, 10,
        ['bouquet-tendresse.jpg'],
        'Roses tendres et œillets roses, pour une naissance ou une déclaration.',
        "Un bouquet tout en rose : roses aux pétales froissés, œillets mignardise et brins de fleurs de cire. Doux et romantique, il célèbre une naissance, une fête des mères ou une déclaration. Hauteur : environ 40 cm."],
    ['AP-BQ-010', 'Promesse de printemps', 'bouquets-de-saison', 42, 12,
        ['bouquet-promesse-de-printemps.jpg'],
        "Asters violets et narcisses : un éclat de couleurs dès l'hiver.",
        "Des asters d'un violet intense, des narcisses blancs au cœur jaune et des chrysanthèmes ronds. Un bouquet vif qui annonce le printemps avant l'heure. Hauteur : environ 35 cm. Les narcisses aiment l'eau fraîche : changez-la souvent."],
    ['AP-BQ-011', 'Cornet de tulipes', 'petites-attentions', 29, 20,
        ['bouquet-cornet-de-tulipes.jpg', 'bouquet-cornet-de-tulipes-2.jpg'],
        'Tulipes roses et rouges roulées dans un cornet de papier.',
        "Une brassée de tulipes roses, fuchsia et rouges, roulées dans un cornet de papier. Simple, joyeux et facile à offrir. Hauteur : environ 35 cm. Les tulipes continuent de pousser dans le vase : recoupez les tiges au bout de deux jours."],
    ['AP-BQ-012', 'Brise de lisianthus', 'bouquets-de-saison', 34, 12,
        ['bouquet-brise-de-lisianthus.jpg', 'bouquet-brise-de-lisianthus-2.jpg'],
        'Lisianthus violets et blancs, légers comme une brise.',
        "Le lisianthus ressemble à une rose délicate, mais il tient bien plus longtemps en vase. Nous l'associons en violet et blanc pour un bouquet frais et lumineux. Hauteur : environ 40 cm. Comptez dix jours de fraîcheur avec une eau changée régulièrement."],
];

// Retrouve un bouquet par son UGS, sinon par son nom (bouquets créés par configurer-boutique.php)
function ap_trouver($ugs, $nom) {
    $id = wc_get_product_id_by_sku($ugs);
    if ($id) {
        return $id;
    }
    $posts = get_posts(['post_type' => 'product', 'post_status' => 'any', 'title' => $nom, 'numberposts' => 1, 'fields' => 'ids']);
    return $posts ? (int) $posts[0] : 0;
}

foreach ($bouquets as $ordre => [$ugs, $nom, $categorie, $prix, $stock, $photos, $court, $long]) {
    $id = ap_trouver($ugs, $nom);

    if ($prix === null) {
        // Produit variable : on change le type si le bouquet existait en produit simple
        if ($id && wc_get_product($id)->get_type() !== 'variable') {
            wp_set_object_terms($id, 'variable', 'product_type');
            delete_post_meta($id, '_regular_price');
            delete_post_meta($id, '_sale_price');
            wc_delete_product_transients($id);
            clean_post_cache($id);
        }
        $p = new WC_Product_Variable($id ?: 0);
        $p->set_manage_stock(false); // le stock est géré taille par taille
    } else {
        $p = $id ? wc_get_product($id) : new WC_Product_Simple();
        $p->set_regular_price((string) $prix);
        $p->set_sale_price('');
        $p->set_date_on_sale_from(null);
        $p->set_date_on_sale_to(null);
        $p->set_manage_stock(true);
        $p->set_stock_quantity($stock);
    }

    $p->set_name($nom);
    $p->set_sku($ugs);
    $p->set_short_description($court);
    $p->set_description($long);
    $p->set_category_ids([$cat[$categorie]]);
    $p->set_menu_order($ordre);
    $p->set_status('publish');
    $p->set_featured(in_array($ugs, ['AP-BQ-001', 'AP-BQ-009', 'AP-BQ-011'], true));

    // Photo principale + galerie
    $ids = [];
    foreach ($photos as $n => $fichier) {
        $alt   = $n === 0
            ? "Bouquet « $nom » composé à la main par Atelier Pivoine"
            : "Bouquet « $nom » : autre vue";
        $ids[] = ap_photo($fichier, $alt);
    }
    $p->set_image_id($ids[0] ?? 0);
    $p->set_gallery_image_ids(array_filter(array_slice($ids, 1)));

    // Promotion datée
    if ($ugs === 'AP-BQ-004') {
        $p->set_sale_price('47');
        $p->set_date_on_sale_from('2026-11-20 00:00:00');
        $p->set_date_on_sale_to('2026-12-24 23:59:59');
    }

    $p->save();
    echo "Bouquet : $nom ($ugs)" . ($prix !== null ? " · $prix € · stock $stock" : '') . ' · ' . count($ids) . " photo(s)\n";

    if ($prix === null) {
        ap_tailles($p);
    }
}

/* --------------------------------------------------------------------------
 * 4. « Le bouquet de Camille » en 3 tailles (attribut global « Taille »)
 * ------------------------------------------------------------------------ */
function ap_tailles(WC_Product_Variable $p) {
    $attribut_id = wc_attribute_taxonomy_id_by_name('taille');
    if (!$attribut_id) {
        $attribut_id = wc_create_attribute(['name' => 'Taille', 'slug' => 'taille', 'type' => 'select', 'order_by' => 'menu_order']);
    }
    if (!taxonomy_exists('pa_taille')) {
        register_taxonomy('pa_taille', ['product'], ['hierarchical' => false, 'show_ui' => false]);
    }

    // slug => [nom affiché, prix TTC, stock]
    $tailles = [
        'petit'    => ['Petit · 30 cm', 45, 8],
        'moyen'    => ['Moyen · 40 cm', 62, 12],
        'genereux' => ['Généreux · 55 cm', 85, 5],
    ];
    $termes = [];
    foreach ($tailles as $slug => [$nom]) {
        $t = get_term_by('slug', $slug, 'pa_taille') ?: (object) ['term_id' => wp_insert_term($nom, 'pa_taille', ['slug' => $slug])['term_id']];
        wp_update_term($t->term_id, 'pa_taille', ['name' => $nom]);
        $termes[] = (int) $t->term_id;
    }

    $attr = new WC_Product_Attribute();
    $attr->set_id($attribut_id);
    $attr->set_name('pa_taille');
    $attr->set_options($termes);
    $attr->set_visible(true);
    $attr->set_variation(true);
    $p->set_attributes([$attr]);
    $p->set_default_attributes(['pa_taille' => 'moyen']);
    $p->save();

    $existantes = [];
    foreach ($p->get_children() as $vid) {
        $existantes[get_post_meta($vid, 'attribute_pa_taille', true)] = $vid;
    }
    $n = 0;
    foreach ($tailles as $slug => [$nom, $prix, $stock]) {
        $v = isset($existantes[$slug]) ? wc_get_product($existantes[$slug]) : new WC_Product_Variation();
        $v->set_parent_id($p->get_id());
        $v->set_attributes(['pa_taille' => $slug]);
        $v->set_regular_price((string) $prix);
        $v->set_sku($p->get_sku() . '-' . strtoupper(substr($slug, 0, 1)));
        $v->set_manage_stock(true);
        $v->set_stock_quantity($stock);
        $v->set_menu_order($n++);
        $v->set_status('publish');
        $v->save();
        echo "  Taille $nom : $prix € · stock $stock\n";
    }
    WC_Product_Variable::sync($p->get_id());
}

/* --------------------------------------------------------------------------
 * 5. Logo (bloc « Logo du site ») et icône de l'onglet
 * ------------------------------------------------------------------------ */
$logo = ap_photo('icon-512.png', 'Pivoine rose, logo Atelier Pivoine');
if ($logo) {
    update_option('site_logo', $logo);
    set_theme_mod('custom_logo', $logo);
    update_option('site_icon', $logo);
    echo "Logo et icône du site réglés\n";
}

wc_delete_product_transients();
echo "Catalogue terminé : " . count($bouquets) . " bouquets.\n";
