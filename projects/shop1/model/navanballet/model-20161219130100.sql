/*
ts:2016-12-19 13:01:00
*/

INSERT INTO `plugin_courses_discounts`
  (`title`, `summary`, `valid_from`, `valid_to`, `amount_type`, `amount`, `schedule_type`, `item_quantity_min`, `item_quantity_scope`)
  VALUES
  ('€20 Off for second child', '€20 Off for second child', '2016-12-01 00:00:00', '2017-12-01 00:00:00', 'Fixed', '20', 'Prepay,PAYG', '1', 'Family');

UPDATE `plugin_courses_discounts` SET `item_quantity_min` = 2 WHERE `title` = '€20 Off for second child';
