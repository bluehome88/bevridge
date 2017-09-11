<?php
/**
Template Name: Page Our Brands
 */

get_header();

$this_post = $post;

$product_types = custom_get_product_types();

query_posts([
  'taxonomy' => 'brands_category',
  'post_type' => 'brand',
  'posts_per_page' => 9999,
  'order' => 'ASC'
]);
?>

<!-- header -->
<div class="header-wrapper">
  <div class="our_brands">
   

      <header class="inner_page_header">
<div class="container">
<h1 class="inner_title"><?= $this_post->post_title ?></h1>  
<section id="brands"><div class="types ">
      <ul>
        <? foreach ($product_types as &$term) { ?>
          <? $name = $term->name === 'Craft Beers' ? 'Craft Beer' : $term->name ?>
          <li><a href="<?= get_term_link($term->term_id) ?>" data-type="<?= $term->slug ?>"><?= $name ?></a></li>
        <? } ?>
      </ul>
    </div></section>
      </div>
      </header>

    
  </div>
</div>
<!-- header -->


<section id="brands" class="our_brands_logo_content">
  <div class="container">
    

    <div class="brands">
      <div class="row">
        <? while (have_posts()): the_post() ?>
          <? if (has_post_thumbnail()) { ?>
            <?php
              $meta = $post->_brand_details = get_post_meta($post->ID, '_brand_details', true);
              $w = @$meta['logo_thumb_width'] ?: 170;
              $h = @$meta['logo_thumb_height'] ?: '';
              $product_type_ids = explode('|', @$post->_brand_details['product_type']);
              $product_type_slugs = [];
              if ($product_type_ids) {
                foreach ($product_types as $term) {
                  if (in_array($term->term_id, $product_type_ids)) {
                    $product_type_slugs[] = $term->slug;
                  }
                }
              }
            ?>
            <a href="#<?= $post->post_name ?>_popup" class="popup <?= join(' ', $product_type_slugs) ?>">
              <img src="<?= get_the_post_thumbnail_url() ?>" width="<?= esc_attr($w) ?>" height="<?= esc_attr($h) ?>">
            </a>
          <? } ?>
        <? endwhile ?>
        </div>
    </div>

  </div>
</section>

<? rewind_posts() ?>

<? while (have_posts()): the_post() ?>
  <? if (has_post_thumbnail()) { ?>
    <?
    $slider_bg_attachment_id = @$post->_brand_details['slider_bg_attachment_id'];
    if ($slider_bg_attachment_id) {
      $slider_bg_attachment_meta = wp_get_attachment_metadata($slider_bg_attachment_id);
    }

    $target_product_cat_slug = get_term(@$post->_brand_details['target_product_cat']);
    $target_product_cat_slug = @$target_product_cat_slug->slug;

    $products = get_posts([
      'post_type' => 'product',
      'tax_query' => array(
          'relation' => 'AND',
          array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $target_product_cat_slug
          )
      )
    ]);

    $product_type_ids = explode('|', @$post->_brand_details['product_type']);
    $product_type_slugs = [];
    if ($product_type_ids) {
      foreach ($product_types as $term) {
        if (in_array($term->term_id, $product_type_ids)) {
          $product_type_slugs[] = $term->slug;
        }
      }
    }
    $popup_title = @$product_type_slugs[0] ?: 'Beer';

    // Trim the plural 's'
    if (substr($popup_title, strlen($popup_title) - 1, 1) === 's') {
      $popup_title = substr($popup_title, 0, strlen($popup_title) - 1);
    }
    ?>

    <div id="<?= $post->post_name ?>_popup" class="brand_popup mfp-hide <? if (empty($products)) echo 'no-products' ?>">
      <div class="overlay">
        <div class="brand_popup__body">
          <? if (!empty($products)) { ?>
            <div class="body__carousel-wrapper" style="background-image: url('<?= esc_url(wp_get_attachment_url($slider_bg_attachment_id)) ?>');">
              <div class="carousel-overlay">
                <div class="body__carousel">
                  <? foreach ($products as $product_post) { ?>
                    <? $product = wc_get_product($product_post->ID) ?>
                    <div>
                      <div class="brand_popup-image-wrap">
                        <img src="<?= esc_url(get_the_post_thumbnail_url($product_post->ID)) ?>" alt="" />
                      </div>
                      <h5><?= $product->get_attribute('pa_vessel') ?: 'Bottle' ?> <?= $product->get_attribute('pa_weight') ?></h5>
                    </div>
                  <? } ?>
                </div>
                <a href="/shop/?b=<?= $target_product_cat_slug ?>" class="btn buynow">Buy now!</a>
              </div>
            </div>
          <? } ?>
          <div class="body__info">
            <a href="#" class="close-popup">
              <img src="/images/icons/popup_close.png" srcser="/images/icons/popup_close@2x.png 2x" alt=""/>
            </a>
            <!--<h2 class="skew">
              <?= $popup_title ?>
            </h2>-->
            <img src="<?= get_the_post_thumbnail_url() ?>" alt="" />
            <? the_content() ?>
          </div>
        </div>
      </div>
    </div>
  <? } ?>
<? endwhile; ?>

<?php get_footer();