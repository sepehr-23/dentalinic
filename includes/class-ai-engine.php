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

        // آدرس‌های ثابت و واقعی سایت جهت لینک‌دهی دقیق
        $call_link = 'https://keshavarzlift.ir/#call';
        $shop_link = 'https://keshavarzlift.ir/shop/';
        $articles_link = 'https://keshavarzlift.ir/%d9%85%d9%82%d8%a7%d9%84%d8%aa-%d9%88-%d8%af%d8%a7%d9%86%d8%b3%d8%aa%d9%86%db%8c-%d9%87%d8%a7/';
        $order_link = 'https://keshavarzlift.ir/%D9%81%D8%B1%D9%85%20%D8%B3%D9%81%D8%A7%D8%B1%D8%B4/';

        $prompt = "تو یک کارشناس فوق‌العاده ارشد سئو و طراح فرانت‌اند حرفه‌ای هستی. وظیفه تو نوشتن یک مقاله فوق‌العاده خیره‌کننده، سئوشده و جذاب درباره موضوع و کلمه کلیدی اصلی '{$keyword}' است.

قوانین سخت‌گیرانه سئو و نگارش که باید دقیقاً رعایت شوند:
۱. **طول جملات:** حداکثر ۷۵ درصد جملات باید کوتاه باشند (کمتر از ۱۵ واژه). جملات طولانی را بشکن و با نقطه جدا کن تا ارورهای خوانایی Yoast و Rank Math حل شوند.
۲. **هدینگ‌ها:** هر ۱۵۰ الی ۲۰۰ کلمه باید یک تگ h2 یا h3 قرار گیرد. حداقل ۳ مورد از هدینگ‌ها باید شامل کلمه کلیدی '{$keyword}' یا مترادف‌های مستقیم آن باشد.
۳. **توزیع و کلمه کلیدی:** کلمه کلیدی تمرکزی '{$keyword}' را به شکل ضخیم <strong>'{$keyword}'</strong> در اولین پاراگراف، بدنه متن و پاراگراف آخر (نتیجه‌گیری) بگنجان.
۴. **لینک‌های داخلی دقیق و فعال:** از لینک‌های واقعی زیر استفاده کن و کلماتی مثل 'خرید'، 'فروشگاه'، 'فرم سفارش'، 'تماس' را دقیقاً به این آدرس‌ها لینک ده:
   - لینک تماس و مشاوره: {$call_link} (مثلاً برای بخش‌های مشاوره تلفنی)
   - لینک فروشگاه قطعات و آسانسور: {$shop_link}
   - لینک مقالات و دانستنی‌های آسانسور: {$articles_link}
   - لینک فرم سفارش طراحی و ساخت آسانسور: {$order_link}
   (توجه: لینک‌دهی باید بسیار طبیعی باشد و نباید زیاده‌روی شود، بلکه در جاهای کاملاً مرتبط استفاده شود.)
۵. **لینک‌های خارجی:** حداقل ۱ لینک خارجی طبیعی به منابع مرجع معتبر جهانی مرتبط با صنعت آسانسور (مثل wikipedia) بده.
۶. **الزام درج ۳ تصویر:** در طول مقاله، دقیقاً ۳ تگ کامنت تصویری برای جایگذاری هوشمند عکس در متن قرار بده (یکی در بالا برای هدر/شاخص، یکی در وسط، یکی در پایین). تگ‌ها باید دقیقاً به این ساختار باشند:
   <!-- PLACE_IMAGE: توصیف تصویر به انگلیسی بدون متن داخل عکس | متن آلت تصویر شامل کلمه کلیدی '{$keyword}' به فارسی -->
   (مثال: <!-- PLACE_IMAGE: modern elevator glass cabin inside luxury building | انواع آسانسور شیشه ای لوکس مناسب خرید ساختمان -->)
   *توجه:* در توصیف انگلیسی تاکید کن عکس واقعی و بدون هیچ نوشته یا متنی روی تصویر باشد (no text on image, realistic).

۷. **الزام درج بخش سوالات متداول (FAQ) و جعبه تماس (CTA) تمام‌عرض:**
   - در پایان مقاله، حتماً یک بخش بسیار شیک سوالات متداول (FAQ) با حداقل ۳ سوال و پاسخ کلیدی و جذاب به همراه تگ‌های h3 و پاسخ‌های خوانا قرار بده.
   - حتماً یک جعبه تماس (CTA) تمام‌عرض مدرن و تیره با گرادینت مطابق با تمپلیت ارسالی بساز که شامل دکمه تماس تلفنی به شماره '09221267593' و لینک به فرم سفارش باشد.

ساختار طراحی ظاهری HTML که باید دقیقاً کپی و شبیه‌سازی شود:
کل مقاله را داخل یک تگ div قرار بده و تمام بخش‌ها را با استایل‌های inline دقیقاً مانند الگوی زیر طراحی کن:
- کل ظرفیت کدهای HTML باید تمام‌عرض و ریسپانسیو باشد.
- تگ‌های h2 با استایل: style=\"color: #00c8ff; font-size: clamp(20px, 2.5vw, 26px); font-weight: 700; margin-top: 40px; border-right: 4px solid #00c8ff; padding-right: 14px;\"
- تگ‌های p با استایل: style=\"color: #d7e4f5; font-size: clamp(15px, 1.5vw, 17px); line-height: 2; margin-bottom: 20px;\"
- لیست‌ها (ul) با استایل: style=\"color: #d7e4f5; font-size: clamp(15px, 1.5vw, 17px); line-height: 2; list-style: none; padding: 0;\" به همراه ایموجی‌های تیک سبز (✅) در ابتدای liها.
- جدول استاندارد و کاملاً ریسپانسیو با استایل‌های دقیق rgba و تگ‌های th/td با سایه‌های ظریف و طراحی تیره مدرن.
- نقل قول یا بخش برجسته (blockquote/div) با استایل: style=\"border-right: 4px solid #00c8ff; padding: 16px 20px; margin: 30px 0; background: rgba(0,200,255,0.05); border-radius: 12px; color: #b0c4e0; font-style: italic;\"
- در انتهای مقاله یک باکس تماس (CTA) بزرگ و فوق‌العاده تمام‌عرض با گرادینت تیره و دکمه شیشه‌ای متحرک به شماره تماس '09221267593' قرار بده.

متن قدیمی یا موضوع کلی جهت نگارش مقاله:
---
{$content}
---

خروجی باید فقط و فقط بدنه اصلی مقاله به صورت کدهای HTML تمیز و طراحی شده به زبان فارسی باشد. هیچ پیام، مقدمه، توضیح انگلیسی، تگ markdown یا کد ```html اضافی صادر نکن:";

        return self::call_gemini( $prompt );
    }
}
