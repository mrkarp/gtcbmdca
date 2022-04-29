<?php
	/**
	* Template Name: Single Event
	*/
	get_header();
?>

<section id="single-main">
<style>
  #map {
    height: 100%;
  }
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }
</style>


	<div class="container py-4">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<div class="row">
			<div class="col my-auto">
				<img class="img w-100" src="<?php (get_field('image') ? the_field('image') : get_template_directory().'/image/300.jpg'); ?>" />
			</div>
			<div class="col">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				
				<p class="mb-0">Event Date: <?php the_field('date'); ?></p>
				<p class="mb-0"><small><a href="https://www.google.com/search?q=<?php the_field('location_address'); ?>"><?php the_field('location_name'); ?></a></small></p>
				<p class="mb-0"><?php the_field('description'); ?></p>
				<p class="mb-0"><small><em>Last Updated <?php the_modified_date(); ?></em></small></p>
			</div>
		</div>
		<div class="row">
			<div id="map"></div>
		</div>

		<?php endwhile; else : ?>
			<p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>
	</div>

</section>

<script>
	function initMap() {
		var map = new google.maps.Map(document.getElementById('map'), {
			center: { lat: 34.397, lng: 150.644 },
			scrollwheel: false,
			zoom: 2
		});
	}
</script>

<?php
get_footer();