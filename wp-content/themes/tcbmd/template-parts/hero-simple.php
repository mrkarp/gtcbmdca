<?php
	/**
	* Template Part: Hero Simple 
	*/
?>

<?php
    // Assign vars
?> 

<div class="hero" style="background-image: linear-gradient(to bottom, rgba(25, 25, 25, 0.6) 0%, rgba(25, 25, 25, 0.6) 100%),url('<?php the_field('hero_image'); ?>');background-size: 100%; background-position-y: <?php the_field('hero_image_y_pos'); ?>">
  <div class="container-fluid p-5 text-center text-white">
    <!-- Header -->
    <div class="row py-5">
      <div class="hero-header  mx-auto">
        <h1 class="py-2"><?php the_field('hero_header'); ?></h1>
      </div>
    </div>
</div>
</div>