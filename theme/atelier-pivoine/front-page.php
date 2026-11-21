<?php get_header(); ?>

<main class="homepage">

    <!-- HERO PLEIN ÉCRAN -->
    <section class="hero">
        <div class="hero-bg">
            <?php atelier_pivoine_img('hero', 'Pivoines roses dans un vase en verre', 1800, true); ?>
        </div>

        <div class="hero-content">
            <p class="hero-eyebrow reveal">FLEURISTE · PARIS 11e</p>

            <h1 class="reveal">Un bouquet.<br><em>Une émotion.</em></h1>

            <p class="hero-description reveal">
                Des fleurs de saison, choisies avec délicatesse
                et assemblées à la main pour faire fleurir vos moments précieux.
            </p>

            <div class="hero-buttons reveal">
                <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="btn-primary">
                    Découvrir les bouquets
                </a>
                <a href="#univers" class="btn-glass">
                    Notre univers <span>↓</span>
                </a>
            </div>
        </div>
    </section>


    <!-- ENGAGEMENTS -->
    <section class="reassurance" aria-label="Nos engagements">
        <div class="reassurance-item"><span class="reassurance-icon">✳</span><span>Fleurs de saison</span></div>
        <div class="reassurance-item"><span class="reassurance-icon">♡</span><span>Compositions artisanales</span></div>
        <div class="reassurance-item"><span class="reassurance-icon">⌁</span><span>Livraison à Paris</span></div>
    </section>


    <!-- UNIVERS -->
    <section class="home-categories" id="univers">

        <div class="section-heading reveal">
            <p class="section-eyebrow">Laissez parler les fleurs</p>
            <h2>Des fleurs pour <em>tous vos moments.</em></h2>
        </div>

        <div class="categories-grid">

            <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="category-card reveal">
                <?php atelier_pivoine_img('cat-1', 'Pivoines roses emballées dans du papier blanc', 900); ?>
                <div class="category-label">
                    <h3>Bouquets de saison</h3>
                    <p>Le charme du naturel</p>
                </div>
                <span class="category-arrow">↗</span>
            </a>

            <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="category-card reveal">
                <?php atelier_pivoine_img('cat-2', 'Pivoines roses dans un vase', 900); ?>
                <div class="category-label">
                    <h3>Petites attentions</h3>
                    <p>Pour faire plaisir simplement</p>
                </div>
                <span class="category-arrow">↗</span>
            </a>

            <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="category-card reveal">
                <?php atelier_pivoine_img('cat-3', 'Mur de roses pêche et crème pour une fête', 900); ?>
                <div class="category-label">
                    <h3>Grands moments</h3>
                    <p>Les occasions à célébrer</p>
                </div>
                <span class="category-arrow">↗</span>
            </a>

        </div>
    </section>


    <!-- PRODUITS -->
    <section class="home-products">

        <div class="section-heading reveal">
            <p class="section-eyebrow">Les coups de cœur de l'atelier</p>
            <h2>Nos bouquets <em>du moment.</em></h2>
        </div>

        <div class="rail" id="rail" tabindex="0" aria-label="Nos bouquets du moment">
            <?php
            $has_products = false;

            if (class_exists('WooCommerce')) :
                $featured_products = new WP_Query([
                    'post_type'      => 'product',
                    'posts_per_page' => 8,
                    'post_status'    => 'publish',
                ]);

                if ($featured_products->have_posts()) :
                    $has_products = true;
                    while ($featured_products->have_posts()) :
                        $featured_products->the_post();
                        $product = wc_get_product(get_the_ID());
                        if (!$product) {
                            continue;
                        }
            ?>
                <article class="product-card">
                    <a href="<?php the_permalink(); ?>" class="product-image">
                        <?php if (function_exists('atelier_pivoine_product_image')) : ?>
                            <?php atelier_pivoine_product_image($product, 700); ?>
                        <?php elseif (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('woocommerce_thumbnail'); ?>
                        <?php else : ?>
                            <div class="product-placeholder"><span>Atelier Pivoine</span></div>
                        <?php endif; ?>
                        <span class="product-view">Découvrir ↗</span>
                    </a>
                    <div class="product-info">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                    </div>
                </article>
            <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
            endif;

            // Aperçu élégant tant que la boutique n'a pas encore de produits
            if (!$has_products) :
                $demo = [
                    ['key' => 'prod-1', 'name' => 'Bouquet de saison'],
                    ['key' => 'prod-2', 'name' => 'Roses de l\'atelier'],
                    ['key' => 'prod-3', 'name' => 'Petite attention'],
                    ['key' => 'prod-4', 'name' => 'Rosée du matin'],
                    ['key' => 'prod-5', 'name' => 'Bouquet pastel'],
                ];
                foreach ($demo as $item) :
            ?>
                <article class="product-card">
                    <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="product-image">
                        <?php atelier_pivoine_img($item['key'], $item['name'], 800); ?>
                        <span class="product-view">Découvrir ↗</span>
                    </a>
                    <div class="product-info">
                        <h3><a href="<?php echo esc_url(home_url('/boutique/')); ?>"><?php echo esc_html($item['name']); ?></a></h3>
                        <div class="product-price">Sur commande</div>
                    </div>
                </article>
            <?php
                endforeach;
            endif;
            ?>
        </div>

        <div class="rail-controls">
            <button type="button" class="rail-btn" data-rail="-1" aria-label="Bouquets précédents">←</button>
            <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="section-link">Voir toute la boutique ↗</a>
            <button type="button" class="rail-btn" data-rail="1" aria-label="Bouquets suivants">→</button>
        </div>

    </section>


    <!-- ATELIER -->
    <section class="home-story" id="atelier">

        <div class="story-image">
            <?php atelier_pivoine_img('story', 'Main tenant un bouquet de pivoines blanches et d\'hortensias roses', 1200); ?>
        </div>

        <div class="story-content reveal">
            <p class="section-eyebrow">Au cœur de Paris 11e</p>
            <h2>La passion des fleurs. <em>Le goût du détail.</em></h2>
            <p>
                Chaque fleur a son caractère, chaque composition son histoire.
                Nous imaginons des bouquets spontanés, généreux et vivants,
                inspirés par la beauté des saisons.
            </p>
            <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="btn-secondary">
                Voir nos bouquets
            </a>
        </div>

    </section>


    <!-- SIGNATURE -->
    <section class="home-message">
        <div class="home-message-bg" aria-hidden="true">
            <?php atelier_pivoine_img('cta', '', 1600); ?>
        </div>
        <div class="home-message-content reveal">
            <p class="section-eyebrow">Atelier Pivoine</p>
            <h2>Cueilli à l'aube,<br><em>offert le jour même.</em></h2>
            <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="btn-primary">
                Trouver mon bouquet
            </a>
        </div>
    </section>

</main>

<?php get_footer(); ?>
