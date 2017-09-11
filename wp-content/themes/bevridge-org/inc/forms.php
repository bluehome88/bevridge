<?php
/**
 * Form Handlers
 */

/*******************************************************************************
    Contact Form
*******************************************************************************/

add_action('wp_ajax_contact_form_submit', 'custom_contact_form_submit');
add_action('wp_ajax_nopriv_contact_form_submit', 'custom_contact_form_submit');

function custom_contact_form_submit () {
  // Humans will never fill out this input
  if ($_REQUEST['isrobot']) {
    status_header(404);
    nocache_headers();
    die;
  }

  if (!trim($_REQUEST['name'])) {
    custom_ajax_die('fail', 'Please, provide your name.', 'name');
  }

  if (!trim($_REQUEST['email'])) {
    custom_ajax_die('fail', 'Please, provide your Email address.', 'email');
  }

  if (!filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
    custom_ajax_die('fail', 'Invalid Email address provided.', 'email');
  }

  if (!trim($_REQUEST['message'])) {
    custom_ajax_die('fail', 'Please, enter some message.', 'message');
  }

  // All OK

  $to = get_option('admin_email');
  $subject = 'Bevridge contact form submission';
  $message = 'Name: ' . $_REQUEST['name'] . "\n";
  $message .= 'Email: ' . $_REQUEST['email'] . "\n";
  $message .= "\n" . $_REQUEST['message'];

  $headers = [];
  $headers['From'] = 'Bevridge <' . $to . '>';
  $headers['Content-Type'] = 'text/plain';
  $headers['Reply-To'] = $_REQUEST['email'];

  if (!wp_mail($to, $subject, $message, $headers)) {
    custom_ajax_die('fail', 'Unknown error has occured. Please, try again later.');
  }

  custom_ajax_die('ok', 'Reservation request has been successfully submitted.');
}
