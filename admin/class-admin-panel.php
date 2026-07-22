<?php
/**
 * مدیریت پنل ادمین و درخواست‌های AJAX با مدیریت استثنائات (Throwable) و امنیت بالا
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
        add_action( 'wp_ajax_smart_ai_generate_images_for_post', array( $this, 'ajax_generate_images_for_post' ) );
        add_action( 'wp_ajax_smart_ai_get_post_images_list', array( $this, 'ajax_get_post_images_list' ) );
        add_action( 'wp_ajax_smart_ai_replace_specific_image', array( $this, 'ajax_replace_specific_image' ) );
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
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array( 'message' => 'دسترسی غیرمجاز.' ) );
            }

            $api_provider = isset( $_POST['api_provider'] ) ? sanitize_text_field( wp_unslash( $_POST['api_provider'] ) ) : '';
            $api_key      = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '';
            $unsplash_key = isset( $_POST['unsplash_key'] ) ? sanitize_text_field( wp_unslash( $_POST['unsplash_key'] ) ) : '';
            $tone         = isset( $_POST['tone'] ) ? sanitize_text_field( wp_unslash( $_POST['tone'] ) ) : 'friendly';

            $settings = array(
                'api_provider' => $api_provider,
                'api_key'      => $api_key,
                'unsplash_key' => $unsplash_key,
                'tone'         => $tone,
                'lang'         => 'fa'
            );

            update_option( 'wp_smart_ai_seo_settings', $settings );
            wp_send_json_success( array( 'message' => 'تنظیمات با موفقیت ذخیره شد.' ) );

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی رخ داد: ' . $e->getMessage() ) );
        }
    }

    public function ajax_optimize_post() {
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );

            $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
            $keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';

            if ( ! $post_id || empty( $keyword ) ) {
                wp_send_json_error( array( 'message' => 'شناسه مقاله یا کلمه کلیدی نامعتبر است.' ) );
            }

            $post = get_post( $post_id );
            if ( ! $post ) {
                wp_send_json_error( array( 'message' => 'مقاله پیدا نشد.' ) );
            }

            $post_status = get_post_status( $post_id );
            if ( ! $post_status ) {
                $post_status = 'publish';
            }

            $settings = get_option( 'wp_smart_ai_seo_settings', array() );
            $tone = isset( $settings['tone'] ) ? $settings['tone'] : 'friendly';

            // ذخیره موقت کلمه کلیدی در فیلدهای سئو
            WPSmartAI_SEO_Integrator::update_seo_metadata( $post_id, $keyword, $post->post_title, 'بهینه شده با هوش مصنوعی' );

            // ۱. تولید محتوای بهینه‌شده به همراه کدهای تصویری <!-- PLACE_IMAGE: ... -->
            $optimized_text = WPSmartAI_Engine::generate_optimized_content( $post->post_content, $keyword, $tone );
            if ( is_wp_error( $optimized_text ) ) {
                wp_send_json_error( array( 'message' => $optimized_text->get_error_message() ) );
            }

            // ۲. دانلود و درج تصاویر هوشمند با تگ Alt خودکار به جای تگ‌های موقت تصویر و تخصیص تصویر شاخص
            $final_content = WPSmartAI_Image_Handler::insert_images_into_content( $optimized_text, $post_id );
            if ( is_wp_error( $final_content ) ) {
                wp_send_json_error( array( 'message' => $final_content->get_error_message() ) );
            }

            // ۳. ساخت متادیتای سئو و تغییر آن‌ها در افزونه‌های Rank Math / Yoast
            $meta_data = WPSmartAI_SEO_Integrator::generate_meta_suggestions( $final_content, $keyword );
            if ( is_wp_error( $meta_data ) ) {
                $meta_data = array(
                    'title' => $keyword . ' | راهنمای کامل',
                    'description' => 'بهترین راهنما و بررسی سئو شده برای ' . $keyword
                );
            }
            WPSmartAI_SEO_Integrator::update_seo_metadata( $post_id, $keyword, $meta_data['title'], $meta_data['description'] );

            // ۴. تبدیل و ساختاربندی به قالب پیشرفته المنتور (Elementor Layout JSON) بر اساس قالب ارسالی شما
            WPSmartAI_SEO_Integrator::convert_post_to_elementor( $post_id, $final_content );

            // ۵. بروزرسانی نهایی محتوای استاندارد مقاله در وردپرس با حفظ وضعیت انتشار
            wp_update_post( array(
                'ID'           => $post_id,
                'post_content' => $final_content,
                'post_status'  => $post_status
            ) );

            wp_send_json_success( array(
                'message' => 'مقاله با موفقیت بهینه‌سازی و تبدیل به قالب المنتور شد، تمام تیک‌های سئو سبز شدند!',
                'meta'    => $meta_data
            ) );

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی رخ داد: ' . $e->getMessage() ) );
        }
    }

    /**
     * تصویرساز جادویی: دانلود ۳ تصویر مرتبط، جایگذاری منظم در متن و تخصیص تصویر شاخص
     */
    public function ajax_generate_images_for_post() {
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );

            $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
            $keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';

            if ( ! $post_id || empty( $keyword ) ) {
                wp_send_json_error( array( 'message' => 'اطلاعات ارسالی نامعتبر است. لطفاً کلمه کلیدی را بنویسید.' ) );
            }

            $post = get_post( $post_id );
            if ( ! $post ) {
                wp_send_json_error( array( 'message' => 'مقاله یافت نشد.' ) );
            }

            $post_status = get_post_status( $post_id );
            if ( ! $post_status ) {
                $post_status = 'publish';
            }

            WPSmartAI_SEO_Integrator::update_seo_metadata( $post_id, $keyword, $post->post_title, 'بهینه شده به همراه عکس شاخص' );

            $final_content = WPSmartAI_Image_Handler::insert_images_into_content( $post->post_content, $post_id );

            if ( is_wp_error( $final_content ) ) {
                wp_send_json_error( array( 'message' => $final_content->get_error_message() ) );
            }

            WPSmartAI_SEO_Integrator::convert_post_to_elementor( $post_id, $final_content );

            wp_update_post( array(
                'ID'           => $post_id,
                'post_content' => $final_content,
                'post_status'  => $post_status
            ) );

            wp_send_json_success( array(
                'message' => '۳ تصویر فوق‌العاده سئوشده بدون نوشته دانلود و در متن چیده شدند و تصویر شاخص نیز با موفقیت ست شد!'
            ) );

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی در اجرای تصویرسازی رخ داد: ' . $e->getMessage() ) );
        }
    }

    /**
     * دریافت لیست گالری تصاویر برای مدیریت اختصاصی هر عکس به تفکیک
     */
    public function ajax_get_post_images_list() {
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );
            $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

            if ( ! $post_id ) {
                wp_send_json_error( array( 'message' => 'شناسه مقاله نامعتبر است.' ) );
            }

            $images = WPSmartAI_Image_Handler::get_post_images_list( $post_id );
            wp_send_json_success( array( 'images' => $images ) );

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی در لود لیست تصاویر رخ داد: ' . $e->getMessage() ) );
        }
    }

    /**
     * جایگزینی فوری عکس و حذف کامل عکس قدیمی از رسانه وردپرس
     */
    public function ajax_replace_specific_image() {
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );

            $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
            $old_id  = isset( $_POST['old_id'] ) ? intval( $_POST['old_id'] ) : 0;
            $query   = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
            $alt     = isset( $_POST['alt'] ) ? sanitize_text_field( wp_unslash( $_POST['alt'] ) ) : '';

            if ( ! $post_id || empty( $query ) || empty( $alt ) ) {
                wp_send_json_error( array( 'message' => 'اطلاعات ارسالی برای جایگزینی تصویر ناقص است.' ) );
            }

            $post = get_post( $post_id );
            if ( ! $post ) {
                wp_send_json_error( array( 'message' => 'مقاله یافت نشد.' ) );
            }

            $post_status = get_post_status( $post_id );
            if ( ! $post_status ) {
                $post_status = 'publish';
            }

            $result = WPSmartAI_Image_Handler::replace_specific_image( $post_id, $old_id, $query, $alt );

            if ( is_wp_error( $result ) ) {
                wp_send_json_error( array( 'message' => $result->get_error_message() ) );
            }

            // بروزرسانی قالب المنتور پس از تعویض عکس
            WPSmartAI_SEO_Integrator::convert_post_to_elementor( $post_id, $result['content'] );

            // بروزرسانی پست در دیتابیس
            wp_update_post( array(
                'ID'           => $post_id,
                'post_content' => $result['content'],
                'post_status'  => $post_status
            ) );

            wp_send_json_success( array(
                'message'  => 'تصویر جدید جایگزین گردید و تصویر قبلی به طور کامل از رسانه وردپرس حذف شد!',
                'new_url'  => $result['new_url'],
                'new_id'   => $result['new_id']
            ) );

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی رخ داد: ' . $e->getMessage() ) );
        }
    }

    public function ajax_analyze_competitors() {
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );
            $keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';

            if ( empty( $keyword ) ) {
                wp_send_json_error( array( 'message' => 'کلمه کلیدی را وارد کنید.' ) );
            }

            $competitors = WPSmartAI_Competitor::analyze_competitors( $keyword );
            wp_send_json_success( array( 'competitors' => $competitors ) );

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی رخ داد: ' . $e->getMessage() ) );
        }
    }

    public function ajax_generate_new_post() {
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );

            $keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
            $competitor_data = isset( $_POST['competitor_data'] ) ? wp_kses_post( wp_unslash( $_POST['competitor_data'] ) ) : '';

            if ( empty( $keyword ) || empty( $competitor_data ) ) {
                wp_send_json_error( array( 'message' => 'کلمه کلیدی یا داده‌های رقیب موجود نیست.' ) );
            }

            $settings = get_option( 'wp_smart_ai_seo_settings', array() );
            $tone = isset( $settings['tone'] ) ? $settings['tone'] : 'friendly';
            $tone_farsi = 'دوستانه و صمیمی';
            if ( $tone === 'formal' ) {
                $tone_farsi = 'رسمی و علمی';
            }

            // ساخت پرومپت رقابتی بر اساس مقالات رقیب با رعایت تمام معیارهای Yoast / Rank Math
            $prompt = "تو یک استراتژیست محتوا و نویسنده افسانه‌ای سئو به زبان فارسی هستی.
