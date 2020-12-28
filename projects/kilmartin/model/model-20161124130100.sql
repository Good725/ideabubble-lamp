/*
ts:2016-11-24 13:01:00
*/
UPDATE `engine_settings` SET `value_live`='1', `value_stage`='1', `value_test`='1', `value_dev`='1' WHERE `variable`='slaask_api_access_frontend';
UPDATE `engine_settings` SET `value_live`='1', `value_stage`='1', `value_test`='1', `value_dev`='1' WHERE `variable`='slaask_api_access_cms';
UPDATE `engine_settings` SET
  `value_live` ='c351267bf6dbdf562e1c97d340820f03',
  `value_stage`='c351267bf6dbdf562e1c97d340820f03',
  `value_test` ='c351267bf6dbdf562e1c97d340820f03',
  `value_dev`  ='c351267bf6dbdf562e1c97d340820f03'
WHERE `variable` = 'slaask_api_key';


INSERT INTO `plugin_reports_widgets` (`name`, `type`, `html`, `date_created`, `publish`, `delete`) VALUES
(
  'Facebook',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'raw_html' LIMIT 1),
  '<div id=\"fb-root\"></div>\n<script>(function(d, s, id) {\n  var js, fjs = d.getElementsByTagName(s)[0];\n  if (d.getElementById(id)) return;\n  js = d.createElement(s); js.id = id;\n  js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8\";\n  fjs.parentNode.insertBefore(js, fjs);\n}(document, \'script\', \'facebook-jssdk\'));</script>\n<div class=\"fb-page\" data-href=\"https://www.facebook.com/kilmartineducationalservices/\" data-tabs=\"timeline\" data-small-header=\"false\" data-adapt-container-width=\"true\" data-hide-cover=\"false\" data-show-facepile=\"true\"><blockquote cite=\"https://www.facebook.com/kilmartineducationalservices/\" class=\"fb-xfbml-parse-ignore\"><a href=\"https://www.facebook.com/kilmartineducationalservices/\">Kilmartin Educational Services</a></blockquote></div>',
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT INTO `plugin_reports_widgets` (`name`, `type`, `html`, `date_created`, `publish`, `delete`) VALUES
(
  'Twitter',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'raw_html' LIMIT 1),
  '{twitter_api_feed-}',
  CURRENT_TIMESTAMP,
  '1',
  '0'
);


INSERT INTO `plugin_reports_reports` (`name`, `dashboard`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Facebook Feed',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Facebook' AND `delete` = '0' ORDER BY `id` DESC LIMIT 1),
  'sql'
);

INSERT INTO `plugin_reports_reports` (`name`, `dashboard`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Twitter Feed',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Twitter' AND `delete` = '0' ORDER BY `id` DESC LIMIT 1),
  'sql'
);

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Manager'),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Facebook Feed' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget'),
  2,
  2,
  1,
  0
);


INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Manager'),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Twitter Feed' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget'),
  3,
  3,
  1,
  0
);
