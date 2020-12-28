/*
ts:2016-01-11 15:35:00
*/
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES
('browser_sniffer_frontend', 'Frontend', '0', '1', '1', '1', '0', 'Notify visitors of the site, if they are using an old browser', 'toggle_button', 'Browser Sniffer', 'Model_Settings,on_or_off'),
('browser_sniffer_backend',  'Backend',  '1', '1', '1', '1', '1', 'Notify users of the CMS, if they are using an old browser',     'toggle_button', 'Browser Sniffer', 'Model_Settings,on_or_off')
;

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`) VALUES
('browser_sniffer_version_chrome',  'Google Chrome version',     '41',  '41',  '41',  '41',  '41',   'Latest version of Google Chrome that you do not support',     'text', 'Browser Sniffer'),
('browser_sniffer_version_firefox', 'Firefox version',           '36',  '36',  '36',  '36',  '36',   'Latest version of Mozilla Firefox that you do not support',   'text', 'Browser Sniffer'),
('browser_sniffer_version_ie',      'Internet Explorer version',  '9',   '9',   '9',   '9',   '9',   'Latest version of Internet Explorer that you do not support', 'text', 'Browser Sniffer'),
('browser_sniffer_version_opera',   'Opera version',             '29',  '29',  '29',  '29',  '29',   'Latest version of Opera that you do not support',             'text', 'Browser Sniffer'),
('browser_sniffer_version_safari',  'Safari version',             '6.1', '6.1', '6.1', '6.1', '6.1', 'Latest version of Safari that you do not support',            'text', 'Browser Sniffer')
;

