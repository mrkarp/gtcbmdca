<?php
	/**
	* Template Name: FAQ
	*/
	get_header();
?>

<main id="faq-main">
	<?php get_template_part("template-parts/hero-simple"); ?>
	<div class="container-fluid py-2">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php get_template_part("template-parts/faq-item"); ?>
		<?php endwhile; endif; ?>
	</div>
</main>

<?php
get_footer();