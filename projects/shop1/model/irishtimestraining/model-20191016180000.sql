/*
ts:2019-10-15 15:02:00
*/

-- Add subjects, if they do not already exist
INSERT INTO `plugin_courses_subjects` (`name`, `order`, `color`, `date_modified`, `created_by`,`modified_by`)
(SELECT * FROM (SELECT 'Personal Effectiveness & Communication Skills', '1' as `order`, '#152553', CURRENT_TIMESTAMP, '1', '1' as `modified_by`)AS tmp
WHERE NOT EXISTS (SELECT name FROM plugin_courses_subjects WHERE name = 'Personal Effectiveness & Communication Skills') LIMIT 1);

INSERT INTO `plugin_courses_subjects` (`name`, `order`, `color`, `date_modified`, `created_by`,`modified_by`)
SELECT * FROM (SELECT 'Leadership & Management', '2', '#925EA4', CURRENT_TIMESTAMP, '1', '1' as `modified_by`)AS tmp
WHERE NOT EXISTS (SELECT name FROM plugin_courses_subjects WHERE name = 'Leadership & Management') LIMIT 1;

INSERT INTO `plugin_courses_subjects` (`name`, `order`, `color`, `date_modified`, `created_by`,`modified_by`)
SELECT * FROM (SELECT 'Strategy & Organisational Development', '3', '#6EB9AA', CURRENT_TIMESTAMP, '1', '1' as `modified_by`)AS tmp
WHERE NOT EXISTS (SELECT name FROM plugin_courses_subjects WHERE name = 'Strategy & Organisational Development') LIMIT 1;

INSERT INTO `plugin_courses_subjects` (`name`, `order`, `color`, `date_modified`, `created_by`,`modified_by`)
SELECT * FROM (SELECT 'Digital Marketing', '4', '#077C94', CURRENT_TIMESTAMP, '1', '1' as `modified_by`)AS tmp
WHERE NOT EXISTS (SELECT name FROM plugin_courses_subjects WHERE name = 'Digital Marketings') LIMIT 1;

INSERT INTO `plugin_courses_subjects` (`name`, `order`, `color`, `date_modified`, `created_by`,`modified_by`)
SELECT * FROM (SELECT 'HR & Learning Development', '5', '#A52680', CURRENT_TIMESTAMP, '1', '1' as `modified_by`)AS tmp
WHERE NOT EXISTS (SELECT name FROM plugin_courses_subjects WHERE name = 'HR & Learning Development') LIMIT 1;

INSERT INTO `plugin_courses_subjects` (`name`, `order`, `color`, `date_modified`, `created_by`,`modified_by`)
SELECT * FROM (SELECT 'Microsoft Office', '6', '#FF9650', CURRENT_TIMESTAMP, '1', '1' as `modified_by`)AS tmp
WHERE NOT EXISTS (SELECT name FROM plugin_courses_subjects WHERE name = 'Microsoft Office') LIMIT 1;

INSERT INTO `plugin_courses_subjects` (`name`, `order`, `color`, `date_modified`, `created_by`,`modified_by`)
SELECT * FROM (SELECT 'Customer Success', '7', '#456EB6', CURRENT_TIMESTAMP, '1', '1' as `modified_by`)AS tmp
WHERE NOT EXISTS (SELECT name FROM plugin_courses_subjects WHERE name = 'Customer Success') LIMIT 1;

INSERT INTO `plugin_courses_subjects` (`name`, `order`, `color`, `date_modified`, `created_by`,`modified_by`)
SELECT * FROM (SELECT 'Sales', '8', '#73A6C4', CURRENT_TIMESTAMP, '1', '1' as `modified_by`)AS tmp
WHERE NOT EXISTS (SELECT name FROM plugin_courses_subjects WHERE name = 'Sales') LIMIT 1;

-- These are subjects, not categories
UPDATE `plugin_courses_categories` SET `delete` = 1
WHERE `category` IN (
  'Communication & interpersonal skills', 'Leadership & management', 'Strategy & organisational development',
  'Digital marketing', 'HR & learning development', 'Microsoft Office', 'Customer Success', 'Sales'
);

