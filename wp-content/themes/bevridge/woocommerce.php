<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */


$url_parts = parse_url($_SERVER['REQUEST_URI']);
parse_str(@$url_parts['query'], $query_parts);

$tabs = custom_get_product_types();
$brands_terms = custom_get_brands_categories();


// NEW IS STORE products HTML
ob_start();

woocommerce_content();
$new_in_store = ob_get_clean();
$new_in_store_count = $wp_query->post_count;

get_header(); ?>

<section id="store" class="page-wrap">
  <? if (is_shop()) { ?>
     
         
<header class="inner_page_header"> <div class="container">
    <div class="my_account_link">
        <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account'); ?>"><?php _e('My Account'); ?></a>
    </div>

    <h1 class="inner_title">STORE</h1> 
  
    </div></header>
    
    <div class="container product_single">
      <div class="cart-wrapper">
        <div class="cart-preview">
          <div class="cart-preview__icon">
            <a href="#"><img src="/images/icons/cart.png" srcset="/images/icons/cart@2x.png" alt="" /></a>
          </div>
          <div class="cart-preview__info">
            <a class="cart-contents" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">
              <span><?= WC()->cart->cart_contents_count ?></span>
            </a>

            <p>VIEW</p>
          </div>
        </div>

        <div class="minicart-overlay overlay-red"></div>
        <div class="mini-cart cart">
          <div class="container">
            <div class="cart-wrapper">
              <? the_widget('WC_Widget_Cart') ?>
            </div>
          </div>
        </div>
      </div>
    
        <div class="shop_area_products">
            <div class="shop_area_products_left">
                 <h1>Blue Moon 24 
<font class="shop_area_products_font_color1">Anniversary special pack </font>
<font class="shop_area_products_font_color2">800 packs available</font></h1>
  <a href="#">buy Now!</a>
    <div class="products_img_absolute"><img src="../images/blue_moon.png" /></div>
              </div>
              <div class="shop_area_products_right">
              <div class="actions-block product">
  <select name="quantity" class="quantity-select">
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
          <option value="9">9</option>
          <option value="10">10</option>
      </select>
  <a rel="nofollow" href="#" data-quantity="1" data-product_id="120" data-product_sku="" class="button product_type_simple add_to_cart_button ajax_add_to_cart"></a>
<script>
  function changeQty()
  {
    jQuery('#top_product .add_to_cart_button').attr("data-quantity", jQuery('#top_product .quantity-select').val());
  }
</script>


</div>
              </div>
        </div>
        
      <div class="filter">
        <ul>
          <? $url =""; foreach ($tabs as &$term) { ?>
            <? $is_active = in_array($term->slug, (array) @$query_parts['cat']) ?>
            <li>
              <? if ($is_active) { ?>
                <?
                $url = preg_replace("/%5B\d*%5D/", '[]', $_SERVER['REQUEST_URI']);
                $url = preg_replace("/(\?)?&?cat\[\]\=$term->slug/", '$1', $url);
                $url = str_replace('?&', '?', $url);
                ?>
                <a href="<?= $url ?>" class="active"><?= $term->name ?></a>
              <? } else { ?>
                <a href="<?= esc_url(add_query_arg('cat[]', $term->slug, $url)) ?>"><?= $term->name ?></a>
              <? } ?>
            </li>
          <? } ?>
        </ul>

        <div class="brands-select">
          <form action="<?= esc_attr($url_parts['path']) ?>">
            <select name="b" id="shop-by-brand-select">
              <option value="">shop by brand</option>
              <? foreach ($brands_terms as &$term) { ?>
                <option value="<?= $term->slug ?>"<? if ($term->slug === @$_GET['b']) echo ' selected' ?>>
                  <?= $term->name ?>
                </option>
              <? } ?>

              <? foreach ($query_parts as $key => $val) { ?>
                <? if ($key === 'b') continue ?>
                <? if (is_array($val)) { ?>
                  <? foreach ($val as $v) { ?>
                    <input type="hidden" name="<?= esc_attr($key) ?>[]" value="<?= esc_attr($v) ?>">
                  <? } ?>
                <? } else { ?>
                  <input type="hidden" name="<?= esc_attr($key) ?>" value="<?= esc_attr($val) ?>">
                <? } ?>
              <? } ?>
            </select>
          </form>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="store">
      <?php
          if( (isset($_GET['b']) && $_GET['b'] ) || ( isset( $_GET['cat']) && $_GET['cat'])){
      ?>
            <div id="category_product" class="">
              <? woocommerce_content() ?>
            </div>
      <?php
          }
          else
          {
      ?>

        <? $popular_count = count(custom_query_popular_products()) ?>
        <? if ($popular_count > 0) { ?>
        <div class="row store__items">
          <div class="label popular">POPULAR</div>
            <div id="popular_products" class="store_items_wrap">
              <? woocommerce_content() ?>
            </div>
        </div>
        <? } ?>

        <? $deals_count = count(custom_query_deals_products()) ?>
        <? if ($deals_count > 0) { ?>
        <div class="row store__items">
          <div class="label popular">SPECIALS & DEALS</div>
            <div id="deals_products" class="store_items_wrap">
              <? woocommerce_content() ?>
            </div>
        </div>
        <? } ?>

        <? if ($new_in_store_count > 0) { ?>
        <div class="row store__items">
          <div class="label popular">NEW IN STORE</div>
            <div id="new_in_store" class="store_items_wrap">
              <?= $new_in_store ?>
            </div>
        </div>
        <? } ?>

        <? if (!$popular_count && !$deals_count && !$new_in_store_count) { ?>
          <p style="margin-bottom: 200px">No products were found based on your criteria.</p>
        <? } 

          }
        ?>
      </div>

      <? dynamic_sidebar('shop_bottom') ?>

    <? } else { ?>

      
        <? woocommerce_content() ?>
       

    <? } ?>
  </div>
</section>

<?php get_footer();
