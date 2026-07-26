<?php get_header();
$photo = st_get_theme_option('st_photo');
$photo_url = $photo ? wp_get_attachment_image_url($photo, 'large') : '';

// If page builder has taken over, render content directly for complete customization control
if (have_posts()) :
    while (have_posts()) : the_post();
        if (class_exists('\\Elementor\\Plugin') && \Elementor\Plugin::$instance->documents->get(get_the_ID())->is_built_with_elementor()) {
            echo '<main class="st-container st-builder-section">';
            the_content();
            echo '</main>';
            get_footer();
            exit;
        }
    endwhile;
endif;
?>
<main class="st-home st-container" role="main">

  <!-- ===== HERO ===== -->
  <section class="st-hero" aria-label="معرفی">
      <div class="st-hero-grid">
          <div class="st-glass-card st-hero-copy">
              <span class="st-chip"><i class="fa-regular fa-user"></i> <?php echo esc_html(st_t('رزومه و پورتفولیو','Resume & Portfolio','Lebenslauf & Portfolio')); ?></span>
              <h1><?php echo esc_html(st_get_theme_option('st_name_fa', 'سپهر')); ?> <span class="highlight"><?php echo esc_html(st_get_theme_option('st_lastname_fa', 'طالبی')); ?></span></h1>
              <div class="st-title-role"><?php echo esc_html(st_get_theme_option('st_title', 'طراح و برنامه‌نویس وردپرس | متخصص ووکامرس و هوش مصنوعی')); ?></div>
              <div class="st-hero-meta">
                  <span><i class="fa-regular fa-calendar"></i> <?php echo esc_html(st_t('متولد: ','Born: ','Geboren: ')) . esc_html(st_get_theme_option('st_birthdate', '۱۳۸۱/۰۵/۰۱')); ?></span>
                  <span><i class="fa-regular fa-location-dot"></i> <?php echo esc_html(st_get_theme_option('st_location', 'اصفهان، ایران')); ?></span>
                  <span><i class="fa-regular fa-envelope"></i> <?php echo esc_html(st_get_theme_option('st_email', 'sphrt23@gmail.com')); ?></span>
                  <?php $phone = st_get_theme_option('st_phone'); if ($phone) : ?>
                    <span><i class="fa-regular fa-phone"></i> <?php echo esc_html($phone); ?></span>
                  <?php endif; ?>
              </div>
              <div class="st-hero-bio">
                  <?php echo esc_html(st_get_theme_option('st_about', 'متخصص در وردپرس، ووکامرس و طراحی وب‌سایت‌های فروشگاهی. دارای تجربه موفق در طراحی و پیاده‌سازی پلتفرم‌های تحت وب، مدیریت پروژه‌های فناوری اطلاعات و سئو مقدماتی. متفکر تحلیلی و راهبردی با توانایی در سازگاری سریع با فناوری‌های نوظهور. متعهد به ارائه راهکارهای کاربردمحور و نوآورانه با برنامه‌ریزی برای دستیابی به نتایج ملموس و توسعه کسب‌وکار.')); ?>
              </div>
              <div class="st-hero-cta">
                  <a class="st-button" href="#projects"><i class="fa-regular fa-folder-open"></i> <?php echo esc_html(st_t('مشاهده پروژه‌ها','View Projects','Projekte ansehen')); ?></a>
                  <?php $cv_url = st_get_theme_option('st_cv_url', '#'); if ($cv_url && $cv_url !== '#') : ?>
                    <a class="st-button st-button-secondary" href="<?php echo esc_url($cv_url); ?>"><i class="fa-regular fa-file-pdf"></i> <?php echo esc_html(st_t('دانلود رزومه','Download CV','Lebenslauf laden')); ?></a>
                  <?php endif; ?>
              </div>
          </div>
          <div class="st-glass-card st-hero-photo">
              <?php if ($photo_url) : ?>
                <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr(st_get_theme_option('st_name', 'سپهر طالبی')); ?>" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 12px; box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3);">
              <?php else : ?>
                <div class="st-avatar-placeholder"><?php echo esc_html(st_t('س','S','S')); ?></div>
              <?php endif; ?>
              <div class="st-name-badge"><?php echo esc_html(st_get_theme_option('st_name', 'سپهر طالبی')); ?></div>
              <div style="font-size:0.8rem; color:#94a3b8; margin-top:4px;"><?php echo esc_html(st_t('طراح و برنامه‌نویس','Designer & Developer','Designer & Entwickler')); ?></div>
              <div style="display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; justify-content:center;">
                  <span class="st-tag st-tag-primary"><i class="fa-regular fa-check"></i> <?php echo esc_html(st_t('وردپرس','WordPress','WordPress')); ?></span>
                  <span class="st-tag st-tag-primary"><i class="fa-regular fa-check"></i> <?php echo esc_html(st_t('ووکامرس','WooCommerce','WooCommerce')); ?></span>
                  <span class="st-tag st-tag-primary"><i class="fa-regular fa-check"></i> PHP</span>
              </div>
          </div>
      </div>
  </section>

  <!-- ===== SKILLS ===== -->
  <section class="st-section" id="skills" aria-label="مهارت‌ها">
      <div class="st-section-head">
          <h2><?php echo esc_html(st_t('مهارت‌های تخصصی','Professional Skills','Fähigkeiten')); ?></h2>
          <span style="color:#94a3b8; font-size:0.85rem;"><?php echo esc_html(st_t('۲۵+ مهارت','25+ Skills','25+ Fähigkeiten')); ?></span>
      </div>
      <div class="st-glass-card" style="padding:28px 30px;">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
              <div class="st-skill-category">
                  <h4><i class="fa-brands fa-wordpress" style="color:#21759b;"></i> <?php echo esc_html(st_t('وردپرس','WordPress','WordPress')); ?></h4>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('وردپرس','WordPress','WordPress')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('ووکامرس','WooCommerce','WooCommerce')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('طراحی قالب','Theme Design','Theme-Design')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('توسعه افزونه','Plugin Dev','Plugin-Entwicklung')); ?></span>
              </div>
              <div class="st-skill-category">
                  <h4><i class="fa-solid fa-code" style="color:#6366f1;"></i> <?php echo esc_html(st_t('برنامه‌نویسی','Programming','Programmierung')); ?></h4>
                  <span class="st-skill-tag">PHP</span>
                  <span class="st-skill-tag">SQL</span>
                  <span class="st-skill-tag">CSS</span>
                  <span class="st-skill-tag">JavaScript</span>
                  <span class="st-skill-tag">HTML</span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('سی شارپ','C#','C#')); ?></span>
              </div>
              <div class="st-skill-category">
                  <h4><i class="fa-solid fa-pen-fancy" style="color:#a855f7;"></i> <?php echo esc_html(st_t('طراحی و محتوا','Design & Content','Design & Inhalt')); ?></h4>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('تولید محتوا','Content Creation','Inhaltserstellung')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('ویرایش عکس','Photo Editing','Fotobearbeitung')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('ویرایش ویدیو','Video Editing','Videobearbeitung')); ?></span>
                  <span class="st-skill-tag">Canva</span>
                  <span class="st-skill-tag">InShot</span>
                  <span class="st-skill-tag">CapCut</span>
              </div>
              <div class="st-skill-category">
                  <h4><i class="fa-solid fa-brain" style="color:#f59e0b;"></i> <?php echo esc_html(st_t('سایر','Other','Andere')); ?></h4>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('هوش مصنوعی','AI','KI')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('روابط عمومی','PR','PR')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('مدیریت زمان','Time Mgmt','Zeitmanagement')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('خلاقیت','Creativity','Kreativität')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('کار تیمی','Teamwork','Teamarbeit')); ?></span>
                  <span class="st-skill-tag"><?php echo esc_html(st_t('سئو','SEO','SEO')); ?></span>
              </div>
          </div>

          <?php
          // Query dynamic skills added through post types if any exist
          $skills_query = st_query_items('st_skill', 12);
          if ($skills_query->have_posts()) :
          ?>
            <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.1);">
                <h4 style="font-size:0.9rem; color:#94a3b8; margin-bottom:12px;"><i class="fa-solid fa-star"></i> <?php echo esc_html(st_t('مهارت‌های ثبت شده','Registered Skills','Registrierte Fähigkeiten')); ?></h4>
                <div class="st-grid st-grid-3">
                  <?php while ($skills_query->have_posts()) : $skills_query->the_post();
                    $level = get_post_meta(get_the_ID(), 'st_skill_level', true);
                    $url = get_post_meta(get_the_ID(), 'st_meta_url', true);
                    $dl = get_post_meta(get_the_ID(), 'st_meta_download_url', true);
                  ?>
                    <div class="st-glass-card st-card" style="padding: 16px;">
                      <?php if (has_post_thumbnail()) : ?><div class="st-featured-image" style="margin-bottom:10px;"><?php the_post_thumbnail('medium'); ?></div><?php endif; ?>
                      <h3 style="font-size:1rem; margin-bottom:6px;"><?php the_title(); ?></h3>
                      <?php if ($level) : ?>
                        <div class="st-skill-bar"><span style="width: <?php echo esc_attr($level); ?>%"></span></div>
                      <?php endif; ?>
                      <p style="font-size:0.85rem; line-height:1.6; margin-bottom:10px;"><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 12)); ?></p>
                      <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <?php if ($url) : ?><a class="st-link" href="<?php echo esc_url($url); ?>" style="font-size:0.8rem; padding: 6px 12px; border-radius: 8px;"><?php echo esc_html(st_t('لینک','Link','Link')); ?></a><?php endif; ?>
                        <?php if ($dl) : ?><a class="st-link st-button-secondary" href="<?php echo esc_url($dl); ?>" download style="font-size:0.8rem; padding: 6px 12px; border-radius: 8px;"><?php echo esc_html(st_t('دانلود','Download','Download')); ?></a><?php endif; ?>
                      </div>
                    </div>
                  <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
          <?php endif; ?>

          <div style="margin-top:16px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.1);">
              <h4 style="font-size:0.85rem; color:#64748b; margin-bottom:6px;"><i class="fa-regular fa-language"></i> <?php echo esc_html(st_t('زبان‌ها','Languages','Sprachen')); ?></h4>
              <span class="st-skill-tag" style="background:#eef2ff; color:#4f46e5;"><?php echo esc_html(st_t('فارسی (مادری)','Persian (Native)','Persisch (Muttersprache)')); ?></span>
              <span class="st-skill-tag" style="background:#dcfce7; color:#16a34a;"><?php echo esc_html(st_t('انگلیسی (پیشرفته)','English (Advanced)','Englisch (Fortgeschritten)')); ?></span>
              <span class="st-skill-tag" style="background:#fef08a20; color:#ca8a04;"><?php echo esc_html(st_t('آلمانی (B1)','German (B1)','Deutsch (B1)')); ?></span>
          </div>
      </div>
  </section>

  <!-- ===== EXPERIENCE ===== -->
  <section class="st-section" id="experience" aria-label="سوابق شغلی">
      <div class="st-section-head"><h2><?php echo esc_html(st_t('سوابق شغلی','Work Experience','Berufserfahrung')); ?></h2></div>
      <div class="st-grid st-grid-2">
          <?php
          $exp_query = st_query_items('st_experience', 6);
          if ($exp_query->have_posts()) : while ($exp_query->have_posts()) : $exp_query->the_post();
              $org = get_post_meta(get_the_ID(), 'st_meta_org', true);
              $role = get_post_meta(get_the_ID(), 'st_meta_role', true);
              $date = get_post_meta(get_the_ID(), 'st_meta_date', true);
              $url = get_post_meta(get_the_ID(), 'st_meta_url', true);
              $dl = get_post_meta(get_the_ID(), 'st_meta_download_url', true);
          ?>
              <div class="st-glass-card st-card">
                  <?php if (has_post_thumbnail()) : ?><div class="st-featured-image" style="margin-bottom:14px;"><?php the_post_thumbnail('medium_large'); ?></div><?php endif; ?>
                  <h3><?php the_title(); ?></h3>
                  <?php if ($role || $org || $date) : ?>
                    <p style="color:#6366f1; font-weight:500; font-size: 0.9rem;">
                      <?php if ($role) echo esc_html($role); ?>
                      <?php if ($org) echo ' | ' . esc_html($org); ?>
                      <?php if ($date) echo ' | ' . esc_html($date); ?>
                    </p>
                  <?php endif; ?>
                  <p><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 22)); ?></p>
                  <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top: 10px;">
                      <?php if ($url) : ?><a class="st-link" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener"><?php echo esc_html(st_t('مشاهده','View','Ansehen')); ?></a><?php endif; ?>
                      <?php if ($dl) : ?><a class="st-link st-button-secondary" href="<?php echo esc_url($dl); ?>" download><?php echo esc_html(st_t('دانلود','Download','Download')); ?></a><?php endif; ?>
                  </div>
              </div>
          <?php endwhile; wp_reset_postdata(); else : ?>
              <!-- Fallback Experience standard details -->
              <div class="st-glass-card st-card">
                  <h3><?php echo esc_html(st_t('سایت ادمین و طراح','Site Admin & Designer','Site Admin & Designer')); ?></h3>
                  <p style="color:#6366f1; font-weight:500;"><?php echo esc_html(st_t('دورکاری و حضوری | تهران | فروردین ۱۴۰۲ - اکنون','Remote & In-person | Tehran | Apr 2023 - Present','Remote & In-person | Teheran | Apr 2023 - Heute')); ?></p>
                  <p><?php echo esc_html(st_t('طراحی وب‌سایت با وردپرس، کدنویسی سفارشی با PHP، مدیریت و سئو وب‌سایت‌ها، تولید محتوا و طراحی بنر. استفاده از هوش مصنوعی در توسعه و بهینه‌سازی.','Website design with WordPress, custom PHP development, site management & SEO, content creation. AI utilization in workflows.','Website-Design mit WordPress, maßgeschneiderte PHP-Entwicklung, SEO und Content-Erstellung.')); ?></p>
                  <div style="margin-top:6px;">
                      <span class="st-tag">وردپرس</span>
                      <span class="st-tag">PHP</span>
                      <span class="st-tag">سئو</span>
                  </div>
              </div>
              <div class="st-glass-card st-card">
                  <h3><?php echo esc_html(st_t('ادمین اینستاگرام','Instagram Admin','Instagram Admin')); ?></h3>
                  <p style="color:#6366f1; font-weight:500;"><?php echo esc_html(st_t('کلینیک دندانپزشکی سام | فروردین ۱۴۰۱ - خرداد ۱۴۰۲','Sam Dental Clinic | Mar 2022 - Jun 2023','Sam Zahnklinik | Mär 2022 - Jun 2023')); ?></p>
                  <p><?php echo esc_html(st_t('تولید محتوا، کمپین‌های تبلیغاتی، پاسخ به پیام‌های مستقیم، افزایش آگاهی و مشارکت بیماران. تقویت مهارت‌های ارتباطی و خلاقیت در فضای دیجیتال.','Content creation, ad campaigns, direct messaging management, increasing patient engagement.','Inhaltserstellung, Werbekampagnen, direktes Messaging, Erhöhung des Patientenengagements.')); ?></p>
                  <div style="margin-top:6px;">
                      <span class="st-tag">تولید محتوا</span>
                      <span class="st-tag">روابط عمومی</span>
                      <span class="st-tag">اینستاگرام</span>
                  </div>
              </div>
          <?php endif; ?>
      </div>
  </section>

  <!-- ===== EDUCATION ===== -->
  <section class="st-section" id="education" aria-label="تحصیلات">
      <div class="st-section-head"><h2><?php echo esc_html(st_t('تحصیلات','Education','Ausbildung')); ?></h2></div>
      <div class="st-grid st-grid-2">
          <?php
          $edu_query = st_query_items('st_education', 6);
          if ($edu_query->have_posts()) : while ($edu_query->have_posts()) : $edu_query->the_post();
              $org = get_post_meta(get_the_ID(), 'st_meta_org', true);
              $role = get_post_meta(get_the_ID(), 'st_meta_role', true);
              $date = get_post_meta(get_the_ID(), 'st_meta_date', true);
              $url = get_post_meta(get_the_ID(), 'st_meta_url', true);
              $dl = get_post_meta(get_the_ID(), 'st_meta_download_url', true);
          ?>
              <div class="st-glass-card st-card">
                  <?php if (has_post_thumbnail()) : ?><div class="st-featured-image" style="margin-bottom:14px;"><?php the_post_thumbnail('medium_large'); ?></div><?php endif; ?>
                  <h3><?php the_title(); ?></h3>
                  <?php if ($role || $org || $date) : ?>
                    <p style="color:#6366f1; font-weight:500; font-size: 0.9rem;">
                      <?php if ($role) echo esc_html($role); ?>
                      <?php if ($org) echo ' | ' . esc_html($org); ?>
                      <?php if ($date) echo ' | ' . esc_html($date); ?>
                    </p>
                  <?php endif; ?>
                  <p><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 22)); ?></p>
                  <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;">
                      <?php if ($url) : ?><a class="st-link" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener"><?php echo esc_html(st_t('مشاهده','View','Ansehen')); ?></a><?php endif; ?>
                      <?php if ($dl) : ?><a class="st-link st-button-secondary" href="<?php echo esc_url($dl); ?>" download><?php echo esc_html(st_t('دانلود','Download','Download')); ?></a><?php endif; ?>
                  </div>
              </div>
          <?php endwhile; wp_reset_postdata(); else : ?>
              <!-- Fallback Education Standard details -->
              <div class="st-glass-card st-card">
                  <h3><?php echo esc_html(st_t('کارشناسی مهندسی پزشکی','B.Sc. in Biomedical Engineering','B.Sc. in Biomedizintechnik')); ?></h3>
                  <p style="color:#6366f1; font-weight:500;"><?php echo esc_html(st_t('گرایش بیوالکتریک | علوم پزشکی تهران | مهر ۱۳۹۹ - خرداد ۱۴۰۴','Bio-electric | Tehran Medical Sciences | Oct 2020 - Jun 2025','Bioelektrik | Medizinische Wissenschaften Teheran | Okt 2020 - Jun 2025')); ?></p>
                  <p><?php echo esc_html(st_t('معدل ۱۷.۲، پروژه پایان‌نامه در زمینه سیستم‌های هوشمند پزشکی.','GPA 17.2, Thesis project in medical intelligence systems.','Notendurchschnitt 17,2, Diplomarbeit im Bereich medizinischer intelligenter Systeme.')); ?></p>
              </div>
              <div class="st-glass-card st-card">
                  <h3><?php echo esc_html(st_t('مدرک دستیار دندانپزشکی','Dental Assistant Degree','Zahnarzt-Assistent-Zertifikat')); ?></h3>
                  <p style="color:#6366f1; font-weight:500;"><?php echo esc_html(st_t('مرکز دانش پژوهان پرستاری | مهر ۱۴۰۱','Nursing Scholar Institute | Oct 2022','Pflegewissenschaftliches Institut | Okt 2022')); ?></p>
                  <p><?php echo esc_html(st_t('دوره تخصصی دستیاری دندانپزشکی با نمره عالی.','Specialized assistant course completed with high grade.','Spezialisierter Assistenzkurs mit hervorragender Note abgeschlossen.')); ?></p>
              </div>
          <?php endif; ?>
      </div>
  </section>

  <!-- ===== PROJECTS ===== -->
  <section class="st-section" id="projects" aria-label="پروژه‌ها">
      <div class="st-section-head">
          <h2><?php echo esc_html(st_t('پروژه‌های شاخص','Featured Projects','Featured Projekte')); ?></h2>
          <span style="color:#94a3b8; font-size:0.85rem;"><?php echo esc_html(st_t('۱۱ پروژه','11 Projects','11 Projekte')); ?></span>
      </div>
      <div class="st-grid st-grid-3">
          <?php
          $project_query = st_query_items('st_project', 12);
          if ($project_query->have_posts()) : while ($project_query->have_posts()) : $project_query->the_post();
              $purl = get_post_meta(get_the_ID(), 'st_project_url', true);
              $pstack = get_post_meta(get_the_ID(), 'st_project_stack', true);
              $pyear = get_post_meta(get_the_ID(), 'st_project_year', true);
              $pdl = get_post_meta(get_the_ID(), 'st_project_download_url', true);
          ?>
              <article class="st-glass-card st-card st-project-card" itemscope itemtype="https://schema.org/CreativeWork">
                  <?php if (has_post_thumbnail()) : ?><div class="st-project-thumb" style="margin-bottom:14px;"><?php the_post_thumbnail('medium_large'); ?></div><?php endif; ?>
                  <h3 itemprop="name"><?php the_title(); ?></h3>
                  <p itemprop="description"><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20)); ?></p>

                  <?php if ($pyear || $pstack) : ?>
                    <div class="st-meta-row" style="margin-top:10px; margin-bottom:10px;">
                        <?php if ($pyear) : ?><span><i class="fa-regular fa-calendar"></i> <?php echo esc_html($pyear); ?></span><?php endif; ?>
                        <?php if ($pstack) : ?><span><i class="fa-regular fa-user"></i> <?php echo esc_html($pstack); ?></span><?php endif; ?>
                    </div>
                  <?php endif; ?>

                  <div class="st-project-links">
                      <?php if ($purl) : ?><a href="<?php echo esc_url($purl); ?>" target="_blank" rel="noopener noreferrer"><i class="fa-regular fa-globe"></i> <?php echo esc_html(str_replace(array('https://','http://','www.'), '', $purl)); ?></a><?php endif; ?>
                      <?php if ($pdl) : ?><a href="<?php echo esc_url($pdl); ?>" download><i class="fa-regular fa-file-arrow-down"></i> <?php echo esc_html(st_t('دانلود فایل','Download File','Datei herunterladen')); ?></a><?php endif; ?>
                  </div>
              </article>
          <?php endwhile; wp_reset_postdata(); else : ?>
              <!-- Fallback custom projects -->
              <article class="st-glass-card st-card st-project-card" itemscope itemtype="https://schema.org/CreativeWork">
                  <h3 itemprop="name"><?php echo esc_html(st_t('خیریه محبت','Mohabbat Charity','Wohltätigkeitsverein Mohabbat')); ?></h3>
                  <p itemprop="description"><?php echo esc_html(st_t('طراحی، پشتیبانی و مدیریت وب‌سایت و کانال ایتا. تولید محتوای سئو شده، توسعه افزونه ویژه کمپین‌های خیریه. بیش از ۱ میلیون بازدید بدون تبلیغات پولی.','Website design, support, and management. SEO-optimized content, custom charity campaign plugin. Over 1M unpaid visits.','Website-Design, Betreuung und Verwaltung. Suchmaschinenoptimierte Inhalte, Spendenkampagnen-Plugin. Over 1M unbezahlte Aufrufe.')); ?></p>
                  <div class="st-meta-row">
                      <span><i class="fa-regular fa-calendar"></i> <?php echo esc_html(st_t('آذر ۱۴۰۳','Dec 2024','Dez 2024')); ?></span>
                      <span><i class="fa-regular fa-user"></i> <?php echo esc_html(st_t('کارفرما','Client','Auftraggeber')); ?></span>
                  </div>
                  <div style="display:flex; flex-wrap:wrap; gap:4px; margin:6px 0;">
                      <span class="st-tag">وردپرس</span>
                      <span class="st-tag">سئو</span>
                      <span class="st-tag st-tag-success">فعال</span>
                  </div>
                  <div class="st-project-links">
                      <a href="https://mohabbatteh.com" target="_blank" rel="noopener noreferrer"><i class="fa-regular fa-globe"></i> mohabbatteh.com</a>
                  </div>
              </article>

              <article class="st-glass-card st-card st-project-card">
                  <h3><?php echo esc_html(st_t('دفتر نمایندگی جنت‌آباد','Jannatabad Office','Jannatabad Büro')); ?></h3>
                  <p><?php echo esc_html(st_t('طراحی وب‌سایت تک‌صفحه‌ای با فروشگاه آنلاین، درگاه پرداخت زرین‌پال، گواهی e-Namad، سئوی محلی پیشرفته و GEO-SEO برای نمایش در ابزارهای هوش مصنوعی.','One-page WooCommerce design with local SEO and GEO-SEO optimized for AI engines.','One-page WooCommerce Design mit lokalem SEO und GEO-SEO optimiert für KI-Suchmaschinen.')); ?></p>
                  <div class="st-meta-row">
                      <span><i class="fa-regular fa-calendar"></i> <?php echo esc_html(st_t('تیر ۱۴۰۳','Jul 2024','Jul 2024')); ?></span>
                      <span><i class="fa-regular fa-user"></i> <?php echo esc_html(st_t('کارفرما','Client','Auftraggeber')); ?></span>
                  </div>
                  <div style="display:flex; flex-wrap:wrap; gap:4px; margin:6px 0;">
                      <span class="st-tag">ووکامرس</span>
                      <span class="st-tag">سئو</span>
                      <span class="st-tag st-tag-success">تکمیل شده</span>
                  </div>
                  <div class="st-project-links">
                      <a href="https://Megaran-soft.ir" target="_blank" rel="noopener noreferrer"><i class="fa-regular fa-globe"></i> Megaran-soft.ir</a>
                  </div>
              </article>

              <article class="st-glass-card st-card st-project-card">
                  <h3><?php echo esc_html(st_t('برند قهوه لیائوفو','Liapho Coffee Brand','Kaffeemarke Liapho')); ?></h3>
                  <p><?php echo esc_html(st_t('طراحی وب‌سایت با هویت برند منحصر‌به‌فرد، توسعه اپلیکیشن وب اتوماسیون فروش با سیستم تیکت، چت خصوصی، گروه‌بندی کاربران و مدیریت فاکتور.','Website design with unique brand identity, sales automation webapp with ticketing, private chat, user groupings, invoices.','Website-Design mit einzigartiger Markenidentität, Verkaufsautomatisierung, Ticketsystem, privaten Chats.')); ?></p>
                  <div class="st-meta-row">
                      <span><i class="fa-regular fa-calendar"></i> ۱۴۰۳</span>
                      <span><i class="fa-regular fa-user"></i> <?php echo esc_html(st_t('کارفرما','Client','Auftraggeber')); ?></span>
                  </div>
                  <div style="display:flex; flex-wrap:wrap; gap:4px; margin:6px 0;">
                      <span class="st-tag">PHP</span>
                      <span class="st-tag">وردپرس</span>
                      <span class="st-tag st-tag-success">تکمیل شده</span>
                  </div>
                  <div class="st-project-links">
                      <a href="https://Liaphocoffee.com" target="_blank" rel="noopener noreferrer"><i class="fa-regular fa-globe"></i> Liaphocoffee.com</a>
                  </div>
              </article>
          <?php endif; ?>
      </div>
  </section>

  <!-- ===== CERTIFICATES ===== -->
  <section class="st-section" id="certificates" aria-label="گواهینامه‌ها">
      <div class="st-section-head"><h2><?php echo esc_html(st_t('گواهینامه‌ها','Certificates','Zertifikate')); ?></h2></div>
      <div class="st-grid st-grid-2">
          <?php
          $cert_query = st_query_items('st_certificate', 6);
          if ($cert_query->have_posts()) : while ($cert_query->have_posts()) : $cert_query->the_post();
              $org = get_post_meta(get_the_ID(), 'st_meta_org', true);
              $role = get_post_meta(get_the_ID(), 'st_meta_role', true);
              $date = get_post_meta(get_the_ID(), 'st_meta_date', true);
              $url = get_post_meta(get_the_ID(), 'st_meta_url', true);
              $dl = get_post_meta(get_the_ID(), 'st_meta_download_url', true);
          ?>
              <div class="st-glass-card st-card">
                  <?php if (has_post_thumbnail()) : ?><div class="st-featured-image" style="margin-bottom:14px;"><?php the_post_thumbnail('medium_large'); ?></div><?php endif; ?>
                  <h3><i class="fa-regular fa-certificate" style="color:#6366f1;"></i> <?php the_title(); ?></h3>
                  <?php if ($role || $org || $date) : ?>
                    <p style="color:#6366f1; font-weight:500; font-size:0.9rem;">
                      <?php if ($role) echo esc_html($role); ?>
                      <?php if ($org) echo ' | ' . esc_html($org); ?>
                      <?php if ($date) echo ' | ' . esc_html($date); ?>
                    </p>
                  <?php endif; ?>
                  <p><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 22)); ?></p>
                  <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;">
                      <?php if ($url) : ?><a class="st-link" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener"><?php echo esc_html(st_t('مشاهده','View','Ansehen')); ?></a><?php endif; ?>
                      <?php if ($dl) : ?><a class="st-link st-button-secondary" href="<?php echo esc_url($dl); ?>" download><?php echo esc_html(st_t('دانلود','Download','Download')); ?></a><?php endif; ?>
                  </div>
              </div>
          <?php endwhile; wp_reset_postdata(); else : ?>
              <!-- Fallback certificates dynamic details -->
              <div class="st-glass-card st-card">
                  <h3><i class="fa-regular fa-certificate" style="color:#6366f1;"></i> <?php echo esc_html(st_t('مدرک دستیار دندانپزشکی','Dental Assistant Degree','Zahnarzt-Assistent-Zertifikat')); ?></h3>
                  <p style="color:#6366f1; font-weight:500;"><?php echo esc_html(st_t('مرکز دانش پژوهان پرستاری | مهر ۱۴۰۱','Nursing Scholar Institute | Oct 2022','Pflegewissenschaftliches Institut | Okt 2022')); ?></p>
                  <p><?php echo esc_html(st_t('دوره تخصصی دستیاری با نمره عالی.','Specialized assistant course completed with high grade.','Spezialisierter Assistenzkurs mit hervorragender Note abgeschlossen.')); ?></p>
              </div>
              <div class="st-glass-card st-card">
                  <h3><i class="fa-regular fa-certificate" style="color:#6366f1;"></i> <?php echo esc_html(st_t('دوره‌های تخصصی وردپرس','Specialized WordPress Training','Spezialisierte WordPress-Kurse')); ?></h3>
                  <p style="color:#6366f1; font-weight:500;"><?php echo esc_html(st_t('خودآموز و تجربی | ۱۴۰۲ - اکنون','Self-taught & Experimental | 2023 - Present','Autodidaktisch & Experimentell | 2023 - Heute')); ?></p>
                  <p><?php echo esc_html(st_t('طراحی قالب، توسعه افزونه، بهینه‌سازی سئو و امنیت وردپرس.','Theme design, plugin development, SEO optimization, and security.','Theme-Design, Plugin-Entwicklung, SEO-Optimierung und Sicherheit.')); ?></p>
              </div>
          <?php endif; ?>
      </div>
  </section>

</main>
<?php get_footer(); ?>
