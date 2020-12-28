/*
ts:2018-01-11 16:20:00
*/

UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `header`        = REPLACE(
    `header`,
    '\"tel:<?= trim($settings_instance->get(\'telephone\')) ?>\"',
    '\"tel:<?= preg_replace(\'/[^0-9]/\', \'\', $settings_instance->get(\'telephone\')) ?>\"'
    )
WHERE
  `stub` = '03';


UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `header`        = REPLACE(
    `header`,
    '\"tel:<?= str_replace(\' \', \'\', $settings_instance->get(\'telephone\')) ?>\"',
    '\"tel:<?= preg_replace(\'/[^0-9]/\', \'\', $settings_instance->get(\'telephone\')) ?>\"'
    )
WHERE
  `stub` = '03';