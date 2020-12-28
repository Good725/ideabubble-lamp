/*
ts:2020-05-12 18:04:00
*/

/** Testimonials **/

DELIMITER ;;

UPDATE `engine_settings`
SET    `value_live`='1', `value_stage`='1', `value_test`='1', `value_dev`='1'
WHERE  `variable`='enable_testimonial_filters';;

-- Insert the "testimonials" page, if it doesn't already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'testimonials',
  'Testimonials',
  '<h1>Testimonials</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '1',
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('testimonials', 'testimonials.html') AND `deleted` = 0)
LIMIT 1;;

-- Update the page
UPDATE
  `plugin_pages_pages`
SET
  `name_tag`      = 'testimonials',
  `title`         = 'Testimonials',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'testimonials' AND `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04') AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = 1,
  `content`       = '',
  `footer`        = '
<div class="bg-light simplebox testimonial-videos">
	<div class="simplebox-title">
		<h2 class="mt-0" style="font-size: 35px; margin-bottom: 8px;">Hear from our clients</h2>
		<p style="font-weight: normal; margin-bottom: 37px;">Description of videos, if needed</p>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="bg-white simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div>{video-NycTraffic.mp4?i=1}</div>
				<h3>Title of video, title of video, title of video, title of video.</h3>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="bg-white simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div>{video-NycTraffic.mp4?i=2}</div>
				<h3>Title of video, title of video, title of video, title of video.</h3>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="bg-white simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div>{video-NycTraffic.mp4?i=3}</div>
				<h3>Title of video, title of video, title of video, title of video.</h3>
			</div>
		</div>
	</div>
</div>
\n
\n<p>{download_brochure-}</p>
\n
'
WHERE
  `name_tag` IN ('testimonials', 'testimonials.html');;

-- Add a testimonial
DELIMITER ;;
INSERT INTO `plugin_testimonials` (`category_id`, `title`, `item_signature`, `item_position`, `item_company`, `content`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES (
  (SELECT `id` FROM `plugin_testimonials_categories` WHERE `category` = 'Testimonials' LIMIT 1),
  'Anna Rozentale',
  'Anna Rozentale',
  'Programme Manager',
  'People Operations Services, Google',
  'I thoroughly enjoyed the Foundations in HRM 2 day programme, as someone new to HR and most of the concepts, I found this introduction very useful. The trainer was a brilliant facilitator.',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1', '1', '1', '0'
);;

UPDATE
  `plugin_testimonials`
SET
  `banner_image` = 'testimonial-banner-1.png',
  `summary`      = 'I thoroughly enjoyed the Foundations in HRM 2 day programme, as someone new to HR and most of the concepts, I found this introduction very useful. The trainer was a brilliant facilitator.',
  `content`      = '<p>I thoroughly enjoyed the Foundations in HRM 2 day programme, as someone new to HR and most of the concepts, I found this introduction very useful. The trainer was a brilliant facilitator.</p>',
  `image`        = 'woman.jpg'
WHERE
  `title` = 'Anna Rozentale';;

-- Split position and company into separate fields
UPDATE
  `plugin_testimonials`
SET
  `item_signature` = 'Finian O\'Brien',
  `item_position`  = 'Group HR Manager',
  `item_company`   = 'Rosderra Irish Meats Group',
  `image`          = ''
WHERE
  `title` = 'Rosderra';;
