<?php
/**
* Custom Post Type: Story
*/
add_theme_support( 'post-thumbnails' );

// Our custom post type function
function create_story_post_type() {
 
    register_post_type( 'stories',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Story' ),
                'singular_name' => __( 'Story' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'stories'),
        )
    );
}

add_action( 'init', 'create_story_post_type' );

/*
* Creating a function to create our CPT
*/
function custom_story_post_type() {
 
    // Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Story', 'Post Type General Name', 'bmd' ),
        'singular_name'       => _x( 'Story', 'Post Type Singular Name', 'bmd' ),
        'menu_name'           => __( 'Story', 'bmd' ),
        'parent_item_colon'   => __( 'Parent Story', 'bmd' ),
        'all_items'           => __( 'All Stories', 'bmd' ),
        'view_item'           => __( 'View Story', 'bmd' ),
        'add_new_item'        => __( 'Add New Story', 'bmd' ),
        'add_new'             => __( 'Add New', 'bmd' ),
        'edit_item'           => __( 'Edit Story', 'bmd' ),
        'update_item'         => __( 'Update Story', 'bmd' ),
        'search_items'        => __( 'Search Story', 'bmd' ),
        'not_found'           => __( 'Not Found', 'bmd' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'bmd' ),
    );
     
    // Set other options for Custom Post Type
    $args = array(
        'label'               => __( 'stories', 'bmd' ),
        'description'         => __( 'Story news and reviews', 'bmd' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        'taxonomies'          => array( 'genres' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
     
    register_post_type( 'stories', $args );
 
}

add_action( 'init', 'custom_story_post_type', 0 );

?>