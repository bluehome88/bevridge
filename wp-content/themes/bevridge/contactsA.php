<?php
/**
Template Name: Page Contact Us
 */

get_header(); ?>

<!-- header -->
<div class="header-wrapper contacts_form_wrapper">
  <div class="header contacts">
     
<header class="inner_page_header"> <div class="container">
	
		<h1 class="inner_title"> Contact Us</h1> 
	
    </div></header>

      <div class="container">
        <div id="contacts">
          <div class="content">
            <div class="left-box">
            <iframe src="https://www.google.com/maps/d/u/1/embed?mid=1Z2Ack42UR1K2Xv--ioE52ox_5XA" width="640" height="380"></iframe>
             <!-- <p><b>For general inquiries:</b> info@thebevridge.com</p>
              <p>1 Global Avenue, Aranguez South, Trinidad.</p>
              <p><b>Tel:</b> (868) 612-4698. <b>Fax:</b> (868) 663-9676.</p>
              <p><b>BEVERAGE CSR North:</b> 612-4698 ext 2507 <b>or</b> 387-0812</p>
              <p><b>BEVERAGE CSR South:</b> 612-4698 ext 2508 <b>or</b> 387-0813</p>
              <p>Amandac@nwtenterprises.com</p>
              <p>Davikas@nwtenterprises.com</p>
              <p>Gabbye@nwtenterprises.com</p>-->
              <h4>FOR GENERAL INQUIRIES:</h4>
              <p>
               info@thebevridge.com<br/>
               1 Global Avenue, Aranguez South, Trinidad.<br/>
               Tel: (868) 612-4698<br/>
               Fax: (868) 663-9676<br/>
              </p>
            </div>
            <div class="right-box">
              <section id="contacts-form">
  <div class="container">
    <div class="form-wrapper">

      <form action="/" method="post" name="contact_form" class="contact-form ajax-form">
        <!-- Humans will never fill out this input -->
        <input type="text" name="isrobot" style="position: absolute; left: -999in">

        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <textarea name="message" placeholder="Message" required></textarea>

        <input type="submit">
        <input type="hidden" name="action" value="contact_form_submit">
      </form>

    </div>
  </div>
</section>
            </div>
          </div>
        </div>
      </div>

   
  </div>
</div>
<!-- header -->






<?php get_footer();