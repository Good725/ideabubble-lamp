/*
ts:2020-05-12 17:59:00
*/

UPDATE
  `engine_feeds`
SET
  `function_call` = 'Controller_Frontend_Courses,embed_subjects_menu'
WHERE
  `short_tag` = 'course_topics'
;

INSERT INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
 'Course Categories',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 '1',
 '0',
 'course_categories',
 'Controller_Frontend_Courses,embed_categories_menu'
);
