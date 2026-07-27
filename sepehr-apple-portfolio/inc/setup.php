<?php
if (!defined('ABSPATH')) exit;

/**
 * Get current application active language.
 */
function st_current_lang() {
    if (function_exists('pll_current_language')) return pll_current_language('slug');
    $locale = determine_locale();
    if (strpos($locale, 'de_') === 0) return 'de';
    if (strpos($locale, 'en_') === 0) return 'en';
    return 'fa';
}

/**
 * Tri-lingual string translation helper.
 */
function st_t($fa, $en = '', $de = '') {
    $lang = st_current_lang();
    if ($lang === 'de' && $de !== '') return $de;
    if ($lang === 'en' && $en !== '') return $en;
    return $fa;
}

/**
 * Setup core features of Sepehr Apple Portfolio Theme.
 */
function st_theme_setup() {
    load_theme_textdomain('st-portfolio', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array('height' => 96, 'width' => 240, 'flex-height' => true, 'flex-width' => true));
    add_theme_support('html5', array('search-form','comment-form','comment-list','gallery','caption','style','script'));
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');

    register_nav_menus(array(
        'primary' => st_t('منوی اصلی','Primary Menu','Hauptmenü'),
        'footer'  => st_t('منوی فوتر','Footer Menu','Footer-Menü'),
    ));
}
add_action('after_setup_theme', 'st_theme_setup');

/**
 * Enqueue scripts and styles.
 */
function st_enqueue_assets() {
    // Vazirmatn and Inter Premium typography web fonts
    wp_enqueue_style('st-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Vazirmatn:wght@300;400;500;700;800&display=swap', array(), null);

    // FontAwesome icons for gorgeous visuals
    wp_enqueue_style('st-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');

    // Main Theme stylesheet
    wp_enqueue_style('st-main-style', get_stylesheet_uri(), array('st-google-fonts', 'st-font-awesome'), '1.0.0');

    // Core custom interactive javascript
    wp_enqueue_script('st-theme-script', get_template_directory_uri() . '/js/custom.js', array('jquery'), '1.0.0', true);

    wp_localize_script('st-theme-script', 'stThemeData', array(
        'light' => st_t('حالت روشن','Light mode','Hellmodus'),
        'dark'  => st_t('حالت تیره','Dark mode','Dunkelmodus'),
    ));
}
add_action('wp_enqueue_scripts', 'st_enqueue_assets');

/**
 * Inject proper body utility classes.
 */
function st_body_classes($classes) {
    $classes[] = 'st-portfolio-theme';
    $classes[] = is_rtl() ? 'st-rtl' : 'st-ltr';
    return $classes;
}
add_filter('body_class', 'st_body_classes');

/**
 * Register Widget sidebars.
 */
function st_register_sidebars() {
    register_sidebar(array(
        'name' => st_t('فوتر','Footer','Footer'),
        'id' => 'footer-1',
        'before_widget' => '<div class="st-footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="st-footer-widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'st_register_sidebars');

/**
 * Support Elementor Location managers.
 */
function st_register_elementor_locations($manager) {
    if (is_object($manager) && method_exists($manager, 'register_all_core_location')) {
        $manager->register_all_core_location();
    }
}
add_action('elementor/theme/register_locations', 'st_register_elementor_locations');
