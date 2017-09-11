<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>


		<footer id="footer">
  <div class="container">
    <div class="footer">
      <div class="logo">
        <a href="/"><img src="/images/logo.png" srcset="/images/logo@2x.png 2x" alt="" /></a>
      </div>
      <div class="footer_block_left">
      <p>The Bevridge Company is a subsidiary of NWT Enterprises Ltd.</p>
      <div class="navigation">
        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php _e( 'Footer Menu', 'twentyseventeen' ); ?>">
          <?php wp_nav_menu( array(
            'theme_location' => 'footer',
            'menu'           => 'Footer Menu',
            'menu_id'        => 'footer-menu',
          ) ); ?>
        </nav>
      </div>
      <div class="social">
        <a href="https://www.facebook.com/thebevridge/"><img src="/images/icons/facebook_white.png" srcset="/images/icons/facebook_white@2x.png 2x" alt=""></a>
        <? /*<a href="#"><img src="/images/icons/twitter_white.png" srcset="/images/icons/twitter_white@2x.png 2x" alt=""></a>*/ ?>
        <a href="https://www.instagram.com/thebevridge/"><img src="/images/icons/insta_white.png" srcset="/images/icons/insta_white@2x.png 2x" alt=""></a>
      </div>
      </div>
      <div class="clear-left"></div>
    </div>
  </div>
</footer>
 

<?php wp_footer(); ?>
  
 
    
</body>
</html>
