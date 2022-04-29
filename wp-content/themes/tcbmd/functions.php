<?php

    #Enqueue style sheet.
    function theme_assets() {
        wp_enqueue_style( 'theme_assets-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
        wp_enqueue_script( 'theme_assets-script', get_stylesheet_directory_uri() . '/main.js', array(), wp_get_theme()->get( 'Version' ) );
    }
    add_action( 'wp_enqueue_scripts', 'theme_assets' );

    // remove custom post type editor components
    add_action('init', 'init_remove_support',100);
    function init_remove_support(){
        $post_type = 'event';
        remove_post_type_support( $post_type, 'editor');
        remove_post_type_support( $post_type, 'discussion');
    }


    #Enable post thumbnail and menus.
    add_theme_support( 'post-thumbnails', 'menus' );
    function register_theme_menus () {
        register_nav_menus( [
            'site-navigation' => _( 'Site Navigation' )
        ] );
    }
    add_action( 'init', 'register_theme_menus' );

    require_once(__DIR__.'/vendor/wp_bootstrap_navwalker.php');
    require_once(__DIR__.'/post-types/dog-post-type.php');
    require_once(__DIR__.'/post-types/event-post-type.php');
    require_once(__DIR__.'/post-types/story-post-type.php');

    #Debug write log function.
    if ( ! function_exists('write_log')) {
        function write_log ( $log )  {
           if ( is_array( $log ) || is_object( $log ) ) {
              error_log( print_r( $log, true ) );
           } else {
              error_log( $log );
           }
        }
     }

    function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    
    # Custom Bootstrap Collapsed Menu including logo
    function create_collapsed_bootstrap_menu( $theme_location ) {
        if ( ($theme_location) && ($locations = get_nav_menu_locations()) && isset($locations[$theme_location]) ) {
             $icon = get_field('heart_icon', 'option');
             $logo = get_field('header_logo', 'option');
             
            $menu_list = '<div class="container-fluid site-navigation-container">' ."\n";
            $menu_list .= '<a href="'.  get_home_url() .'"><img src="'.$logo.'" class="brand m-auto pt-4" /></a>' ."\n";
            $menu_list .= '<div class="navbar-header float-right pt-4">' ."\n";
            $menu_list .= '<button type="button" class="navbar-toggler collapsed" data-toggle="modal" data-target="#menu-modal" aria-expanded="false">' ."\n";
            $menu_list .= '<span class="sr-only">Toggle navigation</span>' ."\n";
            $menu_list .= '<span class="navbar-toggler-icon"></span>' ."\n";
            $menu_list .= '</button>' ."\n";
            $menu_list .= '</div>' ."\n";
            // Modal
            $menu_list .= '<div class="modal bg-primary" tabindex="-1" role="dialog" aria-labelledby="menu-modal" id="menu-modal">' ."\n";
            $menu_list .= '<div class="m-5">' ."\n";

            $menu_list .= '<img class="img pl-4 mb-5" src="' . $icon .'" />' ."\n";

            $menu_list .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="text-white" aria-hidden="true">X</span></button>';
            $menu_list .= '<ul class="nav navbar-nav m-auto text-left">' ."\n";  
             
            $menu = get_term( $locations[$theme_location], 'nav_menu' );
            $menu_items = wp_get_nav_menu_items($menu->term_id);
     
            // Add <li> menu items
            foreach( $menu_items as $menu_item ) {
                if( $menu_item->menu_item_parent == 0 ) {
                     
                    $parent = $menu_item->ID;
                    $bool = true;
                    $menu_array = array();
                    foreach( $menu_items as $submenu ) {
                        if( $submenu->menu_item_parent == $parent ) {
                            $bool = true;
                            $menu_array[] = '<li class="h5 pb-1 pt-1"><a class="text-dark" href="' . $submenu->url . '">' . $submenu->title . '</a></li>' ."\n";
                        }
                    }
                    if( $bool == true && count( $menu_array ) > 0 ) {
                         
                        $menu_list .= '<li class="h5 pb-3 pt-3 border-bottom">' ."\n";
                        $menu_list .= '<a class="text-dark" href="' . $menu_item->url . '">' . $menu_item->title . '</a>' ."\n";
                        $menu_list .= '<a class="text-dark" data-toggle="collapse" href="#list-'. $parent .'" role="button" aria-expanded="false" aria-controls="'.$parent.'"> <i class="fa fa-chevron-down"></i></a>' ."\n";
                        $menu_list .= '<div class="collapse pb-2" id="list-' .$parent .'">' ."\n"; 
                        $menu_list .= '<ul class="pt-2">' ."\n";
                        $menu_list .= implode( "\n", $menu_array );
                        $menu_list .= '</ul>' ."\n";
                        $menu_list .= '</div>' ."\n";
                         
                    } else {
                        $menu_list .= '<li class="h5 pb-3 pt-3 border-bottom">' ."\n";
                        $menu_list .= '<a class="text-dark" href="' . $menu_item->url . '">' . $menu_item->title . '</a>' ."\n";
                    }
                     
                }
                 
                // end <li>
                $menu_list .= '</li>' ."\n";
            }
              
            $menu_list .= '</ul>' ."\n";
            $menu_list .= '</div>' ."\n";
            $menu_list .= '<img class="img pl-4 mb-5" src="' . $logo .'" />' ."\n";

            $menu_list .= '</div>' ."\n";

            $menu_list .= '</div>' ."\n";
      
        } else {
            $menu_list = '<!-- no menu defined in location "'.$theme_location.'" -->';
        }
         
        echo $menu_list;
    }

     # Custom Bootstrap Menu
     function create_bootstrap_menu( $theme_location ) {
        if ( ($theme_location) && ($locations = get_nav_menu_locations()) && isset($locations[$theme_location]) ) {
            $homeURL = get_site_url();
            $logo = "";
             
            $menu_list = '<div class="container-fluid text-center">' ."\n";
            // Modal
            $menu_list .= '<div class="text-center mx-auto">' ."\n";

            $menu_list .= '<ul class="nav navbar-nav m-auto text-left">' ."\n";  
             
            $menu = get_term( $locations[$theme_location], 'nav_menu' );
            $menu_items = wp_get_nav_menu_items($menu->term_id);
     
            // Add <li> menu items
            foreach( $menu_items as $menu_item ) {
                if( $menu_item->menu_item_parent == 0 ) {
                     
                    $parent = $menu_item->ID;
                    $bool = true;
                    $menu_array = array();
                    foreach( $menu_items as $submenu ) {
                        if( $submenu->menu_item_parent == $parent ) {
                            $bool = true;
                            $menu_array[] = '<li class="h6"><a class="text-dark" href="' . $submenu->url . '">' . $submenu->title . '</a></li>' ."\n";
                        }
                    }
                    if( $bool == true && count( $menu_array ) > 0 ) {
                         
                        $menu_list .= '<li class="h6">' ."\n";
                        $menu_list .= '<a class="text-dark d-inline-flex" href="' . $menu_item->url . '">' . $menu_item->title . '</a>' ."\n";
                        $menu_list .= '<a class="text-dark d-inline-flex p-1" data-toggle="collapse" href="#list-'. $parent .'" role="button" aria-expanded="false" aria-controls="'.$parent.'"> <i class="fa fa-chevron-down"></i></a>' ."\n";
                        $menu_list .= '<div class="collapse pb-2" id="list-' .$parent .'">' ."\n"; 
                        $menu_list .= '<ul style="list-style-type:none;">' ."\n";
                        $menu_list .= implode( "\n", $menu_array );
                        $menu_list .= '</ul>' ."\n";
                        $menu_list .= '</div>' ."\n";
                         
                    } else {
                        $menu_list .= '<li class="h6">' ."\n";
                        $menu_list .= '<a class="text-dark" href="' . $menu_item->url . '">' . $menu_item->title . '</a>' ."\n";
                    }
                }
                // end <li>
                $menu_list .= '</li>' ."\n";
            }
              
            $menu_list .= '</ul>' ."\n";
            $menu_list .= '</div>' ."\n";
            $menu_list .= '</div>' ."\n";
      
        } else {
            $menu_list = '<!-- no menu defined in location "'.$theme_location.'" -->';
        }
         
        echo $menu_list;
    }

?>
