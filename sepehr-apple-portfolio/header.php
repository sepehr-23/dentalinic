<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="st-bg-orb st-bg-orb-1"></div>
<div class="st-bg-orb st-bg-orb-2"></div>
<header class="st-site-header st-glass-card">
  <div class="st-container st-header-inner">
    <a class="st-brand" href="<?php echo esc_url(home_url('/')); ?>">
      <?php if (has_custom_logo()) { the_custom_logo(); } else { echo '<span>' . esc_html(st_get_theme_option('st_name', 'سپهر طالبی')) . '</span>'; } ?>
    </a>
    <nav class="st-nav">
      <?php wp_nav_menu(array('theme_location' => 'primary','container' => false,'menu_class' => 'st-menu','fallback_cb' => false)); ?>
    </nav>
    <div class="st-header-actions">
      <?php st_language_switcher(); ?>
      <button class="st-theme-toggle" type="button" aria-label="theme toggle">
        <span class="st-theme-toggle-icon">◐</span>
      </button>
    </div>
  </div>
</header>
