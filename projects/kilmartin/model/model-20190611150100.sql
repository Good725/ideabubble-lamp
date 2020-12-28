/*
ts:2019-06-11 15:01:00
*/

/* Overwritten settings for the microsite */
DELIMITER ;;
INSERT IGNORE INTO `engine_settings_microsite_overwrites`
  (`setting`,              `microsite_id`, `environment`, `value`)
VALUES
  ('template_folder_path', 'keslanguage',  'dev',         '04'),
  ('template_folder_path', 'keslanguage',  'test',        '04'),
  ('template_folder_path', 'keslanguage',  'stage',       '04'),
  ('template_folder_path', 'keslanguage',  'live',        '04'),

  ('assets_folder_path',   'keslanguage',  'dev',         '46'),
  ('assets_folder_path',   'keslanguage',  'test',        '46'),
  ('assets_folder_path',   'keslanguage',  'stage',       '46'),
  ('assets_folder_path',   'keslanguage',  'live',        '46'),

  ('home_page_feed_1',     'keslanguage',  'dev',         'none'),
  ('home_page_feed_1',     'keslanguage',  'test',        'none'),
  ('home_page_feed_1',     'keslanguage',  'stage',       'none'),
  ('home_page_feed_1',     'keslanguage',  'live',        'none'),

  ('home_page_course_categories_feed', 'keslanguage', 'dev',   0),
  ('home_page_course_categories_feed', 'keslanguage', 'test',  0),
  ('home_page_course_categories_feed', 'keslanguage', 'stage', 0),
  ('home_page_course_categories_feed', 'keslanguage', 'live',  0),

  ('page_footer',          'keslanguage',  'dev',         '<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results">BOOK NOW</a></h1>'),
  ('page_footer',          'keslanguage',  'test',        '<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results">BOOK NOW</a></h1>'),
  ('page_footer',          'keslanguage',  'stage',       '<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results">BOOK NOW</a></h1>'),
  ('page_footer',          'keslanguage',  'live',        '<h1 style="text-align:center">Earn your English certificate abroad&nbsp; &nbsp;<a class="button button\-\-continue inverse" href="/available-results">BOOK NOW</a></h1>'),

  ('site_footer_logo',     'keslanguage',  'dev',         'kes_language_logo.svg'),
  ('site_footer_logo',     'keslanguage',  'test',        'kes_language_logo.svg'),
  ('site_footer_logo',     'keslanguage',  'stage',       'kes_language_logo.svg'),
  ('site_footer_logo',     'keslanguage',  'live',        'kes_language_logo.svg'),


  ('site_logo',            'keslanguage',  'dev',         'kes_language_logo.svg'),
  ('site_logo',            'keslanguage',  'test',        'kes_language_logo.svg'),
  ('site_logo',            'keslanguage',  'stage',       'kes_language_logo.svg'),
  ('site_logo',            'keslanguage',  'live',        'kes_language_logo.svg')
;;

