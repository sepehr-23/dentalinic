<?php
/**
 * کلاس دریافت تصاویر جادویی از هوش مصنوعی تصویرساز رایگان (Pollinations AI) و مدیریت گالری مقالات و تصویر شاخص
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPSmartAI_Image_Handler {

    /**
     * تولید عکس جادویی با هوش مصنوعی Pollinations AI (Flux / Stable Diffusion) به صورت ۱۰۰٪ رایگان و بدون نیاز به کلید
     */
    public static function fetch_and_upload_image( $query, $post_id = 0, $alt_text = '' ) {
        // تمیز کردن کوئری
        $query = trim( $query );
        $clean_query = str_replace( array('no text', 'without text', 'text-free'), '', $query );

        // ساخت پرومپت غنی شده و تمیز برای هوش مصنوعی تصویرساز
        $search_query = $clean_query . ' photorealistic 8k, highly detailed, realistic architecture photography, architectural, depth of field, no text, no words, no letters, no watermark';

        error_log("Smart AI SEO - Generating AI Image with prompt: " . $search_query);

        // استفاده از API فوق‌العاده سریع و ۱۰۰٪ رایگان Pollinations AI برای تولید تصویر Flux
        $image_url = 'https://image.pollinations.ai/prompt/' . rawurlencode( $search_query ) . '?width=1024&height=768&nologo=true&private=true&enhance=false&seed=' . rand(1000, 9999);

        error_log("Smart AI SEO - Downloading AI generated image from URL: " . $image_url);

        // دانلود تصویر به پوشه آپلودهای وردپرس
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $tmp = download_url( $image_url );
        if ( is_wp_error( $tmp ) ) {
            error_log("Smart AI SEO - AI image download failed: " . $tmp->get_error_message());
            return $tmp;
        }

        $file_array = array(
            'name'     => sanitize_title( $clean_query ) . '-ai-' . rand(100, 999) . '.jpg',
            'tmp_name' => $tmp
        );

        $id = media_handle_sideload( $file_array, $post_id, $alt_text );

        if ( is_wp_error( $id ) ) {
            error_log("Smart AI SEO - AI media sideload failed: " . $id->get_error_message());
            @unlink( $file_array['tmp_name'] );
            return $id;
        }

        if ( ! empty( $alt_text ) ) {
            update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
        }

        error_log("Smart AI SEO - AI Image generated and uploaded successfully with ID: " . $id);
        return $id;
    }

    /**
     * جایگزینی تگ‌های <!-- PLACE_IMAGE: ... --> با تگ‌های واقعی عکس در متن مقاله و تخصیص تصویر شاخص
     */
    public static function insert_images_into_content( $content, $post_id = 0 ) {
        if ( is_wp_error( $content ) ) {
            return $content;
        }

        // الگو برای استخراج درخواست تصویر به فرمت: <!-- PLACE_IMAGE: query | alt -->
        preg_match_all( '/<!--\s*PLACE_IMAGE:\s*(.*?)\s*-->/', $content, $matches );

        if ( empty( $matches[0] ) ) {
            // اگر از قبل این مقاله تصویر شاخص دارد یا در متن آن تگ تصویر وجود دارد، هرگز تصاویر را عوض نمی‌کنیم!
            $has_thumb = has_post_thumbnail( $post_id );
            $has_img_tag = ( strpos( $content, '<img' ) !== false );

            if ( $has_thumb || $has_img_tag ) {
                error_log("Smart AI SEO - Post already has images, skipping downloading new ones during content optimization.");
                return $content; // محتوا را بدون تغییر عکس برمی‌گردانیم تا هم سریع‌تر کار کند و هم تصاویر حفظ شوند.
            }

            // اگر مقاله کلاً بدون عکس بود، ۳ عکس سئو شده جادویی متناسب با کلمه کلیدی تولید می‌کنیم
            $keyword = get_post_meta( $post_id, 'rank_math_focus_keyword', true );
            if ( empty( $keyword ) ) {
                $keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
            }
            if ( empty( $keyword ) ) {
                $keyword = get_the_title( $post_id );
            }

            error_log("Smart AI SEO - Injected AI images on demand for keyword: " . $keyword);

            // ترجمه کلمه کلیدی به انگلیسی از طریق جمینی
            $queries = WPSmartAI_Engine::translate_keyword_to_english( $keyword );
            if ( is_wp_error( $queries ) ) {
                $queries = array( 'elevator cabin', 'elevator motor', 'escalator' );
            }

            $default_queries = array(
                $queries[0] . ' luxury lift cabin',
                $queries[1] . ' modern elevator motor',
                $queries[2] . ' escalators lobby architectural'
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
                        set_post_thumbnail( $post_id, $attachment_id ); // قرار دادن تصویر شاخص اصلی
                    }
                    $image_src = wp_get_attachment_image_url( $attachment_id, 'large' );
                    $inserted_images_html[] = '
                    <figure class="wp-block-image size-large" style="margin: 35px 0; text-align: center;">
                        <img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $default_alts[$i] ) . '" class="wp-image-' . $attachment_id . '" style="border-radius:16px; max-width: 100%; height: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.15);"/>
                        <figcaption class="wp-element-caption" style="color: #7a9bcb; font-size: 13px; margin-top: 8px;">' . esc_html( $default_alts[$i] ) . '</figcaption>
                    </figure>';
                }
            }

            // پاک کردن عکس‌های تکراری قدیمی در متن
            $content = preg_replace('/<figure class="wp-block-image size-large.*?<\/figure>/is', '', $content);
            $content = preg_replace('/<img[^>]+>/is', '', $content);

            // چیدمان عکس‌ها به صورت متعادل در ابتدا، وسط و انتهای محتوا
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

    /**
     * استخراج تمامی تگ‌های عکس در متن مقاله به همراه آلت، آدرس و شناسه پیوست
     */
    public static function get_post_images_list( $post_id ) {
        $post = get_post( $post_id );
        if ( ! $post ) {
            return array();
        }

        $content = $post->post_content;
        $images = array();

        // واکشی تصویر شاخص ابتدا
        $thumbnail_id = get_post_thumbnail_id( $post_id );
        if ( $thumbnail_id ) {
            $thumbnail_src = wp_get_attachment_image_url( $thumbnail_id, 'large' );
            $images[] = array(
                'type' => 'featured',
                'id'   => $thumbnail_id,
                'url'  => $thumbnail_src,
                'alt'  => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true )
            );
        } else {
            $images[] = array(
                'type' => 'featured',
                'id'   => 0,
                'url'  => '',
                'alt'  => 'بدون تصویر شاخص'
            );
        }

        // واکشی عکس‌های داخل متن به کمک DOM
        preg_match_all( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches );
        if ( ! empty( $matches[0] ) ) {
            foreach ( $matches[0] as $idx => $tag ) {
                $url = $matches[1][$idx];

                // استخراج آلت
                $alt = '';
                if ( preg_match( '/alt=[\'"]([^\'"]*)[\'"]/i', $tag, $alt_match ) ) {
                    $alt = $alt_match[1];
                }

                // استخراج شناسه ضمیمه از کلاس (wp-image-XYZ)
                $attachment_id = 0;
                if ( preg_match( '/wp-image-(\d+)/i', $tag, $id_match ) ) {
                    $attachment_id = intval( $id_match[1] );
                }

                $images[] = array(
                    'type' => 'inline',
                    'index'=> $idx + 1,
                    'id'   => $attachment_id,
                    'url'  => $url,
                    'alt'  => $alt,
                    'tag'  => $tag
                );
            }
        }

        return $images;
    }

    /**
     * جراحی و جایگزینی یک عکس خاص بر اساس شناسه ضمیمه قدیمی با یک عکس دانلود شده جدید
     */
    public static function replace_specific_image( $post_id, $old_attachment_id, $new_query, $new_alt ) {
        $post = get_post( $post_id );
        if ( ! $post ) {
            return new WP_Error( 'post_not_found', 'پست یافت نشد.' );
        }

        // دانلود تصویر جدید از Unsplash
        $new_attachment_id = self::fetch_and_upload_image( $new_query, $post_id, $new_alt );
        if ( is_wp_error( $new_attachment_id ) ) {
            return $new_attachment_id;
        }

        $new_image_src = wp_get_attachment_image_url( $new_attachment_id, 'large' );

        // ۱. بررسی اگر عکس قدیمی تصویر شاخص بوده است
        $thumbnail_id = get_post_thumbnail_id( $post_id );
        if ( intval( $old_attachment_id ) === intval( $thumbnail_id ) || intval( $old_attachment_id ) === 0 ) {
            set_post_thumbnail( $post_id, $new_attachment_id );
        }

        // ۲. جایگزینی در داخل بدنه متن HTML
        $content = $post->post_content;

        if ( $old_attachment_id > 0 ) {
            // جستجو و جایگزینی تگ عکس با کلاس wp-image-OLD
            $pattern = '/<img[^>]+class=[\'"][^\'"]*wp-image-' . $old_attachment_id . '[^\'"]*[\'"][^>]*>/i';
            preg_match( $pattern, $content, $match );

            if ( ! empty( $match[0] ) ) {
                $new_tag = '<img src="' . esc_url( $new_image_src ) . '" alt="' . esc_attr( $new_alt ) . '" class="wp-image-' . $new_attachment_id . '" style="border-radius:16px; max-width: 100%; height: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.15);"/>';
                $content = str_replace( $match[0], $new_tag, $content );
            } else {
                // تلاش ثانویه بر اساس سورس عکس
                $old_src = wp_get_attachment_image_url( $old_attachment_id, 'large' );
                if ( $old_src ) {
                    $content = str_replace( $old_src, $new_image_src, $content );
                }
            }
        }

        // ۳. حذف کامل فایل قدیمی از هاست و رسانه وردپرس برای بهینه‌سازی فضا
        if ( $old_attachment_id > 0 ) {
            wp_delete_attachment( $old_attachment_id, true );
        }

        return array(
            'new_id'  => $new_attachment_id,
            'new_url' => $new_image_src,
            'content' => $content
        );
    }
}
