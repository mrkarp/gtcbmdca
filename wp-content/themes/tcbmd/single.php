<?php
	/**
	* Template Name: Single
	*/
	get_header();
?>

<main id="single-main">
	<div class="container-fluid">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<?php the_content(); ?>
		<?php endwhile; else : ?>
			<p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();