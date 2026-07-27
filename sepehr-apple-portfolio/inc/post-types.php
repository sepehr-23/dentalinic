<?php
if (!defined('ABSPATH')) exit;

/**
 * Register Custom Portfolio Post Types with translations.
 */
function st_register_portfolio_post_types() {
    $types = array(
        'st_project' => array('singular' => st_t('پروژه','Project','Projekt'), 'plural' => st_t('پروژه‌ها','Projects','Projekte'), 'icon' => 'dashicons-portfolio'),
        'st_skill' => array('singular' => st_t('مهارت','Skill','Fähigkeit'), 'plural' => st_t('مهارت‌ها','Skills','Fähigkeiten'), 'icon' => 'dashicons-star-filled'),
        'st_experience' => array('singular' => st_t('سابقه شغلی','Experience','Berufserfahrung'), 'plural' => st_t('سوابق شغلی','Experiences','Berufserfahrungen'), 'icon' => 'dashicons-businessperson'),
        'st_education' => array('singular' => st_t('تحصیل','Education','Ausbildung'), 'plural' => st_t('تحصیلات','Education','Ausbildungen'), 'icon' => 'dashicons-welcome-learn-more'),
        'st_course' => array('singular' => st_t('دوره','Course','Kurs'), 'plural' => st_t('دوره‌ها','Courses','Kurse'), 'icon' => 'dashicons-media-document'),
        'st_certificate' => array('singular' => st_t('گواهینامه','Certificate','Zertifikat'), 'plural' => st_t('گواهینامه‌ها','Certificates','Zertifikate'), 'icon' => 'dashicons-awards'),
    );

    foreach ($types as $type => $data) {
        register_post_type($type, array(
            'labels' => array(
                'name' => $data['plural'],
                'singular_name' => $data['singular'],
                'add_new_item' => st_t('افزودن مورد جدید','Add New Item','Neues Element hinzufügen'),
                'edit_item' => st_t('ویرایش','Edit','Bearbeiten'),
            ),
            'public' => true,
            'show_in_rest' => true,
            'menu_icon' => $data['icon'],
            'supports' => array('title','editor','thumbnail','excerpt','page-attributes'),
            'has_archive' => true,
            'rewrite' => array('slug' => str_replace('st_','',$type)),
        ));
    }
}
add_action('init', 'st_register_portfolio_post_types');
