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
</div>
