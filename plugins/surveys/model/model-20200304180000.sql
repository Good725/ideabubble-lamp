/*
ts:2020-03-04 18:00:00
*/
DELIMITER ;;
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `usable_parameters_in_template`, `overwrite_cms_message`, `date_created`, `date_updated`, `created_by`)
VALUES (
  'survey_invitation_link',
  'Email used to invite someone to fill out a survey',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  '$survey_name invitation',
  '<p>Hello $first_name</p>

  <p>Please use the below link to complete the $survey_name survey.</p>

  <a href="$link">Open survey</a>

  <p>Regards</p>',
  '$first_name, $last_name, $survey_name, $link',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IF(count(*) = 0, 1, `id`) FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
);;