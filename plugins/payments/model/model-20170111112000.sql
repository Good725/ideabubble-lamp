/*
ts:2017-01-11 11:20:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`)
  VALUES
  ('payment-error', 'EMAIL', '1', 'Payment Error', 'A payment failed attempt.<br />\r\nHost: $host <br />\r\nMessage: $message <br />\r\nData: $data', 'Payment', '$host,$message,$data');

INSERT IGNORE INTO plugin_contacts_mailing_list (`name`) VALUES ('Admin');

INSERT INTO `plugin_messaging_notification_template_targets`
  (`template_id`, `target_type`, `target`, `x_details`)
  VALUES
  (
    (select id from plugin_messaging_notification_templates where name='payment-error'),
    'CMS_CONTACT_LIST',
    (select id from plugin_contacts_mailing_list where name='Admin'),
    'to'
  );