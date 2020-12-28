/*
ts:2019-06-21 15:01:00
*/

DELIMITER ;;

UPDATE `engine_settings` SET `value_dev` = 'Ailesbury Hair Clinic'  WHERE `variable` = 'addres_line_1';;
UPDATE `engine_settings` SET `value_dev` = '26 Clare Street'        WHERE `variable` = 'addres_line_2';;
UPDATE `engine_settings` SET `value_dev` = 'Dublin 2'               WHERE `variable` = 'addres_line_3';;
UPDATE `engine_settings` SET `value_dev` = '47'                     WHERE `variable` = 'assets_folder_path';;
UPDATE `engine_settings` SET `value_dev` = 'Ailesbury Hair Clinic'  WHERE `variable` = 'company_name';;
UPDATE `engine_settings` SET `value_dev` = ''                       WHERE `variable` = 'company_slogan';;
UPDATE `engine_settings` SET `value_dev` = 'none'                   WHERE `variable` = 'course_finder_mode';;
UPDATE `engine_settings` SET `value_dev` = 'info@ailesburyhairclinic.com'  WHERE `variable` = 'email';;
UPDATE `engine_settings` SET `value_dev` = 'AilesburyHairClinic'    WHERE `variable` = 'facebook_url';;
UPDATE `engine_settings` SET `value_dev` = 'in/ailesburyhair'       WHERE `variable` = 'linkedin_url';;
UPDATE `engine_settings` SET `value_dev` = 'favicon.ico'            WHERE `variable` = 'site_favicon';;
UPDATE `engine_settings` SET `value_dev` = 'logo.png'               WHERE `variable` = 'site_logo';;
UPDATE `engine_settings` SET `value_dev` = '01 6760969 '            WHERE `variable` = 'telephone';;
UPDATE `engine_settings` SET `value_dev` = 'ailesburyhc'            WHERE `variable` = 'twitter_url';;
UPDATE `engine_settings` SET `value_dev` = '04'                     WHERE `variable` = 'template_folder_path';;

UPDATE
  `engine_settings`
SET
  `value_dev` = '<div class="gray simplebox" style="padding: 1.45em 0;">
	<div class="simplebox-columns" style="max-width: 860px;">
		<div class="simplebox-column simplebox-column-1" style="width: 70%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h1 style="font-size: 38px; line-height: 1;">Need advice? Talk to us, we are happy to help you.</h1>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 30%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><a href="/contact" class="button">BOOK YOUR SESSION</a></p>
			</div>
		</div>
	</div>
</div>

<h2 style="text-align: center">Our happy user&#39;s testimonials</h2>

<div>{testimonialsfeed-Testimonials}</div>
'
WHERE
  `variable` = 'page_footer'
;;

UPDATE `engine_settings`
SET
  `value_live`  = `value_dev`,
  `value_stage` = `value_dev`,
  `value_test`  = `value_dev`
WHERE
  `variable` IN (
    'addres_line_1', 'addres_line_2', 'addres_line_3', 'assets_folder_path', 'company_name', 'company_slogan',
    'course_finder_mode', 'email', 'facebook_url', 'linkedin_url', 'page_footer', 'site_favicon', 'site_logo',
    'telephone', 'twitter_url', 'template_folder_path'
  )
;;

