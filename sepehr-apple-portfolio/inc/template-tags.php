<?php
if (!defined('ABSPATH')) exit;

/**
 * Get theme mod option with fallback.
 */
function st_get_theme_option($key, $default = '') {
    $value = get_theme_mod($key, $default);
    return $value === '' ? $default : $value;
}

/**
 * Get filtered active social links.
 */
function st_social_links() {
    return array_filter(array(
        'LinkedIn'  => st_get_theme_option('st_linkedin'),
        'GitHub'    => st_get_theme_option('st_github'),
        'Behance'   => st_get_theme_option('st_behance'),
        'Instagram' => st_get_theme_option('st_instagram'),
        'Telegram'  => st_get_theme_option('st_telegram'),
    ));
}

/**
 * Render standard tri-lingual language switcher.
 */
function st_language_switcher() {
    if (function_exists('pll_the_languages')) {
        $langs = pll_the_languages(array('raw' => 1, 'hide_if_no_translation' => 0));
        if (!empty($langs)) {
            echo '<div class="st-lang-switcher">';
            foreach ($langs as $lang) {
                printf('<a class="%1$s" href="%2$s">%3$s</a>', esc_attr($lang['current_lang'] ? 'is-active' : ''), esc_url($lang['url']), esc_html(strtoupper($lang['slug'])));
            }
            echo '</div>';
            return;
        }
    }
    echo '<div class="st-lang-switcher"><span class="is-active" data-lang="fa">FA</span><span data-lang="en">EN</span><span data-lang="de">DE</span></div>';
}

/**
 * Query custom portfolio items safely.
 */
function st_query_items($post_type, $limit = 6) {
    return new WP_Query(array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'menu_order date',
        'order' => 'ASC',
    ));
}
