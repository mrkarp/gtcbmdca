<?php
	/**
	* Template Part: Blank 
	*/
?>
<div class="container">
    <!-- Dog Grid Items-->
    <div class="row">
    <?php $args = array( 'post_type' => 'dogs', 'posts_per_page' => 10 ); ?> 
    <?php $the_query = new WP_Query( $args ); ?>
        <?php if ( $the_query->have_posts() ): ?>
            <?php while ( $the_query->have_posts() ): ?>
                <?php $the_query->the_post(); ?>
                <div class="col-md-6 col-lg-4 mb-5">
                    <div class="portfolio-item dog-item-<?php echo $post->ID; ?> mx-auto" data-toggle="modal" data-target="#dogModal<?php echo $post->ID; ?>">
                        <div class="item-caption d-flex align-items-center justify-content-center h-100 w-100">
                            <div class="item-caption-content text-center text-white"><i class="fas fa-plus fa-3x"></i></div>
                        </div>
                        <img class="img-fluid" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" /> <!-- 900x650 -->
                    </div>
                </div>

                <!-- Dog Modal <?php echo $post->ID; ?>-->
                <div class="dog-modal modal fade" id="dogModal<?php echo $post->ID; ?>" tabindex="-1" role="dialog" aria-labelledby="dogModal<?php echo $post->ID; ?>Label" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><i class="fas fa-times"></i></span>
                            </button>
                            <div class="modal-body text-center">
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <!-- Portfolio Modal - Title-->
                                            <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="dogModal<?php echo $post->ID; ?>Label"><?php the_title(); ?></h2>
                                            <div id="logo-break-container">
                                                <?php get_template_part("template-parts/logo-break"); ?>
                                            </div>
                                            <img class="img-fluid rounded mb-5" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" />
                                            <p class="mb-5"><?php the_excerpt(); ?></p>
                                            <button class="btn btn-primary" data-dismiss="modal"><i class="fas fa-times"></i>Close Window</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>