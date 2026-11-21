<?php
/**
 * Connexion / inscription — Atelier Pivoine.
 *
 * Adapté du modèle WooCommerce myaccount/form-login.php (version 9.9.0) :
 * mêmes champs, mêmes nonces et mêmes hooks, présentés en deux onglets
 * avec une colonne photo. Ajoute prénom et nom à l'inscription.
 *
 * @version 9.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$from_checkout = (($_GET['suite'] ?? $_POST['ap_suite'] ?? '') === 'commande');
$registration  = 'yes' === get_option('woocommerce_enable_myaccount_registration');
$show_register = $registration && (isset($_POST['register']) || isset($_GET['inscription']));
$cart_count    = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

do_action('woocommerce_before_customer_login_form'); ?>

<section class="auth" id="customer_login">

    <div class="auth-side">
        <?php atelier_pivoine_img('connexion', '', 1100, true); ?>
        <div class="auth-side-text">
            <p class="section-eyebrow">Mon compte</p>
            <h2><?php echo $from_checkout ? 'Presque <em>fini.</em>' : 'Bienvenue <em>à l\'atelier.</em>'; ?></h2>
            <ul>
                <li>Suivre vos commandes</li>
                <li>Retrouver vos adresses de livraison</li>
                <li>Commander plus vite la prochaine fois</li>
            </ul>
        </div>
    </div>

    <div class="auth-main">

        <?php if ($from_checkout) : ?>
            <p class="auth-note">Connectez-vous pour finaliser votre commande<?php echo $cart_count ? ' (' . (int) $cart_count . ' article' . ($cart_count > 1 ? 's' : '') . ')' : ''; ?>, ou continuez sans compte.</p>
        <?php endif; ?>

        <?php if ($registration) : ?>
            <div class="auth-tabs" role="tablist" aria-label="Connexion ou inscription">
                <button type="button" role="tab" id="tab-login" aria-controls="panel-login" aria-selected="<?php echo $show_register ? 'false' : 'true'; ?>" data-auth-tab="login">Se connecter</button>
                <button type="button" role="tab" id="tab-register" aria-controls="panel-register" aria-selected="<?php echo $show_register ? 'true' : 'false'; ?>" data-auth-tab="register">Créer un compte</button>
            </div>
        <?php endif; ?>

        <div class="auth-panel" id="panel-login" role="tabpanel" aria-labelledby="tab-login"<?php echo $show_register ? ' hidden' : ''; ?>>
            <h2 class="auth-title">Se connecter</h2>

            <form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

                <?php do_action('woocommerce_login_form_start'); ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="username">Adresse e-mail&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text">Obligatoire</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username']) && is_string($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="password">Mot de passe&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text">Obligatoire</span></label>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
                </p>

                <?php do_action('woocommerce_login_form'); ?>

                <div class="auth-row">
                    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                        <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span>Se souvenir de moi</span>
                    </label>
                    <a class="auth-lost" href="<?php echo esc_url(wp_lostpassword_url()); ?>">Mot de passe oublié ?</a>
                </div>

                <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                <?php if ($from_checkout) : ?><input type="hidden" name="ap_suite" value="commande"><?php endif; ?>
                <button type="submit" class="btn-primary btn-block woocommerce-form-login__submit" name="login" value="Se connecter">Se connecter</button>

                <?php do_action('woocommerce_login_form_end'); ?>

            </form>
        </div>

        <?php if ($registration) : ?>
            <div class="auth-panel" id="panel-register" role="tabpanel" aria-labelledby="tab-register"<?php echo $show_register ? '' : ' hidden'; ?>>
                <h2 class="auth-title">Créer un compte</h2>

                <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action('woocommerce_register_form_tag'); ?> >

                    <?php do_action('woocommerce_register_form_start'); ?>

                    <div class="auth-two">
                        <p class="woocommerce-form-row form-row">
                            <label for="reg_prenom">Prénom&nbsp;<span class="required" aria-hidden="true">*</span></label>
                            <input type="text" class="woocommerce-Input input-text" name="ap_prenom" id="reg_prenom" autocomplete="given-name" value="<?php echo esc_attr(wp_unslash($_POST['ap_prenom'] ?? '')); ?>" required aria-required="true" />
                        </p>
                        <p class="woocommerce-form-row form-row">
                            <label for="reg_nom">Nom&nbsp;<span class="required" aria-hidden="true">*</span></label>
                            <input type="text" class="woocommerce-Input input-text" name="ap_nom" id="reg_nom" autocomplete="family-name" value="<?php echo esc_attr(wp_unslash($_POST['ap_nom'] ?? '')); ?>" required aria-required="true" />
                        </p>
                    </div>

                    <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="reg_username">Identifiant&nbsp;<span class="required" aria-hidden="true">*</span></label>
                            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
                        </p>
                    <?php endif; ?>

                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="reg_email">Adresse e-mail&nbsp;<span class="required" aria-hidden="true">*</span></label>
                        <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo (!empty($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
                    </p>

                    <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="reg_password">Mot de passe&nbsp;<span class="required" aria-hidden="true">*</span></label>
                            <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
                        </p>
                    <?php else : ?>
                        <p>Un lien pour choisir votre mot de passe vous sera envoyé par e-mail.</p>
                    <?php endif; ?>

                    <?php do_action('woocommerce_register_form'); ?>

                    <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                    <?php if ($from_checkout) : ?><input type="hidden" name="ap_suite" value="commande"><?php endif; ?>
                    <button type="submit" class="btn-primary btn-block woocommerce-form-register__submit" name="register" value="Créer mon compte">Créer mon compte</button>

                    <?php do_action('woocommerce_register_form_end'); ?>

                </form>
            </div>
        <?php endif; ?>

        <?php if ($from_checkout && $cart_count) : ?>
            <div class="auth-sep"><span>ou</span></div>
            <a class="btn-ghost btn-block" href="<?php echo esc_url(wc_get_checkout_url()); ?>">Commander sans compte</a>
        <?php endif; ?>

    </div>
</section>

<?php do_action('woocommerce_after_customer_login_form'); ?>