INSERT IGNORE INTO `plugin_menus`
(`category`, `title`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) VALUES
(
  'header',
  'Call Me Back',
  '/contact',
  '0',
  '0',
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

INSERT IGNORE INTO `plugin_menus`
(`category`, `title`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) VALUES
(
  'header 1',
  'About',
  '#',
  '1',
  '0',
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;


INSERT IGNORE INTO `plugin_menus`
(`category`, `title`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) VALUES
(
  'header 2',
  'Info',
  '#',
  '1',
  '0',
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

-- Unpublish some of the existing panels
UPDATE `plugin_panels`
SET `publish` = 0
WHERE `title` IN ('Celebrity Success', 'FAQs', 'Latest News', 'Testimonials', 'Video', 'Welcome to Ailesbury Hair Clinic')
OR `position` = 'footer'
;;

-- Add new panels
SELECT IFNULL(`id`, '') INTO @static_panel_id_4 FROM `plugin_panels_types` WHERE `name` = 'static' ORDER BY `id` DESC LIMIT 1;;
SELECT IFNULL(`id`, '') INTO @super_user_id_4   FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1;;

INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `image`, `text`, `date_created`, `date_modified`, `created_by`, `modified_by`)
VALUES
('Hair treatment',     'content_right', '1', @static_panel_id_4, 'hair_treatment.png',     '<p>Hair Treatment</p>\n',                                          CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_user_id_4, @super_user_id_4),
('Guaranteed results', 'content_right', '2', @static_panel_id_4, 'guaranteed_results.png', '<p>Ailesbury Hair Clinic<br />100%<br />Guaranteed Results</p>\n', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_user_id_4, @super_user_id_4),
('Happy customers',    'content_right', '3', @static_panel_id_4, 'testimonials.png',       '<p>Our happy customer&#39;s testimonials</p>\n',                   CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_user_id_4, @super_user_id_4),
('Interesting facts',  'content_right', '4', @static_panel_id_4, 'interesting_facts.png',  '<p>Interesting facts about hair loss</p>\n',                       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_user_id_4, @super_user_id_4);;

-- Use standard form class
UPDATE `plugin_panels` SET `text` = REPLACE(`text`, '"ahc-form"', '"formrt formrt-vertical ahc-form"') WHERE `text` LIKE '%"ahc-form"%';;
UPDATE `plugin_pages_pages` SET `content` = REPLACE(`content`, '"ahc-form"', '"formrt formrt-vertical ahc-form"') WHERE `content` LIKE '%"ahc-form"%';;

-- Add new menus
INSERT INTO `plugin_menus`
(`category`, `title`,              `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`,    `menus_target`) VALUES
('Footer',   'About Us',           '#',        '1',       '0',         '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self'),
('Footer',   'Terms & Conditions', '#',        '1',       '0',         '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self');;

SELECT IFNULL(`id`, '') INTO @about_us_id_2 FROM `plugin_menus` WHERE `title` = 'About Us' ORDER BY `id` DESC LIMIT 1;;

INSERT INTO `plugin_menus`
(`category`, `title`,            `link_url`,                `has_sub`, `parent_id`,    `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`,    `menus_target`) VALUES
('Footer',   'About us',         '/home',                   '0',       @about_us_id_2, '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self'),
('Footer',   'Hair treatment',   '/hair-treatment',         '0',       @about_us_id_2, '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self'),
('Footer',   'Before and after', '/hair-treatment-results', '0',       @about_us_id_2, '3',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self'),
('Footer',   'Latest news',      '/news',                   '0',       @about_us_id_2, '4',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self'),
('Footer',   'Amsterdam',        '/amsterdam',              '0',       @about_us_id_2, '5',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self');;

SELECT IFNULL(`id`, '') INTO @terms_id_1 FROM `plugin_menus` WHERE `title` = 'Terms & Conditions' ORDER BY `id` DESC LIMIT 1;;

INSERT INTO `plugin_menus`
(`category`, `title`,              `link_url`,              `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`,    `menus_target`) VALUES
('Footer',   'Terms & conditions', '/terms-and-conditions', '0',       @terms_id_1, '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self'),
('Footer',   'Privacy policy',     '/hair-treatment',       '0',       @terms_id_1, '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self');;

INSERT INTO `plugin_menus`
(`category`, `title`,        `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`,    `menus_target`, `image_id`) VALUES
('Bars',     'Contact us',   '/contact', '0',       '0',         '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self',        (SELECT IFNULL(`id`, '') FROM `plugin_media_shared_media` WHERE `filename` = 'icon_phone.svg'    AND `location` = 'menus' ORDER BY `id` DESC LIMIT 1)),
('Bars',     'Our services', '',         '0',       '0',         '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self',        (SELECT IFNULL(`id`, '') FROM `plugin_media_shared_media` WHERE `filename` = 'icon_hair.svg'     AND `location` = 'menus' ORDER BY `id` DESC LIMIT 1)),
('Bars',     'About us',     '',         '0',       '0',         '3',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '_self',        (SELECT IFNULL(`id`, '') FROM `plugin_media_shared_media` WHERE `filename` = 'icon_building.svg' AND `location` = 'menus' ORDER BY `id` DESC LIMIT 1));;

/* "Info" submenus */
SELECT IFNULL(`id`, '') INTO @info_id_1 FROM `plugin_menus` WHERE `title` = 'Info' AND `category` = 'header 2' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1;;
INSERT INTO `plugin_menus`
(`category`, `title`,           `link_url`,         `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`) VALUES
('header 2',  'Home',            '/home',           '1',       @info_id_1,  '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2',  'Hair treatment',  '/hair-treatment', '1',       @info_id_1,  '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2',  'Information',     '#',               '1',       @info_id_1,  '3',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2',  'Latest news',     '/news',           '0',       @info_id_1,  '4',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2',  'Privacy policy',  '/privacy-policy', '0',       @info_id_1,  '5',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);;

SELECT IFNULL(`id`, '') INTO @home_menu_id FROM `plugin_menus` WHERE `title` = 'Home' AND `category` = 'header 2' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1;;
INSERT INTO `plugin_menus`
(`category`, `title`,           `link_url`,        `has_sub`, `parent_id`,    `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`) VALUES
('header 2', 'Contact',         '/contact',        '0',       @home_menu_id,  '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2', 'Amsterdam',       '/amsterdam',      '0',       @home_menu_id,  '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2', 'Dublin and Cork Clinics', '/the-ailesbury-clinic-in-dublin-and-cork', '0', @home_menu_id, '3', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);;

SELECT IFNULL(`id`, '') INTO @hair_treatment_menu_id FROM `plugin_menus` WHERE `title` = 'Hair treatment' AND `category` = 'header 2' AND `deleted` = 0 AND 3 = 3 ORDER BY `id` DESC LIMIT 1;;
INSERT INTO `plugin_menus`
(`category`, `title`,                     `link_url`,                                `has_sub`, `parent_id`,             `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`) VALUES
('header 2', 'Results: before and after', '/hair-treatment-results',                 '0',       @hair_treatment_menu_id, '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2', 'AHI technique',             '/ahi-technique',                          '0',       @hair_treatment_menu_id, '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2', 'Hair follicle cloning',     '/hair-follicle-cloning-is-it-the-future', '0',       @hair_treatment_menu_id, '3',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);;

SELECT IFNULL(`id`, '') INTO @information_menu_id FROM `plugin_menus` WHERE `title` = 'Information' AND `category` = 'header 2' AND `deleted` = 0 AND 1 = 1 ORDER BY `id` DESC LIMIT 1;;
INSERT INTO `plugin_menus`
(`category`, `title`,                 `link_url`,                                `has_sub`, `parent_id`,          `menu_order`, `publish`, `deleted`, `date_modified`,   `date_entered`) VALUES
('header 2', 'Hair loss',             '/hair-treatment-results',                 '0',       @information_menu_id, '1',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2', 'Thinning hair',         '/ahi-technique',                          '0',       @information_menu_id, '2',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('header 2', 'Questions and answers', '/hair-follicle-cloning-is-it-the-future', '0',       @information_menu_id, '3',          '1',       '0',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);;

/* Make the home banner preset taller */
UPDATE `plugin_media_shared_media_photo_presets`
SET    `width_large` = '1920', `height_large` = '768', `date_modified` = CURRENT_TIMESTAMP, `modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE  `title` = 'Home banner';;

/* Add a smaller banner preset for content pages */
INSERT INTO `plugin_media_shared_media_photo_presets`
(`title`, `directory`, `width_large`, `height_large`, `action_large`, `thumb`, `width_thumb`, `height_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
('Content banner', 'banners', '1200', '300', 'fith', '0', '0', '0', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), '1', '0');;


-- Update home page content
UPDATE `plugin_pages_pages`
SET    `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'home_page_content_above' AND `deleted` = 0 LIMIT 1),
       `content`   = '<div class="simplebox-about simplebox simplebox-align-top">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width:67%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar">&nbsp;</div>

				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div class="simplebox simplebox-align-top">
					<div class="simplebox-columns">
						<div class="simplebox-column simplebox-column-1" style="width:46%">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

								<p style="text-align: center;"><img alt="" src="/shared_media/ailesbury/media/photos/content/about-ailesbury.jpg" style="height:231px; width:332px" /></p>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2" style="width:54%">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

								<h2 style="margin-top: 0;">About Ailesbury Hair Clinic</h2>

								<p>Our hair treatment specialists at Ailesbury Hair Clinic are here to help you discuss your options, book an appointment or get a quotation. We offer 100% guaranteed results. Arrange a consultation with us today.</p>
							</div>
						</div>
					</div>
				</div>

				<p style="text-align: center;">
				  <img src="/shared_media/ailesbury/media/photos/content/hair-replacement-procedure.png" style="height:158px; width:158px" alt="Hair replacement procedure" />&nbsp;
				  <img src="/shared_media/ailesbury/media/photos/content/testing-and-stem-cell-therapy.png" style="height:158px; width:158px" alt="Testing and stem cell therapy (PRP)"/>&nbsp;
				  <img src="/shared_media/ailesbury/media/photos/content/fut-strip-surgery.png" style="height:158px; width:158px" alt="FUT strip surgery" />&nbsp;
				  <img src="/shared_media/ailesbury/media/photos/content/fue-technique.png" style="height:158px; width:158px" alt="FUE technique" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width:33%">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<div class="gray home-stats simplebox stay_inline">
						<div class="simplebox-columns">
							<div class="simplebox-column simplebox-column-1" style="width: 100px">
								<div class="simplebox-content">
									<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

									<p><img alt="" src="/shared_media/ailesbury/media/photos/content/flower-green.svg" style="height:50px; width:50px" /></p>
								</div>
							</div>

							<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 100px)">
								<div class="simplebox-content">
									<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

									<h1>30 Mill</h1>
									<p>Follicles grown</p>
								</div>
							</div>
						</div>
					</div>

					<div class="gray home-stats simplebox stay_inline">
						<div class="simplebox-columns">
							<div class="simplebox-column simplebox-column-1" style="width: 100px">
								<div class="simplebox-content">
									<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

									<p><img alt="" src="/shared_media/ailesbury/media/photos/content/epidermis-green.svg" style="height:50px; width:50px" /></p>
								</div>
							</div>

							<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 100px)">
								<div class="simplebox-content">
									<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

									<h1>5.5 Mill</h1>

									<p>Follicles transplanted</p>
								</div>
							</div>
						</div>
					</div>

					<div class="gray home-stats simplebox stay_inline">
						<div class="simplebox-columns">
							<div class="simplebox-column simplebox-column-1" style="width: 100px">
								<div class="simplebox-content">
									<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

									<p><img alt="" src="/shared_media/ailesbury/media/photos/content/surgery-green.svg" style="height:49px; width:50px" /></p>
								</div>
							</div>

							<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 100px)">
								<div class="simplebox-content">
									<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

									<h1>786</h1>

									<p>Surgeries performed</p>
								</div>
							</div>
						</div>
					</div>

					<div class="gray home-stats simplebox stay_inline">
						<div class="simplebox-columns">
							<div class="simplebox-column simplebox-column-1" style="width: 100px">
								<div class="simplebox-content">
									<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

									<p><img alt="" src="/shared_media/ailesbury/media/photos/content/scalpel-green.svg" style="height:47px; width:50px" /></p>
								</div>
							</div>

							<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 100px)">
								<div class="simplebox-content">
									<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

									<h1>1500</h1>

									<p>Satisfied customers</p>
								</div>
							</div>
						</div>
					</div>

			</div>
		</div>
	</div>
</div>

<h2 style="text-align: center;">Thousands of happy clients &amp; testimonials</h2>

<div class="happy_customers simplebox simplebox-align-top">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<p><img alt="" src="/shared_media/ailesbury/media/photos/content/calum_best_1.jpg" style="height:529px; width:506px"></p>

				<h3 style="text-align:center">Calum Best</h3>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div class="simplebox">
					<div class="simplebox-columns">
						<div class="simplebox-column simplebox-column-1" style="max-width: 330px">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<p><img alt="" src="/shared_media/ailesbury/media/photos/content/joseph_guthrie.jpg" style="height:220px; width:240px"></p>

								<h3 style="text-align:center">Joseph Guthrie</h3>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2" style="max-width: 330px">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<p><img alt="" src="/shared_media/ailesbury/media/photos/content/john_doe.jpg" style="height:220px; width:240px"></p>

								<h3 style="text-align:center">John Doe</h3>
							</div>
						</div>
					</div>
				</div>

				<div class="simplebox">
					<div class="simplebox-columns">
						<div class="simplebox-column simplebox-column-1" style="max-width: 330px">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<p><img alt="" src="/shared_media/ailesbury/media/photos/content/joseph_guthrie.jpg" style="height:220px; width:240px"></p>

								<h3 style="text-align:center">Joseph Guthrie</h3>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2" style="max-width: 330px">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<p><img alt="" src="/shared_media/ailesbury/media/photos/content/john_doe.jpg" style="height:220px; width:240px"></p>

								<h3 style="text-align:center">John Doe</h3>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<p style="text-align: center;"><a href="/" class="button">VIEW MORE</a></p>

<h2 style="text-align: center;">What we offer</h2>
'
WHERE  `name_tag`= 'home';;

-- Update news page layout
UPDATE `plugin_pages_pages`
SET    `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'news' AND `deleted` = 0 LIMIT 1)
WHERE  `name_tag`  = 'news' AND `deleted` = 0;;
