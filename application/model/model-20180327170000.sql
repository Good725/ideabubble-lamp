/*
ts:2018-03-27 16:00:00
*/

UPDATE `plugin_formbuilder_forms` SET
  `captcha_version` = 2,
  `date_modified` = CURRENT_TIMESTAMP
WHERE
  `captcha_version` != 2
;
