<?php

/**
 * Job Manager application form submit handler
 */
add_filter('job_application_form_fields', function ($fields) {
  $fields['application_attachment']['label'] = 'Attach CV';

  //
  // Add custom appication form fields
  //

  $fields['candidate_phone'] = array(
    'label'       => 'Phone',
    'type'        => 'text',
    'required'    => true,
    'placeholder' => '',
    'priority'    => 1,
    'value'       => sanitize_text_field( @$_POST['candidate_phone'] ?: '' )
  );

  //
  // Simulate "Full Name" POST parameter when handling form submit with custom
  // "First name" and "Last name" (but no "Full Name")
  //

  if (!@$_POST['wp_job_manager_send_application']) return $fields;

  // our custom form doesn't has 'Full name' field
  if (isset($_POST['candidate_name'])) return $fields;

  try {
    if (!@$_POST['candidate_firstname']) {
      throw new Exception('"First name" is a required field');
    }

    if (!@$_POST['candidate_lastname']) {
      throw new Exception('"Last name" is a required field');
    }

    $_POST['candidate_name'] = trim($_POST['candidate_firstname'] . ' ' . $_POST['candidate_lastname']);

    foreach ( $fields as $key => $field ) {
      // Validate required
      if ( $field['required'] && (empty( $_POST[ $key ] ) && empty($_FILES[$key]) ) ) {
        throw new Exception( sprintf( __( '"%s" is a required field', 'wp-job-manager' ), $field['label'] ) );
      }
    }

    if (!is_email(@$_POST['candidate_email'])) {
      throw new Exception( __( 'Please provide a valid email address', 'wp-job-manager-applications' ) );
    }

    if ( !$_FILES['application_attachment']['size'] ) {
      throw new Exception( __( 'Please upload CV file', 'wp-job-manager-applications' ) );
    }

  } catch (Exception $e) {
    $GLOBALS['wp_job_manager_application_submit_error'] = $e->getMessage();
  }

  return $fields;
});
