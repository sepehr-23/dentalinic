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
     * ساخت پرومپت اختصاصی برای سئو فوق حرفه‌ای با رفع تمامی خطاهای Yoast / Rank Math و خروجی فوق‌العاده زیبای CSS مطابق تمپلیت ارسالی کاربر
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

        $prompt = "تو یک کارشناس فوق‌العاده ارشد سئو و طراح فرانت‌اند حرفه‌ای هستی. وظیفه تو نوشتن یک مقاله فوق‌العاده خیره‌کننده، سئوشده و جذاب درباره موضوع و کلمه کلیدی اصلی '{$keyword}' است.

قوانین سخت‌گیرانه سئو و نگارش که باید دقیقاً رعایت شوند:
۱. **طول جملات:** حداکثر ۷۵ درصد جملات باید کوتاه باشند (کمتر از ۱۵ واژه). جملات طولانی را بشکن و با نقطه جدا کن.
۲. **هدینگ‌ها:** هر ۱۵۰ الی ۲۰۰ کلمه باید یک تگ h2 یا h3 قرار گیرد. حداقل ۳ مورد از هدینگ‌ها باید شامل کلمه کلیدی '{$keyword}' یا مترادف‌های آن باشد.
۳. **توزیع و کلمه کلیدی:** کلمه کلیدی تمرکزی '{$keyword}' را به شکل ضخیم <strong>'{$keyword}'</strong> در اولین پاراگراف، بدنه متن و پاراگراف آخر (نتیجه‌گیری) بگنجان.
۴. **لینک‌های داخلی:** حداقل ۲ لینک داخلی طبیعی به صفحات داخلی با دامنه اصلی ما '{$site_url}' بساز (مثلاً '{$site_url}services/' یا '{$site_url}products/' یا '{$site_url}brands/').
۵. **لینک‌های خارجی:** حداقل ۱ یا ۲ لینک خارجی طبیعی به منابع مرجع معتبر (مثل wikipedia) بده.
۶. **تصاویر با آلت اختصاصی:** در جاهای بسیار مناسب مقاله، حداقل ۲ بار تگ کامنت تصویری را دقیقاً به شکل زیر قرار بده تا افزونه آن را با تصاویر واقعی Unsplash و تگ Alt سئو شده جایگزین کند:
   <!-- PLACE_IMAGE: توصیف دقیق تصویر برای جستجو به زبان انگلیسی | متن آلت تصویر شامل کلمه کلیدی {$keyword} به فارسی -->

ساختار طراحی ظاهری HTML که باید دقیقاً کپی و شبیه‌سازی شود:
کل مقاله را داخل یک تگ div قرار بده و تمام بخش‌ها را با استایل‌های inline دقیقاً مانند الگوی زیر طراحی کن:
- تگ‌های h2 با استایل: style=\"color: #00c8ff; font-size: clamp(20px, 2.5vw, 26px); font-weight: 700; margin-top: 40px; border-right: 4px solid #00c8ff; padding-right: 14px;\"
- تگ‌های p با استایل: style=\"color: #d7e4f5; font-size: clamp(15px, 1.5vw, 17px); line-height: 2; margin-bottom: 20px;\"
- لیست‌ها (ul) با استایل: style=\"color: #d7e4f5; font-size: clamp(15px, 1.5vw, 17px); line-height: 2; list-style: none; padding: 0;\" به همراه ایموجی‌های تیک سبز (✅) یا آیکون‌های متناسب در ابتدای liها.
- جدول استاندارد و کاملاً ریسپانسیو با استایل‌های دقیق rgba و تگ‌های th/td با سایه‌های ظریف و طراحی تیره مدرن.
- نقل قول یا بخش برجسته (blockquote/div) با استایل: style=\"border-right: 4px solid #00c8ff; padding: 16px 20px; margin: 30px 0; background: rgba(0,200,255,0.05); border-radius: 12px; color: #b0c4e0; font-style: italic;\"
- در انتهای مقاله یک باکس تماس (CTA) بزرگ و فوق‌العاده با گرادینت تیره و دکمه شیشه‌ای متحرک به شماره تماس '09221267593' و لینک ثبت پیام به آدرس '{$site_url}contact/' قرار بده. استایل این باکس باید دقیقاً مشابه نمونه ارسالی کاربر باشد.

متن قدیمی یا موضوع کلی جهت نگارش مقاله:
---
{$content}
---

خروجی باید فقط و فقط بدنه اصلی مقاله به صورت کدهای HTML تمیز و طراحی شده به زبان فارسی باشد. هیچ پیام، مقدمه، توضیح انگلیسی، تگ markdown یا کد ```html اضافی صادر نکن:";

        return self::call_gemini( $prompt );
    }
}
