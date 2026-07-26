<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
<!-- Google Translate Script Element -->
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'fa',
    includedLanguages: 'en,de,fa',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
    autoDisplay: false
  }, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<style>
/* Hide default Google Translate widgets for high-end feel */
.goog-te-banner-frame, .goog-te-banner, .skiptranslate, #google_translate_element {
  display: none !important;
}
body {
  top: 0px !important;
}
</style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="google_translate_element"></div>
<div class="st-bg-orb st-bg-orb-1"></div>
<div class="st-bg-orb st-bg-orb-2"></div>
<header class="st-site-header st-glass-card">
  <div class="st-container st-header-inner">
    <a class="st-brand" href="<?php echo esc_url(home_url('/')); ?>">
      <?php if (has_custom_logo()) { the_custom_logo(); } else { echo '<span>' . esc_html(st_get_theme_option('st_name', 'سپهر طالبی')) . '</span>'; } ?>
    </a>
    <nav class="st-nav">
      <?php
      if (has_nav_menu('primary')) {
          wp_nav_menu(array('theme_location' => 'primary','container' => false,'menu_class' => 'st-menu','fallback_cb' => false));
      } else {
          // Beautiful interactive custom fallback anchor list
          echo '<ul class="st-menu">';
          printf('<li><a href="%s">%s</a></li>', esc_url(home_url('/')), esc_html(st_t('خانه','Home','Startseite')));
          printf('<li><a href="#skills">%s</a></li>', esc_html(st_t('مهارت‌ها','Skills','Fähigkeiten')));
          printf('<li><a href="#projects">%s</a></li>', esc_html(st_t('پروژه‌ها','Projects','Projekte')));
          printf('<li><a href="#experience">%s</a></li>', esc_html(st_t('سوابق شغلی','Experience','Erfahrung')));
          printf('<li><a href="#education">%s</a></li>', esc_html(st_t('تحصیلات','Education','Ausbildung')));
          echo '</ul>';
      }
      ?>
    </nav>
    <div class="st-header-actions">
      <?php st_language_switcher(); ?>
      <button class="st-theme-toggle" type="button" aria-label="theme toggle">
        <span class="st-theme-toggle-icon">◐</span>
      </button>
    </div>
  </div>
</header>