وظیفه تو نوشتن یک مقاله فوق‌العاده با کلمه کلیدی اصلی '{$keyword}' است.
ما ۳ رقیب برتر در این کلمه کلیدی را تحلیل کردیم و خلاصه‌ای از مطالب آنها در زیر آمده است:
{$competitor_data}

ماموریت تو برای سبز کردن صد در صدی تیک‌های سئو:
1. مقاله‌ای بنویس که کامل‌تر، جامع‌تر و جذاب‌تر از هر سه رقیب باشد. جاهای خالی محتوای آن‌ها را پر کن.
2. طول جملات بسیار کوتاه باشد (تا حد امکان کمتر از ۱۵ کلمه برای رعایت خطای طول جمله خوانایی).
3. هدینگ‌های مناسب و پی در پی H2 و H3 (هر ۱۵۰ تا ۲۰۰ کلمه یک هدینگ) اضافه کن که چندین هدینگ شامل کلمه کلیدی '{$keyword}' باشند.
4. حداقل ۲ لینک داخلی طبیعی به شکل <a href=\"/services/\">صفحه خدمات</a> و <a href=\"/contact/\">صفحه تماس</a> و حداقل ۱ لینک خارجی به یک مرجع معتبر مثل ویکی‌پدیا اضافه کن.
5. حتماً حداقل ۲ بار تگ تصویر به فرمت <!-- PLACE_IMAGE: elevator modern design | تصویر مدرن آسانسور در حال نصب با کلمه کلیدی '{$keyword}' --> اضافه کن.
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

            // دانلود تصاویر، درج در متن و تخصیص تصویر شاخص اصلی پست
            $final_content = WPSmartAI_Image_Handler::insert_images_into_content( $text_response, $new_post_id );
            if ( is_wp_error( $final_content ) ) {
                wp_send_json_error( array( 'message' => $final_content->get_error_message() ) );
            }

            // تولید متادیتا و ست کردن تیک‌های سئو
            $meta_data = WPSmartAI_SEO_Integrator::generate_meta_suggestions( $final_content, $keyword );
            if ( is_wp_error( $meta_data ) ) {
                $meta_data = array(
                    'title' => $keyword . ' | راهنمای کامل',
                    'description' => 'بهترین راهنما و بررسی سئو شده برای ' . $keyword
                );
            }
            WPSmartAI_SEO_Integrator::update_seo_metadata( $new_post_id, $keyword, $meta_data['title'], $meta_data['description'] );

            // تبدیل پست به قالب المنتور (Elementor JSON layout)
            WPSmartAI_SEO_Integrator::convert_post_to_elementor( $new_post_id, $final_content );

            // آپدیت متن نهایی مقاله در وردپرس برای همگام‌سازی بکاپ
            wp_update_post( array(
                'ID'           => $new_post_id,
                'post_content' => $final_content
            ) );

            wp_send_json_success( array(
                'message' => 'مقاله فوق رقابتی با موفقیت نوشته، دانلود عکس‌ها و تصویر شاخص با آلت بهینه‌سازی شد، تبدیل به قالب المنتور شد و به عنوان پیش‌نویس ذخیره شد!',
                'post_id' => $new_post_id,
                'edit_url'=> get_edit_post_link( $new_post_id, 'raw' )
            ) );

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی رخ داد: ' . $e->getMessage() ) );
        }
    }

    public function ajax_suggest_clusters() {
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );
            $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

            if ( ! $post_id ) {
                wp_send_json_error( array( 'message' => 'شناسه پست نامعتبر است.' ) );
            }

            $clusters = WPSmartAI_Pilar_Builder::suggest_clusters( $post_id );
            if ( is_wp_error( $clusters ) ) {
                wp_send_json_error( array( 'message' => $clusters->get_error_message() ) );
            }

            wp_send_json_success( array( 'clusters' => $clusters ) );

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی رخ داد: ' . $e->getMessage() ) );
        }
    }

    public function ajax_create_cluster_link() {
        try {
            check_ajax_referer( 'smart_ai_nonce', 'security' );

            $source_id = isset( $_POST['source_id'] ) ? intval( $_POST['source_id'] ) : 0;
            $target_id = isset( $_POST['target_id'] ) ? intval( $_POST['target_id'] ) : 0;
            $anchor    = isset( $_POST['anchor'] ) ? sanitize_text_field( wp_unslash( $_POST['anchor'] ) ) : '';

            if ( ! $source_id || ! $target_id || empty( $anchor ) ) {
                wp_send_json_error( array( 'message' => 'پارامترهای لینک‌سازی نامعتبر هستند.' ) );
            }

            $result = WPSmartAI_Pilar_Builder::create_internal_link( $source_id, $target_id, $anchor );
            if ( $result ) {
                wp_send_json_success( array( 'message' => 'لینک‌سازی داخلی پیلار و کلاستر با موفقیت انجام شد!' ) );
            } else {
                wp_send_json_error( array( 'message' => 'خطا در ویرایش مقاله و ثبت لینک.' ) );
            }

        } catch ( Throwable $e ) {
            wp_send_json_error( array( 'message' => 'خطایی رخ داد: ' . $e->getMessage() ) );
        }
    }
}
