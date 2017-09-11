<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

  <!-- image for social networks -->
  <meta property="og:image" content="images/socimage.jpg">

  <!-- Icon For Apple Devices -->
  <link rel="apple-touch-icon" sizes="57x57" href="/images/favicons/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/images/favicons/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/images/favicons/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/images/favicons/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/images/favicons/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/images/favicons/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/images/favicons/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/images/favicons/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/images/favicons/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="/images/favicons/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/images/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/images/favicons/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/images/favicons/favicon-16x16.png">

  <!-- init viewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">



  <title>The BevRidge Company | Sales & Distribution of Beer in Trinidad & Tobago</title>

<?php wp_head(); ?>

 <script src="/js/jquery.bxslider.js"></script>
  
  <!-- stylesheets -->
  <link rel="stylesheet" href="/css/fonts.css">
  <link rel="stylesheet" href="/css/libs.css">
</head>

<body <?php body_class(); ?>>

<?php if ( has_nav_menu( 'top' ) ) : ?>
	<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
<?php endif; ?>



