<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<section>
  <div id="primary">
    <main id="main" class="site-main" role="main">
      <article <?php post_class(); ?>>
         <header class="inner_page_header"> <div class="container">
	
		<h1 class="inner_title">Oops! Page not found</h1>
	
    </div></header>
    
     <!-- .entry-header -->
        <div class="entry-content" style="padding:50px 0 120px;">
         <div class="container"> <p style="color:#000;">Looks like this page doesn't exist. <a href="/" style="color:#D0232A;">Go to home page</a>.</p></div>

          <?php // get_search_form(); ?>
        </div><!-- .entry-content -->
      </article><!-- #post-## -->

    </main>
  </div>
</section>

<?php get_footer();