/*
ts:2019-10-15 15:02:00
*/

DELIMITER ;;

-- Add the "leadership and management" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'leadership-and-management',
  'Leadership & Management',
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
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'leadership-and-management' AND `deleted` = 0)
LIMIT 1;;

UPDATE `plugin_pages_pages`
SET
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `content` = '<div class="simplebox simplebox-align-top simplebox-equal-heights simplebox-raised">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width:76.25%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Leadership at every level</h2>

				<p>Successful businesses don&#39;t just rely on one person to take charge. They need leadership skills at every level of the organisation.</p>

				<p>Taking on a project, team, department or company takes guts, emotional intelligence and skill. Make sure you have what it takes.</p>

				<p><a class="button button-category" href="/course-list">Find my course</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width:23.75%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2 class="hidden\-\-tablet hidden\-\-desktop">See Courses</h2>
				<h5 class="hidden\-\-mobile">See Courses</h5>

				<p style="font-size: 16px;">Choose from a list of courses in our Leadership &amp; management category</p>

				<div>{course_selector-}</div>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-overlap-left">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/Handling-Difficult-Conversations.png" style="height:452px; width:678px" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Mastering management</h2>

				<p>A good manager has skills in a wide range of areas. It&#39;s not just about knowing your business inside and out; it&#39;s about getting the most out of people.</p>

				<p>Refresh your skills and add new tools to the mix. Then see your work life run smoother.</p>

				<p><a class="button button-category" href="/course-list.html">Find my course</a></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top">
	<div class="bg-category p-5 simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width:72%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2 class="mt-0">How Can Irish Times Training Help?</h2>

				<p>Irish Times Training knows the benefits of capitalising on both of these skill sets. That&#39;s why our trainers have real-life management and leadership experience. They use that to hone your natural abilities and help you to acquire practical skills relevant to your working life.</p>

				<p>Take a short course for a quick brush up or take on an <strong>Ulster University Accredited course</strong> to gain in-depth knowledge.</p>

				<p class="mb-0"><a class="button bg-success" href="/course-list.html">Find my course</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 m-0 p-0" style="width:28%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/people_talking.png" style="height:264px; width:264px" /></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top">
	<div class="simplebox-title">
		<h2 style="text-align:center">Why are Leadership &amp; Management Important?</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p style="text-align:center"><img alt="" src="/shared_media/irishtimestraining/media/photos/content/Drive_Innovation.svg" style="height:110px;width:69px;" /></p>

				<h5 style="text-align:center">Drive innovation</h5>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h5 style="text-align:center"><img alt="" src="/shared_media/irishtimestraining/media/photos/content/Nurture_Positive_Culture.svg" style="height:110px; width:110px" /></h5>

				<h5 style="text-align:center">Boost team spirit</h5>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h5 style="text-align:center"><img alt="" src="/shared_media/irishtimestraining/media/photos/content/Encourage_Growth.svg" style="height:110px; width:110px" /></h5>

				<h5 style="text-align:center">Encourage growth</h5>
			</div>
		</div>
	</div>
</div>

<div class="simplebox">
	<div class="bg-category simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width:38%">
			<div class="pl-5 simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/upskill_your_team.png" style="height:257px; width:330px" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width:62%">
			<div class="pl-2 pr-5 py-5 simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Looking to upskill your team?</h2>

				<p>Our <span class="nowrap">tailor-made</span> service offers choice, value and the opportunity to tailor learning.</p>

				<p>We work in partnership with you to assess your organisation&rsquo;s current training needs and design a learning and development solution specific to your team.</p>

				<p><a class="button bg-success" href="/course-list.html">Find out more</a></p>
			</div>
		</div>
	</div>
</div>

<div>{course_testimonials-}</div>

<div class="get_in_touch simplebox simplebox-align-center">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
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
</div>
'
WHERE
  `name_tag` = 'leadership-and-management';;

