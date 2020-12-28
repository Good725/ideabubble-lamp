/*
ts:2019-10-15 15:02:00
*/

DELIMITER ;;

-- Add the "about us" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'about-us',
  'About us',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'about-us' AND `deleted` = 0)
LIMIT 1;;

UPDATE `plugin_pages_pages`
SET
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `content` = '<div class="simplebox simplebox-raised">
	<div class="simplebox-columns" style="max-width:800px">
		<div class="border-primary simplebox-column simplebox-column-1" style="border-right: 10px solid;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Our Approach</h2>

				<p>Irish Times Training has been in the professional development and education business for over 40 years. As a subsidiary of The Irish Times, we work with a broad range of people and organisations to deliver the highest quality training in Business, Management, Digital Marketing, Personal Development &amp; Executive Education programmes. Your training will be thought provoking, challenging, interesting and exciting, equipping you with the skills and knowledge to make real change in your organisation.</p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-overlap-left">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/promise.png" style="height:452px; width:678px" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Our promise</h2>

				<p>Our objective is to ensure that you leave with the knowledge, skills &amp; confidence to progress your career and continue to grow your potential. We are committed to supporting you in an immersive learning environment that encourages you to implement what you learn in the classroom and to engage with both trainers and other participants.</p>

				<p class="text-center text-md-right"><a class="button" href="#">CTA button</a></p>
			</div>
		</div>
	</div>
</div>

<div class="bg-md-none bg-primary simplebox text-white">
	<div class="bg-primary simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width:75%">
			<div class="p-md-5 simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2 class="mt-0 text-white">Our service</h2>

				<p class="text-white">We pride ourselves on keeping &ldquo;a step ahead&rdquo;. We do this by working with our panel of experts to regularly update course content and ensure that you are getting real-time insight into what is happening in industry. Providing up-to-date case studies and practical exercises are key features of our classroom-based courses</p>

				<p class="mb-0 text-center text-md-left"><a class="button bg-success" href="#">CTA button</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width:25%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p class="text-center text-md-left"><img alt="" src="/shared_media/irishtimestraining/media/photos/content/experts2.png" style="height:290px; width:290px" /></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox">
	<div class="simplebox-title">
		<h2 class="text-left">Accessibility</h2>

		<p class="text-left">Here is some information to help you in feeling supported and assisted in personal accommodations while participating in our programs:</p>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1 border-left-primary mb-4 mb-md-0" style="border-left: 10px solid;">
			<div class="simplebox-content pl-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3 class="mt-0 mb-3">Info Box 1</h3>

				<p class="my-2">Text for info box. Text for info box. Text for info box</p>

				<p class="m-0"><a href="#" class="text-primary"><strong>Learn more &raquo;</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 border-left-success-hover mb-4 mb-md-0" style="border-left: 10px solid;">
			<div class="simplebox-content pl-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3 class="mt-0 mb-3">Info Box 2</h3>

				<p class="my-2">Text for info box. Text for info box. Text for info box</p>

				<p class="m-0"><a href="#" class="text-success-hover"><strong>Learn more &raquo;</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3 border-left-success mb-4 mb-md-0" style="border-left: 10px solid;">
			<div class="simplebox-content pl-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3 class="mt-0 mb-3">Info Box 3</h3>

				<p class="my-2">Text for info box. Text for info box. Text for info box</p>

				<p class="m-0"><a href="#" class="text-success"><strong>Learn more &raquo;</strong></a></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width: 40%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
					<h2 class="hidden\-\-tablet hidden\-\-desktop">Why train with us?</h2>

				<p style="text-align: center;"><img src="/shared_media/irishtimestraining/media/photos/content/why_train.png" alt="" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 60%;">
			<div class="simplebox-content pl-md-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>
					<h2 class="hidden\-\-mobile">Why train with us?</h2>

					<ul class="list-unstyled accordion-basic">
						<li>
							<h3 class="active">Selling point 1</h3>

							<p>Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point.</p>
						</li>

						<li>
							<h3>Selling point 2</h3>

							<p class="hidden">Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point.</p>
						</li>

						<li>
							<h3>Selling point 3</h3>

							<p class="hidden">Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point.</p>
						</li>

						<li>
							<h3>Selling point 4</h3>

							<p class="hidden">Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point. Text for expanded selling point.</p>
						</li>
					</ul>
				</div>
		</div>
	</div>
</div>

<h2>Our Team</h2>

<p style="max-width: 700px;">We have a full-time staff of 12 and a panel of over 50 learning and development experts. Our tutors are highly experienced, working in their own specialised field and with extensive industry experience. Trainers are continuously assessed through feedback questionnaires which are conducted for every programme we deliver.</p>

<div class="simplebox simplebox-align-top team-section">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_1.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_2.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_3.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-4">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_1.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top team-section">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_2.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_3.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_1.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-4">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_2.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top team-section">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_2.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_3.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_1.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-4">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/bio_2.png" style="height:230px; width:230px"></p>

				<div>
					<h6>Name Surname</h6>

					<p>Title and position</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div>{course_testimonials-}</div>

<div class="get_in_touch simplebox" style="background-color: #eee;">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div><img alt="" src="/shared_media/irishtimestraining/media/photos/content/get_in_touch_guy.png" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Get in touch</h2>

				<p>Contact us to discuss <span class="nowrap">tailor-made</span> courses for your team.</p>

				<p><a class="button bg-success" href="/contact-us">Contact us</a>
				   <a class="button bg-primary" href="/request-a-callback">Request a callback</a></p>
			</div>
		</div>
	</div>
</div>
'
WHERE
  `name_tag` = 'about-us';;
