<?php
/**
 * Modèle par défaut (pages, articles, archives).
 * La page d'accueil utilise front-page.php.
 */
get_header(); ?>

<main class="site-main">
    <div class="page-container">

        <?php if (have_posts()) : ?>

            <?php while (have_posts()) : the_post(); ?>

                <article <?php post_class('entry'); ?>>

                    <header class="entry-header">
                        <?php if (is_singular()) : ?>
                            <h1><?php the_title(); ?></h1>
                        <?php else : ?>
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php endif; ?>
                    </header>

                    <div class="entry-content">
                        <?php is_singular() ? the_content() : the_excerpt(); ?>
                    </div>

                </article>

            <?php endwhile; ?>

            <?php the_posts_pagination(); ?>

        <?php else : ?>

            <article class="entry">
                <header class="entry-header"><h1>Rien à voir ici</h1></header>
                <div class="entry-content">
                    <p>Cette page n'existe pas ou n'est plus disponible.</p>
                    <a class="btn-primary" href="<?php echo esc_url(home_url('/')); ?>">Retour à l'accueil</a>
                </div>
            </article>

        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
