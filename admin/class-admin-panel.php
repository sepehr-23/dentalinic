<?php
/**
 * مدیریت پنل ادمین و درخواست‌های AJAX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPSmartAI_Admin_Panel {

    public function __construct() {
        // افزودن منوها به پیشخوان
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        // لود استایل و جاوااسکریپت فقط در صفحات این افزونه
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // ثبت هندلرهای AJAX
        add_action( 'wp_ajax_smart_ai_save_settings', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_smart_ai_optimize_post', array( $this, 'ajax_optimize_post' ) );
        add_action( 'wp_ajax_smart_ai_analyze_competitors', array( $this, 'ajax_analyze_competitors' ) );
        add_action( 'wp_ajax_smart_ai_generate_new_post', array( $this, 'ajax_generate_new_post' ) );
        add_action( 'wp_ajax_smart_ai_suggest_clusters', array( $this, 'ajax_suggest_clusters' ) );
        add_action( 'wp_ajax_smart_ai_create_cluster_link', array( $this, 'ajax_create_cluster_link' ) );
    }

    public function add_admin_menu() {
        add_menu_page(
            'سئو هوشمند',
            'سئو هوشمند AI',
            'manage_options',
            'wp-smart-ai-seo',
            array( $this, 'render_main_page' ),
            'dashicons-superhero',
            30
        );

        add_submenu_page(
            'wp-smart-ai-seo',
            'بهینه‌سازی مقالات',
            'بهینه‌سازی مقالات',
            'manage_options',
            'wp-smart-ai-seo-optimizer',
            array( $this, 'render_optimizer_page' )
        );

        add_submenu_page(
            'wp-smart-ai-seo',
            'نگارش با تحلیل رقبا',
            'نویسنده جادویی',
            'manage_options',
            'wp-smart-ai-seo-writer',
            array( $this, 'render_writer_page' )
        );

        add_submenu_page(
            'wp-smart-ai-seo',
            'ساختار پیلار-کلاستر',
            'پیلار و کلاستر',
            'manage_options',
            'wp-smart-ai-seo-pillar',
            array( $this, 'render_pillar_page' )
        );
    }

    public function enqueue_admin_assets( $hook ) {
        // لود فایل‌ها فقط برای صفحات افزونه سئو هوشمند
        if ( strpos( $hook, 'wp-smart-ai-seo' ) === false ) {
            return;
        }

        wp_enqueue_style( 'wp-smart-ai-seo-css', WP_SMART_AI_SEO_URL . 'admin/css/admin-style.css', array(), WP_SMART_AI_SEO_VERSION );
        wp_enqueue_script( 'wp-smart-ai-seo-js', WP_SMART_AI_SEO_URL . 'admin/js/admin-script.js', array( 'jquery' ), WP_SMART_AI_SEO_VERSION, true );

        // ارسال داده‌های مورد نیاز به اسکریپت جاوااسکریپت
        wp_localize_script( 'wp-smart-ai-seo-js', 'smart_ai_params', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'smart_ai_nonce' )
        ) );
    }

    /**
     * رندر صفحات مختلف
     */
    public function render_main_page() {
        include WP_SMART_AI_SEO_PATH . 'templates/settings-page.php';
    }

    public function render_optimizer_page() {
        include WP_SMART_AI_SEO_PATH . 'templates/optimizer-page.php';
    }

    public function render_writer_page() {
        include WP_SMART_AI_SEO_PATH . 'templates/writer-page.php';
    }

    public function render_pillar_page() {
        include WP_SMART_AI_SEO_PATH . 'templates/pillar-page.php';
    }

    /**
     * عملیات‌های AJAX
     */
    public function ajax_save_settings() {
        check_ajax_referer( 'smart_ai_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'دسترسی غیرمجاز.' ) );
        }

        $api_provider = sanitize_text_field( $_POST['api_provider'] );
        $api_key      = sanitize_text_field( $_POST['api_key'] );
        $unsplash_key = sanitize_text_field( $_POST['unsplash_key'] );
        $tone         = sanitize_text_field( $_POST['tone'] );

        $settings = array(
            'api_provider' => $api_provider,
            'api_key'      => $api_key,
            'unsplash_key' => $unsplash_key,
            'tone'         => $tone,
            'lang'         => 'fa'
        );

        update_option( 'wp_smart_ai_seo_settings', $settings );
        wp_send_json_success( array( 'message' => 'تنظیمات با موفقیت ذخیره شد.' ) );
    }

    public function ajax_optimize_post() {
        check_ajax_referer( 'smart_ai_nonce', 'security' );
        $post_id = intval( $_POST['post_id'] );
        $keyword = sanitize_text_field( $_POST['keyword'] );

        if ( ! $post_id || empty( $keyword ) ) {
            wp_send_json_error( array( 'message' => 'شناسه مقاله یا کلمه کلیدی نامعتبر است.' ) );
        }

        $post = get_post( $post_id );
        if ( ! $post ) {
            wp_send_json_error( array( 'message' => 'مقاله پیدا نشد.' ) );
        }

        $settings = get_option( 'wp_smart_ai_seo_settings', array() );
        $tone = isset( $settings['tone'] ) ? $settings['tone'] : 'friendly';

        // ۱. تولید محتوای بهینه‌شده به همراه کدهای تصویری <!-- PLACE_IMAGE: ... -->
        $optimized_text = WPSmartAI_Engine::generate_optimized_content( $post->post_content, $keyword, $tone );
        if ( is_wp_error( $optimized_text ) ) {
            wp_send_json_error( array( 'message' => $optimized_text->get_error_message() ) );
        }

        // ۲. دانلود و درج تصاویر هوشمند با تگ Alt خودکار به جای تگ‌های موقت تصویر
        $final_content = WPSmartAI_Image_Handler::insert_images_into_content( $optimized_text, $post_id );

        // ۳. ساخت متادیتای سئو و تغییر آن‌ها در افزونه‌های Rank Math / Yoast
        $meta_data = WPSmartAI_SEO_Integrator::generate_meta_suggestions( $final_content, $keyword );
        WPSmartAI_SEO_Integrator::update_seo_metadata( $post_id, $keyword, $meta_data['title'], $meta_data['description'] );

        // ۴. بروزرسانی نهایی مقاله در وردپرس
        wp_update_post( array(
            'ID'           => $post_id,
            'post_content' => $final_content
        ) );

        wp_send_json_success( array(
            'message' => 'مقاله با موفقیت بهینه‌سازی شد، تصاویر دانلود شدند و چراغ‌های سئو سبز شدند!',
            'meta'    => $meta_data
        ) );
    }

    public function ajax_analyze_competitors() {
        check_ajax_referer( 'smart_ai_nonce', 'security' );
        $keyword = sanitize_text_field( $_POST['keyword'] );

        if ( empty( $keyword ) ) {
            wp_send_json_error( array( 'message' => 'کلمه کلیدی را وارد کنید.' ) );
        }

        $competitors = WPSmartAI_Competitor::analyze_competitors( $keyword );
        wp_send_json_success( array( 'competitors' => $competitors ) );
    }

    public function ajax_generate_new_post() {
        check_ajax_referer( 'smart_ai_nonce', 'security' );
        $keyword = sanitize_text_field( $_POST['keyword'] );
        $competitor_data = $_POST['competitor_data']; // متن‌های خلاصه شده رقیب

        if ( empty( $keyword ) || empty( $competitor_data ) ) {
            wp_send_json_error( array( 'message' => 'کلمه کلیدی یا داده‌های رقیب موجود نیست.' ) );
        }

        $settings = get_option( 'wp_smart_ai_seo_settings', array() );
        $tone = isset( $settings['tone'] ) ? $settings['tone'] : 'friendly';
        $tone_farsi = 'دوستانه و صمیمی';
        if ( $tone === 'formal' ) {
            $tone_farsi = 'رسمی و علمی';
        }

        // ساخت پرومپت رقابتی بر اساس مقالات رقیب
        $prompt = "تو یک استراتژیست محتوا و نویسنده افسانه‌ای سئو به زبان فارسی هستی.
وظیفه تو نوشتن یک مقاله فوق‌العاده با کلمه کلیدی اصلی '{$keyword}' است.
ما ۳ رقیب برتر در این کلمه کلیدی را تحلیل کردیم و خلاصه‌ای از مطالب آنها در زیر آمده است:
{$competitor_data}

ماموریت تو:
1. مقاله‌ای بنویس که کامل‌تر، جامع‌تر و جذاب‌تر از هر سه رقیب باشد. جاهای خالی محتوای آن‌ها را پر کن.
2. لحن متن باید '{$tone_farsi}' باشد.
3. اصول نگارشی فارسی، استفاده فراوان و طبیعی از کلمه کلیدی '{$keyword}' را در هدینگ‌ها (H2, H3)، پاراگراف اول و بدنه رعایت کن.
4. جدول مقایسه‌ای یا لیست‌های نشانه‌دار جذاب بساز.
5. حتماً در جاهای بسیار جذاب مقاله تگ‌های <!-- PLACE_IMAGE: توصیف تصویر به زبان فارسی --> برای درج عکس قرار بده.
6. هیچ متنی جز بدنه اصلی مقاله فارسی خروجی نده.";

        $text_response = WPSmartAI_Engine::call_gemini( $prompt );
        if ( is_wp_error( $text_response ) ) {
            wp_send_json_error( array( 'message' => $text_response->get_error_message() ) );
        }

        // ایجاد پست جدید در حالت پیش‌نویس
        $new_post_id = wp_insert_post( array(
            'post_title'   => 'راهنمای جامع و ناگفته‌های ' . $keyword,
            'post_content' => $text_response,
            'post_status'  => 'draft',
            'post_type'    => 'post'
        ) );

        if ( is_wp_error( $new_post_id ) ) {
            wp_send_json_error( array( 'message' => 'خطا در ایجاد پیش‌نویس پست.' ) );
        }

        // دانلود تصاویر و قرار دادن آلت‌ها
        $final_content = WPSmartAI_Image_Handler::insert_images_into_content( $text_response, $new_post_id );

        // تولید متادیتا و ست کردن تیک‌های سئو
        $meta_data = WPSmartAI_SEO_Integrator::generate_meta_suggestions( $final_content, $keyword );
        WPSmartAI_SEO_Integrator::update_seo_metadata( $new_post_id, $keyword, $meta_data['title'], $meta_data['description'] );

        // آپدیت متن نهایی مقاله
        wp_update_post( array(
            'ID'           => $new_post_id,
            'post_content' => $final_content
        ) );

        wp_send_json_success( array(
            'message' => 'مقاله فوق رقابتی با موفقیت نوشته و به عنوان پیش‌نویس ذخیره شد!',
            'post_id' => $new_post_id,
            'edit_url'=> get_edit_post_link( $new_post_id, 'raw' )
        ) );
    }

    public function ajax_suggest_clusters() {
        check_ajax_referer( 'smart_ai_nonce', 'security' );
        $post_id = intval( $_POST['post_id'] );

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'شناسه پست نامعتبر است.' ) );
        }

        $clusters = WPSmartAI_Pilar_Builder::suggest_clusters( $post_id );
        if ( is_wp_error( $clusters ) ) {
            wp_send_json_error( array( 'message' => $clusters->get_error_message() ) );
        }

        wp_send_json_success( array( 'clusters' => $clusters ) );
    }

    public function ajax_create_cluster_link() {
        check_ajax_referer( 'smart_ai_nonce', 'security' );
        $source_id = intval( $_POST['source_id'] );
        $target_id = intval( $_POST['target_id'] );
        $anchor    = sanitize_text_field( $_POST['anchor'] );

        if ( ! $source_id || ! $target_id || empty( $anchor ) ) {
            wp_send_json_error( array( 'message' => 'پارامترهای لینک‌سازی نامعتبر هستند.' ) );
        }

        $result = WPSmartAI_Pilar_Builder::create_internal_link( $source_id, $target_id, $anchor );
        if ( $result ) {
            wp_send_json_success( array( 'message' => 'لینک‌سازی داخلی پیلار و کلاستر با موفقیت انجام شد!' ) );
        } else {
            wp_send_json_error( array( 'message' => 'خطا در ویرایش مقاله و ثبت لینک.' ) );
        }
    }
}
