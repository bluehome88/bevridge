<?php
/**
 * Displays top navigation
 */
?>

<div class="phone-navigation">
  <a href="#" class="close" title="close">&times;</a>
  <?php wp_nav_menu( array(
    'theme_location' => 'top',
    'menu_class'     => 'top-menu',
  ) ); ?>
  <div class="soc">
    <a href="https://www.facebook.com/thebevridge/"><img src="/images/icons/facebook_white.png" srcset="/images/icons/facebook_white@2x.png 2x" alt=""></a>
    <? /*<a href="#"><img src="/images/icons/twitter_white.png" srcset="/images/icons/twitter_white@2x.png 2x" alt=""></a>*/ ?>
    <a href="https://www.instagram.com/thebevridge/"><img src="/images/icons/insta_white.png" srcset="/images/icons/insta_white@2x.png 2x" alt=""></a>
  </div>
</div>

<!-- nav -->
<div class="nav">
  <div class="container">
    <div class="header__nav">
      <a href="/"><div class="header__logo"></div></a>
      <div class="header__menu">

        <div id="nav-icon1">
          <span></span>
          <span></span>
          <span></span>
        </div>

        <div class="social">
          <ul>
            <li><a href="https://www.instagram.com/thebevridge/"><img src="/images/icons/insta_red.png" alt=""></a></li>
            <?/*<li><a href="#"><img src="/images/icons/twitter_red.png" alt=""></a></li>*/ ?>
            <li><a href="https://www.facebook.com/thebevridge/"><img src="/images/icons/facebook_red.png" alt=""></a></li>
          </ul>
        </div>
        <div class="menu">
         <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php _e( 'Top Menu', 'twentyseventeen' ); ?>">
			<?php wp_nav_menu( array(
				'theme_location' => 'top',
				'menu_id'        => 'top-menu',
			) ); ?>
		</nav>
        </div>
      </div>
    </div>
  </div>
</div>

