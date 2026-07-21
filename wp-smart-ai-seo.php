<?php
/**
 * Plugin Name: افزونه سئو هوشمند و دستیار محتوای هوش مصنوعی (Smart AI SEO)
 * Plugin URI: https://wordpress.org/plugins/wp-smart-ai-seo/
 * Description: دستیار سئو همه کاره متصل به هوش مصنوعی رایگان (Gemini). ویرایش مقالات، تحلیل رقبا، ساختار محتوایی پیلار و کلاستر، دانلود خودکار تصاویر با تگ Alt هوشمند، و سازگاری با رنک مث و یواست سئو.
 * Version: 1.0.0
 * Author: Jules
 * Author URI: https://example.com/
 * License: GPL2
 * Text Domain: wp-smart-ai-seo
 * Domain Path: /languages
 */

// جلوگیری از دسترسی مستقیم به فایل
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// تعریف ثابت‌های عمومی افزونه
define( 'WP_SMART_AI_SEO_VERSION', '1.0.0' );
define( 'WP_SMART_AI_SEO_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_SMART_AI_SEO_URL', plugin_dir_url( __FILE__ ) );

/**
 * فعال‌سازی و غیرفعال‌سازی افزونه
 */
register_activation_hook( __FILE__, 'wp_smart_ai_seo_activate' );
function wp_smart_ai_seo_activate() {
    // ایجاد ساختار دیتابیس در صورت نیاز یا ذخیره تنظیمات پیش‌فرض
    if ( ! get_option( 'wp_smart_ai_seo_settings' ) ) {
        update_option( 'wp_smart_ai_seo_settings', array(
            'api_provider' => 'gemini',
            'api_key'      => '',
            'tone'         => 'friendly',
            'unsplash_key' => '',
            'lang'         => 'fa'
        ) );
    }
}

register_deactivation_hook( __FILE__, 'wp_smart_ai_seo_deactivate' );
function wp_smart_ai_seo_deactivate() {
    // پاکسازی‌های لازم در زمان غیرفعال‌سازی
}

/**
 * بارگذاری خودکار کلاس‌ها و منابع مورد نیاز
 */
function wp_smart_ai_seo_init() {
    // لود کردن فایل‌های اصلی و کلاس‌های پشت صحنه
    require_once WP_SMART_AI_SEO_PATH . 'includes/class-ai-engine.php';
    require_once WP_SMART_AI_SEO_PATH . 'includes/class-competitor.php';
    require_once WP_SMART_AI_SEO_PATH . 'includes/class-seo-integrator.php';
    require_once WP_SMART_AI_SEO_PATH . 'includes/class-image-handler.php';
    require_once WP_SMART_AI_SEO_PATH . 'includes/class-pilar-builder.php';

    // لود کردن پنل مدیریت
    if ( is_admin() ) {
        require_once WP_SMART_AI_SEO_PATH . 'admin/class-admin-panel.php';
        // نمونه‌سازی کنترلر ادمین
        new WPSmartAI_Admin_Panel();
    }
}
add_action( 'plugins_loaded', 'wp_smart_ai_seo_init' );
