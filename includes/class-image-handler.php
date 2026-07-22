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

        // تمیز کردن کوئری و پیوست کردن دستورالعمل برای کیفیت بالا و عدم درج متن روی تصویر
        $query = trim( $query );
        $clean_query = str_replace( array('no text', 'without text'), '', $query );
        $search_query = $clean_query . ' high resolution realistic photography';

        if ( empty( $unsplash_key ) ) {
            // استفاده از تصاویر رندوم و جذاب مرتبط با آسانسور و بالابر در صورت نبودن کلید API
            $random_ids = array(
                'photo-1517649763962-0c623066013b', // movement / elevator
                'photo-1581094288338-2314dddb7eed', // engineering
                'photo-1541888946425-d81bb19240f5', // construction
                'photo-1504307651254-35680f356dfd', // construction site
                'photo-1605647540924-852290f6b0d5', // lift
                'photo-1486406146926-c627a92ad1ab', // building skyscrapers
                'photo-1497366216548-37526070297c'  // office interior
            );
            $selected_photo = $random_ids[ array_rand( $random_ids ) ];
            $image_url = 'https://images.unsplash.com/' . $selected_photo . '?auto=format&fit=crop&w=1200&q=80';
        } else {
            // فراخوانی API رسمی Unsplash با فیلتر جهت دریافت تصاویر فوتورئالیستی بدون متن
            $api_url = 'https://api.unsplash.com/photos/random?query=' . urlencode( $search_query ) . '&client_id=' . $unsplash_key;
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
            'name'     => sanitize_title( $clean_query ) . '-' . rand(100, 999) . '.jpg',
            'tmp_name' => $tmp
        );

        // آپلود فایل در کتابخانه رسانه
        $id = media_handle_sideload( $file_array, $post_id, $alt_text );

        if ( is_wp_error( $id ) ) {
            @unlink( $file_array['tmp_name'] );
            return $id;
        }

        // درج تگ Alt هوشمند برای تصویر آپلود شده جهت رعایت سئو
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

        // اگر هیچ عکسی در متن مشخص نشده بود، ۳ عکس پیش‌فرض مرتبط با موضوع دانلود و در جاهای مناسب متن اضافه می‌کنیم
        if ( empty( $matches[0] ) ) {
            $default_queries = array('elevator building', 'elevator engine', 'luxury lift cabin');
            $default_alts = array(
                'خرید آسانسور مناسب ساختمان مسکونی و تجاری',
                'بهترین برند موتور آسانسور و کیفیت فنی قطعات',
                'طراحی دکوراسیون و کابین شیشه ای لوکس آسانسور'
            );

            $inserted_images_html = array();
            $first_img_id = 0;

            for ( $i = 0; $i < 3; $i++ ) {
                $attachment_id = self::fetch_and_upload_image( $default_queries[$i], $post_id, $default_alts[$i] );
                if ( ! is_wp_error( $attachment_id ) ) {
                    if ( $i === 0 ) {
                        $first_img_id = $attachment_id;
                        set_post_thumbnail( $post_id, $attachment_id ); // قرار دادن تصویر شاخص اصلی
                    }
                    $image_src = wp_get_attachment_image_url( $attachment_id, 'large' );
                    $inserted_images_html[] = '
                    <figure class="wp-block-image size-large" style="margin: 30px 0; text-align: center;">
                        <img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $default_alts[$i] ) . '" class="wp-image-' . $attachment_id . '" style="border-radius:16px; max-width: 100%; height: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.15);"/>
                        <figcaption class="wp-element-caption" style="color: #7a9bcb; font-size: 13px; margin-top: 8px;">' . esc_html( $default_alts[$i] ) . '</figcaption>
                    </figure>';
                }
            }

            // قرار دادن عکس‌ها به صورت مرتب در بالا، وسط و پایین مقاله
            if ( ! empty( $inserted_images_html ) ) {
                $paragraphs = explode( '</p>', $content );
                $total_p = count( $paragraphs );

                if ( isset( $inserted_images_html[0] ) ) {
                    $paragraphs[0] = $inserted_images_html[0] . "\n" . $paragraphs[0];
                }
                if ( isset( $inserted_images_html[1] ) && $total_p > 3 ) {
                    $mid = floor( $total_p / 2 );
                    $paragraphs[$mid] = $paragraphs[$mid] . "\n" . $inserted_images_html[1];
                }
                if ( isset( $inserted_images_html[2] ) && $total_p > 1 ) {
                    $paragraphs[$total_p - 1] = $paragraphs[$total_p - 1] . "\n" . $inserted_images_html[2];
                }
                $content = implode( '</p>', $paragraphs );
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
                <figure class="wp-block-image size-large" style="margin: 30px 0; text-align: center;">
                    <img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $alt_text ) . '" class="wp-image-' . $attachment_id . '" style="border-radius:16px; max-width: 100%; height: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.15);"/>
                    <figcaption class="wp-element-caption" style="color: #7a9bcb; font-size: 13px; margin-top: 8px;">' . esc_html( $alt_text ) . '</figcaption>
                </figure>';

                // جایگزینی تگ کامنت با تصویر واقعی
                $content = str_replace( $full_tag, $html_image, $content );
            } else {
                $content = str_replace( $full_tag, '', $content );
            }
        }

        return $content;
    }
}
