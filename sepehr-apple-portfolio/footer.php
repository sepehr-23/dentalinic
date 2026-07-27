<?php
/**
 * Sepehr Apple Portfolio Theme Footer
 */
?>

<footer class="apple-footer">
	<div class="footer-container">
		<div class="footer-links">
			<?php
			$email = get_theme_mod( 'sepehr_email', 'sepehr.talebi@example.com' );
			$github = get_theme_mod( 'sepehr_github', 'https://github.com' );
			$linkedin = get_theme_mod( 'sepehr_linkedin', 'https://linkedin.com' );
			?>
			<a href="mailto:<?php echo esc_attr( $email ); ?>" target="_blank" aria-label="Email"><i class="fas fa-envelope"></i></a>
			<a href="<?php echo esc_url( $github ); ?>" target="_blank" aria-label="GitHub"><i class="fab fa-github"></i></a>
			<a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
		</div>
		<p class="copyright-text">
			<span class="lang-text fa-text">© <?php echo date('Y'); ?> تمام حقوق مادی و معنوی محفوظ است. طراح: سپهر طالبی</span>
			<span class="lang-text en-text">© <?php echo date('Y'); ?> All rights reserved. Crafted by Sepehr Talebi.</span>
			<span class="lang-text de-text">© <?php echo date('Y'); ?> Alle Rechte vorbehalten. Erstellt von Sepehr Talebi.</span>
		</p>
	</div>
</footer>

<style>
	.apple-footer {
		padding: 40px 22px;
		border-top: 1px solid;
		text-align: center;
		margin-top: 60px;
	}

	body.dark-mode .apple-footer {
		background-color: #0d0d0d;
		border-color: var(--border-dark);
		color: var(--text-secondary-dark);
	}

	body.light-mode .apple-footer {
		background-color: #f5f5f7;
		border-color: var(--border-light);
		color: var(--text-secondary-light);
	}

	.footer-container {
		max-width: 1024px;
		margin: 0 auto;
	}

	.footer-links {
		display: flex;
		justify-content: center;
		gap: 24px;
		margin-bottom: 16px;
	}

	.footer-links a {
		font-size: 20px;
		color: inherit;
		transition: color 0.3s, transform 0.2s;
	}

	.footer-links a:hover {
		color: var(--accent-color);
		transform: scale(1.15);
	}

	.copyright-text {
		font-size: 12px;
		letter-spacing: -0.01em;
		margin: 0;
	}
</style>

<?php wp_footer(); ?>
</body>
</html>
