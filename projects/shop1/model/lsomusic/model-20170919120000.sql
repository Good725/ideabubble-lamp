/*
ts:2017-09-19 12:00:00
*/

INSERT INTO `plugin_dashboards` (`title`, `columns`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (
  'Parent survey',
  '2',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);

-- Group 1
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Group 1',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'survey_question_group'),
  'question_id',
  'order_id',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`) VALUES
(
  'Parent Survey - Group 1',
  'SELECT `question`.`id`, `question`.`title`, `has_question`.`order_id`, `has_question`.`survey_id` FROM `plugin_survey_questions` `question`
\nJOIN `plugin_survey_has_questions` `has_question` ON `has_question`.`question_id` = `question`.`id`
\nJOIN `plugin_survey_groups`        `group`        ON `has_question`.`group_id`    = `group`.`id`
\nWHERE `has_question`.`survey_id` = 2
\nAND `group`.`title` = \'Group 1\'
\nAND `question`.`deleted` = 0
\nAND `has_question`.`deleted` = 0
\nORDER by `has_question`.`order_id`',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Group 1' ORDER BY `date_created` DESC LIMIT 1)
);

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Parent survey'           ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Parent Survey - Group 1' ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget' LIMIT 1),
  '1',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

-- Group 2
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Group 2',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'survey_question_group'),
  'question_id',
  'order_id',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`) VALUES
(
  'Parent Survey - Group 2',
  'SELECT `question`.`id`, `question`.`title`, `has_question`.`order_id`, `has_question`.`survey_id` FROM `plugin_survey_questions` `question`
\nJOIN `plugin_survey_has_questions` `has_question` ON `has_question`.`question_id` = `question`.`id`
\nJOIN `plugin_survey_groups`        `group`        ON `has_question`.`group_id`    = `group`.`id`
\nWHERE `has_question`.`survey_id` = 2
\nAND `group`.`title` = \'Group 2\'
\nAND `question`.`deleted` = 0
\nAND `has_question`.`deleted` = 0
\nORDER by `has_question`.`order_id`',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Group 2' ORDER BY `date_created` DESC LIMIT 1)
);

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Parent survey'           ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Parent Survey - Group 2' ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget' LIMIT 1),
  '2',
  '2',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

-- Group 3
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Group 3',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'survey_question_group'),
  'question_id',
  'order_id',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`) VALUES
(
  'Parent Survey - Group 3',
  'SELECT `question`.`id`, `question`.`title`, `has_question`.`order_id`, `has_question`.`survey_id` FROM `plugin_survey_questions` `question`
\nJOIN `plugin_survey_has_questions` `has_question` ON `has_question`.`question_id` = `question`.`id`
\nJOIN `plugin_survey_groups`        `group`        ON `has_question`.`group_id`    = `group`.`id`
\nWHERE `has_question`.`survey_id` = 2
\nAND `group`.`title` = \'Group 3\'
\nAND `question`.`deleted` = 0
\nAND `has_question`.`deleted` = 0
\nORDER by `has_question`.`order_id`',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Group 3' ORDER BY `date_created` DESC LIMIT 1)
);

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Parent survey'           ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Parent Survey - Group 3' ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget' LIMIT 1),
  '1',
  '3',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

-- Group 4
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Group 4',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'survey_question_group'),
  'question_id',
  'order_id',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`) VALUES
(
  'Parent Survey - Group 4',
  'SELECT `question`.`id`, `question`.`title`, `has_question`.`order_id`, `has_question`.`survey_id` FROM `plugin_survey_questions` `question`
\nJOIN `plugin_survey_has_questions` `has_question` ON `has_question`.`question_id` = `question`.`id`
\nJOIN `plugin_survey_groups`        `group`        ON `has_question`.`group_id`    = `group`.`id`
\nWHERE `has_question`.`survey_id` = 2
\nAND `group`.`title` = \'Group 4\'
\nAND `question`.`deleted` = 0
\nAND `has_question`.`deleted` = 0
\nORDER by `has_question`.`order_id`',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Group 4' ORDER BY `date_created` DESC LIMIT 1)
);

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Parent survey'           ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Parent Survey - Group 4' ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget' LIMIT 1),
  '2',
  '4',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

-- Group 5
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Group 5',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'survey_question_group'),
  'question_id',
  'order_id',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`) VALUES
(
  'Parent Survey - Group 5',
  'SELECT `question`.`id`, `question`.`title`, `has_question`.`order_id`, `has_question`.`survey_id` FROM `plugin_survey_questions` `question`
\nJOIN `plugin_survey_has_questions` `has_question` ON `has_question`.`question_id` = `question`.`id`
\nJOIN `plugin_survey_groups`        `group`        ON `has_question`.`group_id`    = `group`.`id`
\nWHERE `has_question`.`survey_id` = 2
\nAND `group`.`title` = \'Group 5\'
\nAND `question`.`deleted` = 0
\nAND `has_question`.`deleted` = 0
\nORDER by `has_question`.`order_id`',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Group 5' ORDER BY `date_created` DESC LIMIT 1)
);

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Parent survey'           ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Parent Survey - Group 5' ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget' LIMIT 1),
  '1',
  '5',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);
-- Group 6
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Group 6',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'survey_question_group'),
  'question_id',
  'order_id',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`) VALUES
(
  'Parent Survey - Group 6',
  'SELECT `question`.`id`, `question`.`title`, `has_question`.`order_id`, `has_question`.`survey_id` FROM `plugin_survey_questions` `question`
\nJOIN `plugin_survey_has_questions` `has_question` ON `has_question`.`question_id` = `question`.`id`
\nJOIN `plugin_survey_groups`        `group`        ON `has_question`.`group_id`    = `group`.`id`
\nWHERE `has_question`.`survey_id` = 2
\nAND `group`.`title` = \'Group 6\'
\nAND `question`.`deleted` = 0
\nAND `has_question`.`deleted` = 0
\nORDER by `has_question`.`order_id`',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Group 6' ORDER BY `date_created` DESC LIMIT 1)
);

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Parent survey'           ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Parent Survey - Group 6' ORDER BY `date_created` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget' LIMIT 1),
  '2',
  '6',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);