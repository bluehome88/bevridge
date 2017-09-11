<?php
$newsArr = get_news_previews(1, POSTS_MEDIA_PER_PAGE, @$_GET['filter'], @$_GET['cat']);
get_header(); ?>

	<?php
		/* Start the Loop */
		while ( have_posts() ) : the_post();

      ?>
      <section id="newsandmedia" class="newsandmedia_single">
         
           <header class="inner_page_header"> <div class="container">
	
		<h1 class="inner_title">  news & media</h1> 
	
    </div></header>
          
          
         

       <?php /*?> <? get_template_part( 'template-parts/post/content-filters', get_post_format() ); ?><?php */?>

        <div class="container">
          <div class="news__open">
            <div class="news__open-img">
              <?= the_post_thumbnail( 'post-full' ) ?>
            </div>
            <div class="news__open-text">
              <h1><? the_title() ?></h1>
              <p><? the_content() ?></p>

            </div>

          </div>
        </div>

        <div class="container">
          <div class="news">
            <div id="IndexNewsFlet" class="row">
              <?= $newsArr['html'] ?>
            </div>

            <? if ($newsArr['more']) { ?>
            <a class="readall load-more-btn">load more <span class='wait'>...</span></a>
            <? } ?>
          </div>
        </div>
      </section>

      <script>
        jQuery(function($) {

          var newsPage = 2;
          var loadMoreDotsInt;
          var wait = document.querySelector('.wait');
          var $newsContent = $('#IndexNewsFlet');
          var $btn = $('.load-more-btn');

          $btn.click(function(e) {
            e.preventDefault();
            $btn = $(this);

            if ($btn.hasClass('loading'))
              return

            $btn.addClass('loading');

            dotsGoingUp = true;
            wait.innerHTML = '';

            loadMoreDotsInt = setInterval( function() {
            if ( dotsGoingUp )
              wait.innerHTML += '.';
            else {
              wait.innerHTML = '';
              if ( wait.innerHTML === '')
                  dotsGoingUp = true;
            }
            if ( wait.innerHTML.length > 2 )
              dotsGoingUp = false;
            }, 300);

            var req = {
              action: 'load_news_items',
              post_id: <?= $post->ID ?>,
              page: newsPage,
              filter: '<?= @$_GET['filter'] ?>',
              cid: '<?= @$_GET['cid'] ?>'
            };

            $.post('/wp-admin/admin-ajax.php', req, function(res) {
              $newsContent.append(res.html);

              if (!res.more) {
                $btn.hide();
              }

              newsPage++;
            }, 'json').always(function() {
              $btn.removeClass('loading');
              wait.innerHTML = '...';
              clearInterval(loadMoreDotsInt);
            });
          });

          /**
           * news and media filter
           */
          $('#newsandmedia .filter a').click(function(e){
            e.preventDefault();

            $('#newsandmedia .filter a').removeClass('active');
            $(this).addClass('active');
            $newsContent.addClass('loading');

            var target = $(this).data('target');
            var url = '<?= get_permalink() ?>';
            if (target) {
              url += '?cat=' + target;
            }
            window.history.pushState('', '', url);

            // $('.load-more-btn')


            var req = {
              action: 'load_news_items',
              post_id: <?= $post->ID ?>,
              page: 1,
              filter: '',
              cid: target
            };

            $.post('/wp-admin/admin-ajax.php', req, function(res) {
              $newsContent.html(res.html);

              if (!res.more) {
                $btn.hide();
              } else {
                $btn.show();
              }
            }, 'json').always(function() {
              $newsContent.removeClass('loading');
            });
          });
        });
      </script>

      <?
		endwhile; // End of the loop.
	?>

<?php get_footer();
