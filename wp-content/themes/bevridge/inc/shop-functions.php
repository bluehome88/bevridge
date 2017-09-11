<?php

function si_cart_item_update_quantity ($cart_item_key, $quantity) {
  // Get the array of values owned by the product we're updating
  $threeball_product_values = WC()->cart->get_cart_item( $cart_item_key );

  // Get the quantity of the item in the cart
  $threeball_product_quantity = apply_filters( 'woocommerce_stock_amount_cart_item', apply_filters( 'woocommerce_stock_amount', preg_replace( "/[^0-9\.]/", '', filter_var($quantity, FILTER_SANITIZE_NUMBER_INT)) ), $cart_item_key );

  // Update cart validation
  $passed_validation  = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $threeball_product_values, $threeball_product_quantity );

  // Update the quantity of the item in the cart
  if ( $passed_validation ) {
    return WC()->cart->set_quantity( $cart_item_key, $threeball_product_quantity, true );
  }

  return false;
}

function custom_get_brands_categories ($args = []) {
  return get_terms(wp_parse_args([
    'taxonomy' => 'product_cat',
    'parent' => SHOP_BRANDS_CATEGORY_ID
  ], $args));
}

function custom_get_product_types () {
  return get_categories([
    'taxonomy' => 'product_cat',
    'parent' => 41
  ]);
}

/**
 * Query Popular products
 */
function custom_query_popular_products () {
  return query_posts([
    'post_type' => 'product',
    'tax_query' => array(
        'relation' => 'AND',
        array(
          'taxonomy' => 'product_cat',
          'field'    => 'term_id',
          'terms'    => array(33)
        )
    )
  ]);
}

/**
 * Query Specials & Deals products
 */
function custom_query_deals_products () {
  return query_posts([
    'post_type' => 'product',
    'tax_query' => array(
        'relation' => 'AND',
        array(
          'taxonomy' => 'product_cat',
          'field'    => 'term_id',
          'terms'    => array(40)
        )
    )
  ]);
}
