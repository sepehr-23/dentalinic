jQuery(document).ready(function($) {

    // تابع کمکی برای چاپ لاگ زمان‌دار در کنسول‌ها
    function logToConsole(consoleId, message) {
        var wrapper = $('#' + consoleId + '-wrapper');
        var consoleBox = $('#' + consoleId);

        if (wrapper.length && consoleBox.length) {
            wrapper.fadeIn(200);
            var date = new Date();
            var timeStr = '[' + date.toTimeString().split(' ')[0] + '] ';
            consoleBox.append(timeStr + message + "\n");

            // اسکرول خودکار به انتهای کنسول
            consoleBox.scrollTop(consoleBox[0].scrollHeight);
        }
    }

    // کپی کردن محتوای کنسول به کلیپ‌بورد
    $(document).on('click', '.copy-console-log-btn', function(e) {
        e.preventDefault();
        var targetId = $(this).data('target');
        var consoleBox = $('#' + targetId);

        if (consoleBox.length) {
            var text = consoleBox.text();
            if (!text) {
                alert('کنسول خالی است.');
                return;
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    alert('لاگ‌ها با موفقیت در کلیپ‌بورد کپی شدند!');
                }).catch(function(err) {
                    fallbackCopyText(text);
                });
            } else {
                fallbackCopyText(text);
            }
        }
    });

    function fallbackCopyText(text) {
        var tempInput = $('<textarea>');
        $('body').append(tempInput);
        tempInput.val(text).select();
        try {
            document.execCommand('copy');
            alert('لاگ‌ها با موفقیت کپی شدند (روش کمکی)!');
        } catch (err) {
            alert('خطا در کپی لاگ. لطفاً متن را دستی انتخاب و کپی کنید.');
        }
        tempInput.remove();
    }

    // ۱. ذخیره تنظیمات عمومی
    $('#smart-ai-settings-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('.smart-ai-btn');
        var loader = $('#settings-loader');
        var resultBox = $('#settings-result');
        var consoleId = 'settings-console';

        $('#' + consoleId).html(''); // پاک کردن لاگ‌های قبلی
        logToConsole(consoleId, 'شروع ذخیره‌سازی تنظیمات...');

        submitBtn.prop('disabled', true);
        loader.css('display', 'flex');
        resultBox.hide();

        var payload = {
            action: 'smart_ai_save_settings',
            security: smart_ai_params.nonce,
            api_provider: $('#api_provider').val(),
            api_key: $('#api_key').val(),
            unsplash_key: $('#unsplash_key').val(),
            tone: $('#tone').val()
        };

        logToConsole(consoleId, 'ارسال درخواست AJAX به سرور با اطلاعات: ' + JSON.stringify({
            api_provider: payload.api_provider,
            tone: payload.tone,
            has_api_key: payload.api_key ? 'بله' : 'خیر'
        }));

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: payload,
            success: function(response) {
                loader.hide();
                submitBtn.prop('disabled', false);
                logToConsole(consoleId, 'پاسخ سرور دریافت شد: ' + JSON.stringify(response));

                if (response.success) {
                    logToConsole(consoleId, 'عملیات با موفقیت انجام شد: ' + response.data.message);
                    resultBox.removeClass('error').html(response.data.message).fadeIn();
                } else {
                    logToConsole(consoleId, 'سرور با خطا پاسخ داد: ' + (response.data ? response.data.message : 'خطای نامعلوم'));
                    resultBox.addClass('error').html(response.data ? response.data.message : 'خطای نامعلوم').fadeIn();
                }
            },
            error: function(xhr, status, error) {
                loader.hide();
                submitBtn.prop('disabled', false);
                logToConsole(consoleId, '⚠️ خطای شبکه رخ داد!');
                logToConsole(consoleId, 'وضعیت خطا: ' + status);
                logToConsole(consoleId, 'جزئیات خطا: ' + error);
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن کامل پاسخ سرور: ' + xhr.responseText);

                resultBox.addClass('error').html('خطایی در ارتباط با سرور رخ داد. جزئیات کامل در کنسول لاگ ثبت شده است.').fadeIn();
                console.error('Smart AI SEO - Save settings failure:', xhr, status, error);
            }
        });
    });

    // منوی کشویی تنظیمات دستی در بهینه‌سازی مقالات
    $('.toggle-optimizer-settings').on('click', function(e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        $('#optimizer-settings-row-' + postId).slideToggle(250);
    });

    // ۲. سئو و بهینه‌سازی مقاله قدیمی با دکمه جادویی
    $('.optimize-single-post').on('click', function() {
        var btn = $(this);
        var postId = btn.data('post-id');
        var keywordInput = $('#keyword-' + postId);
        var keyword = keywordInput.val();
        var statusCell = $('#status-' + postId);
        var consoleId = 'optimizer-console';

        // واکشی پرومپت و لینک‌های اختصاصی برای این مقاله
        var customPrompt = $('#prompt-' + postId).val() || '';
        var customLinks = $('#links-' + postId).val() || '';

        if (!keyword) {
            alert('لطفاً ابتدا کلمه کلیدی را برای این مقاله وارد کنید.');
            keywordInput.focus();
            return;
        }

        $('#' + consoleId).html(''); // پاکسازی کنسول
        logToConsole(consoleId, 'شروع بهینه‌سازی متنی مقاله با شناسه: ' + postId);
        logToConsole(consoleId, 'کلمه کلیدی تمرکزی: ' + keyword);
        if (customPrompt) logToConsole(consoleId, 'دستورالعمل دستی: ' + customPrompt);
        if (customLinks) logToConsole(consoleId, 'لینک‌های سفارشی: ' + customLinks);

        btn.prop('disabled', true);
        statusCell.html('<div class="smart-ai-spinner"></div> در حال تحلیل و بهبود سئو متنی...');

        var payload = {
            action: 'smart_ai_optimize_post',
            security: smart_ai_params.nonce,
            post_id: postId,
            keyword: keyword,
            custom_prompt: customPrompt,
            custom_links: customLinks
        };

        logToConsole(consoleId, 'ارسال درخواست AJAX به سرور...');

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: payload,
            success: function(response) {
                btn.prop('disabled', false);
                logToConsole(consoleId, 'پاسخ سرور دریافت شد: ' + JSON.stringify(response));

                if (response.success) {
                    logToConsole(consoleId, '✨ مقاله با موفقیت بهینه‌سازی شد! متادیتا و قالب المنتور با موفقیت آپدیت شدند.');
                    statusCell.html('<span style="color: green; font-weight: bold;">✔ بهینه‌سازی شد (تیک سبز سئو ست شد!)</span>');
                    alert(response.data.message);
                } else {
                    logToConsole(consoleId, '❌ خطا در فرآیند بهینه‌سازی: ' + (response.data ? response.data.message : 'خطای سرور'));
                    statusCell.html('<span style="color: red;">❌ خطا در بهینه‌سازی</span>');
                    alert('خطا: ' + (response.data ? response.data.message : 'خطای سرور'));
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                statusCell.html('<span style="color: red;">❌ خطای ارتباطی</span>');

                logToConsole(consoleId, '⚠️ خطای شبکه در بهینه‌سازی رخ داد!');
                logToConsole(consoleId, 'وضعیت خطا: ' + status);
                logToConsole(consoleId, 'جزئیات خطا: ' + error);
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن کامل پاسخ سرور: ' + xhr.responseText);

                console.error('Smart AI SEO - AJAX error:', xhr, status, error);
                alert('خطای سرور رخ داد. لطفاً لاگ‌های داخل کنسول دیباگ پایین صفحه را کپی کرده و بررسی کنید.');
            }
        });
    });

    // ۳. دکمه اختصاصی تصویرساز جادویی ۳‌تایی
    $('.generate-images-post').on('click', function() {
        var btn = $(this);
        var postId = btn.data('post-id');
        var keywordInput = $('#keyword-' + postId);
        var keyword = keywordInput.val();
        var statusCell = $('#status-' + postId);
        var consoleId = 'optimizer-console';

        if (!keyword) {
            alert('لطفاً ابتدا کلمه کلیدی را برای این مقاله وارد کنید تا عکس‌ها مرتبط با آن باشند.');
            keywordInput.focus();
            return;
        }

        $('#' + consoleId).html('');
        logToConsole(consoleId, 'شروع دانلود و درج تصاویر جادویی برای مقاله شناسه: ' + postId);
        logToConsole(consoleId, 'کلمه کلیدی جهت تولید تصاویر: ' + keyword);

        btn.prop('disabled', true);
        statusCell.html('<div class="smart-ai-spinner"></div> در حال تولید و چیدمان تصاویر بدون نوشته...');

        var payload = {
            action: 'smart_ai_generate_images_for_post',
            security: smart_ai_params.nonce,
            post_id: postId,
            keyword: keyword
        };

        logToConsole(consoleId, 'ارسال درخواست تولید تصویر به سرور...');

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: payload,
            success: function(response) {
                btn.prop('disabled', false);
                logToConsole(consoleId, 'پاسخ سرور دریافت شد: ' + JSON.stringify(response));

                if (response.success) {
                    logToConsole(consoleId, '✔ تصاویر با موفقیت تولید و چیده شدند!');
                    statusCell.html('<span style="color: green; font-weight: bold;">✔ تصاویر و شاخص ست شدند!</span>');
                    alert(response.data.message);
                } else {
                    logToConsole(consoleId, '❌ خطا در فرآیند تولید تصویر: ' + (response.data ? response.data.message : 'خطای سرور'));
                    statusCell.html('<span style="color: red;">❌ خطا در ایجاد تصاویر</span>');
                    alert('خطا در بارگذاری تصاویر: ' + (response.data ? response.data.message : 'خطای سرور'));
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                statusCell.html('<span style="color: red;">❌ خطای سرور در دانلود عکس</span>');

                logToConsole(consoleId, '⚠️ خطای شبکه در تصویرساز جادویی رخ داد!');
                logToConsole(consoleId, 'وضعیت خطا: ' + status);
                logToConsole(consoleId, 'جزئیات خطا: ' + error);
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن کامل پاسخ سرور: ' + xhr.responseText);

                console.error('Smart AI SEO - AJAX Failure inside Image Maker:', xhr, status, error);
                alert('خطای اتصال به سرور رخ داد! لطفاً گزارش دیباگ انتهای صفحه را بررسی کنید.');
            }
        });
    });

    // باز و بسته کردن منوی کشویی مدیریت تصاویر تکی
    $('.toggle-image-manager').on('click', function(e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        $('#image-manager-row-' + postId).slideToggle(250);
    });

    // ۴. لود تکی تصاویر گالری مقاله
    $('.load-post-images').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var postId = btn.data('post-id');
        var spinner = $('#gallery-loader-' + postId);
        var galleryGrid = $('#image-manager-gallery-' + postId);
        var consoleBox = $('#image-console-' + postId);
        var consoleId = 'optimizer-console';

        logToConsole(consoleId, 'بارگذاری لیست تصاویر مقاله شناسه: ' + postId);

        btn.prop('disabled', true);
        spinner.show();
        galleryGrid.html('');
        consoleBox.hide().html('');

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: {
                action: 'smart_ai_get_post_images_list',
                security: smart_ai_params.nonce,
                post_id: postId
            },
            success: function(response) {
                btn.prop('disabled', false);
                spinner.hide();
                logToConsole(consoleId, 'پاسخ سرور در لود لیست تصاویر دریافت شد: ' + JSON.stringify(response));

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
                        html += '<div style="font-size: 11px; color: #666;"><strong>...شناسه ضمیمه:</strong> ' + img.id + '</div>';

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
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                spinner.hide();
                consoleBox.html('خطای اتصال به سرور: ' + error).show();

                logToConsole(consoleId, '⚠️ خطای شبکه در دریافت لیست تصاویر!');
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن کامل پاسخ سرور: ' + xhr.responseText);
            }
        });
    });

    // ۵. جایگزینی جادویی و تکی یک عکس خاص و حذف عکس قدیمی از هاست
    $(document).on('click', '.replace-image-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var card = btn.closest('.wp-smart-ai-card');
        var postId = btn.data('post-id');
        var oldId = btn.data('old-id');
        var query = card.find('.new-query-input').val();
        var alt = card.find('.new-alt-input').val();
        var consoleBox = $('#image-console-' + postId);
        var consoleId = 'optimizer-console';

        if (!query) {
            alert('لطفاً کلمه کلیدی انگلیسی برای جستجوی تصویر جدید در Unsplash را وارد کنید.');
            card.find('.new-query-input').focus();
            return;
        }

        logToConsole(consoleId, 'شروع فرآیند جایگزینی تصویر قدیمی با شناسه: ' + oldId + ' برای مقاله: ' + postId);
        logToConsole(consoleId, 'کلمه جستجوی تصویر جدید: ' + query + ' | متن آلت جدید: ' + alt);

        btn.prop('disabled', true).text('در حال تعویض و حذف فایل قبلی...');
        consoleBox.hide().html('');

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
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
                logToConsole(consoleId, 'پاسخ سرور در جایگزینی دریافت شد: ' + JSON.stringify(response));

                if (response.success) {
                    logToConsole(consoleId, '✔ تصویر با موفقیت جایگزین شد و فایل قدیمی از هاست کاملاً پاک گردید.');
                    alert(response.data.message);
                    card.find('img').attr('src', response.data.new_url);
                    btn.data('old-id', response.data.new_id);
                    card.find('.new-query-input').val('');
                } else {
                    logToConsole(consoleId, '❌ خطا در جایگزینی تصویر: ' + (response.data ? response.data.message : 'خطای سرور'));
                    consoleBox.html('خطا در جایگزینی تصویر: ' + (response.data ? response.data.message : 'خطای سرور')).show();
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).text('🔁 جایگزینی جادویی عکس');
                consoleBox.html('خطای اتصال سرور در تعویض تصویر: ' + error).show();

                logToConsole(consoleId, '⚠️ خطای شبکه در جایگزینی تصویر!');
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن کامل پاسخ سرور: ' + xhr.responseText);
            }
        });
    });

    // ۶. مرحله اول: تحلیل و جستجوی رقبای گوگل
    $('#analyze-competitors-btn').on('click', function() {
        var keyword = $('#writer_keyword').val();
        var consoleId = 'writer-console';

        if (!keyword) {
            alert('لطفاً کلمه کلیدی را بنویسید.');
            return;
        }

        $('#' + consoleId).html('');
        logToConsole(consoleId, 'شروع تحلیل رقبا در گوگل برای کلمه کلیدی: ' + keyword);

        var btn = $(this);
        var loader = $('#writer-loader');
        var resultBox = $('#writer-result');
        var compBox = $('#competitor-results-box');

        btn.prop('disabled', true);
        loader.html('<div class="smart-ai-spinner"></div> در حال جستجوی گوگل و تحلیل ۳ رقیب اول...').css('display', 'flex');
        resultBox.hide();
        compBox.hide();

        logToConsole(consoleId, 'ارسال درخواست خزش گوگل و تحلیل محتوا...');

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
                logToConsole(consoleId, 'پاسخ سرور در تحلیل رقبا دریافت شد: ' + JSON.stringify(response));

                if (response.success) {
                    var html = '';
                    var competitors = response.data.competitors;

                    logToConsole(consoleId, 'تحلیل رقبا با موفقیت به پایان رسید. تعداد رقبا یافت شده: ' + competitors.length);

                    competitors.forEach(function(item, index) {
                        html += '<div class="wp-smart-ai-card" style="border-right: 4px solid #440047; padding: 15px; margin-bottom: 10px;">';
                        html += '<h4>رقیب شماره ' + (index + 1) + ': <a href="' + item.url + '" target="_blank">' + item.title + '</a></h4>';
                        html += '<textarea class="competitor-snippet" style="width:100%; height:80px;" readonly>' + item.snippet + '</textarea>';
                        html += '</div>';
                    });

                    $('#competitor-list').html(html);
                    compBox.fadeIn();
                } else {
                    logToConsole(consoleId, '❌ خطا در تحلیل رقبا: ' + (response.data ? response.data.message : 'خطای سرور'));
                    resultBox.addClass('error').html(response.data ? response.data.message : 'خطای سرور').fadeIn();
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                loader.hide();

                logToConsole(consoleId, '⚠️ خطای شبکه در تحلیل رقبا رخ داد!');
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن کامل پاسخ سرور: ' + xhr.responseText);

                resultBox.addClass('error').html('خطا در بارگذاری رقبا. جزئیات کامل در بخش دیباگ پایین صفحه ثبت گردید.').fadeIn();
            }
        });
    });

    // ۷. مرحله دوم: تولید مقاله برتر و نهایی رقابتی (ارسال پرومپت و لینک‌های دستی)
    $('#generate-best-article-btn').on('click', function() {
        var keyword = $('#writer_keyword').val();
        var consoleId = 'writer-console';
        var competitorTexts = '';

        $('.competitor-snippet').each(function() {
            competitorTexts += $(this).val() + "\n---\n";
        });

        var customPrompt = $('#writer_custom_prompt').val() || '';
        var customLinks = $('#writer_custom_links').val() || '';

        logToConsole(consoleId, 'شروع نگارش مقاله رقابتی نهایی با قلم هوش مصنوعی...');
        logToConsole(consoleId, 'کلمه کلیدی: ' + keyword);
        if (customPrompt) logToConsole(consoleId, 'دستورالعمل دستی: ' + customPrompt);
        if (customLinks) logToConsole(consoleId, 'لینک‌های دستی: ' + customLinks);

        var btn = $(this);
        var loader = $('#writer-loader');
        var resultBox = $('#writer-result');

        btn.prop('disabled', true);
        loader.html('<div class="smart-ai-spinner"></div> در حال نگارش مقاله برتر با قلم هوش مصنوعی، ایجاد آلت تگ‌ها و هماهنگی با رنک مث... (ممکن است چند دقیقه طول بکشد)').css('display', 'flex');
        resultBox.hide();

        var payload = {
            action: 'smart_ai_generate_new_post',
            security: smart_ai_params.nonce,
            keyword: keyword,
            competitor_data: competitorTexts,
            custom_prompt: customPrompt,
            custom_links: customLinks
        };

        logToConsole(consoleId, 'ارسال درخواست تولید محتوا به همراه اطلاعات رقبا...');

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: payload,
            success: function(response) {
                btn.prop('disabled', false);
                loader.hide();
                logToConsole(consoleId, 'پاسخ سرور دریافت شد: ' + JSON.stringify(response));

                if (response.success) {
                    logToConsole(consoleId, '🎉 مقاله با موفقیت تولید، تصاویر و شاخص ست شدند و مقاله به عنوان پیش‌نویس ذخیره گردید.');
                    var successHtml = '<h4>🎉 مقاله بی رقیب شما آماده شد!</h4>';
                    successHtml += '<p>' + response.data.message + '</p>';
                    successHtml += '<a href="' + response.data.edit_url + '" class="button button-primary button-large" target="_blank">رفتن به ویرایشگر پیش‌نویس مقاله</a>';
                    resultBox.removeClass('error').html(successHtml).fadeIn();
                } else {
                    logToConsole(consoleId, '❌ خطا در فرآیند تولید مقاله: ' + (response.data ? response.data.message : 'خطای سرور'));
                    resultBox.addClass('error').html(response.data ? response.data.message : 'خطای سرور').fadeIn();
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                loader.hide();

                logToConsole(consoleId, '⚠️ خطای شبکه در نگارش مقاله رقابتی!');
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن پاسخ سرور: ' + xhr.responseText);

                resultBox.addClass('error').html('خطا در تولید مقاله نهایی رقابتی. لطفاً لاگ‌های کنسول دیباگ پایین صفحه را کپی و بررسی کنید.').fadeIn();
            }
        });
    });

    // ۸. پیلار و کلاستر: پیشنهاد خوشه‌های محتوایی
    $('#suggest-clusters-btn').on('click', function() {
        var pillarId = $('#pillar_post_select').val();
        var consoleId = 'pillar-console';

        if (!pillarId) {
            alert('لطفاً یک مقاله مادر (Pillar) انتخاب کنید.');
            return;
        }

        $('#' + consoleId).html('');
        logToConsole(consoleId, 'کشف خوشه‌های پیشنهادی بر اساس مقاله مادر به شناسه: ' + pillarId);

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
                logToConsole(consoleId, 'پاسخ سرور در پیشنهاد کلاسترها دریافت شد: ' + JSON.stringify(response));

                if (response.success) {
                    var clusters = response.data.clusters;
                    var html = '';

                    logToConsole(consoleId, 'خوشه‌ها با موفقیت دریافت شدند. تعداد خوشه‌های پیشنهادی: ' + clusters.length);

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
                    logToConsole(consoleId, '❌ خطا در دریافت کلاسترها: ' + (response.data ? response.data.message : 'خطای سرور'));
                    alert('خطا: ' + (response.data ? response.data.message : 'خطای سرور'));
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false);
                loader.hide();

                logToConsole(consoleId, '⚠️ خطای شبکه در دریافت کلاسترها!');
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن کامل پاسخ سرور: ' + xhr.responseText);

                alert('خطا در ارتباط با سرور رخ داد.');
            }
        });
    });

    // ۹. ایجاد فوری کلاستر و لینک به پیلار
    $(document).on('click', '.create-cluster-post-btn', function() {
        var btn = $(this);
        var title = btn.data('title');
        var keyword = btn.data('keyword');
        var pillarId = btn.data('pillar-id');
        var consoleId = 'pillar-console';

        logToConsole(consoleId, 'ایجاد اتوماتیک مقاله کلاستر با عنوان: "' + title + '" برای مقاله مادر: ' + pillarId);

        btn.prop('disabled', true).text('در حال ساخت محتوا...');

        $.ajax({
            url: smart_ai_params.ajax_url,
            type: 'POST',
            timeout: 300000,
            data: {
                action: 'smart_ai_generate_new_post',
                security: smart_ai_params.nonce,
                keyword: keyword,
                competitor_data: 'نگارش محتوای کلاستر مربوط به مقاله مادر شماره ' + pillarId,
                is_pillar: 1
            },
            success: function(response) {
                logToConsole(consoleId, 'پاسخ سرور در ایجاد کلاستر دریافت شد: ' + JSON.stringify(response));

                if (response.success) {
                    var newPostId = response.data.post_id;
                    logToConsole(consoleId, 'ساخت موفقیت‌آمیز مقاله کلاستر با شناسه جدید: ' + newPostId);
                    logToConsole(consoleId, 'شروع لینک‌سازی متقابل (انکرتکست: "' + keyword + '") به مقاله پیلار شناسه: ' + pillarId);

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
                            logToConsole(consoleId, 'پاسخ سرور در لینک‌سازی دریافت شد: ' + JSON.stringify(linkResponse));
                            btn.html('✔ لینک‌سازی شد!').removeClass('button-primary').css('background', '#46b450');
                            alert('مقاله فرعی با موفقیت ایجاد شد، عکس‌ها دانلود شدند و لینک‌سازی متقابل به پیلار به اتم رسید!');
                        },
                        error: function(xhr, status, error) {
                            logToConsole(consoleId, '⚠️ خطای شبکه در ثبت لینک کلاستر!');
                            logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                        }
                    });
                } else {
                    btn.prop('disabled', false).text('تلاش مجدد');
                    logToConsole(consoleId, '❌ خطا در ایجاد کلاستر: ' + response.data.message);
                    alert('خطا: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                btn.prop('disabled', false).text('تلاش مجدد');
                logToConsole(consoleId, '⚠️ خطای شبکه در ایجاد کلاستر!');
                logToConsole(consoleId, 'کد وضعیت HTTP: ' + xhr.status);
                logToConsole(consoleId, 'متن کامل پاسخ سرور: ' + xhr.responseText);
            }
        });
    });

});
