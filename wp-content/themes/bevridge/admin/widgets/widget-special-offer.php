<?php
/**
 * "Special offer" widget that is displayed at the bottom of /shop/ page
 */
class CustomShopSpecialOfferWidget extends WP_Widget
{
  public function __construct() {
    $args = [
      'classname' => 'custom_widget_special_offer',
      'description' => '"Special offer" widget that is displayed at the bottom of /shop/ page',
    ];

    parent::__construct('custom_widget_special_offer', 'Custom Special Offer', $args);

    $this->alt_option_name = 'custom_widget_special_offer';

    add_action( 'save_post', array($this, 'flush_widget_cache') );
    add_action( 'deleted_post', array($this, 'flush_widget_cache') );
    add_action( 'switch_theme', array($this, 'flush_widget_cache') );
  }

  public function widget( $args, $instance ) {
    global $product;

    $_product = $product;

    if (!@$instance['product_id'] || !is_numeric($instance['product_id'])) return;

    if (!$product = wc_get_product(@$instance['product_id'])) return;

    $GLOBALS['product'] = $product;

    ?>
    <div class="special-offer skew">
      <div class="text-block">
        <?= @$instance['html'] ?>
      </div>
      <div class="store-block product">
        <img src="<?= @$instance['image'] ?>" alt="">
        <div class="actions-block unskew">
          <? wc_get_template('global/quantity-input.php') ?>
          <? wc_get_template('loop/add-to-cart.php', [ 'class' => 'button add_to_cart_button ajax_add_to_cart' ]) ?>
        </div>
      </div>
      <div class="clear-left"></div>
    </div>
    <?

    // revert changes
    $GLOBALS['product'] = $_product;
  }

  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance = wp_parse_args( (array) $new_instance, array( 'product_id' => '', 'html' => '', 'image' => '' ) );

    return $instance;
  }

  public function flush_widget_cache() {
    wp_cache_delete('widget_alliance_build_posts', 'widget');
  }

  public function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 'product_id' => '', 'html' => '', 'image' => '' ) );

    $product_id = isset( $instance['product_id'] ) ? esc_attr( $instance['product_id'] ) : '';
    $html = isset( $instance['html'] ) ? esc_html( $instance['html'] ) : '';
    $image = isset( $instance['image'] ) ? esc_attr( $instance['image'] ) : '';
    ?>
    <p>
      <label for="<?= $this->get_field_name('product_id') ?>">Post ID of the product:</label>
      <input class="widefat" id="<?= $this->get_field_name('product_id') ?>" name="<?= $this->get_field_name('product_id') ?>" type="text" value="<?= $product_id ?>" />
    </p>
    <p>
      <label for="<?= $this->get_field_name('html') ?>">HTML to display:</label>
      <textarea class="widefat" id="<?= $this->get_field_name('html') ?>" name="<?= $this->get_field_name('html') ?>"><?= $html ?></textarea>
    </p>
    <p>
      <label for="<?= $this->get_field_name('image') ?>">URL of the image:</label>
      <input class="widefat" id="<?= $this->get_field_name('image') ?>" name="<?= $this->get_field_name('image') ?>" type="text" value="<?= $image ?>" />
    </p>
    <?
  }
}

add_action('widgets_init', function () {
    register_widget('CustomShopSpecialOfferWidget');
});
