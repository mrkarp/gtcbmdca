<?php
	/**
	* Template Part: About the Club 
	*/
?>

<?php
  $page_id = get_queried_object_id();
?>

<style>
.underline::after {
        background: <?php the_field('breed_underline_color'); ?> none repeat scroll 0 0;
        bottom: 0;
        content: "";
        height: 2px;
        left: 0;
        position: absolute;
        width: 10%;
    }
</style>

<!-- About Club-->
<div class="bg-<?php echo (get_field('breed_background_color') ? the_field('breed_background_color') : "white"); ?> 
	text-<?php echo (get_field('breed_text_color') ? the_field('breed_text_color') : "black"); ?> py-5">
	<div class="container">
	<div class="text-left">
		<h1 class="display-4 position-relative underline">
			<?php the_field('breed_content_header', $page_id); ?>
		</h1>
	</div>
	<div class="row">
		<div class="py-2 col-sm-6 col-xs-12 text-left">
			<p><?php the_field('breed_content_first_text', $page_id); ?></p>
			<p><?php the_field('breed_content_second_text', $page_id); ?></p>
			<a class="btn btn-outline-<?php the_field('breed_underline_color'); ?> w-100" src="<?php the_field('breed_right_content_link'); ?>">Read More</a>
		</div>
		<div class="py-2 col-sm-6 col-xs-12 text-center my-auto">
			<img class="img border border-<?php the_field('breed_underline_color'); ?> rounded w-75" src="<?php the_field('breed_right_image', $page_id); ?>" />
		</div>
	</div>
	</div>
</div>