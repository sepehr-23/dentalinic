<?php get_header(); ?>
<main class="st-page-shell st-container">
  <section class="st-glass-card st-content-card">
    <?php while (have_posts()) : the_post(); ?>
      <article <?php post_class(); ?>>
        <?php if (has_post_thumbnail()) : ?><div class="st-featured-image"><?php the_post_thumbnail('large'); ?></div><?php endif; ?>
        <h1 class="st-section-title"><?php the_title(); ?></h1>
        <div class="entry-content"><?php the_content(); ?></div>
      </article>
    <?php endwhile; ?>
  </section>
</main>
<?php get_footer(); ?>
