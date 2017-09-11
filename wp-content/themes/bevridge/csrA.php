<?php
/**
Template Name: Page Corporate Social Responsibility
 */

$newsArr = get_news_previews();

the_post();

get_header(); ?>


<section id="csr">

  <div class="container">
    <h2 class="skew">
      Corporate Social Responsibility
    </h2>
  </div>

  <div class="container">
    <div class="csr__open">
      <div class="csr__open-img skew">
        <? if ( has_post_thumbnail() ) { ?>
          <? the_post_thumbnail() ?>
        <? } ?>
      </div>
      <div class="csr__open-text">
        <h1><? the_title() ?></h1>
        <? the_content() ?>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="news">
      <div id="IndexNewsFlet" class="row">
        <?= $newsArr['html'] ?>
      </div>
    </div>
  </div>
</section>

<?php get_footer();