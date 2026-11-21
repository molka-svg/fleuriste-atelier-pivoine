<?php
/**
 * Boutique et rayons : bandeau, filtres par prix, tri, cartes avec ajout rapide.
 */

$term        = is_product_taxonomy() ? get_queried_object() : null;
$title       = $term ? $term->name : 'Bouquets de saison';
$description = $term && $term->description
    ? $term->description
    : "Composés chaque matin avec les fleurs arrivées à l'aube. Livrés à Paris le jour même pour toute commande passée avant " . ATELIER_PIVOINE_HEURE_LIMITE . ' h.';

// Le dernier mot du titre passe en italique rose, comme sur l'accueil
$words = explode(' ', $title);
$last  = count($words) > 1 ? array_pop($words) : '';
$base_url  = $term ? get_term_link($term) : wc_get_page_permalink('shop');
$current   = sanitize_key($_GET['prix'] ?? '');
$orderby   = sanitize_key($_GET['orderby'] ?? 'menu_order');
$filters   = atelier_pivoine_price_filters();
$total     = (int) $GLOBALS['wp_query']->found_posts;
?>

<main class="site-main shop-page" id="boutique">

    <section class="page-hero">
        <div class="page-hero-bg" aria-hidden="true">
            <?php atelier_pivoine_img('boutique', '', 1800, true); ?>
        </div>
        <div class="page-hero-content">
            <nav class="breadcrumb reveal" aria-label="Fil d'Ariane">
                <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a> <span aria-hidden="true">›</span> <span>Nos bouquets</span>
            </nav>
            <p class="section-eyebrow reveal">Rayon</p>
            <h1 class="reveal"><?php echo esc_html(implode(' ', $words)); ?><?php if ($last) : ?> <em><?php echo esc_html($last); ?></em><?php endif; ?></h1>
            <p class="page-hero-text reveal"><?php echo esc_html($description); ?></p>
        </div>
    </section>

    <div class="shop-wrap">

        <div class="shop-tools reveal">
            <div class="chips" role="group" aria-label="Filtrer par prix">
                <?php foreach ($filters as $key => [$label]) :
                    $url = $key ? add_query_arg('prix', $key, $base_url) : $base_url;
                    if ($orderby && $orderby !== 'menu_order') {
                        $url = add_query_arg('orderby', $orderby, $url);
                    }
                    ?>
                    <a class="chip" href="<?php echo esc_url($url); ?>"<?php echo $current === $key ? ' aria-current="true"' : ''; ?>><?php echo esc_html($label); ?></a>
                <?php endforeach; ?>
            </div>

            <form class="shop-sort" method="get" action="<?php echo esc_url($base_url); ?>">
                <label for="shop-orderby">Trier</label>
                <select id="shop-orderby" name="orderby" data-ap-autosubmit>
                    <?php foreach ([
                        'menu_order' => "Sélection de l'atelier",
                        'price'      => 'Prix croissant',
                        'price-desc' => 'Prix décroissant',
                        'date'       => 'Nouveautés',
                    ] as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>"<?php selected($orderby, $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($current) : ?><input type="hidden" name="prix" value="<?php echo esc_attr($current); ?>"><?php endif; ?>
                <noscript><button type="submit" class="chip">OK</button></noscript>
            </form>
        </div>

        <?php woocommerce_output_all_notices(); ?>

        <p class="shop-count"><?php echo esc_html($total . ' bouquet' . ($total > 1 ? 's' : '')); ?></p>

        <?php if (have_posts()) : ?>
            <div class="shop-grid">
                <?php while (have_posts()) :
                    the_post();
                    $product = wc_get_product(get_the_ID());
                    if (!$product) {
                        continue;
                    }
                    $badge = $product->is_featured() ? 'Coup de cœur' : ($product->is_on_sale() ? 'Promo' : '');
                    ?>
                    <article class="shop-card reveal">
                        <a class="shop-card-img" href="<?php the_permalink(); ?>">
                            <?php atelier_pivoine_product_image($product, 700); ?>
                            <?php if ($badge) : ?><span class="shop-badge"><?php echo esc_html($badge); ?></span><?php endif; ?>
                            <span class="shop-card-view">Découvrir ↗</span>
                        </a>
                        <div class="shop-card-body">
                            <h2 class="shop-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p class="shop-card-desc"><?php echo esc_html(wp_strip_all_tags($product->get_short_description())); ?></p>
                            <div class="shop-card-foot">
                                <span class="shop-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                                <?php echo atelier_pivoine_qty_controls($product); // phpcs:ignore -- échappé dans la fonction ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination(['prev_text' => '←', 'next_text' => '→', 'class' => 'woocommerce-pagination']); ?>
        <?php else : ?>
            <div class="shop-empty">
                <p class="shop-empty-title">Aucun bouquet dans cette gamme de prix</p>
                <a class="btn-secondary" href="<?php echo esc_url($base_url); ?>">Voir tous les bouquets</a>
            </div>
        <?php endif; ?>

    </div>
</main>
