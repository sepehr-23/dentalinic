<?php
/**
 * قالب صفحه نویسنده جادویی و تحلیل رقبا
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$suggested_links_placeholder = "آدرس‌های پیشنهادی و معتبر سایت شما جهت کپی:\n" .
"1. تماس و مشاوره تلفنی: https://keshavarzlift.ir/#call\n" .
"2. فروشگاه قطعات آسانسور: https://keshavarzlift.ir/shop/\n" .
"3. وبلاگ و دانستنی‌های آسانسور: https://keshavarzlift.ir/%d9%85%d9%82%d8%a7%d9%84%d8%a7%d8%aa-%d9%88-%d8%af%d8%a7%d9%86%d8%b3%d8%aa%d9%86%db%8c-%d9%87%d8%a7/\n" .
"4. فرم سفارش طراحی و ساخت: https://keshavarzlift.ir/%D9%81%D8%B1%D9%85%20%D8%B3%D9%81%D8%A7%D8%B1%D8%B4/";
?>

<div class="wrap wp-smart-ai-wrap">
    <div class="wp-smart-ai-header" style="background: linear-gradient(135deg, #1b5e20 0%, #0d3c12 100%);">
        <div>
            <h1>✍️ نویسنده جادویی فوق‌رقابتی (تحلیل ۳ رقیب اول گوگل)</h1>
            <p>کلمه کلیدی را بدهید تا افزونه ۳ رقیب اول گوگل را آنالیز کرده و مقاله‌ای به مراتب بهتر، غنی‌تر و با ساختار سئوی ۱۰۰٪ سبز برای شما بنویسد.</p>
        </div>
        <div style="font-size: 40px;">🕵️‍♂️✍️</div>
    </div>

    <div class="wp-smart-ai-card">
        <h3>🔍 مرحله اول: مشخص کردن کلمه کلیدی و بررسی رقیبان اول</h3>

        <div class="wp-smart-ai-field-group">
            <label for="writer_keyword">کلمه کلیدی هدف برای سرچ گوگل و نگارش target keyword:</label>
            <input type="text" id="writer_keyword" style="max-width: 500px;" placeholder="مثال: خرید آسانسور خانگی لوکس" />
        </div>

        <button id="analyze-competitors-btn" class="smart-ai-btn">🕵️‍♂️ جستجو در گوگل و بررسی رقبای اول</button>

        <div id="writer-loader" class="smart-ai-loader">
            <div class="smart-ai-spinner"></div>
            <span>درحال پردازش...</span>
        </div>

        <div id="writer-result" class="smart-ai-result-box"></div>
    </div>

    <!-- نتایج تحلیل رقبا که پس از دریافت ظاهر می‌شود -->
    <div id="competitor-results-box" class="wp-smart-ai-card" style="display: none;">
        <h3>📊 نتایج خلاصه و تحلیل ۳ رقیب اول یافت شده در گوگل</h3>
        <p class="description">محتوای زیر خلاصه‌ای از سرفصل‌ها و نگارش سایت‌های برتر گوگل است. با زدن دکمه زیر، هوش مصنوعی با شناسایی کمبودهای این متون، مقاله‌ای بی‌رقیب بازنویسی خواهد کرد.</p>

        <div id="competitor-list" style="margin-bottom: 25px;"></div>

        <h3 style="color: #1b5e20; border-bottom: 2px solid #e1efe2; padding-bottom: 8px;">⚙️ تنظیمات نگارش اختصاصی و لینک‌دهی</h3>

        <div class="wp-smart-ai-field-group">
            <label for="writer_custom_prompt">📝 دستورالعمل و پرامپت دستی شما (لحن، موضوعات خاص یا نکات تمرکزی):</label>
            <textarea id="writer_custom_prompt" rows="3" style="max-width: 600px; width:100%;" placeholder="مثلاً: روی مزایای موتورهای گیرلس و سرعت استاندارد آسانسورهای خانگی تمرکز بیشتری کن و لحن متن را دوستانه بنویس."></textarea>
        </div>

        <div class="wp-smart-ai-field-group">
            <label for="writer_custom_links">🔗 لینک‌های دلخواه و کلمات کلیدی هدف آن‌ها جهت لینک‌دهی طبیعی در متن:</label>
            <p class="description" style="margin-bottom: 8px; color: #444; font-size:12px; line-height:1.6; background: #eef3fb; padding: 10px; border-radius: 6px; white-space: pre-wrap;"><?php echo esc_html( $suggested_links_placeholder ); ?></p>
            <textarea id="writer_custom_links" rows="4" style="max-width: 600px; width:100%;" placeholder="مثال:
https://keshavarzlift.ir/shop/ با کلمه کلیدی 'فروشگاه قطعات آسانسور'
https://keshavarzlift.ir/#call با کلمه کلیدی 'مشاوره تلفنی خرید آسانسور'"></textarea>
        </div>

        <h3 style="margin-top: 30px;">🤖 مرحله دوم: نگارش مقاله جامع و برتر با هوش مصنوعی</h3>
        <button id="generate-best-article-btn" class="smart-ai-btn" style="background: #1b5e20 !important;">🧠 تولید مقاله شگفت‌انگیز فوق‌رقابتی و دانلود عکس‌ها</button>
    </div>
</div>
