<?php

/**
 * Fix Job Manager plugin admin CSS
 */
add_action('admin_head', function () {
  ?>
  <style>
    .wp_job_manager_meta_data label {
      display: block;
      position: static;
      width: auto;
    }
  </style>
  <?
});
