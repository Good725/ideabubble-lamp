/*
ts:2019-08-13 12:30:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
  (2, 'courses_schedule_content_tab', 'Courses / Schedule / Content tab', 'Schedule content tab',    (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'courses')),
  (2, 'assessments_content_tab',      'Assessments / Content tab',        'Assessments content tab', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'assessments'));
