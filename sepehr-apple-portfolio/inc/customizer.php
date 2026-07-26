<?php
if (!defined('ABSPATH')) exit;

/**
 * Register customizer panels and options for Sepehr Apple Portfolio.
 */
function st_customize_register($wp_customize) {
    $wp_customize->add_section('st_profile_section', array(
        'title'    => st_t('پروفایل و هدر اصلی','Profile & Hero','Profil & Hero'),
        'priority' => 30,
    ));

    $fields = array(
        'st_name' => array('label' => st_t('نام','Name','Name'), 'default' => 'سپهر طالبی', 'type' => 'text'),
        'st_title' => array('label' => st_t('عنوان شغلی','Job Title','Berufsbezeichnung'), 'default' => st_t('پورتفولیو و رزومه شخصی','Personal Portfolio & Resume','Persönliches Portfolio & Lebenslauf'), 'type' => 'text'),
        'st_age' => array('label' => st_t('سن','Age','Alter'), 'default' => '23', 'type' => 'text'),
        'st_location' => array('label' => st_t('موقعیت','Location','Standort'), 'default' => st_t('ایران','Iran','Iran'), 'type' => 'text'),
        'st_about' => array('label' => st_t('درباره من','About Me','Über mich'), 'default' => st_t('اینجا خلاصه‌ای کوتاه از معرفی، سبک کاری، تخصص و هدف حرفه‌ای خودت را بنویس.','Write a short introduction about your focus, style and professional goals here.','Schreibe hier eine kurze Vorstellung über deinen Fokus, Stil und deine beruflichen Ziele.'), 'type' => 'textarea'),
        'st_email' => array('label' => st_t('ایمیل','Email','E-Mail'), 'default' => 'hello@example.com', 'type' => 'email'),
        'st_phone' => array('label' => st_t('تلفن','Phone','Telefon'), 'default' => '+98', 'type' => 'text'),
        'st_cv_url' => array('label' => st_t('لینک رزومه PDF','PDF Resume URL','PDF-Lebenslauf URL'), 'default' => '#', 'type' => 'url'),
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
