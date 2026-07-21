<?php
/**
 * قالب صفحه تنظیمات سئو هوشمند
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = get_option( 'wp_smart_ai_seo_settings', array() );
$api_provider = isset( $settings['api_provider'] ) ? $settings['api_provider'] : 'gemini';
$api_key      = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
$unsplash_key = isset( $settings['unsplash_key'] ) ? $settings['unsplash_key'] : '';
$tone         = isset( $settings['tone'] ) ? $settings['tone'] : 'friendly';
?>

<div class="wrap wp-smart-ai-wrap">
    <div class="wp-smart-ai-header">
        <div>
            <h1>دستیار سئو و تولید محتوای هوش مصنوعی</h1>
            <p>تمام کارهای سئو، ویرایش جادویی مقالات قدیمی، تحلیل رقبا، تولید تصاویر با آلت هوشمند و مدل پیلار-کلاستر را با چند کلیک انجام دهید.</p>
        </div>
        <div style="font-size: 40px;">🧠🚀</div>
    </div>

    <div class="wp-smart-ai-card">
        <h3>🔑 تنظیمات اتصال به هوش مصنوعی و رسانه</h3>
        <form id="smart-ai-settings-form">

            <div class="wp-smart-ai-field-group">
                <label for="api_provider">ارائه‌دهنده هوش مصنوعی:</label>
                <select id="api_provider" name="api_provider">
                    <option value="gemini" <?php selected( $api_provider, 'gemini' ); ?>>Google Gemini (فوق‌العاده سریع و رایگان با پشتیبانی عالی فارسی)</option>
                    <option value="groq" <?php selected( $api_provider, 'groq' ); ?>>Groq Cloud (مدل‌های متن‌باز رایگان)</option>
                    <option value="local" <?php selected( $api_provider, 'local' ); ?>>مدل محلی (Ollama / Localhost)</option>
                </select>
            </div>

            <div class="wp-smart-ai-field-group">
                <label for="api_key">کلید API ارائه‌دهنده هوش مصنوعی:</label>
                <input type="password" id="api_key" name="api_key" value="<?php echo esc_attr( $api_key ); ?>" placeholder="کلید API مربوطه را وارد کنید..." />
                <p class="description">برای تولید کلید رایگان جمینی به <a href="https://aistudio.google.com/" target="_blank">Google AI Studio</a> مراجعه کنید.</p>
            </div>

            <div class="wp-smart-ai-field-group">
                <label for="unsplash_key">کلید API رسانه Unsplash (اختیاری):</label>
                <input type="password" id="unsplash_key" name="unsplash_key" value="<?php echo esc_attr( $unsplash_key ); ?>" placeholder="کلید Unsplash Developer API..." />
                <p class="description">جهت دانلود خودکار عکس‌های کاملاً اختصاصی و مرتبط. در صورت خالی بودن، از کتابخانه همگانی به صورت عمومی استفاده خواهد شد.</p>
            </div>

            <div class="wp-smart-ai-field-group">
                <label for="tone">لحن تولید محتوا (سئو کلاه سفید فارسی):</label>
                <select id="tone" name="tone">
                    <option value="friendly" <?php selected( $tone, 'friendly' ); ?>>دوستانه و صمیمی (مناسب بلاگ شخصی و فروشگاهی)</option>
                    <option value="formal" <?php selected( $tone, 'formal' ); ?>>رسمی و اداری (مناسب شرکت‌ها و برندهای معتبر)</option>
                    <option value="expert" <?php selected( $tone, 'expert' ); ?>>تخصصی و علمی (پوشش کامل فنی و آکادمیک)</option>
                </select>
            </div>

            <button type="submit" class="smart-ai-btn">💾 ذخیره تغییرات تنظیمات</button>
        </form>

        <div id="settings-loader" class="smart-ai-loader">
            <div class="smart-ai-spinner"></div>
            <span>در حال ذخیره‌سازی اطلاعات روی دیتابیس ایمن وردپرس...</span>
        </div>

        <div id="settings-result" class="smart-ai-result-box"></div>
    </div>
</div>
