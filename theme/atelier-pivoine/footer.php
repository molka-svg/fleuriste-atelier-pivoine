<footer class="site-footer">

    <div class="footer-main">

        <div class="footer-brand">
            <h2>Atelier Pivoine</h2>
            <p>Cueilli à l'aube, offert le jour même.</p>
        </div>

        <div class="footer-column">
            <h3>La boutique</h3>
            <a href="<?php echo esc_url(home_url('/boutique/')); ?>">Nos fleurs</a>
            <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a>
            <a href="<?php echo esc_url(home_url('/atelier/')); ?>">L'atelier</a>
            <a href="<?php echo esc_url(home_url('/mon-compte/')); ?>">Mon compte</a>
        </div>

        <div class="footer-column">
            <h3>Informations</h3>
            <p>Fleurs de saison</p>
            <p>Composition artisanale</p>
            <p>Paris 11e</p>
        </div>

        <div class="footer-column">
            <h3>Contact</h3>
            <p>27 rue Imaginaire, 75011 Paris</p>
            <p>01 23 45 67 89</p>
            <p>bonjour@atelier-pivoine.test</p>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo esc_html(date('Y')); ?> Atelier Pivoine. Tous droits réservés.</p>
        <a href="#contenu" class="back-to-top">Retour en haut ↑</a>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
