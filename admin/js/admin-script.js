jQuery(document).ready(function($) {

    // ذخیره تنظیمات عمومی
    $('#smart-ai-settings-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('.smart-ai-btn');
        var loader = $('#settings-loader');
        var resultBox = $('#settings-result');

        submitBtn.prop('disabled', true);
        loader.css('display', 'flex');
        resultBox.hide();

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000, // ۵ دقیقه تایم‌اوت
            data: {
                action: 'smart_ai_save_settings',
                security: smart_ai_params.nonce,
                api_provider: $('#api_provider').val(),
                api_key: $('#api_key').val(),
                unsplash_key: $('#unsplash_key').val(),
                tone: $('#tone').val()
            },
            success: function(response) {
                loader.hide();
                submitBtn.prop('disabled', false);
                if (response.success) {
                    resultBox.removeClass('error').html(response.data.message).fadeIn();
                } else {
                    resultBox.addClass('error').html(response.data.message).fadeIn();
                }
            },
            error: function(xhr, status, error) {
                loader.hide();
                submitBtn.prop('disabled', false);
                resultBox.addClass('error').html('خطایی در ارتباط با سرور رخ داد: ' + error).fadeIn();
                console.error('Smart AI SEO - Save settings failure:', xhr, status, error);
            }
        });
    });

    // سئو و بهینه‌سازی مقاله قدیمی با دکمه جادویی
    $('.optimize-single-post').on('click', function() {
        var btn = $(this);
        var postId = btn.data('post-id');
        var keywordInput = $('#keyword-' + postId);
        var keyword = keywordInput.val();
        var statusCell = $('#status-' + postId);

        if (!keyword) {
            alert('لطفاً ابتدا کلمه کلیدی را برای این مقاله وارد کنید.');
            keywordInput.focus();
            return;
        }

        btn.prop('disabled', true);
        statusCell.html('<div class="smart-ai-spinner"></div> در حال تحلیل و بهبود سئو...');

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000, // ۵ دقیقه تایم‌اوت
            data: {
                action: 'smart_ai_optimize_post',
                security: smart_ai_params.nonce,
                post_id: postId,
                keyword: keyword
            },
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    statusCell.html('<span style="color: green; font-weight: bold;">✔ بهینه‌سازی شد (تیک سبز سئو ست شد!)</span>');
                    alert(response.data.message);
                } else {
                    statusCell.html('<span style="color: red;">❌ خطا در بهینه‌سازی</span>');
                    alert('خطا: ' + response.data.message);
                    console.error('Smart AI SEO - Optimization failed:', response);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                statusCell.html('<span style="color: red;">❌ خطای ارتباطی</span>');
                console.error('Smart AI SEO - AJAX error:', xhr, status, error);
                alert('خطای سرور رخ داد. لطفا کنسول مرورگر (F12) یا گزارش خطاهای سرور را چک کنید. جزئیات: ' + error);
            }
        });
    });

    // دکمه اختصاصی تصویرساز جادویی ۳‌تایی
    $('.generate-images-post').on('click', function() {
        var btn = $(this);
        var postId = btn.data('post-id');
        var keywordInput = $('#keyword-' + postId);
        var keyword = keywordInput.val();
        var statusCell = $('#status-' + postId);

        if (!keyword) {
            alert('لطفاً ابتدا کلمه کلیدی را برای این مقاله وارد کنید تا عکس‌ها مرتبط با آن باشند.');
            keywordInput.focus();
            return;
        }

        btn.prop('disabled', true);
        statusCell.html('<div class="smart-ai-spinner"></div> در حال تولید و چیدمان تصاویر بدون نوشته...');

        console.log('Smart AI SEO - Initializing Magic Image Maker for Post ID:', postId, 'with keyword:', keyword);

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000, // ۵ دقیقه تایم‌اوت
            data: {
                action: 'smart_ai_generate_images_for_post',
                security: smart_ai_params.nonce,
                post_id: postId,
                keyword: keyword
            },
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    statusCell.html('<span style="color: green; font-weight: bold;">✔ تصاویر و شاخص ست شدند!</span>');
                    alert(response.data.message);
                } else {
                    statusCell.html('<span style="color: red;">❌ خطا در ایجاد تصاویر</span>');
                    alert('خطا در بارگذاری تصاویر: ' + response.data.message);
                    console.error('Smart AI SEO - Image Maker API error:', response);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                statusCell.html('<span style="color: red;">❌ خطای سرور در دانلود عکس</span>');
                console.error('Smart AI SEO - AJAX Failure inside Image Maker:', xhr, status, error);
                alert('خطای اتصال به سرور رخ داد! ممکن است به دلیل لودینگ طولانی یا عدم تنظیم کلید API در پیشخوان باشد. لطفا کنسول (F12) یا فایل error_log هاست را بررسی نمایید. جزئیات خطای شبکه: ' + error);
            }
        });
    });

    // باز و بسته کردن منوی کشویی مدیریت تصاویر تکی
    $('.toggle-image-manager').on('click', function(e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        $('#image-manager-row-' + postId).slideToggle(250);
    });

    // لود تکی تصاویر گالری مقاله
    $('.load-post-images').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var postId = btn.data('post-id');
        var spinner = $('#gallery-loader-' + postId);
        var galleryGrid = $('#image-manager-gallery-' + postId);
        var consoleBox = $('#image-console-' + postId);

        btn.prop('disabled', true);
        spinner.show();
        galleryGrid.html('');
        consoleBox.hide().html('');

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000, // ۵ دقیقه تایم‌اوت
            data: {
                action: 'smart_ai_get_post_images_list',
                security: smart_ai_params.nonce,
                post_id: postId
            },
            success: function(response) {
                btn.prop('disabled', false);
                spinner.hide();
                if (response.success) {
                    var images = response.data.images;
                    if (images.length === 0) {
                        galleryGrid.html('<p style="grid-column: 1/-1; color: #666; text-align: center;">هیچ تصویری در این مقاله ثبت نشده است. ابتدا روی دکمه تصویرساز جادویی کلیک کنید.</p>');
                        return;
                    }

                    var html = '';
                    images.forEach(function(img) {
                        var cardTitle = img.type === 'featured' ? '📌 تصویر شاخص مقاله' : '🖼️ تصویر داخل متن شماره ' + img.index;
                        var imgUrl = img.url ? img.url : 'https://placehold.co/600x400/222/fff?text=No+Image+Loaded';

                        html += '<div class="wp-smart-ai-card" style="margin-bottom:0; display: flex; flex-direction: column; gap: 10px; border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #fff;" id="image-card-' + img.id + '">';
                        html += '<h5 style="margin: 0 0 5px 0; color: #440047; font-weight: bold;">' + cardTitle + '</h5>';
                        html += '<img src="' + imgUrl + '" style="width: 100%; height: 140px; object-fit: cover; border-radius: 6px; border: 1px solid #eee;" />';
                        html += '<div style="font-size: 11px; color: #666;"><strong>شناسه ضمیمه:</strong> ' + img.id + '</div>';

                        // فیلدهای تعویض جادویی
                        html += '<div style="margin-top: 5px;">';
                        html += '<label style="font-size: 11px; font-weight: bold; display:block; margin-bottom:4px;">کلمه انگلیسی جستجوی تصویر جدید:</label>';
                        html += '<input type="text" class="new-query-input" value="" style="font-size:12px; width:100%; margin-bottom:8px;" placeholder="مثال: luxury building elevator design" />';

                        html += '<label style="font-size: 11px; font-weight: bold; display:block; margin-bottom:4px;">متن Alt تصویر جدید (فارسی):</label>';
                        html += '<input type="text" class="new-alt-input" value="' + img.alt + '" style="font-size:12px; width:100%; margin-bottom:8px;" />';
                        html += '</div>';

                        html += '<button class="button button-secondary replace-image-btn" data-post-id="' + postId + '" data-old-id="' + img.id + '" style="margin-top:5px; width:100%; font-weight: bold; color: #440047; border-color: #440047;">🔁 جایگزینی جادویی عکس</button>';
                        html += '</div>';
                    });

                    galleryGrid.html(html);
                } else {
                    consoleBox.html('خطا در بارگذاری تصاویر: ' + response.data.message).show();
                    console.error('Smart AI SEO - Load gallery error:', response);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                spinner.hide();
                consoleBox.html('خطای اتصال به سرور: ' + error).show();
                console.error('Smart AI SEO - Load gallery AJAX error:', xhr, status, error);
            }
        });
    });

    // جایگزینی جادویی و تکی یک عکس خاص و حذف عکس قدیمی از هاست
    $(document).on('click', '.replace-image-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var card = btn.closest('.wp-smart-ai-card');
        var postId = btn.data('post-id');
        var oldId = btn.data('old-id');
        var query = card.find('.new-query-input').val();
        var alt = card.find('.new-alt-input').val();
        var consoleBox = $('#image-console-' + postId);

        if (!query) {
            alert('لطفاً کلمه کلیدی انگلیسی برای جستجوی تصویر جدید در Unsplash را وارد کنید.');
            card.find('.new-query-input').focus();
            return;
        }

        btn.prop('disabled', true).text('در حال تعویض و حذف فایل قبلی...');
        consoleBox.hide().html('');

        console.log('Smart AI SEO - Triggering specific replacement for attachment ID:', oldId, 'with new query:', query);

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000, // ۵ دقیقه تایم‌اوت
            data: {
                action: 'smart_ai_replace_specific_image',
                security: smart_ai_params.nonce,
                post_id: postId,
                old_id: oldId,
                query: query,
                alt: alt
            },
            success: function(response) {
                btn.prop('disabled', false).text('🔁 جایگزینی جادویی عکس');
                if (response.success) {
                    alert(response.data.message);
                    // بروزرسانی آنی کارت تصویر با آدرس عکس جدید
                    card.find('img').attr('src', response.data.new_url);
                    btn.data('old-id', response.data.new_id);
                    card.find('.new-query-input').val('');
                } else {
                    consoleBox.html('خطا در جایگزینی تصویر: ' + response.data.message).show();
                    console.error('Smart AI SEO - Image replace API error:', response);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).text('🔁 جایگزینی جادویی عکس');
                consoleBox.html('خطای اتصال سرور در تعویض تصویر: ' + error).show();
                console.error('Smart AI SEO - Image replace AJAX error:', xhr, status, error);
            }
        });
    });

    // مرحله اول: تحلیل و جستجوی رقبای گوگل
    $('#analyze-competitors-btn').on('click', function() {
        var keyword = $('#writer_keyword').val();
        if (!keyword) {
            alert('لطفاً کلمه کلیدی را بنویسید.');
            return;
        }

        var btn = $(this);
        var loader = $('#writer-loader');
        var resultBox = $('#writer-result');
        var compBox = $('#competitor-results-box');

        btn.prop('disabled', true);
        loader.html('<div class="smart-ai-spinner"></div> در حال جستجوی گوگل و تحلیل ۳ رقیب اول...').css('display', 'flex');
        resultBox.hide();
        compBox.hide();

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: {
                action: 'smart_ai_analyze_competitors',
                security: smart_ai_params.nonce,
                keyword: keyword
            },
            success: function(response) {
                btn.prop('disabled', false);
                loader.hide();
                if (response.success) {
                    var html = '';
                    var competitors = response.data.competitors;

                    competitors.forEach(function(item, index) {
                        html += '<div class="wp-smart-ai-card" style="border-right: 4px solid #440047; padding: 15px; margin-bottom: 10px;">';
                        html += '<h4>رقیب شماره ' + (index + 1) + ': <a href="' + item.url + '" target="_blank">' + item.title + '</a></h4>';
                        html += '<textarea class="competitor-snippet" style="width:100%; height:80px;" readonly>' + item.snippet + '</textarea>';
                        html += '</div>';
                    });

                    $('#competitor-list').html(html);
                    compBox.fadeIn();
                } else {
                    resultBox.addClass('error').html(response.data.message).fadeIn();
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                loader.hide();
                resultBox.addClass('error').html('خطا در بارگذاری رقبا: ' + error).fadeIn();
            }
        });
    });

    // مرحله دوم: تولید مقاله برتر و نهایی رقابتی
    $('#generate-best-article-btn').on('click', function() {
        var keyword = $('#writer_keyword').val();
        var competitorTexts = '';

        $('.competitor-snippet').each(function() {
            competitorTexts += $(this).val() + "\n---\n";
        });

        var btn = $(this);
        var loader = $('#writer-loader');
        var resultBox = $('#writer-result');

        btn.prop('disabled', true);
        loader.html('<div class="smart-ai-spinner"></div> در حال نگارش مقاله برتر با قلم هوش مصنوعی، ایجاد آلت تگ‌ها و هماهنگی با رنک مث... (ممکن است چند دقیقه طول بکشد)').css('display', 'flex');
        resultBox.hide();

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: {
                action: 'smart_ai_generate_new_post',
                security: smart_ai_params.nonce,
                keyword: keyword,
                competitor_data: competitorTexts
            },
            success: function(response) {
                btn.prop('disabled', false);
                loader.hide();
                if (response.success) {
                    var successHtml = '<h4>🎉 مقاله بی رقیب شما آماده شد!</h4>';
                    successHtml += '<p>' + response.data.message + '</p>';
                    successHtml += '<a href="' + response.data.edit_url + '" class="button button-primary button-large" target="_blank">رفتن به ویرایشگر پیش‌نویس مقاله</a>';
                    resultBox.removeClass('error').html(successHtml).fadeIn();
                } else {
                    resultBox.addClass('error').html(response.data.message).fadeIn();
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                loader.hide();
                resultBox.addClass('error').html('خطا در تولید مقاله نهایی رقابتی: ' + error).fadeIn();
            }
        });
    });

    // پیلار و کلاستر: پیشنهاد خوشه‌های محتوایی
    $('#suggest-clusters-btn').on('click', function() {
        var pillarId = $('#pillar_post_select').val();
        if (!pillarId) {
            alert('لطفاً یک مقاله مادر (Pillar) انتخاب کنید.');
            return;
        }

        var btn = $(this);
        var loader = $('#pillar-loader');
        var resultContainer = $('#pillar-tree-result');

        btn.prop('disabled', true);
        loader.html('<div class="smart-ai-spinner"></div> در حال اسکن مقاله مادر و تولید خوشه‌ها و مشتقات کلمات کلیدی...').css('display', 'flex');
        resultContainer.hide();

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: {
                action: 'smart_ai_suggest_clusters',
                security: smart_ai_params.nonce,
                post_id: pillarId
            },
            success: function(response) {
                btn.prop('disabled', false);
                loader.hide();
                if (response.success) {
                    var clusters = response.data.clusters;
                    var html = '';

                    // هدر پیلار
                    html += '<div class="pillar-node">مقاله مادر منتخب: ' + $('#pillar_post_select option:selected').text() + '</div>';
                    html += '<div class="cluster-nodes">';

                    clusters.forEach(function(cluster, idx) {
                        html += '<div class="cluster-node">';
                        html += '<h5> خوشه پیشنهادی ' + (idx + 1) + ': ' + cluster.title + '</h5>';
                        html += '<p><strong>کلمه کلیدی فرعی:</strong> ' + cluster.keyword + '</p>';
                        html += '<button class="button button-small create-cluster-post-btn" data-title="' + cluster.title + '" data-keyword="' + cluster.keyword + '" data-pillar-id="' + pillarId + '">ایجاد این مقاله و لینک‌سازی خودکار</button>';
                        html += '</div>';
                    });

                    html += '</div>';
                    resultContainer.html(html).fadeIn();
                } else {
                    alert('خطا: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                loader.hide();
                alert('خطا در ارتباط با سرور: ' + error);
            }
        });
    });

    // ایجاد فوری کلاستر و لینک به پیلار
    $(document).on('click', '.create-cluster-post-btn', function() {
        var btn = $(this);
        var title = btn.data('title');
        var keyword = btn.data('keyword');
        var pillarId = btn.data('pillar-id');

        btn.prop('disabled', true).text('در حال ساخت محتوا...');

        // ۱. ابتدا برای تولید محتوای کلاستر درخواست می‌دهیم
        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: {
                action: 'smart_ai_generate_new_post',
                security: smart_ai_params.nonce,
                keyword: keyword,
                competitor_data: 'نگارش محتوای کلاستر مربوط به مقاله مادر شماره ' + pillarId
            },
            success: function(response) {
                if (response.success) {
                    var newPostId = response.data.post_id;

                    // ۲. حالا لینک‌سازی متقابل را انجام می‌دهیم (کلاستر به پیلار با انکرتکست کلمه کلیدی)
                    $.ajax({
                        url: smart_ai_params.ajax_url,
                        type: 'POST',
                        timeout: 300000,
                        data: {
                            action: 'smart_ai_create_cluster_link',
                            security: smart_ai_params.nonce,
                            source_id: newPostId,
                            target_id: pillarId,
                            anchor: keyword
                        },
                        success: function(linkResponse) {
                            btn.html('✔ لینک‌سازی شد!').removeClass('button-primary').css('background', '#46b450');
                            alert('مقاله فرعی با موفقیت ایجاد شد، عکس‌ها دانلود شدند و لینک‌سازی متقابل به پیلار به اتم رسید!');
                        }
                    });
                } else {
                    btn.prop('disabled', false).text('تلاش مجدد');
                    alert('خطا: ' + response.data.message);
                }
            }
        });
    });

});
