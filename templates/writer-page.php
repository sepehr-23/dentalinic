<?php
/**
 * قالب صفحه نویسنده جادویی و تحلیل رقبا
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
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
            <label for="writer_keyword">کلمه کلیدی هدف برای سرچ گوگل و نگارش:</label>
            <input type="text" id="writer_keyword" style="max-width: 500px;" placeholder="مثال: خواص قهوه سرد دم برای لاغری" />
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

        <h3>🤖 مرحله دوم: نگارش مقاله جامع و برتر با هوش مصنوعی</h3>
        <button id="generate-best-article-btn" class="smart-ai-btn" style="background: #1b5e20 !important;">🧠 تولید مقاله شگفت‌انگیز فوق‌رقابتی و دانلود عکس‌ها</button>
    </div>
</div>
