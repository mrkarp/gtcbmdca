<?php
	/**
	* Template Part: Events 
	* http://maps.google.com/maps?f=q&hl=en&q=
	*/

	$date_string = get_field('date');
?>

<div id="events-container" class="p-relative">
	<div class="row mx-0 w-100">
        <?php $args = array( 'post_type' => 'event', 'posts_per_page' => 3 ); ?> 
        <?php $the_query = new WP_Query( $args ); ?>
            <?php if ( $the_query->have_posts() ): ?>
				<?php while ( $the_query->have_posts() ): ?>
				
					<?php $the_query->the_post(); $desc = get_field('description'); ?>
					<!-- Event Card Start -->
					<div class="col-md-4">
						<div class="card mb-4 box-shadow">
							<!-- Event Image -->
							<div class="card-header">
								<h5 class="card-title text-center"><?php the_field('title'); ?></h5>
							</div>
							<div class="card-body">
								<p class="mb-0">Event Date: <?php the_field('date'); ?></p>
								<p><small><a href="https://www.google.com/search?q=<?php the_field('location_address'); ?>"><?php the_field('location_name'); ?></a></small></p>

								<div class="mx-auto mb-2 w-75 text-center">
									<img class="card-img-top " src="<?php (get_field('image') ? the_field('image') : get_template_directory().'/image/300.jpg'); ?>" />
								</div>

								<p class="card-text">
									<small>
										<?php echo substr($desc, 0, 250); ?>
										<?php echo (strlen($desc) >= 250 ? "..." : "")?>
									</small>
								</p>
								<div class="row">
									<div class="col text-center">
										<a class="btn btn-outline-<?php the_field('button_color'); ?> float-left" href="<?php echo get_post_permalink(); ?>">Read More</a>
									</div>
									<div class="col  text-center">
										<p class="mb-0"><small><em>Last Updated <?php the_modified_date(); ?></em></small></p>
									</div>
								</div>
							</div>
						</div>
					</div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>