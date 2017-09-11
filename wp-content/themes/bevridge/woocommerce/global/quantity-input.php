<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if (!isset($product)) global $product;

if ($product->is_in_stock()) {

  $attr_str = '';
  if (is_array(@$attrs)) {
    foreach ($attrs as $key => $value) {
      $attr_str .= $key . '="' . esc_attr($value) . '"';
    }
  }
  ?>
  <select name="<?= esc_attr(@$input_name ?: 'quantity') ?>" class="quantity-select" <?= $attr_str ?>>
    <? for ($i = 1; $i <= max(@$quantity, @$max_value, 10); $i++) { ?>
      <option value="<?= $i ?>"<? if ($i === @$quantity) echo ' selected'?>><?= $i ?></option>
    <? } ?>
  </select>
  <?php

}
