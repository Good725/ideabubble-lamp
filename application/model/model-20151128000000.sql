/*
ts:2015-11-28 00:00:00
*/
CREATE TABLE `engine_external_requests`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  host VARCHAR(255) NOT NULL,
  url VARCHAR(8192) NOT NULL,
  data MEDIUMTEXT,
  response  MEDIUMTEXT,
  http_status VARCHAR(3),
  requested DATETIME,
  requested_by  INT
) ENGINE = INNODB;

ALTER IGNORE TABLE `engine_external_requests` ROW_FORMAT=COMPRESSED;

INSERT IGNORE INTO `settings`
(`variable`,              `name`,               `note`,                                                 `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`) VALUES
('show_need_help_button', 'Need Help Button',   'Show a &quote;need help&quote; button in the header.', '0',          '0',           '0',          '0',         '0', 'toggle_button', 'Website', 'Model_Settings,on_or_off'),
('need_help_page',        'Need Help Page',     'Page that the donation button will link to',           '',           '',            '',           '',          '',  'combobox',      'Website', 'Model_Pages,get_pages_as_options');

