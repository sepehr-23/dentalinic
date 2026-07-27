<?php
/**
 * Sepehr Apple Portfolio Standard Index Template (Fallback)
 */

get_header(); ?>

<div class="main-portfolio-wrapper standard-page-layout">
	<?php if ( have_posts() ) : ?>
		<div class="archive-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'classic-post-article' ); ?> style="margin-bottom: 0;">
					<header class="page-header">
						<h2 class="page-title" style="font-size: 20px;"><a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;"><?php the_title(); ?></a></h2>
						<div class="post-meta-data">
							<span class="meta-item"><?php the_date(); ?></span>
						</div>
					</header>
					<div class="page-entry-content" style="font-size: 14px;">
						<?php the_excerpt(); ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	<?php else : ?>
		<div class="classic-post-article">
			<p>هیچ مطلبی یافت نشد.</p>
		</div>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
