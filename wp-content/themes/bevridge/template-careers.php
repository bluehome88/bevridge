<?php
/**
Template Name: Page Careers
 */

require_once TEMPLATEPATH . '/inc/helpers/Truncator.php';

use HtmlTruncator\Truncator;

$jobs = get_job_listings( apply_filters( 'job_manager_output_jobs_args', array(
  'orderby'           => 'featured',
  'order'             => 'DESC',
  'posts_per_page'    => 999,
  'featured'          => null, // True to show only featured, false to hide featured, leave null to show both.
  'filled'            => null // True to show only filled, false to hide filled, leave null to show both/use the settings.
) ) );

get_header();
?>

<section id="careers" class="page-wrap careers_template_page">

 
  <header class="inner_page_header">
<div class="container">
<h1 class="inner_title">Careers</h1>  
 
      </div>
      </header>

  <!--<div class="container">
    <div class="header__caption">
      <div class="caption__text">
        <p>
          We are looking for candidates<br> like you.<br>
          <span class="uppercase">Check all our vacancies below.</span>
        </p>
      </div>
      <div class="caption__img">
        <img src="/images/careers_caption_img.jpg" alt="" />
        <div class="logo">
          <img src="/images/logo.png" srcset="/images/logo@2x.png" alt="" />
        </div>
      </div>
    </div>
  </div>-->

  <div class="container">
    <div class="careers">

      <? if ( $jobs->have_posts() ) : ?>

        <?php // get_job_manager_template( 'job-listings-start.php' ); ?>

        <?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
          <?php
          ob_start();
          the_content();
          $content_short = $content = ob_get_clean();
          $content_short = preg_replace('/^(.*?<\/p>)\s*<p>/s', '$1', $content_short); // keep only one p
          $content_short = Truncator::truncate($content_short, 16, ' <span class="more">(more)</span>');
          $content_short = str_replace('</p><p> <span class="more">', ' <span class="more">', $content_short);
          $content_short = preg_replace('/<\/p>\s*(<span class="more">\(more\)<\/span>)$/s', ' $1</p>', $content_short);

          $error = @$GLOBALS['wp_job_manager_application_submit_error'];
          ?>
          <div <?php job_listing_class('careers__item ' . (@$_POST['job_id'] == $post->ID ? 'open' : '')); ?> id="job-<?= $post->ID ?>">
            <h3 class="job-title"><?php the_title(); ?></h3>
            <div class="careers__text-block careers__text-block-short" <? if (@$_POST['job_id'] == $post->ID) echo 'style="display: none;"' ?>>
              <div class="careers-job-content"><?= $content_short ?></div>
            </div>
            <div class="careers__text-block careers__text-block-full" <? if (@$_POST['job_id'] == $post->ID) echo 'style="max-height: 9999px; display: block;"' ?>>
              <div class="careers-job-content"><?= $content ?></div>
              <div class="careers-job-apply-form-wrap">
                <? if (@$_POST['job_id'] == $post->ID) { ?>
                  <? if ($error) { ?>
                    <p class="careers-job-apply-form-error"><?= $error ?></p>
                  <? } elseif (@$_POST['wp_job_manager_send_application']) { ?>
                    <p class="careers-job-apply-form-success">Your application has been submitted. Thank you!</p>
                  <? } ?>
                <? } ?>

                <?php
                    if ( get_option( 'job_application_form_require_login', 0 ) && ! is_user_logged_in() ) {
                      get_job_manager_template( 'application-form-login.php', array(), 'wp-job-manager-applications', JOB_MANAGER_APPLICATIONS_PLUGIN_DIR . '/templates/' );
                    }
                    else
                    {
                ?>
                  <form class="careers-job-apply-form careers-custom-job-apply-form" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" enctype="multipart/form-data">
                    <div class="input-row-two-cols">
                      <div class="input-row-col1">
                        <input type="text" name="candidate_firstname" placeholder="First Name" <? if ($error) echo 'value="' . esc_attr($_POST['candidate_firstname']) . '"' ?>>
                      </div>
                      <div class="input-row-col2">
                        <input type="text" name="candidate_lastname" placeholder="Last Name" <? if ($error) echo 'value="' . esc_attr($_POST['candidate_lastname']) . '"' ?>>
                      </div>
                    </div>
                    <div class="input-row">
                      <input type="text" name="candidate_email" placeholder="E-mail" <? if ($error) echo 'value="' . esc_attr($_POST['candidate_email']) . '"' ?>>
                    </div>
                    <div class="input-row">
                      <input type="text" name="candidate_phone" placeholder="Phone" <? if ($error) echo 'value="' . esc_attr($_POST['candidate_phone']) . '"' ?>>
                    </div>
                    <div class="input-row">
                      <textarea name="application_message" rows="5" placeholder="Message" <? if ($error) echo 'value="' . esc_attr($_POST['application_message']) . '"' ?>></textarea>
                    </div>
                    <div class="input-row">
                      <label class="carrers-application_attachment-label">
                        Attach CV
                        <input type="file" accept=".jpg,.jpeg,.jpe,.gif,.png,.bmp,.tiff,.tif,.ico,.asf,.asx,.wmv,.wmx,.wm,.avi,.divx,.flv,.mov,.qt,.mpeg,.mpg,.mpe,.mp4,.m4v,.ogv,.webm,.mkv,.3gp,.3gpp,.3g2,.3gp2,.txt,.asc,.c,.cc,.h,.srt,.csv,.tsv,.ics,.rtx,.css,.htm,.html,.vtt,.dfxp,.mp3,.m4a,.m4b,.ra,.ram,.wav,.ogg,.oga,.mid,.midi,.wma,.wax,.mka,.rtf,.js,.pdf,.class,.tar,.zip,.gz,.gzip,.rar,.7z,.psd,.xcf,.doc,.pot,.pps,.ppt,.wri,.xla,.xls,.xlt,.xlw,.mdb,.mpp,.docx,.docm,.dotx,.dotm,.xlsx,.xlsm,.xlsb,.xltx,.xltm,.xlam,.pptx,.pptm,.ppsx,.ppsm,.potx,.potm,.ppam,.sldx,.sldm,.onetoc,.onetoc2,.onetmp,.onepkg,.oxps,.xps,.odt,.odp,.ods,.odg,.odc,.odb,.odf,.wp,.wpd,.key,.numbers,.pages" multiple name="application_attachment" id="application_attachment">
                      </label>
                    </div>

                    <button type="submit" name="wp_job_manager_send_application" class="careers__learn-more applynow" value="1">APPLY NOW</button>

                    <input type="hidden" name="job_id" value="<?= $post->ID ?>">
                  </form>
                <?php 
                    }
                ?>
              </div>
            </div>
            <a href="<? the_permalink() ?>" class="learnmore careers__learn-more">LEARN MORE</a>
          </div>
        <?php endwhile; ?>

        <?php // get_job_manager_template( 'job-listings-end.php' ); ?>

      <?php else :
        do_action( 'job_manager_output_jobs_no_results' );
      endif;
      ?>

    </div>
  </div>

</section>

<?php
wp_enqueue_script( 'wp-job-manager-job-application' );

get_footer();
