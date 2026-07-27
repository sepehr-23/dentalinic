<?php
/**
 * Sepehr Apple Portfolio Standard Single Post Template
 */

get_header(); ?>

<div class="main-portfolio-wrapper standard-page-layout">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'classic-post-article' ); ?>>
			<header class="page-header">
				<h1 class="page-title"><?php the_title(); ?></h1>
				<div class="post-meta-data">
					<span class="meta-item"><i class="fas fa-calendar-alt"></i> <?php the_date(); ?></span>
					<span class="meta-item"><i class="fas fa-user"></i> <?php the_author(); ?></span>
				</div>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="page-featured-image">
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				<?php endif; ?>
			</header>

			<div class="page-entry-content">
				<?php
				the_content();
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sepehr-apple-portfolio' ),
					'after'  => '</div>',
				) );
				?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</div>

<style>
	.post-meta-data {
		display: flex;
		gap: 16px;
		font-size: 13px;
		margin-bottom: 24px;
	}

	body.dark-mode .post-meta-data {
		color: var(--text-secondary-dark);
	}

	body.light-mode .post-meta-data {
		color: var(--text-secondary-light);
	}

	.meta-item {
		display: flex;
		align-items: center;
		gap: 6px;
	}
</style>

<?php get_footer(); ?>
