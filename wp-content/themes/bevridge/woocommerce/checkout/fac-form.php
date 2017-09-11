<?php
$order = new WC_Order( $_GET['order'] );

$form_html = get_post_meta($order->get_id(), "order_fac_form", true);

if (!$form_html) {
  die("The session is expired.");
}

if (!$order) {
  die('Order not found.');
}

echo $form_html;