/*
ts:2020-08-20 10:30:00
*/

-- Create th
INSERT INTO
  `plugin_reports_reports` (`name`, `summary`, `sql`, `date_created`, `date_modified`, `publish`, `delete`, `autoload`)
VALUES  (
  'Un-migrated phone numbers',
  'List of contacts with phone numbers, not making use of the country and area code fields',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  '1'
);

-- Set its SQL.
-- Find all contacts that have landline or mobile numbers that are not using the dial code fields
UPDATE
  `plugin_reports_reports`
SET
  `sql` = 'SELECT
    `contact`.`id`               AS `Contact Id`,
    CONCAT(`contact`.`first_name`, \' \', `contact`.`last_name`) AS `Contact name`,
    `phone`.`country_dial_code` AS `Country dial code`,
    `phone`.`dial_code`         AS `Dial code`,
    `phone`.`value`             AS `Number`
FROM `plugin_contacts3_contact_has_notifications` `phone`
INNER JOIN `plugin_contacts3_notifications` `type`    ON `phone`.`notification_id` = `type`.`id` AND `type`.`stub` IN (''mobile'', ''landline'')
JOIN `plugin_contacts3_notification_groups` `group`   ON `phone`.`group_id` = `group`.`id`
JOIN `plugin_contacts3_contacts`            `contact` ON `contact`.`notifications_group_id` = `group`.`id`
WHERE (`phone`.`value` IS NOT NULL AND `phone`.`value` != \'\')
AND (`phone`.`country_dial_code` IS NULL OR `phone`.`country_dial_code` = \'\' OR `phone`.`dial_code` IS NULL OR `phone`.`dial_code` = \'\')
order by IFNULL(TRIM(contact.last_name), ''zz'') ASC, IFNULL(TRIM(contact.first_name), ''zz'')'
WHERE
  `name` = 'Un-migrated phone numbers'
;