/* Create banner for the language site home page. */
INSERT INTO `plugin_custom_scroller_sequences` (`title`, `animation_type`, `order_type`, `first_item`, `rotating_speed`, `timeout`, `pagination`, `controls`, `plugin`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES (
  'languages',
  'horizontal',
  'ascending',
  '1',
  '300',
  '7700',
  '1',
  '1',
  (SELECT `id` FROM `engine_plugins` WHERE `name` = 'customscroller' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);;

/* Create home page for the language site, which uses the just-created banner. */
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `banner_photo`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `layout_id`, `category_id`, `publish`) VALUES (
  'home\-\-lang',
  'Home',
  (SELECT CONCAT('0|0|banners|', `id`) FROM `plugin_custom_scroller_sequences` WHERE `title` = 'languages' ORDER BY `id` DESC LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'home' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1),
  '1'
);;

/* Add slides to banner */
INSERT INTO `plugin_custom_scroller_sequence_items` (`sequence_id`, `image`, `image_location`, `order_no`, `title`, `html`, `link_type`, `link_url`, `link_target`, `overlay_position`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'languages' ORDER BY `id` DESC LIMIT 1),
  'banner_people.png',
  'banners',
  '1',
  'Multicultural',
  '<h2>We are proud of your multicultural ethos</h2>\n\n<p><a href="/available-results" class="button">BOOK YOUR PLACE NOW</a></p>',
  'internal',
  (SELECT IFNULL(`id`, 0) FROM `plugin_pages_pages` WHERE `name_tag` = 'available-results' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
  '1',
  'right',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

INSERT INTO `plugin_custom_scroller_sequence_items` (`sequence_id`, `image`, `image_location`, `order_no`, `title`, `html`, `link_type`, `link_url`, `link_target`, `overlay_position`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'languages' ORDER BY `id` DESC LIMIT 1),
  'banner_flags.png',
  'banners',
  '2',
  'Wherever you are',
  '<h2>Wherever you are come learn with us</h2>\n\n<p><a href="/available-results" class="button">BOOK YOUR PLACE NOW</a></p>',
  'internal',
  (SELECT IFNULL(`id`, 0) FROM `plugin_pages_pages` WHERE `name_tag` = 'available-results' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
  '1',
  'right',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

INSERT INTO `plugin_custom_scroller_sequence_items` (`sequence_id`, `image`, `image_location`, `order_no`, `title`, `html`, `link_type`, `link_url`, `link_target`, `overlay_position`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'languages' ORDER BY `id` DESC LIMIT 1),
  'banner_limerick.jpg',
  'banners',
  '3',
  'Wherever you are',
  '<h2>Study in Ireland, while enjoying your culture and landscape</h2>\n\n<p><a href="/available-results" class="button">BOOK YOUR PLACE NOW</a></p>',
  'internal',
  (SELECT IFNULL(`id`, 0) FROM `plugin_pages_pages` WHERE `name_tag` = 'available-results' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
  '1',
  'right',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

/* Update banner slide text */
UPDATE `plugin_custom_scroller_sequence_items`
SET    `title`       = 'Multicultural',
       `html`        = '<h1>We are proud of our multicultural ethos</h1>\n\n<p><a href="/available-results" class="button">BOOK YOUR PLACE NOW</a></p> '
WHERE  `sequence_id` = (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'languages' ORDER BY `id` DESC LIMIT 1)
AND    `order_no`    = '1';;

UPDATE `plugin_custom_scroller_sequence_items`
SET    `title`       = 'Wherever you are',
       `html`        = '<h1>Wherever you are<br />come learn with us</h1>\n\n<p><a href="/available-results" class="button">BOOK YOUR PLACE NOW</a></p> '
WHERE  `sequence_id` = (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'languages' ORDER BY `id` DESC LIMIT 1)
AND    `order_no`    = '2';;

UPDATE `plugin_custom_scroller_sequence_items`
SET    `title`       = 'Study in Ireland',
       `html`        = '<h1>Study in Ireland, while enjoying your culture and landscape</h1>\n\n<p><a href="/available-results" class="button">BOOK YOUR PLACE NOW</a></p> '
WHERE  `sequence_id` = (SELECT `id` FROM `plugin_custom_scroller_sequences` WHERE `title` = 'languages' ORDER BY `id` DESC LIMIT 1)
AND    `order_no`    = '3';;

/* Current home page panels to only display on the regular site */
UPDATE `plugin_panels`
SET    `title`    = CONCAT(`title`, '\-\-coll')
WHERE  `position` = 'home_content'
AND    `title`    NOT LIKE '%\-\-coll'
AND    `title`    NOT LIKE '%\-\-lang';;

/* Insert new panels for the microsite */
INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `image`, `text`, `link_url`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'About the courses\-\-lang',
  'home_content',
  '1',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static'),
  'about_the_courses.jpg',
  '<p>View More</p>',
  '/about-courses',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `image`, `text`, `link_url`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'Who we are\-\-lang',
  'home_content',
  '2',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static'),
  'who_we_are.jpg',
  '<p>View More</p>',
  '/about-us',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `image`, `text`, `link_url`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'About Ireland\-\-lang',
  'home_content',
  '2',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static'),
  'about_ireland.jpg',
  '<p>View More</p>',
  '/about-ireland',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

/* Current home page news items to only display on the regular site */
UPDATE `plugin_news`
SET    `title`       = CONCAT(`title`, '\-\-coll')
WHERE  `category_id` = (SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Home feed')
AND    `title`       NOT LIKE '%\-\-coll'
AND    `title`       NOT LIKE '%\-\-lang';;

/* Insert new news items for the microsite */
INSERT INTO `plugin_news` (`category_id`, `title`, `image`, `seo_title`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  (SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Home feed'),
  'Heritage\-\-lang',
  'heritage.jpg',
  'Heritage',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
),
(
  (SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Home feed'),
  'Arts & Culture\-\-lang',
  'arts_and_culture.jpg',
  'Arts & Culture',
  '2',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
),
(
  (SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Home feed'),
  'Outdoors\-\-lang',
  'outdoors.png',
  'Outdoors',
  '3',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
),
(
  (SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Home feed'),
  'Hidden Heartlands\-\-lang',
  'hidden_heartlands.jpg',
  'Hidden Heartlands',
  '4',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;


/* Separate heading menu for the microsite */
INSERT INTO `plugin_menus` (`category`, `title`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`, `menus_target`, `image_id`) VALUES
(
  'header\-\-lang',
  'Book Courses',
  '/available-results',
  '0',
  '0',
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '_self',
  '0'
),
(
  'header\-\-lang',
  'Call Me Back',
  '/contact-us',
  '0',
  '0',
  '2',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '_self',
  '0'
);;

INSERT INTO `plugin_courses_providers` (`name`, `type_id`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES (
  'Kilmartin Education\-\-lang',
  (SELECT `id` FROM `plugin_courses_providers_types` WHERE `type` = 'Business'),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

INSERT INTO `plugin_courses_courses` (`title`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES (
  'English',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

INSERT INTO `plugin_courses_courses_has_providers` (`course_id`, `provider_id`) VALUES (
  (SELECT `id` FROM `plugin_courses_courses`   WHERE `title` = 'English'                     ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_courses_providers` WHERE `name`  = 'Kilmartin Education\-\-lang' ORDER BY `id` DESC LIMIT 1)
);;

INSERT INTO `plugin_courses_schedules` (
  `name`, `course_id`, `start_date`, `end_date`, `is_confirmed`, `is_fee_required`, `rental_fee`, `date_created`,
  `date_modified`, `created_by`, `modified_by`, `repeat`, `book_on_website`, `fee_per`, `payg_period`, `amendable`
)
VALUES (
  'English 1',
  (SELECT `id` FROM `plugin_courses_courses` WHERE `title` = 'English' ORDER BY `id` DESC LIMIT 1),
  '2019-06-18 14:30:00',
  '2019-08-13 17:00:00',
  '1',
  '1',
  '50.00',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users`          WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users`          WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `plugin_courses_repeat` WHERE `name` = 'Weekly'               LIMIT 1),
  '1',
  'Timeslot',
  'timeslot',
  '1'
);;

INSERT INTO `plugin_courses_timetable` (`timetable_name`, `date_modified`) VALUES (
  CONCAT('English 1', ' ', UNIX_TIMESTAMP()),
  CURRENT_TIMESTAMP
);;

SELECT `id`             INTO @schedule_id_2  FROM `plugin_courses_schedules` WHERE `name`           =    'English 1'  ORDER BY `id` DESC LIMIT 1;;
SELECT IFNULL(`id`, '') INTO @timetable_id_2 FROM `plugin_courses_timetable` WHERE `timetable_name` LIKE 'English 1%' ORDER BY `id` DESC LIMIT 1;;
SELECT IFNULL(`id`, '') INTO @super_id_2     FROM `engine_users`             WHERE `email`          =    'super@ideabubble.ie'           LIMIT 1;;

INSERT INTO `plugin_courses_schedules_events`
(`schedule_id`,  `datetime_start`,      `datetime_end`,        `date_created`,    `date_modified`,  `created_by`,  `timetable_id`,  `trainer_id`) VALUES
(@schedule_id_2, '2019-06-18 14:30:00', '2019-06-18 17:00:00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_id_2,  @timetable_id_2, '0'),
(@schedule_id_2, '2019-06-25 14:30:00', '2019-06-25 17:00:00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_id_2,  @timetable_id_2, '0'),
(@schedule_id_2, '2019-07-02 14:30:00', '2019-07-02 17:00:00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_id_2,  @timetable_id_2, '0'),
(@schedule_id_2, '2019-07-09 14:30:00', '2019-07-09 17:00:00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_id_2,  @timetable_id_2, '0'),
(@schedule_id_2, '2019-07-16 14:30:00', '2019-07-16 17:00:00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_id_2,  @timetable_id_2, '0'),
(@schedule_id_2, '2019-07-23 14:30:00', '2019-07-23 17:00:00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_id_2,  @timetable_id_2, '0'),
(@schedule_id_2, '2019-07-30 14:30:00', '2019-07-30 17:00:00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_id_2,  @timetable_id_2, '0'),
(@schedule_id_2, '2019-08-13 14:30:00', '2019-08-13 17:00:00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @super_id_2,  @timetable_id_2, '0');;

UPDATE
  `plugin_courses_schedules`
SET
  `fee_amount` = '60.00',
  `location_id` = (SELECT IFNULL(`id`, '')
      FROM `plugin_courses_locations`
      WHERE `parent_id` = (SELECT IFNULL(`id`, '')  FROM `plugin_courses_locations` WHERE `name` = 'Limerick' AND `delete` = 0)
      AND `delete` = 0
      LIMIT 1)
WHERE
  `name` = 'English 1'
ORDER BY `id` DESC LIMIT 1;;

UPDATE
  `plugin_courses_courses`
SET
  `year_id`     = (SELECT IFNULL(`id`, '') FROM `plugin_courses_years`      WHERE `publish` = 1 AND `delete`  = 0 LIMIT 1),
  `category_id` = (SELECT IFNULL(`id`, '') FROM `plugin_courses_categories` WHERE `publish` = 1 AND `delete`  = 0 LIMIT 1),
  `subject_id`  = (SELECT IFNULL(`id`, '') FROM `plugin_courses_subjects`   WHERE `publish` = 1 AND `deleted` = 0 AND `name` LIKE '%English%' LIMIT 1)
WHERE
  `title` = 'English'
ORDER BY `id` DESC LIMIT 1;;

INSERT INTO `plugin_courses_courses_has_years` (`course_id`, `year_id`) VALUES
(
  (SELECT `id` FROM `plugin_courses_courses` WHERE `title` = 'English' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_courses_years` WHERE `delete` = 0 LIMIT 1)
);;