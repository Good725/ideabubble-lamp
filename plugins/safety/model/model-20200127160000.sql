/*
ts:2020-01-27 16:00:00
*/

-- Rename plugin from "accidents" to "safety" and replace other instances of the word "accident" with "incident".
UPDATE `engine_plugins` SET `name` = 'safety', `friendly_name` = 'Safety' WHERE `name` IN ('accidents', 'safety');

UPDATE `engine_resources` SET
  `alias`       = 'safety',
  `name`        = 'Safety',
  `description` = 'Access the safety plugin'
WHERE `alias` = 'accidents';

UPDATE `plugin_messaging_notification_templates` SET `name` = 'incident_reported_admin', `date_updated` = CURRENT_TIMESTAMP WHERE `name` = 'accident_reported_admin';
UPDATE `plugin_messaging_notification_templates` SET `name` = 'incident_reported_user',  `date_updated` = CURRENT_TIMESTAMP WHERE `name` = 'accident_reported_user';

UPDATE `engine_feeds` SET
  `name`           = 'Incident reporter',
  `modified_by`   = (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
  `date_modified` = CURRENT_TIMESTAMP,
  `short_tag`     = 'incident_reporter',
  `function_call` = 'Controller_Frontend_Safety,embed_form'
WHERE `name` = 'Accident reporter';

-- If the earlier ALTER IGNORE did not run, add the notes column now
DROP PROCEDURE IF EXISTS `fix_table_xyz`;
DELIMITER ;;
CREATE PROCEDURE fix_table_xyz()
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
BEGIN
    SELECT COUNT(*) INTO @table_is_renamed_20190130    FROM information_schema.`COLUMNS` c WHERE c.TABLE_SCHEMA=DATABASE() AND c.TABLE_NAME = 'plugin_safety_incidents';
    SELECT COUNT(*) INTO @notes_column_exists_20190130 FROM information_schema.`COLUMNS` c WHERE c.TABLE_SCHEMA=DATABASE() AND c.TABLE_NAME = 'plugin_accidents_accidents' AND c.COLUMN_NAME = 'notes';
    IF @table_is_renamed_20190130 = 0 AND @notes_column_exists_20190130 = 0 THEN
        ALTER TABLE `plugin_accidents_accidents` ADD COLUMN `notes` BLOB NULL DEFAULT NULL;
    END IF;
END;;
DELIMITER ;
call fix_table_xyz();
DROP PROCEDURE IF EXISTS `fix_table_xyz`;

DELIMITER ;
-- Rename the table, remove "weather" column, add "action_taken" column, reorder "severity" column options, move "notes" column
ALTER TABLE `plugin_accidents_accidents`
  DROP   COLUMN `weather`,
  ADD    COLUMN `action_taken`        BLOB NULL DEFAULT NULL AFTER `actions_required`,
  CHANGE COLUMN `severity` `severity` ENUM('Near miss', 'Absent ≤ 3 days', 'Absent > 3 days', 'Death') NULL DEFAULT NULL,
  CHANGE COLUMN `notes`    `notes`    BLOB NULL DEFAULT NULL AFTER `status`,
  RENAME TO `plugin_safety_incidents` ;

-- Ensure these have their latest properties to fix any inconsistencies caused by the ALTER IGNORE running or not
ALTER TABLE `plugin_safety_incidents`
CHANGE COLUMN `severity` `severity` ENUM('Near miss', 'Absent ≤ 3 days', 'Absent > 3 days', 'Death') NULL DEFAULT NULL,
CHANGE COLUMN `status` `status` ENUM('Pending', 'Resolved') NULL DEFAULT NULL ;

-- If the earlier ALTER IGNORE did not run, rename the location column now
DROP PROCEDURE IF EXISTS `fix_location_column_6`;
DELIMITER ;;
CREATE PROCEDURE fix_location_column_6()
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
BEGIN
    SELECT COUNT(*) INTO @location_id_column_exists_20190130_2 FROM information_schema.`COLUMNS` c WHERE c.TABLE_SCHEMA=DATABASE() AND c.TABLE_NAME = 'plugin_safety_incidents' AND c.COLUMN_NAME = 'location_id'/* 6 */;
    IF @location_id_column_exists_20190130_2 = 0 THEN
        ALTER TABLE `plugin_safety_incidents` CHANGE COLUMN `location` `location_id` INT(11) NULL DEFAULT NULL ;
    END IF;
END;;
DELIMITER ;
CALL fix_location_column_6();
DROP PROCEDURE IF EXISTS `fix_location_column_6`;

-- Insert dashboard alert notification, distinct from the email notifications
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `usable_parameters_in_template`, `overwrite_cms_message`, `date_created`, `date_updated`, `created_by`)
VALUES (
  'incident_reported_alert_admin',
  'Dashboard notification sent to the administration when someone reports an incident',
  'DASHBOARD',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'system'),
  'Incident reported',
  '<p>An incident has been reported; <strong>$title</strong>.</p>
\n
\n<p><strong>Reporter</strong>: $first_name $last_name<br />
\n<strong>Email</strong>: $email<br />
\n<strong>Phone</strong>: $mobile</p>
  ',
  '$email, $first_name, $last_name, $mobile, $title',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
);

-- Rename other notifications to be distinct
UPDATE `plugin_messaging_notification_templates` SET `name` = 'incident_reported_email_admin', `date_updated` = CURRENT_TIMESTAMP WHERE `name` = 'incident_reported_admin';
UPDATE `plugin_messaging_notification_templates` SET `name` = 'incident_reported_email_user',  `date_updated` = CURRENT_TIMESTAMP WHERE `name` = 'incident_reported_user';

-- "accident" -> "incident"
DELIMITER ;;
UPDATE `plugin_messaging_notification_templates`
SET
  `description`  = 'Email sent to the administration when someone reports an incident',
  `subject`      = 'Incident reported',
  `date_updated` = CURRENT_TIMESTAMP,
  `message`      = '<p>An incident has been reported; <strong>$title</strong>.</p>
\n
\n<p><strong>Reporter</strong>: $first_name $last_name<br />
\n<strong>Email</strong>: $email<br />
\n<strong>Phone</strong>: $mobile</p>'
WHERE
  `name` = 'incident_reported_email_admin'
;;


UPDATE `plugin_messaging_notification_templates`
SET
  `description`  = 'Email sent to the reporter of an incident',
  `date_updated` = CURRENT_TIMESTAMP,
  `message`      = '<p>Hello $first_name</p>

  <p>Thank you for reporting an incident. We are currently processing your request.</p>

  <p>Regards</p>'
WHERE
  `name` = 'incident_reported_email_user'
;;

-- Add default recipients
INSERT INTO `plugin_messaging_notification_template_targets`
(`template_id`, `target_type`, `target`, `x_details`, `date_created`) VALUES
(
  (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'incident_reported_email_admin'),
  'CMS_ROLE',
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  'to',
  CURRENT_TIMESTAMP
),
(
  (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'incident_reported_alert_admin'),
  'CMS_ROLE',
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  'to',
  CURRENT_TIMESTAMP
)
;;