<?php
/**
 * قالب صفحه ویرایش و سئوی جادویی مقالات قدیمی (فقط بهینه‌سازی محتوای متنی)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// خواندن مقالات اخیر سایت
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => 15,
    'post_status'    => array( 'publish', 'draft' )
);
$posts_query = new WP_Query( $args );

$suggested_links_placeholder = "آدرس‌های پیشنهادی و معتبر سایت شما جهت کپی:\n" .
"1. تماس و مشاوره تلفنی: https://keshavarzlift.ir/#call\n" .
"2. فروشگاه قطعات آسانسور: https://keshavarzlift.ir/shop/\n" .
"3. وبلاگ و دانستنی‌های آسانسور: https://keshavarzlift.ir/%d9%85%d9%82%d8%a7%d9%8ال%d8%a7%d8%aa-%d9%88-%d8%af%d8%a7%d9%86%d8%b3%d8%aa%d9%86%db%8c-%d9%87%d8%a7/\n" .
"4. فرم سفارش طراحی و ساخت: https://keshavarzlift.ir/%D9%81%D8%B1%D9%85%20%D8%B3%D9%81%D8%A7%D8%B1%D8%B4/";
?>

<div class="wrap wp-smart-ai-wrap">
    <div class="wp-smart-ai-header" style="background: linear-gradient(135deg, #0e5a8a 0%, #08334f 100%);">
        <div>
            <h1>🪄 بهینه‌سازی و سئوی جادویی مقالات قدیمی</h1>
            <p>محتوای متنی مقالات قبلی خود را با تگ‌های هدینگ عالی، لینک‌سازی داخلی صحیح، و چک‌لیست خوانایی و سئوی کاملاً سبز بازسازی کنید.</p>
        </div>
        <div style="font-size: 40px;">🪄📝</div>
    </div>

    <div class="wp-smart-ai-card">
        <h3>📋 لیست آخرین مقالات شما جهت بهبود محتوا</h3>
        <p class="description">برای هر مقاله کلمه کلیدی اصلی را مشخص کرده و در صورت تمایل تنظیمات اختصاصی لینک‌دهی یا پرامپت دستی را باز کنید و دکمه ویرایش را بزنید.</p>

        <?php if ( $posts_query->have_posts() ) : ?>
            <table class="wp-smart-ai-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">عنوان مقاله</th>
                        <th style="width: 15%;">وضعیت فعلی</th>
                        <th style="width: 25%;">کلمه کلیدی تمرکزی هدف (سئو)</th>
                        <th style="width: 15%;">وضعیت بهینه‌سازی AI</th>
                        <th style="width: 15%;">عملیات جادویی</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ( $posts_query->have_posts() ) : $posts_query->the_post();
                        $post_id = get_the_ID();
                        $keyword = get_post_meta( $post_id, 'rank_math_focus_keyword', true );
                        if ( empty( $keyword ) ) {
                            $keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
                        }
                    ?>
                        <tr>
                            <td>
                                <strong><a href="<?php echo get_edit_post_link( $post_id ); ?>" target="_blank"><?php the_title(); ?></a></strong>
                            </td>
                            <td><?php echo ( get_post_status() === 'publish' ) ? '<span style="color: green;">✔ منتشر شده</span>' : '<span style="color: orange;">پیش‌نویس</span>'; ?></td>
                            <td>
                                <input type="text" id="keyword-<?php echo $post_id; ?>" class="regular-text" style="width: 90%;" value="<?php echo esc_attr( $keyword ); ?>" placeholder="مثال: خرید آسانسور کارگاهی" />
                            </td>
                            <td id="status-<?php echo $post_id; ?>">
                                <span style="color: #666;">⏳ در انتظار بهینه‌سازی</span>
                            </td>
                            <td>
                                <button class="button button-primary optimize-single-post" data-post-id="<?php echo $post_id; ?>" style="width: 100%;">✨ ویرایش جادویی</button>
                                <button class="button button-link toggle-optimizer-settings" data-post-id="<?php echo $post_id; ?>" style="margin-top: 6px; display: block; width: 100%; text-align: center; font-size: 11px; color: #0073aa;">⚙️ تنظیمات لینک و پرامپت دستی</button>
                            </td>
                        </tr>
                        <!-- منوی کشویی تنظیمات اختصاصی بهینه‌سازی -->
                        <tr id="optimizer-settings-row-<?php echo $post_id; ?>" style="display:none; background: #fcfcfc;">
                            <td colspan="5" style="padding: 15px; border-top: 1px solid #ccd0d4; border-bottom: 2px solid #0e5a8a;">
                                <div style="background: #ffffff; padding: 15px; border-radius: 6px; border: 1px solid #ddd;">
                                    <h5 style="margin: 0 0 10px 0; color: #0e5a8a;">⚙️ تنظیمات دستی بهینه‌سازی مقاله:</h5>

                                    <div class="wp-smart-ai-field-group">
                                        <label style="font-size:11px; font-weight:bold; display:block; margin-bottom:4px;">📝 دستورالعمل و پرامپت دستی شما:</label>
                                        <textarea id="prompt-<?php echo $post_id; ?>" rows="2" style="font-size:12px; width:100%;" placeholder="مثلاً: روی کلمه کلیدی تمرکز بیشتری کن، جملات را بسیار کوتاه بنویس و لحن مقاله را کاملا دوستانه و شیوا طراحی کن."></textarea>
                                    </div>

                                    <div class="wp-smart-ai-field-group" style="margin-top: 10px;">
                                        <label style="font-size:11px; font-weight:bold; display:block; margin-bottom:4px;">🔗 لینک‌های دلخواه و کلمات کلیدی هدف آن‌ها جهت درج در متن:</label>
                                        <textarea id="links-<?php echo $post_id; ?>" rows="3" style="font-size:12px; width:100%;" placeholder="مثال:&#10;https://keshavarzlift.ir/shop/ با کلمه کلیدی 'خرید قطعات آسانسور'"></textarea>
                                        <p class="description" style="font-size:11px; color:#555; background:#f5f5f5; padding:8px; border-radius:4px; margin-top:5px; white-space: pre-wrap;"><?php echo esc_html($suggested_links_placeholder); ?></p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; wp_reset_postdata(); ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>هیچ مقاله‌ای یافت نشد.</p>
        <?php endif; ?>

        <!-- کنسول گزارشات لایو و کپی کدهای خطا -->
        <div class="wp-smart-ai-card smart-ai-console-wrapper" id="optimizer-console-wrapper" style="margin-top: 25px; border-right: 4px solid #0e5a8a; background: #1c1d22; color: #a9b2c3; font-family: monospace; border-radius: 8px; padding: 15px; display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d3139; padding-bottom: 8px; margin-bottom: 10px;">
                <span style="color: #00c8ff; font-weight: bold; font-size: 13px;">🖥️ کنسول گزارشات و خطاها (Live Debug Console)</span>
                <button class="button button-secondary copy-console-log-btn" data-target="optimizer-console" style="font-size: 11px; background: #2d3139; color: #fff; border: none; border-radius: 4px; padding: 4px 10px; cursor: pointer;">📋 کپی کردن لاگ</button>
            </div>
            <div class="smart-ai-console-log" id="optimizer-console" style="white-space: pre-wrap; font-size: 12px; line-height: 1.6; max-height: 250px; overflow-y: auto; padding-right: 5px; direction: ltr; text-align: left;"></div>
        </div>

    </div>
</div>
