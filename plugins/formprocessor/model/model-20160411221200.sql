/*
ts:2016-04-11 22:12:00
*/

UPDATE `plugin_messaging_notification_templates` SET `name` = 'newsletter-signup' WHERE `name` = 'successful-payback-new-member-admin';
UPDATE `plugin_messaging_notification_templates`
  SET
    `message` = '<p>\n	<b>New Member has been Added to your Mailing List</b>\n</p>\n<p>\n	Name: $name<br/>\n	Email: $email\n</p>\n',
    `create_via_code` = 'Newsletter Signup',
    `usable_parameters_in_template` = '$email, $name'
  WHERE `name` = 'newsletter-signup';

