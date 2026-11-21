<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#contenu">Aller au contenu</a>

<header class="site-header" id="site-header">
    <div class="header-inner">

        <a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <img
                src="<?php echo esc_url(get_template_directory_uri() . '/images/logo.svg'); ?>"
                alt="Atelier Pivoine — Fleuriste Paris 11e"
                class="site-logo"
            >
        </a>

        <nav class="main-navigation" id="main-navigation" aria-label="Navigation principale">
            <a href="<?php echo esc_url(home_url('/')); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>>Accueil</a>
            <?php $is_shop = function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout()); ?>
            <a href="<?php echo esc_url(home_url('/boutique/')); ?>"<?php echo $is_shop ? ' aria-current="page"' : ''; ?>>Nos bouquets</a>
            <a href="<?php echo esc_url(home_url('/atelier/')); ?>"<?php echo is_page('atelier') ? ' aria-current="page"' : ''; ?>>L'atelier</a>
        </nav>

        <div class="header-actions">
            <?php
            $cart_url   = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/panier/');
            $cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
            ?>
            <?php
            $account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : wp_login_url();
            $user        = wp_get_current_user();
            if (is_user_logged_in()) :
                $first    = $user->first_name ?: $user->display_name;
                $initials = mb_strtoupper(mb_substr($first, 0, 1) . mb_substr($user->last_name, 0, 1));
            ?>
                <a class="header-account is-logged" href="<?php echo esc_url($account_url); ?>" aria-label="Mon compte">
                    <span class="account-avatar" aria-hidden="true"><?php echo esc_html($initials); ?></span>
                    <span class="account-label"><?php echo esc_html($first); ?></span>
                </a>
            <?php else : ?>
                <a class="header-account" href="<?php echo esc_url($account_url); ?>">
                    <svg class="account-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span class="account-label">Se connecter</span>
                </a>
            <?php endif; ?>

            <a class="header-cart" href="<?php echo esc_url($cart_url); ?>" data-ap-open-cart aria-controls="cart-drawer">
                <span>Panier</span>
                <span class="cart-count<?php echo $cart_count ? '' : ' is-empty'; ?>"><?php echo (int) $cart_count; ?></span>
                <span class="cart-arrow">↗</span>
            </a>

            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation">
                <span class="menu-toggle-bar"></span>
                <span class="menu-toggle-bar"></span>
                <span class="screen-reader-text">Menu</span>
            </button>
        </div>

    </div>
</header>

<div id="contenu"></div>
