<?php
define('SCRIPTS_VER', '9');
define('POSTS_MEDIA_PER_PAGE', 3);
define('SHOP_PER_PAGE', 30);
define('LATEST_NEWS_CATEGORY_ID', 18);
define('SHOP_BRANDS_CATEGORY_ID', 23);

require_once __DIR__ . '/inc/ajax.php';
require_once __DIR__ . '/inc/shortcodes.php';
require_once __DIR__ . '/inc/forms.php';
require_once __DIR__ . '/inc/shop-functions.php';
require_once __DIR__ . '/inc/shop-hooks.php';
require_once __DIR__ . '/inc/news-media-preview.php';
require_once __DIR__ . '/inc/careers.php';
require_once __DIR__ . '/inc/theme-setup.php';
require_once ABSPATH . '/vendor/autoload.php';

if (is_admin()) {
  require_once __DIR__ . '/admin/metaboxes/brand-details.php';
  require_once __DIR__ . '/admin/widgets/widget-special-offer.php';
  require_once __DIR__ . '/admin/others.php';
}

function get_excerpt(){
$excerpt = get_the_content();
$excerpt = preg_replace(" ([.*?])",'',$excerpt);
$excerpt = strip_shortcodes($excerpt);
$excerpt = strip_tags($excerpt);
$excerpt = substr($excerpt, 0, 50);
$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
$excerpt = trim(preg_replace( '/s+/', ' ', $excerpt));
$excerpt = $excerpt.'... <a href="'.$permalink.'">more</a>';
return $excerpt;
}