/*
ts:2018-10-04 07:30:00
*/


INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'timeoff', 'Timeoff Plugin', 'Timeoff Plugin');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'timeoff_requests_edit', 'Timeoff Requests Edit All', 'Timeoff Requests Edit All', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timeoff'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'timeoff_requests_edit_limited', 'Timeoff Requests Edit Limited', 'Timeoff Requests Edit Limited', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timeoff'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'timeoff_requests_approve', 'Timeoff Requests Approve All', 'Timeoff Requests Approve All', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timeoff'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'timeoff_requests_approve_limited', 'Timeoff Requests Approve Limited', 'Timeoff Requests Approve Limited', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timeoff'));
