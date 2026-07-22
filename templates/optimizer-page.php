<?php
/**
 * قالب صفحه ویرایش و سئوی جادویی مقالات قدیمی
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
?>

<div class="wrap wp-smart-ai-wrap">
    <div class="wp-smart-ai-header" style="background: linear-gradient(135deg, #0e5a8a 0%, #08334f 100%);">
        <div>
            <h1>🪄 بهینه‌سازی و سئوی جادویی مقالات قدیمی</h1>
            <p>مقالات قبلی خود را با بهترین تگ‌های هدینگ، تصاویر خیره‌کننده با برچسب Alt هدف، لینک‌سازی داخلی و چک‌لیست سئوی کاملاً سبز بازسازی کنید.</p>
        </div>
        <div style="font-size: 40px;">🪄📝</div>
    </div>

    <div class="wp-smart-ai-card">
        <h3>📋 لیست آخرین مقالات شما</h3>
        <p class="description">برای هر مقاله کلمه کلیدی اصلی را مشخص کرده و دکمه ویرایش جادویی را بزنید تا هوش مصنوعی بلافاصله آن را متحول کند.</p>

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
                        // تلاش برای دریافت کلمه کلیدی ذخیره شده در یواست یا رنک مث
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
                                <button class="button button-secondary generate-images-post" data-post-id="<?php echo $post_id; ?>" style="margin-top: 6px; display: block; width: 100%; color: #440047; border-color: #440047;">📸 تصویرساز ۳‌تایی جادویی</button>
                                <button class="button button-link toggle-image-manager" data-post-id="<?php echo $post_id; ?>" style="margin-top: 6px; display: block; width: 100%; text-align: center; font-size: 11px; color: #0073aa;">🖼️ مدیریت تکی تصاویر گالری</button>
                            </td>
                        </tr>
                        <!-- پنل کشویی مدیریت گالری تصاویر تکی -->
                        <tr id="image-manager-row-<?php echo $post_id; ?>" style="display:none; background: #fcfcfc;">
                            <td colspan="5" style="padding: 20px; border-top: 1px solid #ccd0d4; border-bottom: 2px solid #999;">
                                <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #ddd;">
                                    <h4 style="margin-top: 0; color: #440047; border-bottom: 2px solid #efe5f0; padding-bottom: 8px;">🖼️ پنل مدیریت و تعویض تکی تصاویر گالری برای مقاله</h4>
                                    <p class="description" style="margin-bottom: 15px;">در این بخش می‌توانید تصاویر دانلود شده را لود کرده و هر کدام را که مایلید با کلمه کلیدی دلخواه جایگزین کنید. تصویر قبلی به طور کامل از رسانه هاست شما حذف خواهد شد.</p>

                                    <button class="button button-primary load-post-images" data-post-id="<?php echo $post_id; ?>">🔄 لود و مدیریت گالری تصاویر مقاله</button>

                                    <div class="image-loading-spinner" id="gallery-loader-<?php echo $post_id; ?>" style="display:none; margin: 15px 0;">
                                        <div class="smart-ai-spinner" style="display: inline-block; vertical-align: middle;"></div>
                                        <span style="margin-right: 8px;">در حال اسکن کدهای HTML مقاله و لود تصاویر...</span>
                                    </div>

                                    <!-- محل نمایش عکس ها -->
                                    <div id="image-manager-gallery-<?php echo $post_id; ?>" class="image-manager-gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;"></div>

                                    <!-- محل نمایش لاگ خطاها و گزارش لحظه ای -->
                                    <div class="image-manager-console" id="image-console-<?php echo $post_id; ?>" style="display:none; margin-top: 15px; padding: 12px; background: #fff5f5; border-right: 4px solid #d63638; border-radius: 4px; color: #d63638; font-family: monospace; font-size: 12px;"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; wp_reset_postdata(); ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>هیچ مقاله‌ای یافت نشد.</p>
        <?php endif; ?>
    </div>
</div>
