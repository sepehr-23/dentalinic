<?php
/**
 * کلاس تحلیل محتوای درختی و مدل پیلار-کلاستر
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPSmartAI_Pilar_Builder {

    /**
     * پیدا کردن یا پیشنهاد مقالات فرعی بر اساس موضوع مقاله مادر (Pillar)
     */
    public static function suggest_clusters( $pillar_post_id ) {
        $post = get_post( $pillar_post_id );
        if ( ! $post ) {
            return new WP_Error( 'post_not_found', 'مقاله مادر یافت نشد.' );
        }

        $title = $post->post_title;
        $content = $post->post_content;

        $prompt = "تو یک متخصص استراتژی محتوایی سئو هستی.
ما یک مقاله مادر (Pillar Post) داریم به نام: '{$title}'
محتوای کلی آن به شرح زیر است:
---
{$content}
---

لطفاً ۵ عنوان مقاله فرعی و تخصصی‌تر (Cluster Posts) را پیشنهاد بده که می‌توانند مشتقات کلمات کلیدی این مقاله مادر باشند تا با نوشتن آن‌ها و لینک‌سازی متقابل، رتبه این مقاله مادر افزایش یابد.
فرمت پاسخ باید دقیقاً یک JSON به صورت زیر و کاملاً فارسی باشد:
[
  {
    \"title\": \"عنوان مقاله کلاستر ۱\",
    \"keyword\": \"کلمه کلیدی فرعی ۱\"
  },
  {
    \"title\": \"عنوان مقاله کلاستر ۲\",
    \"keyword\": \"کلمه کلیدی فرعی ۲\"
  }
]";

        $response = WPSmartAI_Engine::call_gemini( $prompt );
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $clean_json = preg_replace( '/```json|```/', '', $response );
        $clean_json = trim( $clean_json );
        $data = json_decode( $clean_json, true );

        return is_array( $data ) ? $data : array();
    }

    /**
     * لینک‌سازی متقابل خودکار بین کلاستر و پیلار
     */
    public static function create_internal_link( $source_post_id, $target_post_id, $anchor_text ) {
        $source_post = get_post( $source_post_id );
        $target_post = get_post( $target_post_id );

        if ( ! $source_post || ! $target_post ) {
            return false;
        }

        $target_url = get_permalink( $target_post_id );
        $link_html = ' <a href="' . esc_url( $target_url ) . '" title="' . esc_attr( $target_post->post_title ) . '">' . esc_html( $anchor_text ) . '</a> ';

        // قرار دادن لینک روی انکر تکست در متن مقاله مبدأ
        $content = $source_post->post_content;

        // جایگزینی اولین تطابق کلمه به صورت لینک شده
        $pos = mb_strpos( $content, $anchor_text );
        if ( $pos !== false ) {
            $content = mb_substr( $content, 0, $pos ) . $link_html . mb_substr( $content, $pos + mb_strlen( $anchor_text ) );

            // آپدیت پست مبدا
            wp_update_post( array(
                'ID'           => $source_post_id,
                'post_content' => $content
            ) );
            return true;
        }

        // اگر انکر تکست پیدا نشد، لینک را در انتهای مقاله اضافه می‌کنیم
        $content .= "\n\n<p>همچنین بخوانید: " . $link_html . "</p>";
        wp_update_post( array(
            'ID'           => $source_post_id,
            'post_content' => $content
        ) );

        return true;
    }
}
