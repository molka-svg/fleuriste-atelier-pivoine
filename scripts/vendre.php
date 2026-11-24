<?php
/**
 * Partie 4 du sujet, « Vendre » : TVA, virement, code promo, pages légales,
 * consentement, e-mails. À lancer après configurer-boutique.php et catalogue.php :
 *
 *   docker cp scripts/vendre.php <conteneur-wordpress>:/tmp/
 *   docker exec -u www-data <conteneur-wordpress> php /tmp/vendre.php
 *
 * Relançable sans doublon. Tout est fictif (société, SIRET, RIB, adresses).
 * Le paiement par carte (bac à sable) se règle à part : ses clés ne vont jamais dans Git.
 */
require '/var/www/html/wp-load.php';

/* ---------- 1. TVA : prix saisis et affichés TTC ---------- */
update_option('woocommerce_calc_taxes', 'yes');
update_option('woocommerce_prices_include_tax', 'yes');
update_option('woocommerce_tax_display_shop', 'incl');
update_option('woocommerce_tax_display_cart', 'incl');
update_option('woocommerce_tax_total_display', 'itemized');
update_option('woocommerce_tax_based_on', 'shipping');
update_option('woocommerce_default_customer_address', 'base');
update_option('woocommerce_shipping_tax_class', 'inherit');

global $wpdb;
$classe = 'taux-reduit';
if (!in_array($classe, WC_Tax::get_tax_class_slugs(), true)) {
    WC_Tax::create_tax_class('Taux réduit fleurs', $classe);
}
foreach (['' => ['20.0000', 'TVA 20 %'], $classe => ['10.0000', 'TVA 10 %']] as $c => [$taux, $nom]) {
    $ligne = ['tax_rate_country' => 'FR', 'tax_rate_state' => '', 'tax_rate' => $taux, 'tax_rate_name' => $nom,
        'tax_rate_priority' => 1, 'tax_rate_compound' => 0, 'tax_rate_shipping' => 1, 'tax_rate_order' => 0, 'tax_rate_class' => $c];
    $id = $wpdb->get_var($wpdb->prepare("SELECT tax_rate_id FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country='FR' AND tax_rate_class=%s", $c));
    $id ? WC_Tax::_update_tax_rate($id, $ligne) : WC_Tax::_insert_tax_rate($ligne);
    echo "TVA : $nom\n";
}
// Bouquets de fleurs coupées non transformées : 10 %
foreach (wc_get_products(['limit' => -1, 'status' => 'publish']) as $p) {
    $p->set_tax_status('taxable');
    $p->set_tax_class($classe);
    $p->save();
}
// WooCommerce attend des frais de port HT : on part du prix TTC annoncé au client
$ttc = ['Coursier à Paris' => 8.90, 'Livraison standard (48 h)' => 6.90, 'Livraison express (24 h)' => 12.90];
foreach (WC_Shipping_Zones::get_zones() as $z) {
    foreach ((new WC_Shipping_Zone($z['id']))->get_shipping_methods() as $m) {
        if ($m->id === 'flat_rate' && isset($ttc[$m->get_title()])) {
            update_option($m->get_instance_option_key(), array_merge($m->instance_settings, [
                'tax_status' => 'taxable', 'cost' => (string) round($ttc[$m->get_title()] / 1.10, 4),
            ]));
            echo 'Port : ' . $m->get_title() . ' ' . $ttc[$m->get_title()] . " € TTC\n";
        }
    }
}
WC_Cache_Helper::get_transient_version('shipping', true);

/* ---------- 2. Virement bancaire (RIB volontairement fictif) ---------- */
update_option('woocommerce_bacs_settings', [
    'enabled' => 'yes', 'title' => 'Virement bancaire',
    'description' => 'Boutique de démonstration : ne faites aucun virement. Indiquez le numéro de commande comme référence.',
    'instructions' => 'Boutique de démonstration : ne faites aucun virement réel. Votre bouquet est préparé à réception du virement.',
]);
update_option('woocommerce_bacs_accounts', [[
    'account_name' => 'Atelier Pivoine (fictif)', 'account_number' => '', 'bank_name' => 'Banque fictive',
    'sort_code' => '', 'iban' => 'FR00 0000 0000 0000 0000 0000 000', 'bic' => 'FICTFRPP',
]]);
echo "Virement activé\n";

