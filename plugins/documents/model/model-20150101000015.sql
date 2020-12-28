/*
ts:2015-01-01 00:00:15
*/
INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('documents', 'Documents', '1', '0', NULL);

CREATE TABLE IF NOT EXISTS `plugin_documents_folder_options` (
`id` INT(10) UNSIGNED AUTO_INCREMENT,
`folder_name` VARCHAR(255),
`friendly_name` VARCHAR(255),
`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
`date_modified` TIMESTAMP NULL  ,
`created_by` INT NOT NULL ,
`modified_by`   INT NOT NULL ,
`publish`       TINYINT NOT NULL DEFAULT 1 ,
`deleted`       TINYINT NOT NULL DEFAULT 0,
PRIMARY KEY (`id`)) ENGINE = InnoDB;


INSERT IGNORE INTO `plugin_documents_folder_options` (`folder_name`, `friendly_name`, `created_by`,`modified_by`) VALUES
('Bookings'          , 'bookings',2,2),
('Invoices'          , 'invoices',2,2),
('Images'            , 'images',2,2),
('Bills'             , 'bills',2,2),
('Timetables'        , 'timetables',2,2),
('Other Documents'   , 'other_documents',2,2);

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('template_folder_path', 'Template Base Folder Path', NULL, NULL, '/Library/WebServer/tmp/templates/', '/Library/WebServer/tmp/templates/', NULL, 'both', 'Base folder for the document templates', 'text', 'Documents', 0, ''),
('destination_folder_path','Destination Base Folder Path', NULL, NULL, '/Library/WebServer/tmp/save/', '/Library/WebServer/tmp/save/', NULL, 'both', 'Base folder to save the documents to.', 'text', 'Documents', 0, '');

DELETE FROM `settings` where `group` = 'Documents';

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('doc_generation_active', 'Active', '1', '1', '1', '1', '0', 'both', 'Toggles for allowing doc generation or not', 'toggle_button', 'Word Template Generation', 0, 'Model_Settings,on_or_off'),
('doc_template_path', 'Template Path', '/templates/', '/templates/', '/templates/', '/templates/', '/templates/', 'both', 'Location in FILES of document templates', 'text', 'Word Template Generation', 0, ''),
('doc_destination_path','Destination Path', NULL, NULL, NULL, NULL, NULL, 'both', 'Location in FILES plugin to save the documents to.', 'text', 'Word Template Generation', 0, ''),
('doc_temporary_path','Temporary Path', '/temp_location/', '/temp_location/', '/temp_location/', '/temp_location/', '/temp_location/', 'both', 'Location on server root save temporary output.', 'text', 'Word Template Generation', 0, ''),
('doc_test_mode', 'Test Mode', '0', '0', '0', '0', '0', 'both', 'Toggles for allowing docs to be saved in test location instead of live', 'toggle_button', 'Word Template Generation', 0, 'Model_Settings,on_or_off'),
('doc_test_destination_path','Test Destination Path', '/test', '/test', '/test', '/test', '/test', 'both', 'Location in FILES plugin to save the documents to in test mode', 'text', 'Word Template Generation', 0, '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('word2pdf_active', 'Active', '1', '1', '1', '1', '0', 'both', 'Toggles for allowing PDF generation or not', 'toggle_button', 'Word to PDF Conversion', 0, 'Model_Settings,on_or_off'),
('word2pdf_thirdparty_active', '3rd Party Active', '1', '1', '1', '1', '0', 'both', 'Toggles for allowing 3rd party pdf service to be used or not', 'toggle_button', 'Word to PDF Conversion', 0, 'Model_Settings,on_or_off'),
('word2pdf_thirdparty_url','3rd Party URL', 'http://do.convertapi.com/word2pdf', 'http://do.convertapi.com/word2pdf', 'http://do.convertapi.com/word2pdf', 'http://do.convertapi.com/word2pdf', 'http://do.convertapi.com/word2pdf', 'both', 'URL for 3rd party Word2PDF api ', 'text', 'Word to PDF Conversion', 0, ''),
('word2pdf_thirdparty_api','3rd Party API Key', NULL, '562713330', '562713330', '562713330', '562713330', 'both', 'API Key for 3rd Party service ', 'text', 'Word to PDF Conversion', 0, ''),
('word2pdf_thirdparty_balance','3rd Party Credit Balance', NULL, NULL, NULL, NULL, NULL, 'both', 'Show credit remaining on API, (2 credits per conversion or €.02 x 2 = .04 cents) ', 'text', 'Word to PDF Conversion', 0, ''),
('word2pdf_local_active', 'Local Active', '1', '1', '1', '1', '0', 'both', 'Toggles for allowing Local PDF generation or not', 'toggle_button', 'Word to PDF Conversion', 0, 'Model_Settings,on_or_off'),
('word2pdf_local_url','Local URL', 'http://convertapi.ideabubble.ie/word2pdf/', 'http://convertapi.ideabubble.ie/word2pdf/', 'http://convertapi.ideabubble.ie/word2pdf/', 'http://convertapi.ideabubble.ie/word2pdf/', 'http://convertapi.ideabubble.ie/word2pdf/', 'both', 'URL for Local Word2PDF api ', 'text', 'Word to PDF Conversion', 0, ''),
('word2pdf_local_api','Local API Key', NULL, '562713330', '562713330', '562713330', '562713330', 'both', 'API Key for Local Service ', 'text', 'Word to PDF Conversion', 0, '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('word2pdf_savepdf', 'Save PDF', '0', '0', '0', '0', '0', 'both', 'Toggles for allowing PDF to be saved to the system', 'toggle_button', 'Word to PDF Conversion', 0, 'Model_Settings,on_or_off');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('doc_save', 'Save DOCX', '1', '1', '1', '1', '1', 'both', 'Toggles for allowing doc saving or not', 'toggle_button', 'Word Template Generation', 0, 'Model_Settings,on_or_off');

INSERT IGNORE INTO `activities_actions` (`stub`, `name`) VALUES
('generate-docx',  'Generate DOCX'),
('generate-pdf',   'Generate PDF');

TRUNCATE `plugin_documents_folder_options`;
INSERT IGNORE INTO `plugin_documents_folder_options` (`folder_name`, `friendly_name`, `created_by`,`modified_by`) VALUES
('Contacts', 'contact',2,2);

UPDATE `plugins` SET icon = 'files.png' WHERE friendly_name = 'Documents';

INSERT IGNORE INTO `plugin_files_file` (`type`,`name`,`parent_id`) VALUES (0,'contacts',1);
    select last_insert_id() into @refid_plugin_reports_reports;
    INSERT IGNORE INTO `plugin_files_file` (`type`,`name`,`parent_id`) VALUES (0,'all',@refid_plugin_reports_reports);
INSERT IGNORE INTO `plugin_files_file` (`type`,`name`,`parent_id`) VALUES (0,'templates',1);
