/*
ts:2020-04-27 18:00:00
*/

-- Embed a feed of upcoming courses via a short tag
INSERT INTO `engine_feeds`(`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`,  `deleted`, `short_tag`, `function_call`)
VALUES ('Upcoming courses feed', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0', 'upcoming_courses', 'Controller_Frontend_Courses,embed_upcoming_feed');

-- Embed a single testimonial via a short tag
INSERT INTO `engine_feeds`(`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`,  `deleted`, `short_tag`, `function_call`)
VALUES ('Testimonial', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0', 'testimonial', 'Controller_Frontend_Testimonials,embed_testimonial');

-- Allow testimonials to have a banner image
ALTER TABLE `plugin_testimonials` ADD COLUMN `banner_image` VARCHAR(255) NULL DEFAULT NULL AFTER `image`;

INSERT INTO `plugin_media_shared_media_photo_presets` (`title`, `directory`, `width_large`, `height_large`, `action_large`, `thumb`, `width_thumb`, `height_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES ('Testimonial banner', 'testimonial_banners', '1920', '700', 'fith', '1', '960', '350', 'fith', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0');

-- Setting for information to show in course results
INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'show_in_course_result',
  'Show on course list',
  'courses',
  'a:1:{i:0;s:13:"date_selector";}',
  'a:1:{i:0;s:13:"date_selector";}',
  'a:1:{i:0;s:13:"date_selector";}',
  'a:1:{i:0;s:13:"date_selector";}',
  'a:1:{i:0;s:13:"date_selector";}',
  'both',
  'Show the selected data on results in the course list.',
  'multiselect',
  '0',
  'Courses',
  '0',
  '{"category":"Category", "county": "County", "date_selector":"Date selector", "duration":"Duration", "start_date":"Start date"}'
);

UPDATE
  `engine_settings`
SET
  `group`       = 'Website',
  `options`     = '{"category":"Category", "county": "County", "date_selector":"Date selector", "duration":"Duration", "start_date":"Start date", "topics":"Topics"}',
  `value_dev`   = 'a:2:{i:0;s:13:"date_selector";i:1;s:6:"topics";}',
  `value_test`  = 'a:2:{i:0;s:13:"date_selector";i:1;s:6:"topics";}',
  `value_stage` = 'a:2:{i:0;s:13:"date_selector";i:1;s:6:"topics";}',
  `value_live`  = 'a:2:{i:0;s:13:"date_selector";i:1;s:6:"topics";}',
  `default`     = 'a:2:{i:0;s:13:"date_selector";i:1;s:6:"topics";}'
WHERE
  `variable` = 'show_in_course_result';


-- Setting for filters to show next to the search results
INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'course_list_filters',
  'Filters in course list',
  'courses',
  '',
  '',
  '',
  '',
  '',
  'both',
  'Show the selected data on results in the course list.',
  'multiselect',
  '0',
  'Website',
  '0',
  '{"keyword":"Keyword",
"course_counties":"Counties (courses)",
"event_counties":"Counties (events)",
"locations":"Locations",
"years":"Years",
"course_categories":"Categories (courses)",
"event_categories":"Categories (events)",
"levels":"Levels",
"subjects":"Subjects",
"types":"Types"}'
);

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'a:8:{i:0;s:7:"keyword";i:1;s:14:"event_counties";i:2;s:9:"locations";i:3;s:5:"years";i:4;s:17:"course_categories";i:5;s:16:"event_categories";i:6;s:6:"levels";i:7;s:8:"subjects";}',
  `value_test`  = 'a:8:{i:0;s:7:"keyword";i:1;s:14:"event_counties";i:2;s:9:"locations";i:3;s:5:"years";i:4;s:17:"course_categories";i:5;s:16:"event_categories";i:6;s:6:"levels";i:7;s:8:"subjects";}',
  `value_stage` = 'a:8:{i:0;s:7:"keyword";i:1;s:14:"event_counties";i:2;s:9:"locations";i:3;s:5:"years";i:4;s:17:"course_categories";i:5;s:16:"event_categories";i:6;s:6:"levels";i:7;s:8:"subjects";}',
  `value_live`  = 'a:8:{i:0;s:7:"keyword";i:1;s:14:"event_counties";i:2;s:9:"locations";i:3;s:5:"years";i:4;s:17:"course_categories";i:5;s:16:"event_categories";i:6;s:6:"levels";i:7;s:8:"subjects";}',
  `default`     = 'a:8:{i:0;s:7:"keyword";i:1;s:14:"event_counties";i:2;s:9:"locations";i:3;s:5:"years";i:4;s:17:"course_categories";i:5;s:16:"event_categories";i:6;s:6:"levels";i:7;s:8:"subjects";}'
WHERE
  `variable` = 'course_list_filters'/*1.0*/;


-- Settings for the labels to accompany the filters
INSERT INTO `engine_settings` (`variable`, `name`, `default`, `note`, `type`, `group`) VALUES
('search_category_label_keyword',           'Keyword filter label',           'Keyword',       'The label for the keyword search filter',           'text', 'Website'),
('search_category_label_course_counties',   'Course counties filter label',   'Locations',     'The label for the course counties search filter',   'text', 'Website'),
('search_category_label_event_counties',    'Event counties filter label',    'Locations',     'The label for the event counties search filter',    'text', 'Website'),
('search_category_label_locations',         'Locations filter label',         'Locations',     'The label for the locations search filter',         'text', 'Website'),
('search_category_label_years',             'Years filter label',             'Years',         'The label for the years search filter',             'text', 'Website'),
('search_category_label_course_categories', 'Course categories filter label', 'Class Types',   'The label for the course categories search filter', 'text', 'Website'),
('search_category_label_event_categories',  'Event counties filter label',    'Categories',    'The label for the event categories search filter',  'text', 'Website'),
('search_category_label_levels',            'Levels filter label',            'Subject Levels','The label for the levels search filter',            'text', 'Website'),
('search_category_label_subjects',          'Subjects filter label',          'Subjects',      'The label for the subjects search filter',          'text', 'Website');

INSERT INTO `engine_settings` (`variable`, `name`, `default`, `note`, `type`, `group`) VALUES
('search_category_label_types',             'Types filter label',             'Types',         'The label for the types search filter',             'text', 'Website');

UPDATE `engine_settings`
SET
  `value_dev`   = `default`,
  `value_test`  = `default`,
  `value_live`  = `default`,
  `value_stage` = `default`
WHERE
  `variable` like 'search_category_label_%'/*1.1*/;

-- Setting for breadcrumb text for course details
INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'course_details_breadcrumbs',
  'Course details breadcrumbs',
  'courses',
  'topics',
  'topics',
  'topics',
  'topics',
  'topics',
  'both',
  'What type of breadcrumbs are to be used in the course_detail2 layout',
  'select',
  '0',
  'Courses',
  '0',
  '{"categories":"Categories", "topics": "Topics"}'
);

UPDATE `engine_settings`
SET
  `type` = 'dropdown',
  `options` = '{"": "None", "categories":"Categories", "subjects": "Subjects"}'
WHERE `variable` = 'course_details_breadcrumbs';

-- Setting for banner text for course details
INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'course_details_banner_text',
  'Course details banner text',
  'courses',
  'summary',
  'summary',
  'summary',
  'summary',
  'summary',
  'both',
  'What text is to be used in the banner in the course_detail2 layout',
  'select',
  '0',
  'Courses',
  '0',
  '{"":"None", "accredited_provider":"Accreditation provider", "summary":"Summary"}'
);

UPDATE `engine_settings`
SET    `type`     = 'dropdown'
WHERE  `variable` = 'course_details_banner_text';


INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'course_layout_auto_details',
  'Automatically display details on course page',
  'courses',
  '0',
  '0',
  '0',
  '0',
  '0',
  'both',
  'Automatically show the course name, duration, level, accreditation body and timeslots at the top of the course details page',
  'toggle_button',
  '0',
  'Courses',
  '0',
  'Model_Settings,on_or_off'
);

INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`) VALUES
(
  'footer_bottom_html',
  'Footer bottom text',
  '',
  '',
  '',
  '',
  '',
  '',
  'both',
  'Text to appear at the very bottom of the footer',
  'wysiwyg',
  '0',
  'Website',
  '0'
);

INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`) VALUES
(
  'footer_contact_html',
  'Footer contact text',
  '',
  '',
  '',
  '',
  '',
  '',
  'both',
  'Custom text to appear in the footer at the bottom of the contact-us section',
  'wysiwyg',
  '0',
  'Website',
  '0'
);