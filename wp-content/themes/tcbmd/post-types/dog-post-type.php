<?php
/**
* Custom Post Type: Dogs
*/
add_theme_support( 'post-thumbnails' );

// Our custom post type function
function create_dog_post_type() {
 
    register_post_type( 'dogs',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Dogs' ),
                'singular_name' => __( 'Dog' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'dogs'),
        )
    );
}

add_action( 'init', 'create_dog_post_type' );

/*
* Creating a function to create our CPT
*/
function custom_dog_post_type() {
 
    // Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Dogs', 'Post Type General Name', 'bmd' ),
        'singular_name'       => _x( 'Dog', 'Post Type Singular Name', 'bmd' ),
        'menu_name'           => __( 'Dogs', 'bmd' ),
        'parent_item_colon'   => __( 'Parent Dog', 'bmd' ),
        'all_items'           => __( 'All Stories', 'bmd' ),
        'view_item'           => __( 'View Dog', 'bmd' ),
        'add_new_item'        => __( 'Add New Dog', 'bmd' ),
        'add_new'             => __( 'Add New', 'bmd' ),
        'edit_item'           => __( 'Edit Dog', 'bmd' ),
        'update_item'         => __( 'Update Dog', 'bmd' ),
        'search_items'        => __( 'Search Dog', 'bmd' ),
        'not_found'           => __( 'Not Found', 'bmd' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'bmd' ),
    );
     
    // Set other options for Custom Post Type
    $args = array(
        'label'               => __( 'dogs', 'bmd' ),
        'description'         => __( 'Dog news and reviews', 'bmd' ),
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
     
    register_post_type( 'dogs', $args );
 
}

add_action( 'init', 'custom_dog_post_type', 0 );

?>