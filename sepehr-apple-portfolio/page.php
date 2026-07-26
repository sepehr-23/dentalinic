<?php
/**
 * Sepehr Apple Portfolio Standard Page Template
 * Supports Elementor editing seamlessly by rendering standard post contents
 */

get_header(); ?>

<div class="main-portfolio-wrapper standard-page-layout">
	<?php
	while ( have_posts() ) :
		the_post();

		// Check if Elementor has taken over this page
		if ( function_exists( 'elementor_theme_do_location' ) || ( class_exists( '\\Elementor\\Plugin' ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor() ) ) {
			the_content();
		} else {
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'classic-post-article' ); ?>>
				<header class="page-header">
					<h1 class="page-title"><?php the_title(); ?></h1>
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
		}
	endwhile;
	?>
</div>

<style>
	.standard-page-layout {
		min-height: 70vh;
		padding-top: 100px;
	}

	.classic-post-article {
		backdrop-filter: blur(30px);
		-webkit-backdrop-filter: blur(30px);
		border: 1px solid;
		border-radius: 24px;
		padding: 32px;
		margin-bottom: 40px;
	}

	body.dark-mode .classic-post-article {
		background-color: var(--glass-bg-dark);
		border-color: var(--border-dark);
		box-shadow: var(--shadow-dark);
	}

	body.light-mode .classic-post-article {
		background-color: var(--glass-bg-light);
		border-color: var(--border-light);
		box-shadow: var(--shadow-light);
	}

	.page-title {
		font-size: 32px;
		font-weight: 700;
		margin-bottom: 24px;
	}

	.page-featured-image {
		margin-bottom: 24px;
		border-radius: 16px;
		overflow: hidden;
	}

	.page-featured-image img {
		width: 100%;
		height: auto;
		display: block;
	}

	.page-entry-content {
		font-size: 16px;
		line-height: 1.8;
	}
</style>

<?php get_footer(); ?>
