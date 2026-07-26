<?php
if (!defined('ABSPATH')) exit;

/**
 * Register customizer panels and options for Sepehr Apple Portfolio.
 * Provides explicit tri-lingual (FA, EN, DE) translatable fields in theme customizer.
 */
function st_customize_register($wp_customize) {
    $wp_customize->add_section('st_profile_section', array(
        'title'    => st_t('تنظیمات پورتفولیو (Portfolio Theme Options)','Portfolio Theme Options','Portfolio Theme Options'),
        'priority' => 30,
    ));

    // Define individual tri-lingual fields
    $fields = array(
        // Name
        'st_name_fa' => array('label' => 'نام (Farsi)', 'default' => 'سپهر طالبی', 'type' => 'text'),
        'st_name_en' => array('label' => 'Name (English)', 'default' => 'Sepehr Talebi', 'type' => 'text'),
        'st_name_de' => array('label' => 'Name (Deutsch)', 'default' => 'Sepehr Talebi', 'type' => 'text'),

        // Title
        'st_title_fa' => array('label' => 'عنوان شغلی (Farsi)', 'default' => 'پورتفولیو و رزومه شخصی', 'type' => 'text'),
        'st_title_en' => array('label' => 'Job Title (English)', 'default' => 'Personal Portfolio & Resume', 'type' => 'text'),
        'st_title_de' => array('label' => 'Berufsbezeichnung (Deutsch)', 'default' => 'Persönliches Portfolio & Lebenslauf', 'type' => 'text'),

        // About
        'st_about_fa' => array('label' => 'درباره من (Farsi)', 'default' => 'اینجا خلاصه‌ای کوتاه از معرفی، سبک کاری، تخصص و هدف حرفه‌ای خودت را بنویس.', 'type' => 'textarea'),
        'st_about_en' => array('label' => 'About Me (English)', 'default' => 'Write a short introduction about your focus, style and professional goals here.', 'type' => 'textarea'),
        'st_about_de' => array('label' => 'Über mich (Deutsch)', 'default' => 'Schreibe hier eine kurze Vorstellung über deinen Fokus, Stil und deine beruflichen Ziele.', 'type' => 'textarea'),

        // Basic Info
        'st_age' => array('label' => st_t('سن','Age','Alter'), 'default' => '23', 'type' => 'text'),
        'st_location_fa' => array('label' => 'موقعیت (Farsi)', 'default' => 'ایران', 'type' => 'text'),
        'st_location_en' => array('label' => 'Location (English)', 'default' => 'Iran', 'type' => 'text'),
        'st_location_de' => array('label' => 'Standort (Deutsch)', 'default' => 'Iran', 'type' => 'text'),

        'st_email' => array('label' => st_t('ایمیل','Email','E-Mail'), 'default' => 'hello@example.com', 'type' => 'email'),
        'st_phone' => array('label' => st_t('تلفن','Phone','Telefon'), 'default' => '+98', 'type' => 'text'),
        'st_cv_url' => array('label' => st_t('لینک رزومه PDF','PDF Resume URL','PDF-Lebenslauf URL'), 'default' => '#', 'type' => 'url'),

        // Social Media Link list
        'st_linkedin' => array('label' => 'LinkedIn URL', 'default' => '#', 'type' => 'url'),
        'st_github' => array('label' => 'GitHub URL', 'default' => '#', 'type' => 'url'),
        'st_behance' => array('label' => 'Behance URL', 'default' => '#', 'type' => 'url'),
        'st_instagram' => array('label' => 'Instagram URL', 'default' => '#', 'type' => 'url'),
        'st_telegram' => array('label' => 'Telegram URL', 'default' => '#', 'type' => 'url'),
    );

    foreach ($fields as $key => $field) {
        $sanitize = in_array($field['type'], array('url')) ? 'esc_url_raw' : ($field['type'] === 'email' ? 'sanitize_email' : 'sanitize_text_field');
        if ($field['type'] === 'textarea') $sanitize = 'sanitize_textarea_field';

        $wp_customize->add_setting($key, array('default' => $field['default'], 'sanitize_callback' => $sanitize));
        $wp_customize->add_control($key, array(
            'section' => 'st_profile_section',
            'label'   => $field['label'],
            'type'    => $field['type'],
        ));
    }

    $wp_customize->add_setting('st_photo', array('sanitize_callback' => 'absint'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'st_photo', array(
        'label' => st_t('تصویر پروفایل','Profile Image','Profilbild'),
        'section' => 'st_profile_section',
        'mime_type' => 'image',
    )));
}
add_action('customize_register', 'st_customize_register');
