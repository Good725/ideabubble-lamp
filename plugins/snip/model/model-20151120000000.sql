/*
ts:2015-11-20 00:00:00
*/

-- FLS-6
INSERT INTO `plugins` (`name`, `friendly_name`) VALUES ('snip', 'SNIP');

INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('snip_sync_incremental', 'Incremental syncronization', 1, 'both', '', 'toggle_button', 'SNIP', 0, 'Model_Settings,on_or_off');
INSERT INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('snip_ftp_host', 'FTP Host', 'snip.com', 'both', '', 'text', 'SNIP', 0, '');
INSERT INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('snip_ftp_user', 'FTP User', 'ideabubble', 'both', '', 'text', 'SNIP', 0, '');
INSERT INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('snip_ftp_password', 'FTP Password', '12345678', 'both', '', 'text', 'SNIP', 0, '');
INSERT INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('snip_ftp_import_folder', 'Import Folder(ftp)', 'import', 'both', '', 'text', 'SNIP', 0, '');
INSERT INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('snip_ftp_export', 'Export Folder(local)', 'export', 'both', '', 'text', 'SNIP', 0, '');

CREATE TABLE `plugin_snip_sync_history`
(
	`id`			INT AUTO_INCREMENT PRIMARY KEY,
	`data_type`		VARCHAR(20) NOT NULL,
	`cms_id`		INT NOT NULL,
	`snip_id`		INT,
	`synced`		DATETIME,
	
	KEY		(`data_type`, `cms_id`)
) ENGINE = INNODB;
