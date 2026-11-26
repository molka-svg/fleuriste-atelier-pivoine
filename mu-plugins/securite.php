<?php
/**
 * Plugin Name: Sécurité de la boutique
 * Description: Masque la liste des utilisateurs dans l'API REST, coupe XML-RPC et cache la version de WordPress.
 */

// 1. /wp-json/wp/v2/users ne donne plus les identifiants de connexion aux visiteurs
//    (risque : un robot récupère « admin » puis tente des mots de passe).
add_filter('rest_endpoints', function ($routes) {
    if (!is_user_logged_in()) {
        unset($routes['/wp/v2/users'], $routes['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $routes;
});
// …ni par l'adresse /?author=1 qui redirige vers /author/<identifiant>/
add_action('template_redirect', function () {
    if (isset($_GET['author']) && !is_user_logged_in()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});

// 2. XML-RPC coupé : la boutique ne s'en sert pas, les robots si (milliers d'essais de mots de passe en une requête).
add_filter('xmlrpc_enabled', '__return_false');

// 3. Version de WordPress retirée du code de la page (moins d'indices pour cibler une faille connue).
remove_action('wp_head', 'wp_generator');
