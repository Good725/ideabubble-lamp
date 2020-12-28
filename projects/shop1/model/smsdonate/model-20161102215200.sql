/*
ts:2016-11-02 21:52:00
*/

UPDATE plugin_reports_widgets SET `name` = 'Number Of Requests' WHERE `name` = 'Daily Number Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Completed Requests' WHERE `name` = 'Daily Number Of Completed Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Requests' WHERE `name` = 'Daily Value Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Paid Requests' WHERE `name` = 'Daily Value Of Paid Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Awaiting Requests' WHERE `name` = 'Daily Number Of Valid Requests Awaiting Decision';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Awaiting Requests' WHERE `name` = 'Daily Value Of Valid Requests Awaiting Decision';

UPDATE plugin_reports_widgets SET `name` = 'Weekly Number Of Requests' WHERE `name` = 'Weekly Number Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Requests' WHERE `name` = 'Weekly Value Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Completed Requests' WHERE `name` = 'Weekly Number Of Completed Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Paid Requests' WHERE `name` = 'Weekly Value Of Paid Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Awaiting Requests' WHERE `name` = 'Weekly Number Of Valid Requests Awaiting Decision';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Awaiting Requests' WHERE `name` = 'Weekly Value Of Valid Requests Awaiting Decision';

UPDATE plugin_reports_widgets SET `name` = 'Number Of Requests' WHERE `name` = 'Monthly Number Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Requests' WHERE `name` = 'Monthly Value Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Completed Requests' WHERE `name` = 'Monthly Number Of Completed Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Paid Requests' WHERE `name` = 'Monthly Value Of Paid Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Awaiting Requests' WHERE `name` = 'Monthly Number Of Valid Requests Awaiting Decision';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Awaiting Requests' WHERE `name` = 'Monthly Value Of Valid Requests Awaiting Decision';

INSERT INTO `plugin_messaging_notification_templates`
  SET
    `send_interval` = null,
    `name` = 'donation-sms-status-confirm',
    `description` = '',
    `driver` = 'SMS',
    `type_id` = 4,
    `subject` = '',
    `sender` = '',
    `message` = 'Your request has been confirmed',
    `overwrite_cms_message` = '0',
    `page_id` = '0',
    `header` = '',
    `footer` = '',
    `schedule` = null,
    `date_created` = NOW(),
    `date_updated` = NOW(),
    `last_sent` = null,
    `publish` = 1,
    `deleted` = 0,
    `create_via_code` = 'DONATION SMS reply',
    `usable_parameters_in_template` = null,
    `category_id` = '1';

INSERT INTO `plugin_messaging_notification_templates`
  SET
    `send_interval` = null,
    `name` = 'donation-sms-status-reject',
    `description` = '',
    `driver` = 'SMS',
    `type_id` = 4,
    `subject` = '',
    `sender` = '',
    `message` = 'Your request has been rejected',
    `overwrite_cms_message` = '0',
    `page_id` = '0',
    `header` = '',
    `footer` = '',
    `schedule` = null,
    `date_created` = NOW(),
    `date_updated` = NOW(),
    `last_sent` = null,
    `publish` = 1,
    `deleted` = 0,
    `create_via_code` = 'DONATION SMS reply',
    `usable_parameters_in_template` = null,
    `category_id` = '1';

INSERT INTO `plugin_messaging_notification_templates`
  SET
    `send_interval` = null,
    `name` = 'donation-sms-status-complete',
    `description` = '',
    `driver` = 'SMS',
    `type_id` = 4,
    `subject` = '',
    `sender` = '',
    `message` = 'Your request has been completed',
    `overwrite_cms_message` = '0',
    `page_id` = '0',
    `header` = '',
    `footer` = '',
    `schedule` = null,
    `date_created` = NOW(),
    `date_updated` = NOW(),
    `last_sent` = null,
    `publish` = 1,
    `deleted` = 0,
    `create_via_code` = 'DONATION SMS reply',
    `usable_parameters_in_template` = null,
    `category_id` = '1';

