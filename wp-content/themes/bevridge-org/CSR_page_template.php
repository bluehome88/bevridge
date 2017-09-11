<?php
/**
Template Name: CSR Page Template
 */

get_header(); ?>


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="inner_page_header"> <div class="container">
	
 <h1 class="inner_title">Events & Promotions</h1>
	
    </div></header> 
	<div class="inner_page_content">
    <div class="container">
			<?php
			while ( have_posts() ) : the_post();

				 

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

<?php
			the_content();

		 
		?>
        </div>
			 
		 
	</div><!-- .entry-content -->
</article><!-- #post-## -->


<?php get_footer();