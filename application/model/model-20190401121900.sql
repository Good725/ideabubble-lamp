/*
ts:2019-04-01 12:20:00
*/

INSERT INTO `engine_settings` (`variable`,`name`,`linked_plugin_name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`readonly`,`group`,`required`,`options`,`config_overwrite`,`expose_to_api`)
VALUES ('browser_sniffer_unsupported_options','Unsupported Browser Options',NULL,NULL,NULL,NULL,'',NULL,'both','Select the Browsers you do not want users to use. This will prompt a user with one of these browsers that their browser may not work with the site.','multiselect',0,'Browser Sniffer',0,'Model_Settings,get_unsupported_browser_options',0,0);
INSERT INTO `engine_settings` (`variable`,`name`,`linked_plugin_name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`readonly`,`group`,`required`,`options`,`config_overwrite`,`expose_to_api`)
VALUES ('browser_sniffer_recommended_browser','Recommended Browser',NULL,NULL,NULL,NULL,'1',NULL,'both','When the Browser Sniffer Alert is shown. This will be the browser the user will be prompted to install.','select',0,'Browser Sniffer',0,'Model_Settings,get_recommended_browser',0,0);