/* ---------- 3. Code promo avec restrictions ---------- */
$c = new WC_Coupon('BIENVENUE10');
if (!$c->get_id()) $c->set_code('BIENVENUE10');
$c->set_description('-10 % dès 40 €, une fois par client, jusqu\'au 31 décembre 2026');
$c->set_discount_type('percent');
$c->set_amount(10);
$c->set_minimum_amount(40);
$c->set_date_expires('2026-12-31');
$c->set_usage_limit_per_user(1);
$c->set_individual_use(true);
$c->save();
echo "Code promo BIENVENUE10\n";

/* ---------- 4. Pages légales ---------- */
$pages = [
'mentions-legales' => ['Mentions légales', <<<'H'
<h2>Éditeur</h2>
<p>Atelier Pivoine, SARL fictive au capital de 1 000 €, 27 rue Imaginaire, 75011 Paris. SIRET : 000 000 000 00000 (fictif). TVA intracommunautaire : FR00000000000 (fictif).</p>
<p>Directrice de la publication : Camille Rose (personnage fictif). Contact : bonjour@atelier-pivoine.test, 01 00 00 00 00.</p>
<h2>Hébergement</h2>
<p>Site de démonstration exécuté en local avec Docker. En production, le nom, l'adresse et le téléphone de l'hébergeur seraient indiqués ici.</p>
<h2>Projet pédagogique</h2>
<p>Ce site est réalisé par des étudiants d'IRIS (Mastère 1). Aucune commande n'est livrée et aucun paiement réel n'est encaissé.</p>
<h2>Propriété intellectuelle</h2>
<p>Le logo et les textes sont créés pour ce projet. Les photos viennent d'Unsplash et de Pexels ; leurs sources sont citées dans le README du projet.</p>
H],
'conditions-generales-de-vente' => ['Conditions générales de vente', <<<'H'
<h2>1. Objet</h2>
<p>Ces conditions s'appliquent aux ventes de bouquets d'Atelier Pivoine (commerce fictif) à des particuliers, en France métropolitaine.</p>
<h2>2. Produits et prix</h2>
<p>Les bouquets sont composés à la main : les fleurs peuvent varier légèrement selon les arrivages, à valeur équivalente. Les prix sont en euros, toutes taxes comprises. Les frais de livraison sont affichés avant la validation.</p>
<h2>3. Commande</h2>
<p>La vente est conclue quand le client valide sa commande, après avoir accepté ces conditions. Un e-mail de confirmation lui est envoyé.</p>
<h2>4. Paiement</h2>
<p>Le client peut payer avec l'un des moyens proposés au moment de la commande. Par virement, le bouquet est préparé à réception du paiement.</p>
<h2>5. Livraison et retrait</h2>
<p>À Paris : coursier à 8,90 €, offert dès 60 €. Ailleurs en France : livraison standard à 6,90 €, offerte dès 60 €, ou express à 12,90 €. Retrait gratuit au 27 rue Imaginaire, du mardi au samedi, de 9 h à 19 h.</p>
<h2>6. Droit de rétractation</h2>
<p>Les fleurs coupées se détériorent rapidement : conformément à l'article L221-28 du Code de la consommation, le droit de rétractation de 14 jours ne s'applique pas.</p>
<h2>7. Réclamations et garanties</h2>
<p>Un bouquet abîmé ou non conforme ? Écrivez-nous sous 48 heures, avec une photo, à bonjour@atelier-pivoine.test : nous le remplaçons ou le remboursons. Les garanties légales restent applicables.</p>
<h2>8. Médiation et droit applicable</h2>
<p>En cas de litige, le client peut saisir gratuitement un médiateur de la consommation (organisme fictif pour ce projet). Le droit français s'applique.</p>
H],
'politique-de-confidentialite' => ['Politique de confidentialité', <<<'H'
<h2>Responsable</h2>
<p>Atelier Pivoine (fictif), 27 rue Imaginaire, 75011 Paris, bonjour@atelier-pivoine.test.</p>
<h2>Données collectées</h2>
<p>Nom, e-mail, téléphone, adresse, contenu de la commande, message de la carte et, pour un compte, un mot de passe chiffré. Les données de carte bancaire ne sont jamais enregistrées sur notre site.</p>
<h2>Utilisation et base légale</h2>
<p>Préparer et livrer la commande (exécution du contrat), tenir la comptabilité (obligation légale), répondre aux demandes (intérêt légitime).</p>
<h2>Destinataires et durée</h2>
<p>L'atelier, le transporteur et le prestataire de paiement, pour leur seule mission. Les factures sont conservées 10 ans ; un compte inactif est supprimé après 3 ans.</p>
<h2>Cookies</h2>
<p>Seuls les cookies nécessaires au panier et à la connexion sont utilisés. Les polices sont chargées depuis Google Fonts, qui reçoit l'adresse IP du visiteur.</p>
<h2>Vos droits</h2>
<p>Vous pouvez demander l'accès, la rectification, l'effacement ou la portabilité de vos données, et vous opposer à leur utilisation, à bonjour@atelier-pivoine.test. Vous pouvez aussi saisir la CNIL (cnil.fr).</p>
H],
];
$ids = [];
foreach ($pages as $slug => [$titre, $html]) {
    $html = preg_replace('#<h2>(.*?)</h2>#', "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">$1</h2>\n<!-- /wp:heading -->", $html);
    $html = preg_replace('#<p>(.*?)</p>#s', "<!-- wp:paragraph -->\n<p>$1</p>\n<!-- /wp:paragraph -->", $html);
    // get_page_by_path trouve aussi les brouillons (WordPress en crée un pour la confidentialité)
    $q = get_page_by_path($slug, OBJECT, 'page');
    $d = ['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $titre, 'post_name' => $slug, 'post_content' => $html];
    $ids[$slug] = $q ? wp_update_post($d + ['ID' => $q->ID]) : wp_insert_post($d);
    echo "Page : $titre\n";
}
update_option('wp_page_for_privacy_policy', $ids['politique-de-confidentialite']);
// Case de consentement : apparaît dès qu'une page de CGV est choisie
update_option('woocommerce_terms_page_id', $ids['conditions-generales-de-vente']);
update_option('woocommerce_checkout_terms_and_conditions_checkbox_text', "J'ai lu et j'accepte les [terms]");
update_option('woocommerce_checkout_privacy_policy_text', 'Vos données servent uniquement à traiter votre commande, comme expliqué dans notre [privacy_policy].');
update_option('woocommerce_registration_privacy_policy_text', 'Vos données servent à gérer votre compte et vos commandes, comme expliqué dans notre [privacy_policy].');

/* ---------- 5. E-mails aux couleurs de la charte, adresses fictives ---------- */
update_option('woocommerce_email_from_name', 'Atelier Pivoine');
update_option('woocommerce_email_from_address', 'bonjour@atelier-pivoine.test');
update_option('woocommerce_email_base_color', '#2F4A3A');
update_option('woocommerce_email_background_color', '#FBF6EF');
update_option('woocommerce_email_body_background_color', '#FFFFFF');
update_option('woocommerce_email_text_color', '#1F1F1F');
update_option('woocommerce_email_footer_text', 'Atelier Pivoine, fleuriste à Paris 11e (commerce fictif)');
$logo = wp_get_attachment_image_url((int) get_option('site_logo'), 'thumbnail');
if ($logo) {
    update_option('woocommerce_email_header_image', $logo);
    update_option('woocommerce_email_header_image_width', '90');
}
foreach (['new_order', 'cancelled_order', 'failed_order'] as $e) {
    $o = (array) get_option("woocommerce_{$e}_settings", []);
    $o['recipient'] = 'commandes@atelier-pivoine.test';
    update_option("woocommerce_{$e}_settings", $o);
}
echo "E-mails configurés\nTerminé.\n";
