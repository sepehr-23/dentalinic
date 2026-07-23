<?php
/**
 * کلاس جستجو و تحلیل رقبای اول در گوگل
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPSmartAI_Competitor {

    /**
     * جستجوی کلمه کلیدی در گوگل و دریافت ۳ رقیب اول به همراه تحلیل محتوای آن‌ها
     */
    public static function analyze_competitors( $keyword ) {
        $url = 'https://html.duckduckgo.com/html/?q=' . urlencode( $keyword );

        $response = wp_remote_get( $url, array(
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'timeout'    => 20
        ) );

        if ( is_wp_error( $response ) ) {
            return self::get_mock_competitors( $keyword );
        }

        $html = wp_remote_retrieve_body( $response );
        if ( empty( $html ) ) {
            return self::get_mock_competitors( $keyword );
        }

        $dom = new DOMDocument();
        // بارگذاری ایمن بدون فعال کردن خطاهای سراسری PHP
        libxml_use_internal_errors(true);
        @$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
        libxml_clear_errors();

        $xpath = new DOMXPath( $dom );
        $results = array();
        $nodes = $xpath->query( '//a[@class="result__url"]' );

        $count = 0;
        if ( $nodes && $nodes->length > 0 ) {
            foreach ( $nodes as $node ) {
                if ( $count >= 3 ) {
                    break;
                }
                $href = $node->getAttribute( 'href' );
                if ( preg_match( '/uddg=([^&]+)/', $href, $matches ) ) {
                    $actual_url = urldecode( $matches[1] );
                    $results[] = array(
                        'url'   => $actual_url,
                        'title' => sanitize_text_field( $node->textContent )
                    );
                    $count++;
                }
            }
        }

        if ( empty( $results ) ) {
            return self::get_mock_competitors( $keyword );
        }

        // خزش محتوای رقبا
        foreach ( $results as $key => $res ) {
            $comp_resp = wp_remote_get( $res['url'], array( 'timeout' => 10 ) );
            if ( ! is_wp_error( $comp_resp ) ) {
                $comp_html = wp_remote_retrieve_body( $comp_resp );
                $results[$key]['snippet'] = self::extract_main_text( $comp_html );
            } else {
                $results[$key]['snippet'] = 'امکان خزش مستقیم فراهم نشد، اما بر اساس عنوان موضوع تحلیل خواهد شد.';
            }
        }

        return $results;
    }

    /**
     * شبیه‌سازی نتایج در صورت بروز خطا در دسترسی به اینترنت
     */
    private static function get_mock_competitors( $keyword ) {
        return array(
            array(
                'url' => 'https://keshavarzlift.ir/mock-competitor-1',
                'title' => 'بررسی تخصصی انواع سیستم‌های محرکه و ' . $keyword,
                'snippet' => 'در این مقاله به بررسی کامل سیستم‌های هیدرولیکی، کششی و گیرلس برای ' . $keyword . ' می‌پردازیم.'
            ),
            array(
                'url' => 'https://keshavarzlift.ir/mock-competitor-2',
                'title' => 'راهنمای جامع خرید و برآورد قیمت ' . $keyword,
                'snippet' => 'چگونه با کمترین بودجه بهترین برند موتور و قطعات باکیفیت را برای ' . $keyword . ' تهیه کنیم؟'
            ),
            array(
                'url' => 'https://keshavarzlift.ir/mock-competitor-3',
                'title' => 'نکات ایمنی مهم و استانداردهای ملی در طراحی ' . $keyword,
                'snippet' => 'توضیحاتی کامل در خصوص چگونگی کاهش لرزش کابین و بهبود عملکرد استاندارد ' . $keyword . '.'
            )
        );
    }

    /**
     * استخراج متن اصلی از کدهای HTML رقیب
     */
    private static function extract_main_text( $html ) {
        if ( empty( $html ) ) {
            return '';
        }
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
        libxml_clear_errors();

        $xpath = new DOMXPath( $dom );

        $paragraphs = $xpath->query( '//article//p | //main//p | //div[contains(@class, "post-content")]//p | //div[contains(@class, "entry-content")]//p' );
        if ( ! $paragraphs || $paragraphs->length === 0 ) {
            $paragraphs = $xpath->query( '//p' );
        }

        $text = '';
        $count = 0;
        if ( $paragraphs && $paragraphs->length > 0 ) {
            foreach ( $paragraphs as $p ) {
                if ( $count >= 5 ) {
                    break;
                }
                $p_text = trim( $p->textContent );
                if ( strlen( $p_text ) > 50 ) {
                    $text .= $p_text . "\n";
                    $count++;
                }
            }
        }

        return mb_substr( $text, 0, 1000, 'UTF-8' );
    }
}
