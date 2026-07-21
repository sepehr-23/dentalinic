<?php
/**
 * کلاس دریافت تصاویر از Unsplash و درج خودکار با تگ Alt هوشمند
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPSmartAI_Image_Handler {

    /**
     * پیدا کردن تصویر در Unsplash، دانلود و پیوست کردن آن به رسانه وردپرس
     */
    public static function fetch_and_upload_image( $query, $post_id = 0, $alt_text = '' ) {
        $settings = get_option( 'wp_smart_ai_seo_settings', array() );
        $unsplash_key = isset( $settings['unsplash_key'] ) ? $settings['unsplash_key'] : '';

        if ( empty( $unsplash_key ) ) {
            // اگر کلید اختصاصی نبود، از کلید آزمایشی یا عکس‌های دامی باکیفیت Unsplash بر اساس موضوع استفاده می‌کنیم
            $image_url = 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80';
        } else {
            // فراخوانی API رسمی Unsplash
            $api_url = 'https://api.unsplash.com/photos/random?query=' . urlencode( $query ) . '&client_id=' . $unsplash_key;
            $response = wp_remote_get( $api_url );
            if ( ! is_wp_error( $response ) ) {
                $body = json_decode( wp_remote_retrieve_body( $response ), true );
                if ( isset( $body['urls']['regular'] ) ) {
                    $image_url = $body['urls']['regular'];
                }
            }
        }

        if ( empty( $image_url ) ) {
            $image_url = 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80';
        }

        // دانلود تصویر به پوشه آپلودهای وردپرس
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        // دانلود فایل به صورت موقت
        $tmp = download_url( $image_url );
        if ( is_wp_error( $tmp ) ) {
            return $tmp;
        }

        $file_array = array(
            'name'     => sanitize_title( $query ) . '.jpg',
            'tmp_name' => $tmp
        );

        // آپلود فایل در کتابخانه رسانه
        $id = media_handle_sideload( $file_array, $post_id, $alt_text );

        if ( is_wp_error( $id ) ) {
            @unlink( $file_array['tmp_name'] );
            return $id;
        }

        // درج تگ Alt هوشمند برای تصویر آپلود شده
        if ( ! empty( $alt_text ) ) {
            update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
        }

        return $id;
    }

    /**
     * جایگزینی تگ‌های <!-- PLACE_IMAGE: ... --> با تگ‌های واقعی عکس در متن مقاله
     */
    public static function insert_images_into_content( $content, $post_id = 0 ) {
        // الگو برای استخراج درخواست تصویر
        preg_match_all( '/<!--\s*PLACE_IMAGE:\s*(.*?)\s*-->/', $content, $matches );

        if ( empty( $matches[0] ) ) {
            return $content;
        }

        foreach ( $matches[0] as $index => $full_tag ) {
            $image_query = $matches[1][$index];

            // تولید عنوان آلت به کمک کلمات هم‌خانواده
            $alt_text = $image_query;

            // دانلود و دریافت شناسه پیوست
            $attachment_id = self::fetch_and_upload_image( $image_query, $post_id, $alt_text );

            if ( ! is_wp_error( $attachment_id ) ) {
                $image_src = wp_get_attachment_image_url( $attachment_id, 'large' );
                $html_image = '
                <figure class="wp-block-image size-large">
                    <img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $alt_text ) . '" class="wp-image-' . $attachment_id . '"/>
                    <figcaption class="wp-element-caption">' . esc_html( $alt_text ) . '</figcaption>
                </figure>';

                // جایگزینی تگ کامنت با تصویر واقعی
                $content = str_replace( $full_tag, $html_image, $content );
            } else {
                // اگر عکسی دانلود نشد کامنت را از صفحه پاک می‌کنیم
                $content = str_replace( $full_tag, '', $content );
            }
        }

        return $content;
    }
}
