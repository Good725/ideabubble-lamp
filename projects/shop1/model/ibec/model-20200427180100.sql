/*
ts:2020-04-27 18:01:00
*/

-- Add top-nav menu items
INSERT INTO `plugin_menus`
(`category`, `title`,          `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`,    `created_by`, `modified_by`, `menus_target`, `image_id`) VALUES
('top nav',  'About us',        '',        '',         '0',       '0',         '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('top nav',  'Emerging trends', '',        '',         '0',       '0',         '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('top nav',  'Contact us',      '',        '',         '0',       '0',         '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('top nav',  'Student portal',  '',        '',         '0',       '0',         '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0');

-- Add footer menus items
INSERT INTO `plugin_menus`
(`category`, `title`,               `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`,    `created_by`, `modified_by`, `menus_target`, `image_id`) VALUES
('footer',   'The Academy',         '',         '',         '1',       '0',         '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Find Your Programme', '',         '',         '1',       '0',         '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Stay Connected',      '',         '',         '1',       '0',         '3',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0');

INSERT INTO `plugin_menus`
(`category`, `title`,                  `link_tag`, `link_url`,               `has_sub`, `parent_id`,                                                                                                                        `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`,    `created_by`, `modified_by`, `menus_target`, `image_id`) VALUES
('footer',   'About Us',               '',         '/about-us',              '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'The Academy'         ORDER BY `id` DESC LIMIT 1), '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Customised Programmes',  '',         '/customised-programmes', '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'The Academy'         ORDER BY `id` DESC LIMIT 1), '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Accredited Partner',     '',         '/accredited-partner',    '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'The Academy'         ORDER BY `id` DESC LIMIT 1), '3',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Management Development', '',         '/management-development','0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'Find Your Programme' ORDER BY `id` DESC LIMIT 1), '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Emerging Trends',        '',         '/emerging-trends',       '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'Stay Connected'      ORDER BY `id` DESC LIMIT 1), '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0');

INSERT INTO `plugin_menus`
(`category`, `title`,                       `link_tag`, `link_url`,                    `has_sub`, `parent_id`,                                                                                                                        `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`,    `created_by`, `modified_by`, `menus_target`, `image_id`) VALUES
('footer',   'Join Ibec',                   '',         '/join-ibec',                  '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'The Academy'         ORDER BY `id` DESC LIMIT 1), '4',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Privacy Statement',           '',         '/privacy',                    '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'The Academy'         ORDER BY `id` DESC LIMIT 1), '5',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Terms & Conditions',          '',         '/terms-and-conditions',       '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'The Academy'         ORDER BY `id` DESC LIMIT 1), '6',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Personal Development',        '',         '/personal-development',       '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'Find Your Programme' ORDER BY `id` DESC LIMIT 1), '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Emerging Trends',             '',         '/emerging-trends',            '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'Find Your Programme' ORDER BY `id` DESC LIMIT 1), '3',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Industrial Relations',        '',         '/industrial-relations',       '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'Find Your Programme' ORDER BY `id` DESC LIMIT 1), '4',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Occupational Health & Safety','',         '/occupational-health-safety', '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'Find Your Programme' ORDER BY `id` DESC LIMIT 1), '5',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0'),
('footer',   'Hear from our Clients',       '',         '/testimonials',               '0',       (SELECT `id` FROM `plugin_menus` `x` WHERE `category` = 'footer' AND `title` = 'Stay Connected'      ORDER BY `id` DESC LIMIT 1), '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,         null,          '_self',        '0');

-- Update settings
UPDATE `engine_settings` SET `value_dev` = '04',    `value_test`  = '04',    `value_stage` = '04',    `value_live` = '04'    WHERE `variable` =  'template_folder_path';
UPDATE `engine_settings` SET `value_dev` = '51',    `value_test`  = '51',    `value_stage` = '51',    `value_live` = '51'    WHERE `variable` =  'assets_folder_path';
UPDATE `engine_settings` SET `value_dev` = 'none',  `value_test`  = 'none',  `value_stage` = 'none',  `value_live` = 'none'  WHERE `variable` =  'course_finder_mode';
UPDATE `engine_settings` SET `value_dev` = '0',     `value_test`  = '0',     `value_stage` = '0',     `value_live` = '0'     WHERE `variable` IN ('frontend_login_link', 'home_page_course_categories_feed');
UPDATE `engine_settings` SET `value_dev` = '',      `value_test`  = '',      `value_stage` = '',      `value_live` = ''      WHERE `variable` = 'site_footer_logo';
UPDATE `engine_settings` SET `value_dev` = 'FALSE', `value_test`  = 'FALSE', `value_stage` = 'FALSE', `value_live` = 'FALSE' WHERE `variable` = 'newsletter_subscription_form';
UPDATE `engine_settings` SET `value_dev` = 1,       `value_test` = 1,        `value_stage` = 1,       `value_live` = 1       WHERE `variable` = 'course_layout_auto_details';
UPDATE `engine_settings` SET `value_dev` = '6',     `value_test`  = '6',     `value_stage` = '6',     `value_live` = '6'     WHERE `variable` = 'news_feed_item_count';
UPDATE `engine_settings` SET `value_dev` = 'Ibec',  `value_test`  = 'Ibec',  `value_stage` = 'Ibec',  `value_live` = 'Ibec'  WHERE `variable` = 'company_title';
UPDATE `engine_settings` SET `value_dev` = '1',     `value_test`  = '1',     `value_stage` = '1',     `value_live` = '1'     WHERE `variable` = 'site_searchbar';
UPDATE `engine_settings` SET `value_dev` = '10',    `value_test`  = '10',    `value_stage` = '10',    `value_live` = '10'    WHERE `variable` = 'testimonials_feed_item_count';

UPDATE `engine_settings` SET `value_dev` = 'categories', `value_test`  = 'categories', `value_stage` = 'categories', `value_live` = 'categories' WHERE `variable` = 'course_details_breadcrumbs';
UPDATE `engine_settings` SET `value_dev` = 'accreditation_provider', `value_test`  = 'accreditation_provider', `value_stage` = 'accreditation_provider', `value_live` = 'accreditation_provider' WHERE `variable` = 'course_details_banner_text';

UPDATE `engine_settings` SET `value_dev` = 'Dublin Office',                   `value_test`  = 'Dublin Office',                   `value_stage` = 'Dublin Office',                   `value_live` = 'Dublin Office'                   WHERE `variable` = 'addres_line_1';
UPDATE `engine_settings` SET `value_dev` = '84/86 Lower Baggot Street',       `value_test`  = '84/86 Lower Baggot Street',       `value_stage` = '84/86 Lower Baggot Street',       `value_live` = '84/86 Lower Baggot Street'       WHERE `variable` = 'addres_line_2';
UPDATE `engine_settings` SET `value_dev` = 'Dublin 2, Ireland<br />D02 H720', `value_test`  = 'Dublin 2, Ireland<br />D02 H720', `value_stage` = 'Dublin 2, Ireland<br />D02 H720', `value_live` = 'Dublin 2, Ireland<br />D02 H720' WHERE `variable` = 'addres_line_3';


-- Widgets
INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`) VALUES
('Download brochure', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0', 'download_brochure'),
('Get started',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0', 'get_started');

INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`) VALUES
('Spotlights', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0', 'spotlights');

-- "Download brochure" widget
UPDATE `engine_feeds`
SET `content` = '<div class="simplebox simplebox-download_brochure">
	<div class="simplebox-background_image">
		<p><img alt="" src="/shared_media/ibec/media/photos/content/pdf-download-1.jpg" style="height:830px; width:2560px"></p>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h1 class="text-white">Management Training<br />Programmes <span style="font-weight: 200;">2020</span></h1>

				<h2><a class="button-full-brochure" href="/coming-soon" style="color: #a7a8a7; text-decoration: none;">Download our full brochure</a></h2>
			</div>
		</div>
	</div>
</div>
'
WHERE `name` = 'Download brochure';

-- "Get started" widget
UPDATE `engine_feeds`
SET `content` = '<div class="bg-light-gray-gradient simplebox simplebox-get_started">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width: 45%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Get started</h2>

				<p style="font-size: 18px; font-weight: normal;">If you have any questions about our programmes, or need help choosing the right programme for you, call us today...</p>

				<p><a class="read_more" href="/contact-us" style="font-size: 18px;"><strong>Contact us</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 pb-0" style="width: 55%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div><img alt="" class="d-block ml-auto mr-sm-auto" src="/shared_media/ibec/media/photos/content/contact-1.png" style="height:450px; width:434px"></div>
			</div>
		</div>
	</div>
</div>
'
WHERE `name` = 'Get started';

-- Spotlights widget
UPDATE `engine_feeds`
SET `content` = '<div class="bg-white simplebox simplebox-spotlights2 simplebox-equal-heights mb-0">
	<div class="simplebox-title">
		<h2>Spotlights</h2>
	</div>

	<div class="simplebox-columns">
		<div class="bg-main_gray simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<a href="/news">
					<h3 class="text-dark_purple">
						<img class="hidden\-\-desktop hidden\-\-tablet" alt="" src="/shared_media/ibec/media/photos/content/grey-man-120.jpg" style="float:right; height:245px; width:120px" />
						<img class="hidden\-\-mobile" alt="" src="/shared_media/ibec/media/photos/content/grey-man.jpg" style="float:right; height:190px; width:86px" />
						News <span class="d-block d-sm-inline">&amp;</span> Events
					</h3>
				</a>
			</div>
		</div>

		<div class="bg-dark_purple simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<a href="/coming-soon">
					<h3 class="text-main_gray">Top Tips<br />2020</h3>
				</a>
			</div>
		</div>

		<div class="bg-bright_purple simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3><a class="text-dark_purple" href="/coming-soon">Ibec <br class="hidden\-\-tablet_up" />Membership
					<br /><span class="text-white">Join today and progress your business</span></a></h3>

			</div>
		</div>

		<div class="bg-light simplebox-column simplebox-column-4">
			<div class="simplebox-content" style="background: url(\'shared_media/ibec/media/photos/content/white-flower.jpg\') no-repeat; background-size: cover;">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<a href="/news">
					<h3 class="text-dark_purple">Emerging <br class="hidden\-\-tablet_up" />trends</h3>
				</a>
			</div>
		</div>
	</div>
</div>'
WHERE `name` = 'Spotlights';


-- New news category
INSERT INTO `plugin_news_categories`
  (`category`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `delete`)
VALUES
  ('Emerging trends', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0');

INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `event_date`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of article. Emerging trend 1', 'emerging-trends-1.jpg', '2020-04-24 00:00:00', '1', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of article. Emerging trend 2', 'emerging-trends-2.jpg', '2020-04-22 00:00:00', '2', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of article. Emerging trend 3', 'emerging-trends-3.jpg', '2020-04-20 00:00:00', '3', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0');

-- Testimonial
DELIMITER ;;
INSERT INTO `plugin_testimonials` (`category_id`, `title`, `item_signature`, `item_company`, `content`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES (
  (SELECT `id` FROM `plugin_testimonials_categories` WHERE `category` = 'Testimonials' LIMIT 1),
  'Rosderra',
  'Finian O\'Brien',
  'Group HR Manager<br />Rosderra Irish Meats Group',
  '<p>Ibec&rsquo;s collaborative approach enabled us to develop a bespoke programme that really suited our learner&rsquo;s needs. They really understood what we wanted to achieve. As a result the design and delivery of the programme was very real, practical and included real business improvements for the organisation</p>\n',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1', '1', '1', '0'
);;

UPDATE
  `plugin_testimonials`
SET
  `banner_image` = 'testimonial-banner-1.png',
  `summary` = 'Ibec’s collaborative approach enabled us to develop a bespoke programme that really suited our learner’s needs.\n\nThey really understood what we wanted to achieve. As a result the design and delivery of the programme was very real, practical and included real business improvements for the organisation.',
  `content` = '<p>Ibec&rsquo;s collaborative approach enabled us to develop a bespoke programme that really suited our learner&rsquo;s needs.</p>
\n<p>They really understood what we wanted to achieve. As a result the design and delivery of the programme was very real, practical and included real business improvements for the organisation.</p>'
WHERE
  `title` = 'Rosderra';;

-- Home page content
DELIMITER ;;
UPDATE
  `plugin_pages_pages`
SET
  `content` = '<div class="simplebox simplebox-align-top simplebox-equal-heights simplebox-raised spotlights">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Accredited</h2>

				<p>Programmes</p>

				<p style="text-align:right"><a class="read_more" href="/individuals" style="font-size: 18px;"><strong>Read more</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Short</h2>

				<p>Programmes</p>

				<p style="text-align:right"><a class="read_more" href="/teams" style="font-size: 18px;"><strong>Read more</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Customised</h2>

				<p>Programmes</p>

				<p style="text-align:right"><a class="read_more" href="/teams" style="font-size: 18px;"><strong>Read more</strong></a></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-overlap-right why-choose-us">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h1>Why choose us?</h1>

				<ul>
					<li>Over 30 years training managers</li>
					<li>Innovative portfolio of in-company programmes, online courses, seminars and short courses nationwide</li>
					<li>70 highly qualified facilitators</li>
					<li>Excellent customer satisfaction and repeat business</li>
				</ul>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div class="hidden\-\-desktop"><img alt="" src="/shared_media/ibec/media/photos/content/why-ibec.jpg" style="float:right; height:281px; width:360px" /></div>
				<div class="hidden\-\-mobile hidden\-\-tablet"><img alt="" src="/shared_media/ibec/media/photos/content/why-choose-us.png" style="float:right; height:420px; width:740px" /></div>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-equal-heights simplebox-raised simplebox-strokes mt-5">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Our accreditations</h3>

				<ul>
					<li>13 years of ISO accreditation</li>
					<li>Many of our programmes are accredited by Technological University Dublin, European Mentoring and Coaching Council, Mediators Institute of Ireland, Quality &amp; Qualifications Ireland, the Pre-Hospital Emergency Care Council and are on the National Framework of Qualifications</li>
				</ul>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Our graduates</h3>

				<ul>
					<li>Over 8,000 managers trained in 2019</li>
					<li>515 graduates from accredited programmes</li>
				</ul>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Our programmes</h3>

				<ul>
					<li>320 in-company programmes</li>
					<li>28 accredited programmes</li>
					<li>290 public programmes</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<p style="margin-bottom: 50px; text-align: right;"><a href="/course-list" class="read_more-lg"><strong>Learn more</strong></a></p>

<div class="simplebox simplebox-align-top simplebox-video">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width: 32.6%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Video</h2>

				<p>Description of video. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 67.4%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p>{video-NycTraffic.mp4}</p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox-featured_programmes simplebox simplebox-align-top">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Featured Programmes</h2>

				<p>Description of featured programmes.</p>

				<div>{upcoming_courses-}</div>
			</div>
		</div>
	</div>
</div>

<div class="bg-light bg-lighter fullwidth">{news_category-Emerging Trends-Description of emerging trends}</div>

<div>{testimonial-Rosderra}</div>

<div>{our_clients-}</div>

<div class="bg-light bg-lighter pt-md-4 pb-md-2 simplebox simplebox-accredited">
	<div class="simplebox-columns" style="max-width: 838px;">
		<div class="simplebox-column simplebox-column-1 accredited-partner-text" style="width: 38%; ">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3>Accredited Partner</h3>
				<p>Brief description and info on accreditation partnership</p>
				<p><a href="#" class="read_more"><strong>Read more about our partnership</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 62%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div style="max-width: 450px;"><img src="/shared_media/ibec/media/photos/content/TUD_RGB_1.png" style="float: right; height: 197px; width: 312px;" /></div>
			</div>
		</div>
	</div>
</div>
'
WHERE `name_tag` IN ('home', 'home.html');;

UPDATE
  `plugin_pages_pages`
SET
  `title`   = 'Find a Programme',
  `content` = '',
  `footer`  = '<div class="simplebox simplebox-training">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width: 45%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Do you require customised,<br />in-company training?</h2>

				<p>Ibec&#39;s Management Training team can tailor our most popular courses to your requirements, and run them on an in-company basis, saving you time and money.</p>

				<p><a class="read_more-lg text-dark_purple" href="/contact-us"><strong>Contact us</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 55%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div><img alt="" class="d-block mx-auto" src="/shared_media/ibec/media/photos/content/contact-2.png" style="height:450px; width:288px"></div>
			</div>
		</div>
	</div>
</div>
\n
\n<p>{download_brochure-}</p>'
WHERE
  `name_tag` IN ('course-list', 'course-list.html');;

UPDATE
  `plugin_pages_pages`
SET
  `title`   = 'Checkout',
  `content` = '<h1>Checkout</h1>
\n<p>Secondary text. Secondary text. Secondary text.</p>'
WHERE
  `name_tag` IN ('checkout', 'checkout.html');;

-- Client logos widget
UPDATE `engine_feeds`
SET `content` = '<div class="bg-success fullwidth pt-sm-5 pb-sm-4" style="border: 1px solid var(\-\-success);">
	<div class="container">
		<h1>Our clients</h1>

		<div class="hidden\-\-mobile"><img alt="" src="/shared_media/ibec/media/photos/content/logos-1.png" style="height:649px; width:1140px"></div>
		<div class="hidden\-\-tablet hidden\-\-desktop"><img alt="" src="/shared_media/ibec/media/photos/content/mobile-logos-1.png" style="height:298px; width:320px"></div>
	</div>
</div>'
WHERE `name` = 'Our clients';;

UPDATE `plugin_courses_categories` SET `color` = '' WHERE `color` = '#000000';;

-- Data shown on course results
UPDATE
  `engine_settings`
SET
  `value_live`  = 'a:4:{i:0;s:8:"category";i:1;s:6:"county";i:2;s:8:"duration";i:3;s:10:"start_date";}',
  `value_stage` = 'a:4:{i:0;s:8:"category";i:1;s:6:"county";i:2;s:8:"duration";i:3;s:10:"start_date";}',
  `value_test`  = 'a:4:{i:0;s:8:"category";i:1;s:6:"county";i:2;s:8:"duration";i:3;s:10:"start_date";}',
  `value_dev`   = 'a:4:{i:0;s:8:"category";i:1;s:6:"county";i:2;s:8:"duration";i:3;s:10:"start_date";}'
WHERE
  `variable`='show_in_course_result'/* 1.3 */;;

-- Filter options shown for course results
UPDATE
  `engine_settings`
SET
  `value_live`  = 'a:4:{i:0;s:7:"keyword";i:1;s:15:"course_counties";i:2;s:17:"course_categories";i:3;s:5:"types";}',
  `value_stage` = 'a:4:{i:0;s:7:"keyword";i:1;s:15:"course_counties";i:2;s:17:"course_categories";i:3;s:5:"types";}',
  `value_test`  = 'a:4:{i:0;s:7:"keyword";i:1;s:15:"course_counties";i:2;s:17:"course_categories";i:3;s:5:"types";}',
  `value_dev`   = 'a:4:{i:0;s:7:"keyword";i:1;s:15:"course_counties";i:2;s:17:"course_categories";i:3;s:5:"types";}',
  `default`     = 'a:8:{i:0;s:7:"keyword";i:1;s:14:"event_counties";i:2;s:9:"locations";i:3;s:5:"years";i:4;s:17:"course_categories";i:5;s:16:"event_categories";i:6;s:6:"levels";i:7;s:8:"subjects";}'
WHERE
  `variable` = 'course_list_filters'/*1.1*/;;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Categories',
  `value_test`  = 'Categories',
  `value_live`  = 'Categories',
  `value_stage` = 'Categories'
WHERE
  `variable` = 'search_category_label_course_categories'/*1.1*/;;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Search',
  `value_test`  = 'Search',
  `value_live`  = 'Search',
  `value_stage` = 'Search'
WHERE
  `variable` = 'search_category_label_keyword'/*1.1*/;;


UPDATE
  `plugin_courses_courses`
SET
  `summary`     = '<p>This programme is to provide participants with a comprehensive knowledge and practical understanding of the whole area of HRM, ensuring both competence and confidence.</p>',
  `description` = '<h2>Summary</h2>
\n
\n<p>{document-start}</p>
\n
\n<div class="simplebox simplebox-course-intro">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

					<p>This programme is to provide participants with a comprehensive knowledge and practical understanding of the whole area of HRM, ensuring both competence and confidence in this area are developed.</p>

					<p><strong>At the end of the programme participants will:</strong></p>

					<ul class="small-bullets">
						<li>Understand the key concepts/elements of HRM and how they apply in practice</li>
						<li>Demonstrate an understanding of strategic management, employee engagement, talent management, competency modelling, change management, succession planning, performance management and how to implement each of them in the workplace</li>
						<li>Be able to apply the skills central to HRM at work and to discuss these with confidence, using real examples to show their application Understand how leadership behaviours affect how others perform at work</li>
						<li>Develop new strategies to improve levels of employee motivation</li>
						<li>Be able to take control of their own time, motivation and planning in the area of HRM at work</li>
					</ul>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-programme-for">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>This programme is for:</h2>
				<p>Participants who are looking for an in depth knowledge and understanding of the whole area of HRM in practice. It is also aimed at those thinking about a career in HR or people management.</p>
			</div>
		</div>
	</div>
</div>

<h2>Approach</h2>

<p>There are a number of activities and case studies used throughout the programme which ensures that all of the learning is applied in a real way. The programme leader encourages group discussion and involvement throughout each session, ensuring opportunities for questions and real issues are discussed at all times. Best practice examples in all areas of HRM will be highlighted and discussed throughout the programme, giving participants plenty of opportunities to build on the elements of these that could be applied in their own organisations.</p>

<h2>Programme Schedule</h2>

<div class="simplebox simplebox-align-top simplebox-equal-heights simplebox-schedule">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3>Overview of HRM</h3>
				<ul>
					<li>The role of the new HR function<br />Key HR themes</li>
					<li>HR organisational design, including best practice in this area</li>
					<li>Process design, including the HR value chain</li>
					<li>Impact of HR</li>
					<li>HR and the new economy</li>
					<li>HR and its customers</li>
				</ul>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3>Leadership and Employee Engagement</h3>
				<ul>
					<li>Leadership and culture formation</li>
					<li>The role of leadership and contingent&nbsp;styles</li>
					<li>Leadership and influence</li>
					<li>Your style and its impact</li>
					<li>Transactional analysis</li>
					<li>Employee engagement</li>
				</ul>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3>Talent Management and Competency Modelling</h3>
				<ul>
					<li>Talent Management &ndash; what is it?</li>
					<li>What does it involve?</li>
					<li>Best practice in this area</li>
					<li>Competency modelling</li>
					<li>Developing a model for your organisation</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-equal-heights simplebox-schedule">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3>Organisational Development / Change Management</h3>
				<ul>
					<li>What is organisational development / change management</li>
					<li>Change pitfalls</li>
					<li>Types of change and readiness assessment</li>
					<li>The change process &ndash; diagnosis and planning</li>
					<li>Models of change management that work</li>
					<li>The role of HR in the process</li>
				</ul>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3>Strategic Learning and Development, Succession and Performance Management</h3>
				<ul>
					<li>Learning and development &ndash; what is it?</li>
					<li>A strategic approach</li>
					<li>How to plan your training and development in a practical way</li>
					<li>Succession management &ndash; developing your people</li>
					<li>Performance management &ndash; getting the best from the right people</li>
					<li>Best practice examples Module</li>
				</ul>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3>Employment Law </h3>
				<ul>
					<li>Contracts of employment</li>
					<li>Equality, bullying and harassment</li>
					<li>Organisation of working time</li>
					<li>Health and safety</li>
					<li>Grievances</li>
					<li>Discipline &middot; Protective Leave</li>
				</ul>
			</div>
		</div>
	</div>
</div>\n
\n
\n<p>{document-end}</p>\n

<div class="bg-lighter pt-md-4 pb-md-2 simplebox simplebox-accredited">
	<div class="simplebox-columns" style="max-width: 838px;">
		<div class="simplebox-column simplebox-column-1 accredited-partner-text" style="width: 38%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h3>Accredited Partner</h3>
				<p>Brief description and info on accreditation partnership</p>
				<p><a href="#" class="read_more"><strong>Read more about our partnership</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 62%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div style="max-width: 450px;"><img src="/shared_media/ibec/media/photos/content/TUD_RGB_1.png" style="float: right; height: 197px; width: 312px;" /></div>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-directors">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h1>Programme Directors</h1>
				<h3>Jennifer O&rsquo;Sullivan</h3>

				<div class="hidden\-\-desktop hidden\-\-tablet fullwidth mb-3"><img alt="" src="/shared_media/ibec/media/photos/content/program-director-mobile.jpg" style="height:600px;width:468px"></div>

				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
				<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim.</p>

				<p><a class="read_more-lg" href="#"><strong>Call to action link</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 hidden\-\-mobile">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div><img alt="" src="/shared_media/ibec/media/photos/content/program-director.jpg" style="float:right; height:600px; width:468px"></div>
			</div>
		</div>
	</div>
</div>

<div>{testimonial-Rosderra}</div>

<div class="simplebox simplebox-align-top simplebox-video">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width: 32.6%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Video</h2>

				<p>Description of video. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 67.4%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p>{video-NycTraffic.mp4}</p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox-featured_programmes simplebox simplebox-align-top">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Featured Programmes</h2>

				<p>Description of featured programmes.</p>

				<div>{upcoming_courses-}</div>
			</div>
		</div>
	</div>
</div>

<div class="bg-light mb-0 simplebox simplebox-align-top simplebox-equal-heights spotlights">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h1>Download your brochure</h1>

				<h2>Download our annual course brochure</h2>

				<p><a class="download_pdf" href="#">Download PDF</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h1>Join Ibec</h1>

				<h2>Become an Ibec member and avail of discounted rates</h2>

				<p><a class="read_more" href="#">Join now</a></p>
			</div>
		</div>
	</div>
</div>
\n
\n<p>{get_started-}</p>
'
WHERE
  `title` = 'Diploma in Human Resource Management';;


-- Add the "standard-page" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'standard-page',
  'Standard page',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '1',
  '0',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'standard-page' AND `deleted` = 0)
LIMIT 1;;

UPDATE
  `plugin_pages_pages`
SET
  `title`         = 'Standard page',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  `publish`       = '1',
  `modified_by`   = '1',
  `last_modified` = CURRENT_TIMESTAMP,
  `content`       = '<div class="mb-sm-5 pb-4 reverse-mobile simplebox simplebox-overlap-left">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div><img alt="" src="/shared_media/ibec/media/photos/content/Handling-Difficult-Conversations.jpg" style="float:left; height:480px; width:678px"></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Text Box 1</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper. Suspendisse ultrices gravida dictum fusce ut placerat orci. At consectetur lorem donec massa. Bibendum arcu vitae elementum curabitur vitae nunc sed. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus. Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui</p>
				<p><a class="read_more" href="#" style="font-size: 18px;">Call to action</a></p>
			</div>
		</div>
	</div>
</div>

<div class="my-0 my-sm-5 simplebox simplebox-success">
	<div class="bg-success pt-0 px-3 p-sm-5 simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div class="hidden\-\-tablet_up"><img alt="" src="/shared_media/ibec/media/photos/content/emerging-trends-content-mobile.jpg" style="float:right; height:480px; width:678px"></div>

				<h2 class="mt-0">Text Box 2</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper. Suspendisse ultrices gravida dictum fusce ut placerat orci. At consectetur lorem donec massa. Bibendum arcu vitae elementum curabitur vitae nunc sed. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus. Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui.</p>
				<p><a class="read_more" href="#" style="font-size: 18px;"><strong class="text-primary">Call to action</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 hidden\-\-mobile">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div><img alt="" src="/shared_media/ibec/media/photos/content/emerging-trends.jpg" style="float:right; height:480px; width:678px"></div>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-typography">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<h1>Heading 1</h1>

				<h2>Heading 2</h2>

				<h3>Heading 3</h3>

				<h4>Heading 4</h4>

				<h5>Heading 5</h5>

				<h6>Heading 6</h6>
			</div>
		</div>
	</div>
</div>
<hr />

<div class="simplebox simplebox-paragraph">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<h2>Paragraph</h2>

				<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>

				<p>Lorem ipsum dolor sit amet, emphasis consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			</div>
		</div>
	</div>
</div>
<h2>List Types</h2>

<div class="mb-5 simplebox">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h4>Ordered List</h4>
				<ol>
					<li>List item 1</li>
					<li>List item 2</li>
					<li>List item 3
						<ol>
							<li>List item 3.1</li>
							<li>List item 3.2</li>
							<li>List item 3.3
								<ol>
									<li>List item 3.3.1</li>
									<li>List item 3.3.2</li>
									<li>List item 3.3.3</li>
								</ol>
							</li>
							<li>List item 3.4</li>
							<li>List item 3.5</li>
						</ol>
					</li>
					<li>List item 4</li>
					<li>List item 5</li>
				</ol>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h4>Unordered List</h4>
				<ul>
					<li>List item 1</li>
					<li>List item 2</li>
					<li>List item 3
						<ul>
							<li>List item 3.1</li>
							<li>List item 3.2</li>
							<li>List item 3.3
								<ul>
									<li>List item 3.3.1</li>
									<li>List item 3.3.2</li>
									<li>List item 3.3.3</li>
								</ul>
							</li>
							<li>List item 3.4</li>
							<li>List item 3.5</li>
						</ul>
					</li>
					<li>List item 4</li>
					<li>List item 5</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="bg-light featured_programme simplebox simplebox-align-top">
	<div class="simplebox-title">
		<h2>Featured Programme</h2>

		<p>Description of featured programmes&nbsp;if required.</p>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width:40%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div><img alt="" src="/shared_media/ibec/media/photos/content/featured_programme.png" style="display:block; height:270px; width:440px" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width:60%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h6>Management Development</h6>

				<h3>Diploma in Leadership</h3>

				<p style="font-size:14px">This programme will teach you the skills you need to lead yourself and others. It will develop your understanding of the different styles of leadership as well as...</p>

				<p style="text-align:right"><a class="read_more" href="/course-list">More info</a></p>
			</div>
		</div>
	</div>
</div>

<div class="bg-light simplebox" style="padding:32px 0 40px">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p style="text-align:right"><strong><a class="read_more-lg" href="/course-list">See all programmes</a></strong></p>
			</div>
		</div>
	</div>
</div>
\n
\n<p>{download_brochure-}</p>
\n
\n<p>{get_started-}</p>
\n'
WHERE
  `name_tag` = 'standard-page'
;;


-- Add the "privacy policy" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'checkout-terms',
  'Checkout terms',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '1',
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'checkout-terms' AND `deleted` = 0)
LIMIT 1;;

