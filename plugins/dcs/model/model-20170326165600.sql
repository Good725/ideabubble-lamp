/*
ts:2017-03-26 16:56:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('dcs', 'DCS API', '0', '0');
INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `note`, `type`, `group`) VALUES ('dcsapi_key', 'DCS API Key', 'dcs', 'DCS API KEY', 'text', 'DCS API');
INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `note`, `type`, `group`) VALUES ('dcsapi_password', 'DCS API Password', 'dcs', 'DCS API Password', 'text', 'DCS API');
INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `note`, `type`, `group`) VALUES ('dcsapi_security', 'DCS API Security', 'dcs', 'DCS API Security', 'text', 'DCS API');
INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `note`, `type`, `group`) VALUES ('dcsapi_username', 'DCS API Username', 'dcs', 'DCS API Username', 'text', 'DCS API');
INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `note`, `type`, `group`) VALUES ('dcsapi_vendor', 'DCS API Vendor', 'dcs', 'DCS API Vendor', 'text', 'DCS API');


CREATE TABLE plugin_dcs_sync
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  type  VARCHAR(50) NOT NULL,
  remote_id VARCHAR(100) NOT NULL,
  cms_id  VARCHAR(100) NOT NULL,
  synced DATETIME NOT NULL,

  KEY (remote_id),
  KEY (cms_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO `engine_cron_tasks` (`title`, `frequency`, `plugin_id`, `publish`) VALUES ('DCS API', '{\"minute\":[\"0\"],\"hour\":[\"0\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', (select id from engine_plugins where `name` = 'dcs'), '0');
