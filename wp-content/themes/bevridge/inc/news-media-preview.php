<?php
function get_news_previews($page = 1, $per_page = POSTS_MEDIA_PER_PAGE, $filter = null, $cat_id = null) {
  global $wpdb;

  $offset = ($page - 1) * $per_page;

  $posts = get_news_posts($filter, $cat_id, $offset);
  $total_posts = @get_category($cat_id)->category_count ?: wp_count_posts()->publish;
  $data = [
    'html' => '',
    'more' => true
  ];

  foreach($posts as $post) {
    $data['html'] .= get_news_html($post);
  }

  if ($total_posts <= $page * $per_page) {
    $data['more'] = false;
  }

  return $data;
}

function get_news_posts($filter = null, $cat_id = '', $offset = 0) {
  global $wpdb;

  $order = 'DESC';
  if ($filter === 'latest_news' || $filter == null ) {
      $filter = 'date';
  }

	$args = array(
		'posts_per_page'   => POSTS_MEDIA_PER_PAGE,
		'offset'           => $offset,
		'category'         => $cat_id,
		'orderby'          => $filter,
		'order'            => $order,
		'post_type'        => 'post',
		'post_status'      => 'publish',
		'suppress_filters' => true
	);

  return get_posts( $args );
}

function get_news_html($post) {
	$content = strip_tags($post->post_content);
	$maxPos = 150;
  $link = get_permalink($post->ID);
	if (strlen($content) > $maxPos) {
    $lastPos = ($maxPos - 3) - strlen($content);
    $content = substr($content, 0, strrpos($content, ' ', $lastPos)) . '...';
	}
	$html = '<div class="news__item latestnews flexpost">
    <div class="news__img">
      <a href="' . $link . '">
        '. get_the_post_thumbnail( $post->ID, 'thumbnail') . '
      </a>
    </div>
    <div class="news__text">
      <a href="' . $link . '">
        <h3>'. get_the_title($post) .'</h3>
        <p>'. $content .'</p>
      </a>
    </div>
    <div class="news__action">
      <a href="'. $link .'" title="Read more..."></a>
    </div>
  </div>';

  return $html;
}