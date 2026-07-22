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
                            <td><strong><a href="<?php echo get_edit_post_link( $post_id ); ?>" target="_blank"><?php the_title(); ?></a></strong></td>
                            <td><?php echo ( get_post_status() === 'publish' ) ? '<span style="color: green;">✔ منتشر شده</span>' : '<span style="color: orange;">پیش‌نویس</span>'; ?></td>
                            <td>
                                <input type="text" id="keyword-<?php echo $post_id; ?>" class="regular-text" style="width: 90%;" value="<?php echo esc_attr( $keyword ); ?>" placeholder="مثال: خرید آسانسور کارگاهی" />
                            </td>
                            <td id="status-<?php echo $post_id; ?>">
                                <span style="color: #666;">⏳ در انتظار بهینه‌سازی</span>
                            </td>
                            <td>
                                <button class="button button-primary optimize-single-post" data-post-id="<?php echo $post_id; ?>" style="width: 100%;">✨ ویرایش جادویی</button>
                                <button class="button button-secondary generate-images-post" data-post-id="<?php echo $post_id; ?>" style="margin-top: 6px; display: block; width: 100%; color: #440047; border-color: #440047;">📸 تصویرساز جادویی</button>
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
