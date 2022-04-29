<?php
	/**
	* Template Part: Hero Image 
	*/
?>

<?php
    // Assign vars
?> 
  <div class="hero" style="background-image: linear-gradient(to bottom, rgba(25, 25, 25, 0.6) 0%, rgba(25, 25, 25, 0.6) 100%),url('<?php the_field('hero_image'); ?>');">
    <div class="container-fluid p-5 text-center text-white">
      <!-- Header -->
      <div class="row">
        <div class="hero-header pt-5 mx-auto">
          <h1><?php the_field('hero_header_top'); ?></h2>
          <h5><?php the_field('hero_header_middle'); ?></h5>
          <h3><?php the_field('hero_header_bottom'); ?></h3>
        </div>
      </div>

      <!-- Break -->
      <div class="row">
        <div class="col mt-auto">
          <hr class="m-0" style="border-top: 10px solid white;"/>
        </div>

        <div class="col text-center">
          <img src="<?php the_field('skyline_image'); ?>" class="hero-image w-75" />
        </div>

        <div class="col mt-auto">
          <hr class="m-0" style="border-top: 10px solid white;"/>
        </div>
      </div>

      <!-- Text -->
      <div class="row">
        <div class="col pt-4">
          <em><?php the_field('hero_text'); ?></em>
        </div>
      </div>
  </div>

  <div class="split-bottom" style="background-color:white">
      <?php get_template_part("template-parts/evsents"); ?>
  </div>
</div>