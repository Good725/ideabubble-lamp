/*
ts:2019-10-18 14:00:00
*/

DELIMITER ;;

-- Add 'Diploma in Digital Marketing' course, if it does not already exist
INSERT INTO `plugin_courses_courses` (`title`, `subject_id`, `date_created`, `created_by`, `modified_by`, `display_availability`)
(
  SELECT * FROM (SELECT
    'Diploma in Digital Marketing' AS `title`,
    (SELECT `id` FROM `plugin_courses_subjects` WHERE `title` = 'Digital Marketing' LIMIT 1) AS `subject_id`,
    CURRENT_TIMESTAMP AS `date_created`,
    '1' AS `created_by`,
    '1' AS `modified_by`,
    'per_schedule' AS `display_availability`
    ) AS tmp
  WHERE NOT EXISTS (SELECT `title` FROM `plugin_courses_courses` WHERE `title` = 'Diploma in Digital Marketing'
) LIMIT 1);;


-- Update the content of the 'Diploma in Digital Marketing' course
UPDATE
  `plugin_courses_courses`
SET
  `summary`     = '<p>Our 6 Week Digital Marketing Course content goes here to tell the customer all about what we offer and why they should purchase.</p>',
  `description` = '<div class="simplebox simplebox-overlap-right">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Tagline for course</h2>

				<p>The Diploma in Digital Marketing is delivered by digital marketing experts and is designed to equip you with the most up-to-date tools and techniques to drive and deliver a digital marketing strategy that will reach, engage, inform &amp; delight your customers.</p>

				<p><a class="button bg-category" href="#">CTA Button</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img src="/shared_media/irishtimestraining/media/photos//content/digital.png" /></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-equal-heights has_testimonials">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width: 68%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>About the course</h2>

				<p>Life for marketers used to be more straight forward. Reaching customers was easier if you were able to create a compelling message based on identifying customer needs. Now, marketers are required to build immersive experiences that engage consumers and seamlessly integrate a whole new range of skills and capabilities.</p>

				<p>The Diploma in Digital Marketing is delivered by digital marketing experts and is designed to equip you with the most up-to-date tools and techniques to drive and deliver a digital marketing strategy that will reach, engage, inform & delight your customers. We are constantly reviewing and updating the modules and content ensuring we are at the cutting edge in digital marketing teaching and practices.</p>

				<p>You can master all the essentials with the full Diploma in Digital Marketing or focus in on one key area, as many of the modules can be taken as stand-alone courses. Visit our Digital Marketing page for a full listing of our digital marketing courses.</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 32%;">
			<div class="simplebox-content bg-category">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p>{course_testimonials-}</p>
			</div>
		</div>
	</div>
</div>

<div>{addthis_toolbox-}</div>

<p>&nbsp;</p>

<div class="simplebox simplebox-align-top">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2 class="hidden\-\-tablet hidden\-\-desktop">Course Modules</h2>

				<div><img src="/shared_media/irishtimestraining/media/photos/content/promise2.png" alt="" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2 class="hidden\-\-mobile">Course Modules</h2>

				<ul>
					<li>Creating a Digital Marketing Strategy</li>
					<li>Creating an Effective Website</li>
					<li>Search Engine Optimisation (SEO)</li>
					<li>Creating a High-Impact Content Strategy</li>
					<li>Creating Live Action Marketing Videos</li>
					<li>Google Ads & Search Engine Marketing</li>
					<li>Creating Online Display Advertising Campaigns</li>
					<li>Social Media Marketing</li>
					<li>Email Marketing & Lead Generation</li>
					<li>Google Analytics &amp; Data-Driven Marketing</li>
					<li>Creating an Integrated Digital Marketing Plan</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-course-columns">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Who should attend</h2>

				<p>The practitioner interested in developing a focused Digital Strategy that can be used to inform and implement a Digital Marketing Plan for yourself, your company and/or client.</p>
				<p>The business owner/manager who wants to learn about how Digital Marketing can be used for their company and/or to be able to have an informed conversation with their website or marketing consultants.</p>
				<p>Anyone looking to earn a Digital Marketing qualification. There is the option to earn a QQI (formerly FETAC) Level 5 Minor Award on completion of a collection of work (see QQI Certification below)</p>

				<p><a href="#" class="button bg-success">CTA Button</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Certificates&nbsp;&amp;&nbsp;Accreditation</h2>

				<p>Participants who attend a minimum of 10 modules will be awarded an Irish Times Training Diploma in Digital Marketing (in association with the Irish Internet Association).</p>
				<p><strong>Digital Marketing QQI (formerly FETAC) Accreditation</strong><br />If you wish to obtain the Digital Marketing Level 5 Minor Award (5N1364), you are required to complete a collection of work (5 practical assignments) during the course, in addition to a Digital Marketing plan (due 6 weeks after the completion of the course). Minimum attendance requirement is 10 modules. (QQI Accreditation Fee of &euro;50 applicable).</p>
				<p><strong>Accreditation</strong><br />QQI (formerly FETAC) Digital Marketing Level 5 Minor Award (5N1364)</p>
			</div>
		</div>
	</div>
