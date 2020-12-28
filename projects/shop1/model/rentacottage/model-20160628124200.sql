/*
ts:2016-06-28 12:42:00
*/

UPDATE `plugin_formbuilder_forms`
SET    `email_all_fields` = 0
WHERE  `fields` LIKE '%value="contact-form"%'
;
