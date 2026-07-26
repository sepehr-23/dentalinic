<?php
/*
Template Name: Elementor Full Width
*/
get_header();
while (have_posts()) : the_post();
    echo '<main class="st-container st-builder-section st-glass-card">';
    the_content();
    echo '</main>';
endwhile;
get_footer();
