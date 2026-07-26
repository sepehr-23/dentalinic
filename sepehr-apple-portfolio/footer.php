<footer class="st-site-footer">
  <div class="st-container st-glass-card st-footer-card">
    <div>
      <h3><?php echo esc_html(st_get_theme_option('st_name', 'سپهر طالبی')); ?></h3>
      <p><?php echo esc_html(st_get_theme_option('st_title', st_t('پورتفولیو و رزومه شخصی','Personal Portfolio & Resume','Persönliches Portfolio & Lebenslauf'))); ?></p>
    </div>
    <div class="st-footer-socials">
      <?php foreach (st_social_links() as $label => $url) : if ($url && $url !== '#') : ?>
        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener"><?php echo esc_html($label); ?></a>
      <?php endif; endforeach; ?>
    </div>
    <?php if (is_active_sidebar('footer-1')) : ?><div class="st-footer-widgets"><?php dynamic_sidebar('footer-1'); ?></div><?php endif; ?>
    <div class="st-copyright"><?php echo esc_html(date_i18n('Y')); ?> © <?php echo esc_html(st_get_theme_option('st_name', 'سپهر طالبی')); ?></div>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
