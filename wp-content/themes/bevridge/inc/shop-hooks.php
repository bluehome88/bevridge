<?php

// Products per page
add_filter('loop_shop_per_page', function ($cols) {
  return SHOP_PER_PAGE;
}, 20 );

add_filter('woocommerce_show_page_title', '__return_false');

// Custom product title
function woocommerce_template_loop_product_title () {
  echo '<h3 class="woocommerce-loop-product__title">' . get_the_title() . '</h3>';
}

add_filter('woocommerce_before_shop_loop_item', function(){
//  $post_meta = get_post_meta(get_the_ID(), '_brand_details', true);

  $brands = custom_get_brands_categories([ 'hide_empty' => false ]);
  $categories = get_the_terms(get_the_ID(), 'product_cat');
  echo "<div style='line-height:40px; text-transform:uppercase;min-height:40px;'>";
  foreach( $categories as $category )
  {
    foreach( $brands as $brand ){
      if( $brand->term_id == $category->term_id )
        echo $brand->name;
    }
  }
  echo "</div>";
});

add_filter('posts_clauses_request', function ($clauses) {
  global $wpdb, $wp_query;

  if (!$wp_query->get_queried_object()) return $clauses;
  if (!is_shop()) return $clauses;

  //
  // SHOP BY BRAND select-box
  //
  if (isset($_GET['b'])) {
    $brands_terms = custom_get_brands_categories();

    foreach ($brands_terms as &$term) {
      if ($term->slug !== $_GET['b']) continue;

      $clauses['where'] .= " AND (
        $wpdb->posts.ID IN (
          SELECT object_id
          FROM $wpdb->term_relationships
          WHERE term_taxonomy_id = $term->term_id
        )
      )";

      break;
    }
  }

  return $clauses;
});

add_filter('parse_query', function ($query) {
  if (!$query->get_queried_object()) return $query;
  if (!is_shop()) return $query;

  //
  // Tabs on /shop/ page
  //
  if (isset($_GET['cat'])) {
    $cat_terms = custom_get_product_types();
    $req_cats = (array) $_GET['cat'];

    $found_ids = [];
    foreach ($cat_terms as &$term) {
      if (in_array($term->slug, $req_cats)) {
        $found_ids[] = intval($term->term_id);
      }
    }

    if ($found_ids) {
      $query->query_vars['tax_query'][] = array(
        'taxonomy' => 'product_cat',
        'field'    => 'id',
        'terms'    => $found_ids,
        'operator' => 'IN',
      );
    }
  }

  return $query;
});

/**
 * Disable reviews tab on single product page
 */
add_filter('woocommerce_product_tabs', function ($tabs) {
  unset($tabs['reviews']);
  return $tabs;
}, 20);


add_filter( 'woocommerce_payment_gateways_settings', 'add_minimum_order_amount' );

function add_minimum_order_amount( $settings ) {

  $updated_settings = array();
  foreach ( $settings as $section ) {

    if ( isset( $section['id'] ) && 'checkout_process_options' == $section['id'] && isset( $section['type'] ) && 'sectionend' == $section['type'] ) {

      $updated_settings[] = array(

        'name'     => __( 'Minimum Order Amount' ),
        'desc_tip' => __( 'Customer should not be able to check out if their order is less than the minimum.'),
        'id'       => 'woocommerce_minimum_order_amount',
        'type'     => 'number',
        'css'      => 'min-width:300px;',
        'std'      => '50',  // WC < 2.0
        'default'  => '50',  // WC >= 2.0
      );

    }
    $updated_settings[] = $section;
  }

  return $updated_settings;
}

add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart' , 'wc_minimum_order_amount' );
function wc_minimum_order_amount() {
    // Set this variable to specify a minimum order value
    $minimum = get_option('woocommerce_minimum_order_amount');

    if ( WC()->cart->total < $minimum ) {

        if( is_cart() ) {

            wc_print_notice( 
                sprintf( 'We’re sorry, the minimum order to checkout is %s' , 
                    wc_price( $minimum )
                    // wc_price( WC()->cart->total )
                ), 'error' 
            );

        } else {

            wc_add_notice( 
                sprintf( 'We’re sorry, the minimum order to checkout is %s' , 
                    wc_price( $minimum )
                    // wc_price( WC()->cart->total )
                ), 'error' 
            );

        }
    }

}