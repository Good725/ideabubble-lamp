/*
ts:2016-02-16 09:47:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  SET
    `send_interval` = null,
    `name` = 'db-update-error',
    `description` = '',
    `driver` = 'EMAIL',
    `type_id` = 1,
    `subject` = 'DB Update Failed',
    `sender` = '',
    `message` = '$error',
    `overwrite_cms_message` = 0,
    `page_id` = null,
    `header` = '',
    `footer` = '',
    `schedule` = null,
    `date_created` = NOW(),
    `created_by` = 1,
    `date_updated` = NOW(),
    `last_sent` = null,
    `publish` = 1,
    `deleted` = 0,
    `create_via_code` = 'DALM',
    `usable_parameters_in_template` = ' $error ',
    `doc_generate` = null,
    `doc_helper` = null,
    `doc_template_path` = null,
    `doc_type` = null;
