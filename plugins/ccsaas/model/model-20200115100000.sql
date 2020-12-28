/*
ts: 2020-01-15 10:00:00
*/

INSERT IGNORE INTO `engine_plugins`
  (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUES
  ('ccsaas', 'CC SAAS', '1', '0', NULL);

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('ccsaas', 'ccsaas_vhost_conf_template', 'VHOST Conf Template', '/etc/httpd/vhosts.d/vhost.sample', '/etc/httpd/vhosts.d/vhost.sample', '/etc/httpd/vhosts.d/vhost.sample',  '/etc/httpd/vhosts.d/vhost.sample',  '',  'both', '', 'text', 'CC SAAS Conf', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('ccsaas', 'ccsaas_vhost_dir', 'VHOST Conf Directory', '/etc/httpd/vhosts.d', '/etc/httpd/vhosts.d', '/etc/httpd/vhosts.d',  '/etc/httpd/vhosts.d',  '',  'both', '', 'text', 'CC SAAS Conf', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('ccsaas', 'ccsaas_mode', 'Mode', '', '', '',  '',  '',  'both', '', 'select', 'CC SAAS Conf', 0, 'Model_Ccsaas,get_settings_modes');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('ccsaas', 'ccsaas_central_host', 'Central Host', 'http://ideabubble.ie', 'http://ideabubble.ie', 'http://ideabubble.ie',  'http://ideabubble.ie',  '',  'both', '', 'text', 'CC SAAS Conf', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('ccsaas', 'ccsaas_branch_allowed_ips', 'Branch Allowed IPs', '88.208.233.233,213.171.220.156,78.47.221.220', '88.208.233.233,213.171.220.156,78.47.221.220', '88.208.233.233,213.171.220.156,78.47.221.220',  '88.208.233.233,213.171.220.156,78.47.221.220',  '',  'both', '', 'text', 'CC SAAS Conf', 0, '');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES ('0', 'ccsaas', 'CC SAAS', 'CC SAAS');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'ccsaas_edit', 'CC SAAS Edit', 'CC SAAS Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'ccsaas'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'ccsaas_view', 'CC SAAS View', 'CC SAAS View', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'ccsaas'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'ccsaas_view_limited', 'CC SAAS View Limited', 'CC SAAS View Limited', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'ccsaas'));

CREATE TABLE plugin_ccsaas_branch_servers
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  host  VARCHAR(255),
  ip4  VARCHAR(15),

  `published`     TINYINT(1) NOT NULL DEFAULT 1,
  `deleted`       TINYINT(1) NOT NULL DEFAULT 0,
  `date_created`  DATETIME,
  `date_modified` DATETIME,
  `created_by`    INT NULL,
  `modified_by`   INT NULL
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_ccsaas_hosts
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  contact_id  INT,
  hostname  VARCHAR(255) NOT NULL,
  starts DATE,
  expires DATE,
  is_trial  TINYINT NOT NULL DEFAULT 0,
  project_folder VARCHAR(100) NOT NULL DEFAULT 'shop1',
  branch_server_id  INT,

  `published`     TINYINT(1) NOT NULL DEFAULT 1,
  `deleted`       TINYINT(1) NOT NULL DEFAULT 0,
  `date_created`  DATETIME,
  `date_modified` DATETIME,
  `created_by`    INT NULL,
  `modified_by`   INT NULL
)
ENGINE = INNODB
CHARSET = UTF8;

ALTER TABLE `plugin_ccsaas_hosts` RENAME `plugin_ccsaas_websites`;

ALTER TABLE plugin_ccsaas_websites ADD COLUMN cms_skin VARCHAR(50);

INSERT INTO engine_project_role
  (role,description,publish,deleted,allow_frontend_login)
  VALUES
  ('Website Owner', 'Website Owner', 1, 0, 1);
