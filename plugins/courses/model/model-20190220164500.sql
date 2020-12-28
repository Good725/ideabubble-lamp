/*
ts:2019-02-20 16:45:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
(
  'course_search_default_layout',
  'Search results default layout',
  'courses',
  'grid',
  'grid',
  'grid',
  'grid',
  'grid',
  'Which layout the search results page should use by default',
  'dropdown',
  'Courses',
  '{"grid":"Grid","list":"List"}'
);

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
(
  'course_search_default_sorting_direction',
  'Search results default sorting direction',
  'courses',
  'asc',
  'asc',
  'asc',
  'asc',
  'asc',
  'Which direction the search result should be sorted by default. Depending on the form of sorting: ascending means A to Z or oldest to newest and descending means Z to A or newest to oldest.',
  'dropdown',
  'Courses',
  '{"asc":"Ascending","desc":"Descending"}'
);