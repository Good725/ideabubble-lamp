/*
ts:2020-07-03 15:22:00
*/

-- Create report for tracking contacts who subscribed via the "newsletter subscription" form
DELIMITER ;;
INSERT INTO `plugin_reports_reports` (`name`, `summary`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES (
  'Opt-in mailing list',
  'List of contacts created by people using the newsletter subscription form.',
  "SELECT
  `contact`.`first_name` AS `First name`,
  `contact`.`last_name` AS `Last name`,
  `email`.`value` AS `Email`,
  'Yes' AS 'Opted',
  DATE_FORMAT(`contact`.`date_created`, '%d/%m/%Y') AS `Date`
FROM `plugin_contacts3_contacts` `contact`
  JOIN `plugin_contacts3_contact_has_tags` `has_tag`
    ON `has_tag`.`contact_id` = `contact`.`id`
  JOIN `plugin_contacts3_tags` `tag`
    ON `has_tag`.`tag_id` = `tag`.`id`
  JOIN `plugin_contacts3_contact_has_notifications` `email`
    ON `contact`.`notifications_group_id` = `email`.`group_id`
  JOIN `plugin_contacts3_notifications` `email_notif`
    ON `email`.`notification_id` = `email_notif`.`id`
    AND `email_notif`.`stub` = 'email'
WHERE `tag`.`name` = 'newsletter_signup'
  AND `contact`.`delete` = 0
ORDER BY `contact`.`date_created`;",
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);;

-- Update the report to instead track people with the "marketing updates" preference
-- This will track both people who subscribed via the newsletter form and the checkbox on the checkout
-- Update to display the email of the linked user, if an email has not been set at contact-notification level
UPDATE
  `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `sql` = "SELECT
\n    `contact`.`first_name` AS `First name`,
\n    `contact`.`last_name` AS `Last name`,
\n    IFNULL(`email`.`value`, `user`.`email`) AS `Email`,
\n    IF(`has_preference`.`id`, 'Yes', 'No') AS 'Opted',
\n    DATE_FORMAT(`has_preference`.`date_created`, '%d/%m/%Y') AS `Date`
\nFROM `plugin_contacts3_contacts` `contact`
\n  JOIN `plugin_contacts3_contact_has_preferences` `has_preference`
\n    ON `has_preference`.`contact_id` = `contact`.`id`
\n
\n  JOIN `plugin_contacts3_preferences` `preference`
\n    ON `has_preference`.`preference_id` = `preference`.`id`
\n   AND `has_preference`.`deleted` = 0
\n
\n  LEFT JOIN `engine_users` `user`
\n    ON `contact`.`linked_user_id` = `user`.`id`
\n
\n  LEFT JOIN `plugin_contacts3_contact_has_notifications` `email`
\n    ON `contact`.`notifications_group_id` = `email`.`group_id`
\n   AND `email`.`notification_id` = (SELECT `id` FROM `plugin_contacts3_notifications` WHERE `stub` = 'email')
\n
\nWHERE
\n  `preference`.`stub` = 'marketing_updates'
\n
\nORDER BY `has_preference`.`date_created` DESC;"
 WHERE
  `name` = 'Opt-in mailing list';;



