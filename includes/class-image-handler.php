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

        // پاک کردن کلمات متنی ناخواسته
        $query = trim( $query );
        $clean_query = str_replace( array('no text', 'without text', 'text-free'), '', $query );

        // الحاق تگ‌های کیفیتی برای فوتورئالیسم و جلوگیری از متون روی تصویر
        $search_query = $clean_query . ' photorealistic interior design realistic no text';

        error_log("Smart AI SEO - Searching Unsplash for: " . $search_query);

        $image_url = '';
        if ( ! empty( $unsplash_key ) ) {
            // فراخوانی API رسمی Unsplash
            $api_url = 'https://api.unsplash.com/photos/random?query=' . urlencode( $search_query ) . '&client_id=' . $unsplash_key;
            $response = wp_remote_get( $api_url );
            if ( ! is_wp_error( $response ) ) {
                $body = json_decode( wp_remote_retrieve_body( $response ), true );
                if ( isset( $body['urls']['regular'] ) ) {
                    $image_url = $body['urls']['regular'];
                }
            } else {
                error_log("Smart AI SEO - Unsplash API Error: " . $response->get_error_message());
            }
        }

        // اگر کلید موجود نبود یا عکس پیدا نشد، از تصاویر هاردکدشده باکیفیت و جذاب مرتبط با صنعت آسانسور و معماری استفاده می‌کنیم
        if ( empty( $image_url ) ) {
            $elevator_photos = array(
                'photo-1605647540924-852290f6b0d5', // modern lift
                'photo-1517649763962-0c623066013b', // architecture lift
                'photo-1581094288338-2314dddb7eed', // engineering engine
                'photo-1541888946425-d81bb19240f5', // construction
                'photo-1504307651254-35680f356dfd', // construction site
                'photo-1486406146926-c627a92ad1ab', // commercial building skyscraper
                'photo-1497366216548-37526070297c'  // luxury lobby escalators
            );
            $selected_photo = $elevator_photos[ array_rand( $elevator_photos ) ];
            $image_url = 'https://images.unsplash.com/' . $selected_photo . '?auto=format&fit=crop&w=1200&q=80';
        }

        error_log("Smart AI SEO - Downloading image from URL: " . $image_url);

        // دانلود تصویر به پوشه آپلودهای وردپرس
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $tmp = download_url( $image_url );
        if ( is_wp_error( $tmp ) ) {
            error_log("Smart AI SEO - Download failed: " . $tmp->get_error_message());
            return $tmp;
        }

        $file_array = array(
            'name'     => sanitize_title( $clean_query ) . '-' . rand(100, 999) . '.jpg',
            'tmp_name' => $tmp
        );

        // آپلود فایل در کتابخانه رسانه
        $id = media_handle_sideload( $file_array, $post_id, $alt_text );

        if ( is_wp_error( $id ) ) {
            error_log("Smart AI SEO - Sideload media failed: " . $id->get_error_message());
            @unlink( $file_array['tmp_name'] );
            return $id;
        }

        // درج تگ Alt هوشمند برای تصویر آپلود شده
        if ( ! empty( $alt_text ) ) {
            update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
        }

        error_log("Smart AI SEO - Image downloaded successfully with ID: " . $id);
        return $id;
    }

    /**
     * جایگزینی تگ‌های <!-- PLACE_IMAGE: ... --> با تگ‌های واقعی عکس در متن مقاله و تخصیص تصویر شاخص
     */
    public static function insert_images_into_content( $content, $post_id = 0 ) {
        // الگو برای استخراج درخواست تصویر به فرمت: <!-- PLACE_IMAGE: query | alt -->
        preg_match_all( '/<!--\s*PLACE_IMAGE:\s*(.*?)\s*-->/', $content, $matches );

        if ( empty( $matches[0] ) ) {
            // اگر تگی در متن نبود، کلمه کلیدی پست را برای تولید عکس‌ها استخراج می‌کنیم
            $keyword = get_post_meta( $post_id, 'rank_math_focus_keyword', true );
            if ( empty( $keyword ) ) {
                $keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
            }
            if ( empty( $keyword ) ) {
                $keyword = get_the_title( $post_id );
            }

            error_log("Smart AI SEO - Injected images on demand for keyword: " . $keyword);

            // ترجمه کلمه کلیدی به انگلیسی از طریق جمینی به صورت ۳ فاکتور مجزا
            $queries = WPSmartAI_Engine::translate_keyword_to_english( $keyword );

            $default_queries = array(
                $queries[0] . ' luxury lift',
                $queries[1] . ' motor engine',
                $queries[2] . ' escalator architecture'
            );
            $default_alts = array(
                'خرید و نصب آسانسور لوکس با کلمه کلیدی ' . $keyword,
                'موتور اصلی آسانسور و کیفیت فنی قطعات ' . $keyword,
                'طراحی کابین آسانسور و دکوراسیون ' . $keyword
            );

            $inserted_images_html = array();
            $first_img_id = 0;

            for ( $i = 0; $i < 3; $i++ ) {
                $attachment_id = self::fetch_and_upload_image( $default_queries[$i], $post_id, $default_alts[$i] );
                if ( ! is_wp_error( $attachment_id ) ) {
                    if ( $i === 0 ) {
                        $first_img_id = $attachment_id;
                        set_post_thumbnail( $post_id, $attachment_id ); // تصویر شاخص اصلی
                    }
                    $image_src = wp_get_attachment_image_url( $attachment_id, 'large' );
                    $inserted_images_html[] = '
                    <figure class="wp-block-image size-large" style="margin: 35px 0; text-align: center;">
                        <img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $default_alts[$i] ) . '" class="wp-image-' . $attachment_id . '" style="border-radius:16px; max-width: 100%; height: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.15);"/>
                        <figcaption class="wp-element-caption" style="color: #7a9bcb; font-size: 13px; margin-top: 8px;">' . esc_html( $default_alts[$i] ) . '</figcaption>
                    </figure>';
                } else {
                    error_log("Smart AI SEO - Failed to fetch image: " . $attachment_id->get_error_message());
                }
            }

            // پاک کردن هر تگ عکس تکراری قبلی در HTML جهت جلوگیری از به وجود آمدن گالری‌های خراب
            $content = preg_replace('/<figure class="wp-block-image size-large.*?<\/figure>/is', '', $content);
            $content = preg_replace('/<img[^>]+>/is', '', $content);

            // توزیع تمیز تصاویر در بالا، وسط و انتهای متن اصلی
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

                $content = str_replace( $full_tag, $html_image, $content );
            } else {
                $content = str_replace( $full_tag, '', $content );
            }
        }

        return $content;
    }
}
