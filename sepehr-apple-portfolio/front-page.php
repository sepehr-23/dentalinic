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
<main class="st-home">
  <!-- Hero Section -->
  <section class="st-hero st-container">
    <div class="st-hero-grid">
      <div class="st-glass-card st-hero-copy">
        <span class="st-chip"><?php echo esc_html(st_t('رزومه و پورتفولیو','Resume & Portfolio','Lebenslauf & Portfolio')); ?></span>
        <h1><?php echo esc_html(st_get_theme_option('st_name', 'سپهر طالبی')); ?></h1>
        <h2><?php echo esc_html(st_get_theme_option('st_title', st_t('پورتفولیو و رزومه شخصی','Personal Portfolio & Resume','Persönliches Portfolio & Lebenslauf'))); ?></h2>
        <p><?php echo esc_html(st_get_theme_option('st_about')); ?></p>
        <div class="st-hero-meta">
          <span><?php echo esc_html(st_t('سن: ','Age: ','Alter: ')) . esc_html(st_get_theme_option('st_age', '23')); ?></span>
          <span><?php echo esc_html(st_t('موقعیت: ','Location: ','Standort: ')) . esc_html(st_get_theme_option('st_location', 'ایران')); ?></span>
          <span><?php echo esc_html(st_get_theme_option('st_email', 'hello@example.com')); ?></span>
        </div>
        <div class="st-hero-cta">
          <a class="st-button" href="#projects"><?php echo esc_html(st_t('مشاهده پروژه‌ها','View Projects','Projekte ansehen')); ?></a>
          <?php $cv_url = st_get_theme_option('st_cv_url', '#'); if ($cv_url && $cv_url !== '#') : ?>
            <a class="st-button st-button-secondary" href="<?php echo esc_url($cv_url); ?>"><?php echo esc_html(st_t('دانلود رزومه','Download CV','Lebenslauf laden')); ?></a>
          <?php endif; ?>
        </div>
      </div>
      <div class="st-glass-card st-hero-photo">
        <?php if ($photo_url) : ?>
          <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr(st_get_theme_option('st_name', 'سپهر طالبی')); ?>">
        <?php else : ?>
          <div class="st-photo-placeholder"><?php echo esc_html(st_t('عکس پروفایل','Profile Image','Profilbild')); ?></div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php if (have_posts()) : while (have_posts()) : the_post(); if (trim(get_the_content())) : ?>
    <section class="st-container st-builder-section st-glass-card">
      <?php the_content(); ?>
    </section>
  <?php endif; endwhile; endif; ?>

  <!-- Skills section -->
  <section class="st-container st-section" id="skills">
    <div class="st-section-head"><h2><?php echo esc_html(st_t('مهارت‌ها','Skills','Fähigkeiten')); ?></h2></div>
    <div class="st-grid st-grid-3">
      <?php $skills = st_query_items('st_skill', 6); if ($skills->have_posts()) : while ($skills->have_posts()) : $skills->the_post(); $level = get_post_meta(get_the_ID(), 'st_skill_level', true); ?>
        <article class="st-glass-card st-card">
          <h3><?php the_title(); ?></h3>
          <?php if ($level) : ?>
            <div class="st-skill-bar"><span style="width: <?php echo esc_attr($level); ?>%"></span></div>
          <?php endif; ?>
          <p><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 18)); ?></p>
        </article>
      <?php endwhile; wp_reset_postdata(); else : ?><div class="st-glass-card st-card"><?php echo esc_html(st_t('هنوز مهارتی ثبت نشده است.','No skills added yet.','Noch keine Fähigkeiten hinzugefügt.')); ?></div><?php endif; ?>
    </div>
  </section>

  <!-- Projects section -->
  <section class="st-container st-section" id="projects">
    <div class="st-section-head"><h2><?php echo esc_html(st_t('پروژه‌ها','Projects','Projekte')); ?></h2></div>
    <div class="st-grid st-grid-3">
      <?php $projects = st_query_items('st_project', 6); if ($projects->have_posts()) : while ($projects->have_posts()) : $projects->the_post();
        $purl = get_post_meta(get_the_ID(), 'st_project_url', true);
        $pstack = get_post_meta(get_the_ID(), 'st_project_stack', true);
        $pyear = get_post_meta(get_the_ID(), 'st_project_year', true);
        $pdl = get_post_meta(get_the_ID(), 'st_project_download_url', true);
      ?>
        <article class="st-glass-card st-card">
          <?php if (has_post_thumbnail()) : ?><div class="st-project-thumb"><?php the_post_thumbnail('medium_large'); ?></div><?php endif; ?>
          <h3><?php the_title(); ?></h3>
          <p><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20)); ?></p>

          <?php if ($pstack || $pyear) : ?>
            <div class="st-meta-row" style="margin-top: 14px; margin-bottom: 14px;">
              <?php if ($pstack) : ?><span><?php echo esc_html($pstack); ?></span><?php endif; ?>
              <?php if ($pyear) : ?><span><?php echo esc_html($pyear); ?></span><?php endif; ?>
            </div>
          <?php endif; ?>

          <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <?php if ($purl) : ?><a class="st-link" href="<?php echo esc_url($purl); ?>" target="_blank" rel="noopener"><?php echo esc_html(st_t('مشاهده پروژه','Open Project','Projekt öffnen')); ?></a><?php endif; ?>
            <?php if ($pdl) : ?><a class="st-link st-button-secondary" href="<?php echo esc_url($pdl); ?>" download style="padding: 12px 18px; border-radius: 16px; font-weight: 700;"><?php echo esc_html(st_t('دانلود فایل','Download File','Datei laden')); ?></a><?php endif; ?>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); else : ?><div class="st-glass-card st-card"><?php echo esc_html(st_t('هنوز پروژه‌ای ثبت نشده است.','No projects added yet.','Noch keine Projekte hinzugefügt.')); ?></div><?php endif; ?>
    </div>
  </section>

  <!-- Other Sections (Experience, Education, Course, Certificate) -->
  <?php
  $sections = array(
    'st_experience' => array('label' => st_t('سوابق شغلی','Experience','Berufserfahrung'), 'anchor' => 'experience'),
    'st_education' => array('label' => st_t('تحصیلات','Education','Ausbildung'), 'anchor' => 'education'),
    'st_course' => array('label' => st_t('دوره‌ها','Courses','Kurse'), 'anchor' => 'courses'),
    'st_certificate' => array('label' => st_t('گواهینامه‌ها','Certificates','Zertifikate'), 'anchor' => 'certificates'),
  );
  foreach ($sections as $type => $data) :
  ?>
    <section class="st-container st-section" id="<?php echo esc_attr($data['anchor']); ?>">
      <div class="st-section-head"><h2><?php echo esc_html($data['label']); ?></h2></div>
      <div class="st-grid st-grid-2">
        <?php $loop = st_query_items($type, 6); if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post();
          $org = get_post_meta(get_the_ID(), 'st_meta_org', true);
          $role = get_post_meta(get_the_ID(), 'st_meta_role', true);
          $date = get_post_meta(get_the_ID(), 'st_meta_date', true);
          $url = get_post_meta(get_the_ID(), 'st_meta_url', true);
          $dl = get_post_meta(get_the_ID(), 'st_meta_download_url', true);
        ?>
          <article class="st-glass-card st-card">
            <h3><?php the_title(); ?></h3>

            <?php if ($role || $org || $date) : ?>
              <div class="st-meta-stack" style="margin-bottom: 12px;">
                <?php if ($role) : ?><span><?php echo esc_html($role); ?></span><?php endif; ?>
                <?php if ($org) : ?><span><?php echo esc_html($org); ?></span><?php endif; ?>
                <?php if ($date) : ?><span><?php echo esc_html($date); ?></span><?php endif; ?>
              </div>
            <?php endif; ?>

            <p style="margin-bottom: 14px;"><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 22)); ?></p>

            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
              <?php if ($url) : ?><a class="st-link" href="<?php echo esc_url($url); ?>"><?php echo esc_html(st_t('بیشتر','More','Mehr')); ?></a><?php endif; ?>
              <?php if ($dl) : ?><a class="st-link st-button-secondary" href="<?php echo esc_url($dl); ?>" download style="padding: 12px 18px; border-radius: 16px; font-weight: 700;"><?php echo esc_html(st_t('دانلود مدرک','Download Certificate','Zertifikat laden')); ?></a><?php endif; ?>
            </div>
          </article>
        <?php endwhile; wp_reset_postdata(); else : ?><div class="st-glass-card st-card"><?php echo esc_html(st_t('موردی ثبت نشده است.','Nothing added yet.','Noch nichts hinzugefügt.')); ?></div><?php endif; ?>
      </div>
    </section>
  <?php endforeach; ?>
</main>
<?php get_footer(); ?>
