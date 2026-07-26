<!DOCTYPE html>
<html <?php language_attributes(); ?> class="theme-transition">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
	<style>
		/* Custom Theme Variable Injection and Base Styling */
		:root {
			--font-primary: 'Inter', 'Vazirmatn', sans-serif;
			--apple-bg-dark: #000000;
			--apple-bg-light: #f5f5f7;
			--glass-bg-dark: rgba(22, 22, 23, 0.75);
			--glass-bg-light: rgba(255, 255, 255, 0.7);
			--border-dark: rgba(255, 255, 255, 0.12);
			--border-light: rgba(0, 0, 0, 0.08);
			--text-dark: #f5f5f7;
			--text-light: #1d1d1f;
			--text-secondary-dark: #86868b;
			--text-secondary-light: #6e6e73;
			--accent-color: #0071e3;
			--accent-gradient: linear-gradient(135deg, #2997ff, #0071e3);
			--shadow-dark: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
			--shadow-light: 0 8px 32px 0 rgba(0, 0, 0, 0.04);
		}

		/* Global Style Rules for Seamless Theme Swapping */
		body {
			font-family: var(--font-primary);
			margin: 0;
			padding: 0;
			transition: background-color 0.5s ease, color 0.5s ease;
		}

		body.dark-mode {
			background-color: var(--apple-bg-dark);
			color: var(--text-dark);
		}

		body.light-mode {
			background-color: var(--apple-bg-light);
			color: var(--text-light);
		}

		.theme-transition * {
			transition: background-color 0.4s ease, border-color 0.4s ease, color 0.4s ease, box-shadow 0.4s ease !important;
		}

		/* Navigation Container styled beautifully */
		header.apple-nav {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			height: 48px;
			z-index: 1000;
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			border-bottom: 1px solid;
			transition: all 0.3s;
		}

		body.dark-mode header.apple-nav {
			background-color: var(--glass-bg-dark);
			border-color: var(--border-dark);
		}

		body.light-mode header.apple-nav {
			background-color: var(--glass-bg-light);
			border-color: var(--border-light);
		}

		.nav-container {
			max-width: 1024px;
			margin: 0 auto;
			padding: 0 22px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			height: 100%;
		}

		.brand-logo {
			font-weight: 600;
			font-size: 16px;
			letter-spacing: -0.01em;
			color: inherit;
			text-decoration: none;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.brand-logo i {
			font-size: 18px;
		}

		.nav-controls {
			display: flex;
			align-items: center;
			gap: 16px;
		}

		/* Language and Theme Switcher Buttons */
		.switcher-btn {
			background: none;
			border: none;
			color: inherit;
			cursor: pointer;
			font-size: 13px;
			font-weight: 500;
			padding: 4px 8px;
			border-radius: 980px;
			transition: background-color 0.2s;
			display: flex;
			align-items: center;
			gap: 4px;
		}

		body.dark-mode .switcher-btn:hover {
			background-color: rgba(255, 255, 255, 0.1);
		}

		body.light-mode .switcher-btn:hover {
			background-color: rgba(0, 0, 0, 0.05);
		}

		.lang-select-container {
			position: relative;
			display: inline-block;
		}

		.lang-dropdown {
			position: absolute;
			top: 100%;
			margin-top: 6px;
			right: 0;
			border-radius: 12px;
			border: 1px solid;
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			display: none;
			flex-direction: column;
			min-width: 120px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
			overflow: hidden;
			z-index: 1001;
		}

		body.dark-mode .lang-dropdown {
			background-color: var(--glass-bg-dark);
			border-color: var(--border-dark);
		}

		body.light-mode .lang-dropdown {
			background-color: var(--glass-bg-light);
			border-color: var(--border-light);
		}

		.lang-dropdown.show {
			display: flex;
		}

		.lang-dropdown button {
			background: none;
			border: none;
			color: inherit;
			text-align: right;
			padding: 10px 14px;
			cursor: pointer;
			width: 100%;
			font-size: 13px;
			transition: background 0.2s;
		}

		html[dir="ltr"] .lang-dropdown button {
			text-align: left;
		}

		body.dark-mode .lang-dropdown button:hover {
			background-color: rgba(255, 255, 255, 0.08);
		}

		body.light-mode .lang-dropdown button:hover {
			background-color: rgba(0, 0, 0, 0.05);
		}
	</style>
</head>
<body <?php body_class( 'dark-mode' ); ?>>
<?php wp_body_open(); ?>

<header class="apple-nav">
	<div class="nav-container">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-logo">
			<i class="fab fa-apple"></i>
			<span class="brand-name">Sepehr Talebi</span>
		</a>

		<div class="nav-controls">
			<!-- Language Switcher -->
			<div class="lang-select-container">
				<button class="switcher-btn" id="langSelectorBtn" aria-label="Toggle language">
					<i class="fas fa-globe"></i>
					<span id="activeLangLabel">فارسی</span>
				</button>
				<div class="lang-dropdown" id="langDropdown">
					<button onclick="setAppLanguage('fa')">فارسی</button>
					<button onclick="setAppLanguage('en')">English</button>
					<button onclick="setAppLanguage('de')">Deutsch</button>
				</div>
			</div>

			<!-- Dark/Light Theme Toggle Switcher -->
			<button class="switcher-btn" id="themeToggleBtn" onclick="toggleThemeMode()" aria-label="Toggle theme">
				<i class="fas fa-moon" id="themeIcon"></i>
			</button>
		</div>
	</div>
</header>
