<?php
/**
 * Plugin Name: Webhooks en local (Docker)
 * Description: Permet à WooCommerce d'envoyer ses webhooks au conteneur « webhook » (bonus du sujet).
 */

// Dans Docker, WordPress ne peut pas s'appeler lui-même sur localhost:8080 (WP-Cron / Action Scheduler) :
// on livre donc les webhooks tout de suite, pendant la commande, au lieu de les mettre en file d'attente.
add_filter('woocommerce_webhook_deliver_async', '__return_false');

// Le contenu d'un webhook est construit avec l'API REST v3, que WooCommerce ne charge pas
// pendant une commande passée par la Store API : on la charge pour cette seule route.
add_filter('wc_rest_should_load_namespace', function ($charger, $ns, $route) {
    return $charger || (str_starts_with($ns, 'wc/v3') && str_starts_with($route, 'wc/store/v1/checkout'));
}, 10, 3);

// Commande passée par l'API (Store API, collection Postman) : WooCommerce déclenche « order.created »
// dès le brouillon de commande. On renvoie l'événement quand la commande quitte l'état « brouillon ».
add_action('woocommerce_order_status_changed', function ($id_commande, $avant) {
    if ($avant !== 'checkout-draft') {
        return;
    }
    foreach (WC_Data_Store::load('webhook')->search_webhooks(['status' => 'active']) as $id) {
        $webhook = wc_get_webhook($id);
        if ($webhook && $webhook->get_topic() === 'order.created') {
            $webhook->deliver($id_commande);
        }
    }
}, 10, 2);

// WordPress refuse par sécurité d'envoyer des requêtes vers une adresse interne.
// On autorise uniquement le conteneur « webhook » du docker-compose.
add_filter('http_request_host_is_external', function ($externe, $hote) {
    return $hote === 'webhook' ? true : $externe;
}, 10, 2);