</div>

<div style="max-width: 670px; margin: auto;">
    <h2>Frequently Asked Questions</h2>

    <h6>Do I need to bring anything with me?</h6>

    <p>No, all course materials are provided. This includes notes, note paper and pens.</p>

    <h6>Is lunch provided?</h6>

    <p>Lunch is included in the course fee for the daytime course only. Coffee &amp; light refreshments are included.</p>

    <h6>Do I need to prepare anything in advance?</h6>

    <p>No pre-preparation is required for this course.</p>

    <h6>Can I pay in installments?</h6>

    <p>Yes, we offer a payment plan option for this course which is three installments over the duration of the course.</p>

    <h6>How do I get the QQI accreditation?</h6>

    <p>If you wish to obtain the Digital Marketing Level 5 Minor Award (5N1364), you are required to complete a collection of work (5 practical assignments) during the course, in addition to a Digital Marketing plan (due 6 weeks after the completion of the course). You must have attended a minimum of 10 modules to be eligible to go forward for this and a QQI Accreditation Fee of &euro;50 is applicable.</p>

    <h6>What happens if I miss a module during the course?</h6>

    <p>If you miss a module you can sit-in on a future course and this is valid for 12 months from the start date of your original course.</p>
</div>

<div class="simplebox simplebox-align-top simplebox-equal-heights simplebox-padded-content">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1 bg-category text-white mb-4">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2 class="m-0">Find out more</h2>

				<p>Any questions about this course.<br />Want to know if it&#39;s the right course for you?</p>

				<p class="my-3"><a href="#" class="button bg-success">CTA Button</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 bg-success text-white mb-4">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2 class="m-0">Download brochure</h2>

				<p class="my-3">Download a PDF of our course brochure.<br />&nbsp;</p>

				<p><a href="#" class="button bg-category" data-toggle="modal" data-target="#course-details-brochure-modal">Download PDF</a></p>
			</div>
		</div>
	</div>
</div>

<div class="get_in_touch simplebox simplebox-align-center">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1 pb-0">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div><img alt="" src="/shared_media/irishtimestraining/media/photos/content/get_in_touch_girl.png" class="ml-md-auto mr-md-5" style="height:480px; width:1920px" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Get in touch</h2>

				<p>Contact us to discuss <span class="nowrap">tailor-made</span> courses for your team</p>

				<p><a class="button bg-success" href="/contact-us">Contact us</a>
				   <a class="button bg-primary" href="/request-a-callback">Request a callback</a></p>
			</div>
		</div>
	</div>
</div>'
WHERE
  `title` = 'Diploma in Digital Marketing'
;;

UPDATE `engine_settings`
SET
  `value_live`  = '<p>I agree to allow my details to be used to sign up to the Irish Times Training mailing list. See <a href="/privacy-policy">Privacy Policy</a> for full details.</p>',
  `value_stage` = '<p>I agree to allow my details to be used to sign up to the Irish Times Training mailing list. See <a href="/privacy-policy">Privacy Policy</a> for full details.</p>',
  `value_test`  = '<p>I agree to allow my details to be used to sign up to the Irish Times Training mailing list. See <a href="/privacy-policy">Privacy Policy</a> for full details.</p>',
  `value_dev`   = '<p>I agree to allow my details to be used to sign up to the Irish Times Training mailing list. See <a href="/privacy-policy">Privacy Policy</a> for full details.</p>'
WHERE `variable` = 'newsletter_signup_terms';;