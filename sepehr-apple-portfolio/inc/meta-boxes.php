<?php
if (!defined('ABSPATH')) exit;

/**
 * Register custom meta boxes for portfolio items.
 */
function st_meta_boxes() {
    add_meta_box('st_skill_meta', st_t('جزئیات مهارت','Skill Details','Skill-Details'), 'st_render_skill_meta', 'st_skill', 'normal', 'high');
    add_meta_box('st_project_meta', st_t('جزئیات پروژه','Project Details','Projekt-Details'), 'st_render_project_meta', 'st_project', 'normal', 'high');
    foreach (array('st_experience','st_education','st_course','st_certificate') as $type) {
        add_meta_box('st_common_meta', st_t('جزئیات','Details','Details'), 'st_render_common_meta', $type, 'normal', 'high');
    }
}
add_action('add_meta_boxes', 'st_meta_boxes');

/**
 * Helper to render custom administration fields.
 */
function st_field($name, $label, $type = 'text') {
    $value = get_post_meta(get_the_ID(), $name, true);
    echo '<p><label style="display:block;margin-bottom:6px;font-weight:700;">' . esc_html($label) . '</label>';
    printf('<input type="%1$s" name="%2$s" value="%3$s" style="width:100%%;padding:10px;" /></p>', esc_attr($type), esc_attr($name), esc_attr($value));
}

function st_render_skill_meta($post) {
    wp_nonce_field('st_save_meta', 'st_meta_nonce');
    st_field('st_skill_level', st_t('درصد مهارت','Skill Percentage','Skill-Prozent'));
}

function st_render_project_meta($post) {
    wp_nonce_field('st_save_meta', 'st_meta_nonce');
    st_field('st_project_url', st_t('لینک پروژه','Project URL','Projekt-URL'), 'url');
    st_field('st_project_stack', st_t('تکنولوژی‌ها','Tech Stack','Tech-Stack'));
    st_field('st_project_year', st_t('سال','Year','Jahr'));
}

function st_render_common_meta($post) {
    wp_nonce_field('st_save_meta', 'st_meta_nonce');
    st_field('st_meta_org', st_t('سازمان / محل','Organization / Place','Organisation / Ort'));
    st_field('st_meta_role', st_t('عنوان / نقش','Title / Role','Titel / Rolle'));
    st_field('st_meta_date', st_t('بازه زمانی','Date Range','Zeitraum'));
    st_field('st_meta_url', st_t('لینک مرتبط','Related URL','Zugehörige URL'), 'url');
}

/**
 * Save meta options correctly.
 */
function st_save_meta($post_id) {
    if (!isset($_POST['st_meta_nonce']) || !wp_verify_nonce($_POST['st_meta_nonce'], 'st_save_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array('st_skill_level','st_project_url','st_project_stack','st_project_year','st_meta_org','st_meta_role','st_meta_date','st_meta_url');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $sanitize_cb = 'sanitize_text_field';
            if (in_array($field, array('st_project_url', 'st_meta_url'))) {
                $sanitize_cb = 'esc_url_raw';
            }
            update_post_meta($post_id, $field, $sanitize_cb(wp_unslash($_POST[$field])));
        }
    }
}
add_action('save_post', 'st_save_meta');
