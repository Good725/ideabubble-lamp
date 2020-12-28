/*
ts:2019-10-14 17:00:01
*/

DELIMITER ;;

-- Add the "springboard" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'springboard',
  'Springboard+',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'springboard' AND `deleted` = 0)
LIMIT 1;;

UPDATE `plugin_pages_pages`
SET
  `modified_by` = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `content` = '<div class="simplebox simplebox-raised">
	<div class="simplebox-columns" style="max-width:800px">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Bring your career to the next level</h2>

				<p>Employed, unemployed or returning to the workforce after time out? You may be eligible for a free or 90% subsidised Springboard+ higher education course co-funded by the Government of Ireland and the European Social Fund.</p>

				<p>In partnership with Ulster University, Irish Times Training deliver are delivering three third-level courses through Springboard+ 2019/20 in International Business, Capital Markets &amp; Entrepreneurship</p>

				<p><a class="button button-category mt-4" href="#">CTA button</a></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-overlap-left">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/group.png" style="height:452px; width:678px" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Postgraduate Certificate in International Business</h2>

				<p>Designed for individuals with a background in business or business-related studies who want to develop an integrated and critically-aware understanding of the international activities of a range of business organisations, at both the strategic and operational level.</p>

				<p><a class="button button-category mt-4" href="#">CTA button</a></p>
			</div>
		</div>
	</div>
</div>

<div class="background-extended simplebox simplebox-overlap-right-mobile">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Postgraduate Certificate in Global Capital Markets</h2>

				<p>Designed for graduates from any and all disciplines who want to pursue a career in the financial services industry and/ or those who want to specialise in a key subject area and those who are new to the financial services sector and want to get a better grounding in the principles of finance.</p>

				<p><a class="button button-category mt-4" href="#">CTA button</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/experts.png" style="height:420px; width:508px" /></p>
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

				<h2 class="m-0">Entrepreneurs Programme</h2>

				<h6 class="mt-0" style="font-size: 16px;">(leading to an Advanced Cert. in Management Practice)</h6>

				<p>Designed for individuals at the very early stage of forming a business to develop from a seedling idea to a fully functioning business, ensuring they have a well thought out and actionable business plan and are equipped with practical and usable skills to enable business growth.</p>

				<p><a class="button button-category mt-4" href="#">CTA button</a></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top reverse-mobile">
	<div class="bg-category simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width:70%">
			<div class="p-3 p-md-5 simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>About Springboard+</h2>

				<p>Springboard+ provides free higher education courses in areas of identified skills needs to unemployed people, those previously self-employed and those returning to work. Courses will also be free for employed people on NFQ Level 6 courses. For employed participants on courses NFQ level 7 &ndash; 9, 90% of the course fee will be funded, with participants required to contribute just 10% of the fee.</p>

				<p>Springboard+ allows you to bring your career to the next level &ndash; learn new skills, enhance your existing skills, get the promotion or job you&rsquo;ve always dreamed of.</p>

				<p>Springboard+ is managed by The Higher Education Authority on behalf of The Department of Education and Skills. Springboard+ is co-funded by the Government of Ireland and the European Social Fund as part of the ESF programme for Employability, Inclusion and Learning 2014-2020.</p>

				<p><a class="button bg-success" href="/course-list">Find my course</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width:30%">
			<div class="pt-5 simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p class="text-center text-md-left"><img alt="" src="/shared_media/irishtimestraining/media/photos/content/walk_the_talk.png" style="height:264px; width:264px" /></p>
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

				<div><img class="d-block" alt="" src="/shared_media/irishtimestraining/media/photos/content/get_in_touch_torso.png" /></div>
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

<p class="my-1">&nbsp;</p>

<h1 class="border-title-both">Some of our clients</h1>

'
WHERE
  `name_tag` = 'springboard';;
