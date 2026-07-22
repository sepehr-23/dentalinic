<?php
/**
 * کلاس مدیریت اتصال به مدل‌های هوش مصنوعی (مدل gemini-flash-latest)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPSmartAI_Engine {

    /**
     * ارسال پرومپت به مدل جمینی گوگل و دریافت پاسخ متنی
     */
    public static function call_gemini( $prompt, $api_key = '' ) {
        if ( empty( $api_key ) ) {
            $settings = get_option( 'wp_smart_ai_seo_settings', array() );
            $api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
        }

        if ( empty( $api_key ) ) {
            return new WP_Error( 'missing_api_key', 'لطفا کلید API هوش مصنوعی جمینی را در بخش تنظیمات وارد کنید.' );
        }

        // استفاده از مدل بروز شده gemini-flash-latest به درخواست کاربر و rawurlencode برای امنیت کلید
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . rawurlencode( $api_key );

        $body = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array(
                            'text' => $prompt
                        )
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.7,
                'maxOutputTokens' => 8192
            )
        );

        $response = wp_remote_post( $url, array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body'    => wp_json_encode( $body ),
            'timeout' => 120
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        if ( $code !== 200 ) {
            return new WP_Error( 'api_error', 'خطا در ارتباط با سرور گوگل: ' . $response_body );
        }

        $data = json_decode( $response_body, true );
        if ( isset( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        return new WP_Error( 'invalid_response', 'پاسخ نامعتبر از API دریافت شد.' );
    }

    /**
     * ساخت پرومپت اختصاصی برای سئو فوق حرفه‌ای با رفع تمامی خطاهای Yoast / Rank Math
     */
    public static function generate_optimized_content( $content, $keyword, $tone = 'friendly' ) {
        $tone_farsi = 'دوستانه و صمیمی';
        if ( $tone === 'formal' ) {
            $tone_farsi = 'رسمی و اداری';
        } elseif ( $tone === 'expert' ) {
            $tone_farsi = 'تخصصی و علمی';
        }

        // اخذ دامنه سایت برای ساخت لینک‌های داخلی طبیعی
        $site_url = esc_url( home_url('/') );

        $prompt = "تو یک کارشناس و استراتژیست فوق‌العاده ارشد سئو و تولید محتوای فارسی هستی. وظیفه تو نگارش یا بازنویسی کامل یک مقاله بی‌رقیب است.
موضوع و کلمه کلیدی اصلی مقاله: '{$keyword}' است.

برای رفع تمامی خطاهای سخت‌گیرانه Yoast SEO و Rank Math، باید دقیقاً قوانین زیر را در متن اعمال کنی:

۱. **طول جملات (بسیار مهم):** حداکثر ۷۵ درصد جملات باید کوتاه باشند (کمتر از ۱۵ واژه). از جملات طولانی بپرهیز و جملات بزرگ را به جملات کوچک و مستقل با نقطه از هم جدا کن.
۲. **توزیع زیرعنوان‌ها:** هر ۱۵۰ الی ۲۵۰ کلمه باید یک هدینگ H2 یا H3 داشته باشد. متن نباید طولانی و بدون هدینگ رها شود.
۳. **کلمه کلیدی در زیرعنوان:** حداقل ۲ یا ۳ زیرعنوان (H2 یا H3) باید دقیقاً شامل عبارت کلیدی '{$keyword}' یا مترادف‌های مستقیم آن باشند.
۴. **تراکم و جایگاه کلمه کلیدی:** کلمه کلیدی تمرکزی '{$keyword}' را در اولین پاراگراف (۵۰ کلمه اول)، در بدنه متن (با چگالی ۱.۵ درصد) و در پاراگراف آخر (نتیجه‌گیری) قرار ده.
۵. **لینک‌های خارجی (Outbound Links):** حداقل ۲ لینک خارجی طبیعی و معتبر به سایت‌های مرجع (مانند ویکی‌پدیا یا سایت‌های تخصصی جهانی مرتبط) اضافه کن. تگ لینک‌ها باید به صورت <a href=\"https://wikipedia.org\" target=\"_blank\">انکرتکست جذاب</a> باشد.
۶. **لینک‌های داخلی (Internal Links):** حداقل ۲ لینک داخلی طبیعی به صفحات داخلی با انکر تکست‌هایی شامل کلمه کلیدی یا مترادف‌های آن اضافه کن. آدرس لینک‌ها را بر اساس دامنه اصلی ما '{$site_url}' بساز (مثلاً '{$site_url}services/' یا '{$site_url}contact/').
۷. **قرارگیری تصاویر هوشمند:** در طول مقاله در جاهای کاملاً مناسب، حداقل ۲ بار تگ کامنت درج تصویر به صورت زیر قرار بده:
   <!-- PLACE_IMAGE: توصیف دقیق تصویر برای جستجو به زبان انگلیسی | متن آلت تصویر شامل کلمه کلیدی {$keyword} به فارسی -->
۸. **لحن نگارش:** لحن متن باید کاملاً '{$tone_farsi}' باشد.

متن قدیمی یا موضوع مقاله:
---
{$content}
---

مقاله‌ای که می‌نویسی باید تمام تیک‌های قرمز سئو را سبز کند. خروجی فقط و فقط باید به صورت ساختار یافته HTML تمیز (فقط تگ‌های p, h2, h3, ul, li, strong, a) به زبان فارسی باشد و هیچ توضیح اضافه دیگر یا تگ markdown اضافی ننویس:";

        return self::call_gemini( $prompt );
    }
}
