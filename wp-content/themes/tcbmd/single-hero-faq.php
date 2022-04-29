<?php
	/**
	* Template Name: Single With Hero and FAQ
	*/
	get_header();
?>

<section>
	<?php get_template_part("template-parts/hero-simple"); ?>
	<div class="container pt-5">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php the_content(); ?>
		 <?php endwhile; endif; ?>

		 <?php get_template_part("template-parts/faq-item"); ?>
	</div>
</section>

<?php
get_footer();