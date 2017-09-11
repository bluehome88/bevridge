<?php

add_action('wp_ajax_si_cart_update_quantity', 'custom_ajax_cart_update_quantity');
add_action('wp_ajax_nopriv_si_cart_update_quantity', 'custom_ajax_cart_update_quantity');

function custom_ajax_cart_update_quantity () {
  echo si_cart_item_update_quantity($_POST['cart_item_key'], $_POST['quantity']) ? '1' : '0';
  die;
}

add_action('wp_ajax_load_news_items', 'wp_ajax_load_news_items');
add_action('wp_ajax_nopriv_load_news_items', 'wp_ajax_load_news_items');

function wp_ajax_load_news_items () {
  // $cats = [
  //     LATEST_NEWS_CATEGORY_ID
  // ];
  //
  $cats = @$_POST['cid'];

  $data = get_news_previews($_POST['page'], POSTS_MEDIA_PER_PAGE, @$_POST['filter'], $cats);

  echo json_encode($data);
  die;
}

/**
 * AJAX helper functions
 */

function custom_ajax_die ($status, $message, $incorrectField = null) {
  $arr = [
    'status' => $status,
    'message' => $message
  ];

  if ($incorrectField) $arr['incorrectField'] = $incorrectField;

  die(json_encode($arr));
}
