<?php
/*
Plugin Name: Product Taxonomies
Plugin URI: http://wordpress.org/plugins/
Description: This is not just a plugin,
Author: Johb
Version: 1.0.0
Author URI: http://bandalism.org/
*/

// TODO: Change this
define('ARTIST_PAGE_ID',34);

// Register Custom Taxonomy
if ( ! function_exists( 'johb_artist_taxonomy' ) ) {

    // Register Custom Taxonomy
    function johb_artist_taxonomy() {
    
        $labels = array(
            'name'                       => _x( 'Artists', 'Taxonomy General Name', 'text_domain' ),
            'singular_name'              => _x( 'Artist', 'Taxonomy Singular Name', 'text_domain' ),
            'menu_name'                  => __( 'Artist', 'text_domain' ),
            'all_items'                  => __( 'All Artists', 'text_domain' ),
            'parent_item'                => __( 'Parent Artist', 'text_domain' ),
            'parent_item_colon'          => __( 'Parent Artist:', 'text_domain' ),
            'new_item_name'              => __( 'New Artist Name', 'text_domain' ),
            'add_new_item'               => __( 'Add New Artist', 'text_domain' ),
            'edit_item'                  => __( 'Edit Artist', 'text_domain' ),
            'update_item'                => __( 'Update Artist', 'text_domain' ),
            'view_item'                  => __( 'View Artist', 'text_domain' ),
            'separate_items_with_commas' => __( 'Separate artists with commas', 'text_domain' ),
            'add_or_remove_items'        => __( 'Add or remove artists', 'text_domain' ),
            'choose_from_most_used'      => __( 'Choose from the most used artists', 'text_domain' ),
            'popular_items'              => __( 'Popular Items', 'text_domain' ),
            'search_items'               => __( 'Search artists', 'text_domain' ),
            'not_found'                  => __( 'Not Found', 'text_domain' ),
            'no_terms'                   => __( 'No items', 'text_domain' ),
            'items_list'                 => __( 'Items list', 'text_domain' ),
            'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        );
        register_taxonomy( 'artist', array( 'post','product' ), $args );
    
    }
    add_action( 'init', 'johb_artist_taxonomy', 0 );
    
}

/*
Display the post terms from a custom taxonomy terms in meta section of Woocommerce single product page
https://stackoverflow.com/questions/50862261/display-a-custom-taxonomy-in-woocommerce-single-product-pages
*/
function gon_product_meta_end() {
    
    global $product;
    error_log('HELLO');
    $taxonomy = 'artist'; // <== Here set your custom taxonomy
 
    if( ! taxonomy_exists( $taxonomy ) ) 
        return; // exit

    $term_ids = wp_get_post_terms( $product->get_id(), $taxonomy, array('fields' => 'ids') );

    if ( ! empty($term_ids) ) {
        echo get_the_term_list( $product->get_id(), $taxonomy, '<span class="posted_in">' . _n( 'Artist:', 'Artists:', count( $term_ids ), 'woocommerce' ) . ' ', ', ', '</span>' );
    }
}
add_action( 'woocommerce_product_meta_end', 'gon_product_meta_end' , 25 );

// JOHB HERE
function gon_breadcrumbs($crumbs)
{


  if (is_tax('artist')) {


    $artists_page_id = ARTIST_PAGE_ID;

    if (!empty($artists_page_id) && $artists_page_id != '-') {

        $cur_artist = get_queried_object();
        $artist_ancestors = get_ancestors($cur_artist->term_id, 'artist', 'taxonomy');


     
      $artist_page_pos = count($crumbs) - (count($artist_ancestors) + 2);
      if (is_paged())
        $artist_page_pos -= 1;

      if (isset($crumbs[$artist_page_pos][1]))
        $crumbs[$artist_page_pos][1] = get_page_link($artists_page_id);
        
    }

  }

  return $crumbs;
}
add_filter('woocommerce_get_breadcrumb', 'gon_breadcrumbs');
