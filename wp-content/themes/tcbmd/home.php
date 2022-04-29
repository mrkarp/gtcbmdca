<?php
    /**
    * Template Name: Home
    */
	get_header();
?>
<!-- Hero -->
<section id="hero-container">
    <?php get_template_part("template-parts/hero"); ?>
</section>
<!-- About the Club -->
<section id="about-the-club-container">
    <?php get_template_part("template-parts/about-the-club"); ?>
</section>
<!-- Membership Benefits -->
<section id="membership-benefits-container">
    <?php get_template_part("template-parts/membership-benefits"); ?>
</section>
<!-- About the Breed -->
<section id="about-the-breed-container">
    <?php get_template_part("template-parts/about-the-breed"); ?>
</section>
<!-- Break -->
<section id="logo-break-container">
    <?php get_template_part("template-parts/logo-break"); ?>
</section>
<!-- Events -->
<section id="events-container">
    <?php get_template_part("template-parts/events"); ?>
</section>

<?php
get_footer();
