<?php
	/**
	* Template Part: Quick Stories 
    */
    $c = true;
?>
<div class="container">
    <!-- Dog Grid Items-->
    <div class="row">
    <?php $args = array( 'post_type' => 'stories', 'posts_per_page' => 3 ); ?> 
        <?php $the_query = new WP_Query( $args ); ?>
            <?php if ( $the_query->have_posts() ): ?>
                <?php while ( $the_query->have_posts() ): ?>
                    <?php $the_query->the_post(); $id = get_the_ID(); ?>
                    <?php if($c = !$c) : // even ?>
                        <div class="col my-auto">
                    <?php else: ?>
                        <div class="col my-auto">
                    <?php endif; ?>
                        <img class="img-fluid" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" /> <!-- 900x650 -->
                    </div>
                    <div class="col my-auto">
                        <?php the_excerpt(); ?>
			            <a class="btn btn-outline-primary w-100" src="<?php get_permalink(); ?>">Read More</a>
                    </div>
                    <hr class="bg-primary w-100" />
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>