<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>
<div class="cart__body">
  <div class="cart__table cart_list product_list_widget <?= esc_attr( $args['list_class'] ); ?>">
    <div class="cart__empty empty">
      <p>CART EMPTY !</p>
    </div>

    <?php if ( ! WC()->cart->is_empty() ) : ?>
      <div class="table cart-head">
        <div class="tr">
          <div class="th">Product</div>
          <div class="th"></div>
          <div class="th">Qty</div>
          <div class="th">Price</div>
        </div>
      </div>

      <?php do_action( 'woocommerce_before_mini_cart_contents' ); ?>

      <div class="mini-cart-items">
        <?php
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
          $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
          $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

          if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
            $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
            $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
            $product_price     = apply_filters( 'woocommerce_cart_item_price', str_replace('.00', '', WC()->cart->get_product_price( $_product )), $cart_item, $cart_item_key );
            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
            ?>
            <div class="cart__item <?= esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
              <div class="table">
                <div class="tr">
                  <div class="td">
                    <? if ($_product->is_visible()) { ?>
                      <a href="<?= esc_url( $product_permalink ); ?>">
                    <? } ?>
                      <div class="img-box">
                        <?= str_replace( array( 'http:', 'https:' ), '', $thumbnail ) ?>
                      </div>
                    <? if ($_product->is_visible()) { ?>
                      </a>
                    <? } ?>
                  </div>
                  <div class="td">
                    <div class="info-block">
                      <h3>
                        <? if ($_product->is_visible()) { ?>
                          <a href="<?= esc_url( $product_permalink ); ?>">
                        <? } ?>
                        <?= $product_name ?>
                        <? if ($_product->is_visible()) { ?>
                          </a>
                        <? } ?>
                      </h3>

                      <p class="product-weight"><?= $_product->get_attribute('pa_weight') ?: '' ?></p>
                    </div>
                  </div>
                  <div class="td">
                    <div class="qty-wrap">
                      <? wc_get_template('global/quantity-input.php', [
                        'quantity' => $cart_item['quantity'],
                        'attrs' => [
                          'data-cart_item_key' => $cart_item_key
                        ],
                        'product' => $_product
                      ]) ?>
                    </div>
                  </div>
                  <div class="td">
                    <p class="price"><?= $product_price ?></p>

                    <?php
                    echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                      '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"></a>',
                      esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
                      __( 'Remove this item', 'woocommerce' ),
                      esc_attr( $product_id ),
                      esc_attr( $_product->get_sku() )
                    ), $cart_item_key );
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <?php
          }
        }
        ?>
      </div>

      <?php do_action( 'woocommerce_mini_cart_contents' ); ?>

    <?php endif; ?>
  </div><!-- end product list -->

  <?php if ( ! WC()->cart->is_empty() ) : ?>
    <div class="cart__total">
      <div class="cart__total-heading">
        <p class="total"><?php _e( 'Subtotal', 'woocommerce' ); ?> <span>(Price before shipping)</span></p>
      </div>
      <div class="cart__total-price">
        <p class="price total">
          <?/* echo WC()->cart->get_cart_subtotal() */

          if ( 'excl' === WC()->cart->tax_display_cart ) {

            $cart_subtotal = WC()->cart->subtotal_ex_tax;

            if ( WC()->cart->tax_total > 0 && WC()->cart->prices_include_tax ) {
              $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
            }
          } else {

            $cart_subtotal = WC()->cart->subtotal;

            if ( WC()->cart->tax_total > 0 && ! WC()->cart->prices_include_tax ) {
              $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
            }
          }

          echo wc_price($cart_subtotal);
          ?>
        </p>
      </div>
    </div>

    <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

    <div class="cart__actions buttons">
      <a class="cart__action cart__action--red continue-shopping">continue shopping</a>
      <a href="/checkout/" class="button checkout wc-forward cart__action cart__action--black check-out">check out now</a>
    </div>

  <?php endif; ?>

</div>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
