/*
ts:2019-11-19 10:30:00
*/


DELIMITER ;;

-- Add the "thankyou" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'thankyou',
  'Thank you',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '0',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'thankyou' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'thankyou' AND `deleted` = 0)
LIMIT 1;;

UPDATE
  `plugin_pages_pages`
SET
  `title`         = 'Thank you',
  `publish`       = 1,
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'thankyou' LIMIT 1),
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `content`       = '<h1>Thank you</h1>

<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/thank_you.png" style="height:499px; width:800px"></p>

<div class="simplebox simplebox-raised">
	<div class="simplebox-columns" style="max-width:705px">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2 class="text-primary">Thank you for your booking</h2>

				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt. Ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</p>

				<p>Looking forward to seeing you</p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox just_booked">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>I just booked with CourseCo</h2>

				<p>Spread the good word about us. Invite your friends to join our community.</p>

				<p>
					<a class="share_button share_button\-\-facebook" href=\"https:\/\/www.facebook.com\/sharer\/sharer.php?u=http%3A%2F%2Firishtimestraining.com%3Fog_data%3Dsuccess\">Share on Facebook</a>
					<a class="share_button share_button\-\-twitter" href=\"http:\/\/twitter.com\/home\/?status=I+just+booked+with+http%3A%2F%2Firishtimestraining.com.\">Share on Twitter</a>
					<a class="share_button share_button\-\-email" href=\"mailto:?subject=I+just+booked+with+Irish+Times+Training&amp;body=I+just+booked+with+http%3A%2F%2Firishtimestraining.com\">Share by email</a>
				</p>

				<p>Check out our other <a href="/course-list">deals and offers</a>.</p>
			</div>
		</div>
	</div>
</div>',
  `footer` ='<div class="get_in_touch simplebox" style="background-color: #eee; margin-top: 70px;">
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
'
WHERE
  `name_tag` = 'thankyou'
;;