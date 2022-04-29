<?php
/**
* Custom Post Type: event
*/
add_theme_support( 'post-thumbnails' );

// Our custom post type function
function create_event_post_type() {
 
    register_post_type( 'event',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Events' ),
                'singular_name' => __( 'Event' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'event'),
        )
    );
}

add_action( 'init', 'create_event_post_type' );

/*
* Creating a function to create our CPT
*/
function custom_event_post_type() {
    $args = array(
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true, 
        'show_in_menu' => true, 
        'capability_type' => 'post',
        'has_archive' => true, 
        'supports' => array('')
    ); 
    register_post_type('event',$args);
 
}

add_action( 'init', 'custom_event_post_type', 0 );


?>