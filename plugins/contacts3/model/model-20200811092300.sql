/*
ts:2020-08-11 09:23:00
*/

ALTER TABLE plugin_contacts3_contacts ADD COLUMN gdpr_cleansed_datetime DATETIME;
ALTER TABLE plugin_contacts3_contacts ADD COLUMN gdpr_cleansed_by_report_id INT;

INSERT INTO `engine_cron_tasks`
  (`title`, `frequency`, `plugin_id`, `publish`, `action`, `extra_parameters`)
  VALUES
  ('GDPR cleanse', '{\"minute\":[\"0\"],\"hour\":[\"0\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', (select id from engine_plugins where name='contacts3'), '0', 'cron_gdpr_cleanse', '\-\-uri=\"/frontend/contacts/cron_gdpr_cleanse\"');


CREATE TABLE plugin_contacts3_gdpr_cleanse_reports
(
  id  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  report_name VARCHAR(100) NOT NULL
)
ENGINE=INNODB
CHARSET = UTF8;
