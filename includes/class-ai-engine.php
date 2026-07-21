<?php
/**
 * کلاس مدیریت اتصال به مدل‌های هوش مصنوعی (مخصوصا Gemini گوگل)
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

        // آدرس API مدل Gemini 1.5 Flash (بهینه و رایگان)
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . esc_attr( $api_key );

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
     * ساخت پرومپت اختصاصی برای سئو و بهینه‌سازی مقاله قدیمی بر اساس تنظیمات فارسی
     */
    public static function generate_optimized_content( $content, $keyword, $tone = 'friendly' ) {
        $tone_farsi = 'دوستانه و صمیمی';
        if ( $tone === 'formal' ) {
            $tone_farsi = 'رسمی و اداری';
        } elseif ( $tone === 'expert' ) {
            $tone_farsi = 'تخصصی و علمی';
        }

        $prompt = "تو یک کارشناس فوق‌العاده سئو و تولید محتوای فارسی هستی. وظیفه تو بازنویسی و بهینه‌سازی مقاله زیر است.
موضوع و کلمه کلیدی اصلی مقاله: '{$keyword}' است.

قوانین بازنویسی که باید دقیقاً رعایت کنی:
1. لحن مقاله باید '{$tone_farsi}' باشد.
2. کلمه کلیدی '{$keyword}' را به شکلی طبیعی در عنوان اصلی، پاراگراف اول، حداقل ۳ بار در طول متن، و پاراگراف آخر قرار بده. همچنین از مشتقات و کلمات هم‌خانواده آن استفاده کن.
3. هدینگ‌های مناسب (H2 و H3) با ساختار عالی ایجاد کن که جذاب و شامل کلمه کلیدی یا هم‌خانواده‌های آن باشند.
4. خوانایی متن را با شکستن جملات طولانی، استفاده از لیست‌های نشانه‌دار (Bullet Points) و جداول در صورت لزوم بهبود ببخش.
5. بخش‌هایی که به نظر ناقص می‌آیند را گسترش بده تا مقاله کامل، جامع و همه‌جانبه شود.
6. خروجی فقط و فقط باید به زبان فارسی و با رعایت قواعد نگارشی و علائم سجاوندی فارسی باشد.
7. همچنین جاهایی که احساس می‌کنی نیاز به قرارگیری تصویر وجود دارد، یک تگ کامنت به صورت <!-- PLACE_IMAGE: توصیف تصویر به زبان فارسی --> قرار بده تا افزونه متوجه شود آنجا باید تصویر درج کند.

متن اصلی مقاله که باید بهینه‌سازی شود:
---
{$content}
---

متن بازنویسی شده نهایی سئو شده را مستقیماً برگردان (هیچ توضیح اضافه دیگری به جز خود متن مقاله ننویس):";

        return self::call_gemini( $prompt );
    }
}
