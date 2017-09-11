<?php
add_shortcode('brand_details_list', function ($atts, $content = '') {
  $content = preg_replace('/<br ?\/?>/', "\n", $content);
  $content = trim($content);

  $lines = explode("\n", $content);
  $lines = array_filter($lines);
  foreach ($lines as &$line) {
    $parts = explode(': ', $line);
    if (count($parts) === 2) {
      $line = "<span class='brand-details-name'>$parts[0]</span>
      <span class='brand-details-value'>$parts[1]</span>";
    }
  }

  $content = join('</li><li>', $lines);
  $content = "<ul class='brand-details-list'><li>$content</li></ul>";

  return $content;
});

add_shortcode('attachment_download', function ($atts, $content = '') {
  if (!@$atts['href']) return $content;

  ob_start();
  ?>
  <div class="attachment-download">
    <span class="attachment-download-title">Attachment "<?= basename($atts['href']) ?>"</span>
    <a href="<?= esc_url($atts['href']) ?>" class="button-download-attachment" download>Download</a>
  </div>
  <?
  $content = ob_get_clean();

  return $content;
});
