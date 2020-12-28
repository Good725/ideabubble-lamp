/*
ts:2016-06-28 12:41:00
*/

UPDATE `plugin_formbuilder_forms`
SET    `email_all_fields` = 1
WHERE  `fields` LIKE '%value="contact-form"%'
;
