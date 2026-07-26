<?php get_header(); ?>
<main class="st-page-shell st-container">
  <section class="st-glass-card st-content-card">
    <h1 class="st-section-title"><?php the_archive_title(); ?></h1>
    <div class="st-grid st-grid-2">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article <?php post_class('st-glass-card st-loop-card'); ?>>
          <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <div><?php the_excerpt(); ?></div>
        </article>
      <?php endwhile; else : ?>
        <p><?php echo esc_html(st_t('موردی پیدا نشد.','Nothing found.','Nichts gefunden.')); ?></p>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php get_footer(); ?>
