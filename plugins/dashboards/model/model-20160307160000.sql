/*
ts:2016-03-07 16:00:00
*/

-- traffic dashboard
INSERT IGNORE INTO `plugin_dashboards` (`title`, `columns`, `date_filter`, `date_modified`) VALUES ('Traffic', 3, 1, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`)
  -- website traffic widget
  SELECT `dashboard`.`id` AS `dashboard_id`,  `gadget`.`id` AS `gadget_id`, `type`.`id`, 1, 1 FROM `plugin_dashboards` `dashboard`, `plugin_reports_reports` `gadget`, `plugin_dashboards_gadget_types` `type`
  WHERE `dashboard`.`title` = 'Traffic' AND `gadget`.`name`  = 'Website Traffic'  AND `type`.`stub` = 'Widget'
  -- top web pages widget
  UNION SELECT `dashboard`.`id` AS `dashboard_id`,  `gadget`.`id` AS `gadget_id`, `type`.`id`, 2, 1 FROM `plugin_dashboards` `dashboard`, `plugin_reports_reports` `gadget`, `plugin_dashboards_gadget_types` `type`
  WHERE `dashboard`.`title` = 'Traffic' AND `gadget`.`name`  = 'Top Web Pages'    AND `type`.`stub` = 'Widget'
  -- top referrals widget
  UNION SELECT `dashboard`.`id` AS `dashboard_id`,  `gadget`.`id` AS `gadget_id`, `type`.`id`, 3, 1 FROM `plugin_dashboards` `dashboard`, `plugin_reports_reports` `gadget`, `plugin_dashboards_gadget_types` `type`
  WHERE `dashboard`.`title` = 'Traffic' AND `gadget`.`name`  = 'Top Referrals'     AND `type`.`stub` = 'Widget'
;
