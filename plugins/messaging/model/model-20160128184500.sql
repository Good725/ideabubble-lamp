/*
ts:2016-01-28 18:45:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  SET
    `name`='report-document-print',
    `description`='Default Template to use for Report Printing',
    `driver`='EMAIL',
    `type_id`=1,
    `subject`='Print Documents',
    `message`='Print Documents',
    `date_created`=NOW(),
    `created_by`=1,
    `date_updated`=NOW(),
    `publish`=1,
    `deleted`=0,
    `create_via_code`='Report Print Document Generation',
    `usable_parameters_in_template`='$reportname',
    `doc_generate`=0,
    `doc_helper`=null,
    `doc_template_path`=null,
    `doc_type`=null;
