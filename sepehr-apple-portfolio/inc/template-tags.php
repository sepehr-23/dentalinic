<?php
if (!defined('ABSPATH')) exit;

/**
 * Get dynamic theme option depending on manual selected translation options
 */
function st_get_theme_option($key, $default = '') {
    $lang = st_current_lang();
    // Try to retrieve language specific value first
    $lang_key = $key . '_' . $lang;
    $value = get_theme_mod($lang_key, '');

    if ($value === '') {
        $value = get_theme_mod($key, $default);
    }
    return $value === '' ? $default : $value;
}

/**
 * Get filtered active social links.
 */
function st_social_links() {
    return array_filter(array(
        'LinkedIn'  => get_theme_mod('st_linkedin'),
        'GitHub'    => get_theme_mod('st_github'),
        'Behance'   => get_theme_mod('st_behance'),
        'Instagram' => get_theme_mod('st_instagram'),
        'Telegram'  => get_theme_mod('st_telegram'),
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
                printf('<a class="%1$s" href="%2$s" data-lang="%3$s">%4$s</a>', esc_attr($lang['current_lang'] ? 'is-active' : ''), esc_url($lang['url']), esc_attr($lang['slug']), esc_html(strtoupper($lang['slug'])));
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
