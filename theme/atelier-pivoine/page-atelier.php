<?php
/**
 * Template Name: L'atelier
 *
 * Présentation de l'atelier : histoire, engagements, horaires et adresse.
 */
get_header(); ?>

<main class="site-main atelier-page">

    <section class="atelier-hero">
        <div class="atelier-hero-img">
            <?php atelier_pivoine_img('atelier', "Fleuriste composant un bouquet sur son plan de travail", 1400, true); ?>
        </div>
        <div class="atelier-hero-text">
            <nav class="breadcrumb reveal" aria-label="Fil d'Ariane">
                <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a> <span aria-hidden="true">›</span> <span>L'atelier</span>
            </nav>
            <p class="section-eyebrow reveal">Notre histoire</p>
            <h1 class="reveal">Un carnet d'herbier <em>sur le comptoir.</em></h1>
            <p class="reveal">Une petite boutique du 11e, quelques rues après Oberkampf. Le rideau se lève à 9 h, les seaux sont déjà pleins : renoncules, eucalyptus, branches de saison arrivées à l'aube.</p>
            <p class="reveal">Camille Rose compose chaque bouquet à la main, sur un grand plan de travail en bois, et l'emballe dans un papier kraft noué d'un ruban.</p>
            <a class="btn-primary reveal" href="<?php echo esc_url(home_url('/boutique/')); ?>">Voir les bouquets du jour</a>
        </div>
    </section>

    <section class="atelier-values" aria-label="Nos engagements">
        <article class="value-card reveal">
            <span class="value-num">01</span>
            <h2>De saison</h2>
            <p>Nous travaillons les fleurs au rythme des saisons, sans fleurs qui ont fait le tour du monde.</p>
        </article>
        <article class="value-card reveal">
            <span class="value-num">02</span>
            <h2>Artisanal</h2>
            <p>Chaque bouquet est unique, composé à la main au moment de votre commande.</p>
        </article>
        <article class="value-card reveal">
            <span class="value-num">03</span>
            <h2>Proche</h2>
            <p>Livraison à vélo dans Paris, le jour même pour une commande passée avant <?php echo (int) (defined('ATELIER_PIVOINE_HEURE_LIMITE') ? ATELIER_PIVOINE_HEURE_LIMITE : 14); ?> h.</p>
        </article>
    </section>

    <section class="atelier-craft">
        <div class="atelier-craft-text reveal">
            <p class="section-eyebrow">Le savoir-faire</p>
            <h2>Des mains, <em>un ruban, une carte.</em></h2>
            <p>Avant de partir, chaque bouquet est vérifié tige par tige, emballé dans un papier kraft et accompagné de votre message, écrit à la main.</p>
        </div>
        <div class="atelier-craft-img reveal">
            <?php atelier_pivoine_img('atelier-mains', 'Mains de la fleuriste assemblant un petit bouquet', 1100); ?>
        </div>
    </section>

    <section class="atelier-visit">
        <div class="visit-card reveal">
            <p class="section-eyebrow">Venir à l'atelier</p>
            <h2>Horaires</h2>
            <table class="visit-hours">
                <tbody>
                    <tr><td>Lundi</td><td>Fermé</td></tr>
                    <tr><td>Mardi – vendredi</td><td>9 h – 19 h 30</td></tr>
                    <tr><td>Samedi</td><td>9 h – 20 h</td></tr>
                    <tr><td>Dimanche</td><td>9 h – 13 h</td></tr>
                </tbody>
            </table>
            <p class="visit-address">27 rue Imaginaire, 75011 Paris<br>01 23 45 67 89 · bonjour@atelier-pivoine.test</p>
        </div>
        <div class="visit-map reveal">
            <span class="visit-map-pin" aria-hidden="true">✿</span>
            <strong>Paris 11e</strong>
            <span>Métro Oberkampf · 4 min à pied</span>
        </div>
    </section>

</main>

<?php get_footer(); ?>
