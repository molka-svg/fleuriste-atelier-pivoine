<?php
/**
 * Bonus : crée (ou met à jour) le webhook WooCommerce « Commande créée » vers le conteneur « webhook ».
 * Le secret de signature vient de WEBHOOK_SECRET dans le fichier .env : jamais dans Git.
 *
 *   docker compose run --rm cli wp eval-file /scripts/webhook.php
 */
$secret = getenv('WEBHOOK_SECRET');
if (!$secret) {
    WP_CLI::warning('WEBHOOK_SECRET vide dans .env : webhook non créé.');
    return;
}
$nom = 'Commande créée → récepteur local';
global $wpdb;
$id = (int) $wpdb->get_var($wpdb->prepare("SELECT webhook_id FROM {$wpdb->prefix}wc_webhooks WHERE name = %s", $nom));

$webhook = new WC_Webhook($id);
$webhook->set_name($nom);
$webhook->set_topic('order.created');
$webhook->set_delivery_url('http://webhook/');
$webhook->set_secret($secret);
$webhook->set_api_version('wp_api_v3');
$webhook->set_user_id(1);
$webhook->set_status('active');
$webhook->save();
WP_CLI::success("Webhook n° {$webhook->get_id()} actif : order.created → http://webhook/ (affichage : http://localhost:8090)");
