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

        // تمیز کردن کوئری
        $query = trim( $query );

        if ( empty( $unsplash_key ) ) {
            // استفاده از تصاویر رندوم و جذاب مرتبط با موضوع آسانسور و بالابر در صورت نبودن کلید
            $random_ids = array(
                'photo-1498050108023-c5249f4df085', // tech
                'photo-1581094288338-2314dddb7eed', // engineering
                'photo-1541888946425-d81bb19240f5', // construction
                'photo-1504307651254-35680f356dfd', // builder
                'photo-1605647540924-852290f6b0d5'  // modern elevator / architecture
            );
            $selected_photo = $random_ids[ array_rand( $random_ids ) ];
            $image_url = 'https://images.unsplash.com/' . $selected_photo . '?auto=format&fit=crop&w=1200&q=80';
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
            $image_url = 'https://images.unsplash.com/photo-1581094288338-2314dddb7eed?auto=format&fit=crop&w=1200&q=80';
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
            'name'     => sanitize_title( $query ) . '-' . rand(100, 999) . '.jpg',
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
     * جایگزینی تگ‌های <!-- PLACE_IMAGE: ... --> با تگ‌های واقعی عکس در متن مقاله و تخصیص تصویر شاخص
     */
    public static function insert_images_into_content( $content, $post_id = 0 ) {
        // الگو برای استخراج درخواست تصویر به فرمت: <!-- PLACE_IMAGE: query | alt -->
        preg_match_all( '/<!--\s*PLACE_IMAGE:\s*(.*?)\s*-->/', $content, $matches );

        if ( empty( $matches[0] ) ) {
            // اگر هیچ عکسی در متن مشخص نشده بود، یک عکس پیش‌فرض مرتبط با کلمه کلیدی در ابتدای متن قرار می‌دهیم
            $default_query = 'elevator construction';
            $default_alt = get_the_title( $post_id );
            $attachment_id = self::fetch_and_upload_image( $default_query, $post_id, $default_alt );
            if ( ! is_wp_error( $attachment_id ) ) {
                set_post_thumbnail( $post_id, $attachment_id ); // تصویر شاخص
                $image_src = wp_get_attachment_image_url( $attachment_id, 'large' );
                $html_image = '
                <figure class="wp-block-image size-large">
                    <img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $default_alt ) . '" class="wp-image-' . $attachment_id . '"/>
                </figure>';
                return $html_image . "\n" . $content;
            }
            return $content;
        }

        $first_image_id = 0;

        foreach ( $matches[0] as $index => $full_tag ) {
            $raw_param = $matches[1][$index];
            $parts = explode( '|', $raw_param );

            $image_query = trim( $parts[0] );
            $alt_text = isset( $parts[1] ) ? trim( $parts[1] ) : $image_query;

            // دانلود و دریافت شناسه پیوست
            $attachment_id = self::fetch_and_upload_image( $image_query, $post_id, $alt_text );

            if ( ! is_wp_error( $attachment_id ) ) {
                if ( $first_image_id === 0 ) {
                    $first_image_id = $attachment_id;
                    // قرار دادن عکس اول به عنوان تصویر شاخص (Featured Image)
                    set_post_thumbnail( $post_id, $attachment_id );
                }

                $image_src = wp_get_attachment_image_url( $attachment_id, 'large' );
                $html_image = '
                <figure class="wp-block-image size-large" style="margin: 20px 0; text-align: center;">
                    <img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $alt_text ) . '" class="wp-image-' . $attachment_id . '" style="border-radius:16px; max-width: 100%; height: auto;"/>
                    <figcaption class="wp-element-caption" style="color: #7a9bcb; font-size: 13px; margin-top: 8px;">' . esc_html( $alt_text ) . '</figcaption>
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
