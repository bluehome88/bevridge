<div class="container">
  <div class="filter">
    <?
    $ln = get_category_by_slug('latest-news')->term_id;
    $lv = get_category_by_slug('latest-videos')->term_id;
    $cat = @$_GET['cat'];
    ?>
    <ul>
      <li><a href="<?= get_permalink() ?>" data-target="" class="<?= $cat ? '' : 'active' ?>">ALL</a></li>
      <li><a href="<?= get_permalink() ?>?cat=<?= $ln ?>" data-target="<?= $ln ?>" class="<?= $ln == $cat ? 'active' : '' ?>">LATEST NEWS</a></li>
      <li><a href="<?= get_permalink() ?>?cat=<?= $lv ?>" data-target="<?= $lv ?>" class="<?= $lv == $cat ? 'active' : '' ?>">LATEST VIDEOS</a></li>
    </ul>
  </div>
</div>