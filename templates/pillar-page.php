<?php
/**
 * قالب صفحه پیلار و کلاستر (ساختار خوشه محتوایی)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// واکشی پست‌ها برای انتخاب به عنوان پیلار
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
);
$all_posts = get_posts( $args );
?>

<div class="wrap wp-smart-ai-wrap">
    <div class="wp-smart-ai-header" style="background: linear-gradient(135deg, #e65100 0%, #a33600 100%);">
        <div>
            <h1>🌲 استراتژی خوشه محتوایی (Pillar-Cluster Strategy)</h1>
            <p>ایجاد اعتبار موضوعی (Topical Authority) در گوگل با شناسایی مقالات مادر (Pillar) و پیشنهاد مقالات تخصصی کلاستر همرا با لینک‌سازی اتوماتیک.</p>
        </div>
        <div style="font-size: 40px;">🕸🌲</div>
    </div>

    <div class="wp-smart-ai-card">
        <h3>🌱 انتخاب مقاله مادر (Pillar Post)</h3>
        <p class="description">مقاله‌ای جامع از سایت خود که قصد دارید به عنوان موضوع مرجع شناخته شود را انتخاب کنید تا هوش مصنوعی خلاءهای محتوایی آن را با ساخت کلاستر پر کند.</p>

        <div class="wp-smart-ai-field-group">
            <label for="pillar_post_select">انتخاب مقاله مادر موجود در سایت:</label>
            <select id="pillar_post_select" style="max-width: 500px;">
                <option value="">-- یک مقاله را انتخاب کنید --</option>
                <?php foreach ( $all_posts as $p ) : ?>
                    <option value="<?php echo $p->ID; ?>"><?php echo esc_html( $p->post_title ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button id="suggest-clusters-btn" class="smart-ai-btn" style="background: #e65100 !important;">🧠 کشف خلاءهای محتوایی و پیشنهاد ساختار خوشه (کلاستر)</button>

        <div id="pillar-loader" class="smart-ai-loader">
            <div class="smart-ai-spinner"></div>
            <span>درحال بارگذاری ساختار محتوا...</span>
        </div>
    </div>

    <!-- نتایج گرافیکی پیلار-کلاستر پس از پردازش -->
    <div id="pillar-tree-result" class="wp-smart-ai-card" style="display: none; border: 1px dashed #e65100;">
        <!-- اطلاعات از طریق جی‌کوئری تزریق می‌شوند -->
    </div>

    <!-- کنسول گزارشات لایو و کپی کدهای خطا -->
    <div class="wp-smart-ai-card smart-ai-console-wrapper" id="pillar-console-wrapper" style="margin-top: 25px; border-right: 4px solid #e65100; background: #1c1d22; color: #a9b2c3; font-family: monospace; border-radius: 8px; padding: 15px; display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d3139; padding-bottom: 8px; margin-bottom: 10px;">
            <span style="color: #00c8ff; font-weight: bold; font-size: 13px;">🖥️ کنسول گزارشات و خطاها (Live Debug Console)</span>
            <button class="button button-secondary copy-console-log-btn" data-target="pillar-console" style="font-size: 11px; background: #2d3139; color: #fff; border: none; border-radius: 4px; padding: 4px 10px; cursor: pointer;">📋 کپی کردن لاگ</button>
        </div>
        <div class="smart-ai-console-log" id="pillar-console" style="white-space: pre-wrap; font-size: 12px; line-height: 1.6; max-height: 250px; overflow-y: auto; padding-right: 5px; direction: ltr; text-align: left;"></div>
    </div>

</div>
