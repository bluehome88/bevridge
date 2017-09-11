<?php
/**
 * Brand post details
 */

add_action('dbx_post_advanced', function () {
  global $post;

  if ($post->post_type === 'brand') {
    add_meta_box('custom_brand_details', 'Brand details', 'custom_metabox_brand_details', null, 'normal');
  }
});

function custom_metabox_brand_details ($post) {
  $details = get_post_meta($post->ID, '_brand_details', true);
  $brands = custom_get_brands_categories([ 'hide_empty' => false ]);
  $product_types = custom_get_product_types();
  if (@$details['slider_bg_attachment_id']) {
    $slider_bg_attachment_meta = wp_get_attachment_metadata($details['slider_bg_attachment_id']);
    $slider_bg_filename = basename($slider_bg_attachment_meta['file']);
  }
  ?>
    <div class="brand_metabox_item">
      <p class="post-attributes-label-wrapper">
        <span class="post-attributes-label">Target brand:</span>
        <p>Select the target brand category from the list of product's categories to associate the current brand/post with. If you can't find the necessary category then it's probably missed and must be created <a href="/wp-admin/edit-tags.php?taxonomy=product_cat&post_type=product" target="_blank">here</a> (<a href="http://take.ms/wCUdV" target="_blank">screenshot</a>).</p>
      </p>
      <select name="brand_target_product_cat">
        <? foreach ($brands as $term) { ?>
          <option value="<?= esc_attr($term->term_id) ?>"<? if (@$details['target_product_cat'] === $term->term_id) echo ' selected' ?>><?= $term->name ?></option>
        <? } ?>
      </select>
    </div>

    <div class="brand_metabox_item">
      <p class="post-attributes-label-wrapper">
        <span class="post-attributes-label">Product type:</span>
        <p>Products of what types the brand is producing? Product types are categories of products that can be added/edited <a href="/wp-admin/edit-tags.php?taxonomy=product_cat&post_type=product" target="_blank">here</a> (<a href="http://take.ms/CHPsN0" target="_blank">screenshot</a>)</p>
      </p>
      <? $selected_types = explode('|', @$details['product_type']) ?>
      <select id="brand_product_type_select" multiple>
        <? foreach ($product_types as $term) { ?>
          <option value="<?= $term->term_id ?>" <? if (in_array($term->term_id, $selected_types)) echo 'selected' ?>><?= $term->name ?></option>
        <? } ?>
      </select>

      <input type="hidden" name="brand_product_type" value="<?= @$details['product_type'] ?>">
    </div>

    <div class="brand_metabox_item">
      <p class="post-attributes-label-wrapper">
        <span class="post-attributes-label">Thumbnail size:</span>
        <p style="margin-top: 0">Width and height of brand's thumbnail image (<a href="http://take.ms/ulxY4" target="_blank">screenshot</a>).</p>
      </p>
      <input type="text" name="brand_logo_thumb_width" size="3" value="<?= @$details['logo_thumb_width'] ?>"> x <input type="text" name="brand_logo_thumb_height" size="3" value="<?= @$details['logo_thumb_height'] ?>"> px
    </div>

    <div class="brand_metabox_item" id="custom_brand_slider_bg_wrap">
      <p class="post-attributes-label-wrapper">
        <span class="post-attributes-label">Popup background image:</span>
        <p style="margin-top: 0">Background image under the slider (<a href="http://take.ms/PP184" target="_blank">screenshot</a>).</p>
      </p>

      <div id="custom_brand_slider_bg_selected"<? if (!@$slider_bg_attachment_meta) echo " style='display:none'" ?>>
        <span class="attachment_filename" style="vertical-align:sub"><? if (@$slider_bg_attachment_meta) echo $slider_bg_filename ?></span>
        <button type="button" class="custom_brand_slider_bg_btn button">Change image</button>
      </div>

      <div id="custom_brand_slider_bg_notselected"<? if (@$slider_bg_attachment_meta) echo " style='display:none'" ?>>
        <button type="button" class="custom_brand_slider_bg_btn button">Add image</button>
      </div>

      <input type="hidden" name="brand_slider_bg_attachment_id" value="<?= @$details['slider_bg_attachment_id'] ?>">
    </div>
  <?php
}

// Handle form submit
add_action('save_post', function ($post_id) {
  if (@$_POST['brand_target_product_cat']) {
    $brand_details = [
      'target_product_cat' => (int) $_POST['brand_target_product_cat'],
      'logo_thumb_width' => (int) $_POST['brand_logo_thumb_width'],
      'logo_thumb_height' => (int) $_POST['brand_logo_thumb_height'],
      'product_type' => $_POST['brand_product_type'],
      'slider_bg_attachment_id' => (int) $_POST['brand_slider_bg_attachment_id']
    ];
    update_post_meta($post_id, '_brand_details', $brand_details);
  }
});

add_action('admin_head', function () {
  ?>
  <style>
  .brand_metabox_item {
    margin: 5px 0;
    border-top: 1px solid #ccc;
    padding: 0 2px 14px;
  }

  .brand_metabox_item:first-child {
    border-top: none;
    margin: 0;
  }

  #brand_product_type_select {
    min-width: 150px;
    min-height: 100px;
  }
  </style>
  <?
});

add_action('admin_footer', function () {
  ?>
  <script>
    /* global jQuery wp */
    (function ($) {
      $('#brand_product_type_select').change(function () {
        $('[name=brand_product_type]').val($(this).val().join('|'))
      })

      let sliderBgUploader
      $('.custom_brand_slider_bg_btn').click(function (e) {
        e.preventDefault()

        // If the uploader object has already been created, reopen the dialog
        if (sliderBgUploader) return sliderBgUploader.open()

        // Extend the wp.media object
        sliderBgUploader = wp.media.frames.file_frame = wp.media({
          multiple: false,
          title: 'Choose Image',
          button: {
            text: 'Choose Image'
          },
          // Tell the modal to show only images
          library: {
            type: 'image'
          }
        })

        // When a file is selected, grab the URL and set it as the text field's value
        sliderBgUploader.on('select', () => {
          const attachment = sliderBgUploader.state().get('selection').first().toJSON()
          $('#custom_brand_slider_bg_selected').show()
          $('#custom_brand_slider_bg_notselected').hide()

          $('#custom_brand_slider_bg_selected .attachment_filename').text(attachment.filename)
          $('[name=brand_slider_bg_attachment_id]').val(attachment.id)
        })

        // Open the uploader dialog
        sliderBgUploader.open()
      })
    })(jQuery)
  </script>
  <?
});
