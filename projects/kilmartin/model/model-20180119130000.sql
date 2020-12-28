/*
ts:2018-01-19 13:00:00
*/

UPDATE
  `plugin_pages_layouts`
SET
  `deleted` = 1
WHERE
  `layout` IN (
    'absence',
    'checkout_summary',
    'homework_profile',
    'profile',
    'students_absence_notes',
    'timesheet1'
  );

UPDATE
  `plugin_pages_pages`
SET
  `deleted` = 1
WHERE
  `name_tag` IN (
    'absence',
    'checkout-summary',
    'homework_profile',
    'profile',
    'students_absence_notes',
    'timesheet1'
  );