UPDATE
  `plugin_pages_pages`
SET
  `modified_by`   = '1',
  `last_modified` = CURRENT_TIMESTAMP,
  `content`       = '<h1>Checkout terms</h1>

<h6>Ibec Privacy Statement</h6>
<p>Ibec is committed to protecting your information. We use the personal data you provide when registering to administer your participation in the event or training course. For more information about how Ibec deals with your personal data, please read our <a href="/privacy" target="_blank">privacy statement</a>.</p>

<h6>Delegate cancellation policy</h6>
<p>Any cancellations received in writing to <a href="mailto:training@ibec.ie">training@ibec.ie</a> up to 5 business days prior to the event are refundable. All bookings are provisional until full payment is received.</p>

<h6>Photography at events</h6>
<p>There may be a photographer and videographer at the event and we may publish images from this event on our website(s) and on our social media accounts.</p>
'
WHERE
  `name_tag` = 'checkout-terms'
;;

UPDATE
  `engine_settings`
SET
  `value_dev`   = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'checkout-terms' ORDER BY `id` DESC LIMIT 1),
  `value_test`  = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'checkout-terms' ORDER BY `id` DESC LIMIT 1),
  `value_stage` = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'checkout-terms' ORDER BY `id` DESC LIMIT 1),
  `value_live`  = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'checkout-terms' ORDER BY `id` DESC LIMIT 1)
