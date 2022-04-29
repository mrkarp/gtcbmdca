<?php
	/**
	* Template Name: Contact Us
	*/
	get_header();
?>

<section id="contact-us">
	<?php get_template_part("template-parts/hero-simple"); ?>

	<div class="container pt-5">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
		 <?php endwhile; endif; ?>
	</div>
</section>

<?php
get_footer();