WHERE
  `variable` = 'privacy_policy_page';;

-- Footer bottom
UPDATE `engine_settings` SET `value_dev` = '&copy; Ibec 2020<br />Ibec clg is registered in Ireland. Registration No. 8706'  WHERE `variable` = 'company_copyright';;
UPDATE `engine_settings`
  SET `value_dev` = '<span class="hidden\-\-mobile">www.ibec.ie<br /></span>

  Registered address: 84/86 Lower Baggot Street, Dublin 2

  <span class="hidden\-\-tablet hidden\-\-desktop"><br /><br />www.ibec.ie</span>'
WHERE `variable` = 'cms_copyright';;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<div class="simplebox simplebox-equal-heights stay_inline">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1 text-left">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="Ibec Academy" src="/shared_media/ibec/media/photos/content/Ibec-Academy-System-Logo-White.png" style="height:60px; width:197px"></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 ml-auto ml-md-0 text-left text-md-center">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="ISO 9001:2015 AQA Quality Assured Service" src="/shared_media/ibec/media/photos/content/EQA_ISO_9001_2015_QAS_Logo.png" style="float:right; height:60px; width:135px"></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3 text-right text-md-center">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p>
					<a class="footer-social-link" href="https://www.linkedin.com/company/42331" target="_blank" title="LinkedIn"><img alt="" src="/shared_media/ibec/media/photos/content/LinkedIn.svg" style="height:50px; width:50px"></a>

					<a class="footer-social-link" href="https://www.facebook.com/smallfirmsassociation/" target="_blank" title="Facebook"><img alt="" src="/shared_media/ibec/media/photos/content/Facebook.svg" style="height:50px; width:50px"></a>

					<a class="footer-social-link" href="https://twitter.com/ibec_irl" target="_blank" title="Twitter"><img alt="" src="/shared_media/ibec/media/photos/content/Twitter.svg" style="height:50px; width:50px"></a>
				</p>
			</div>
		</div>
	</div>
</div>'
WHERE
  `variable` = 'footer_bottom_html';;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<p><a class="button button-outline w-100" href="/contact-us">CONTACT US TODAY</a></p>
\n
\n<p><a class="read_more" href="/subscribe">Sign up to our Mailing List</a></p>'
WHERE
  `variable` = 'footer_contact_html';;

UPDATE
  `engine_settings`
SET
  `value_test`  = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live`  = `value_dev`
WHERE
  `variable` IN ('company_copyright', 'cms_copyright', 'footer_bottom_html', 'footer_contact_html')/* v2.7 */;;