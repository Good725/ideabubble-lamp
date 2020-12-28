/*
ts:2016-09-15 10:00:00
*/

ALTER IGNORE TABLE `engine_settings` ADD COLUMN `readonly` INT(1) NOT NULL DEFAULT 0  AFTER `type` ;

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `note`, `type`, `readonly`, `group`) VALUES
('database_sync_source_server',   'Source Database Server', 'The server the database was last synchronised from',           'text', '1', 'Database Synchronisation'),
('database_sync_source_database', 'Source Database Name',   'The name of the database that this one was synchronised from', 'text', '1', 'Database Synchronisation'),
('database_sync_date',            'Date Last Synchronised', 'The date the database was last synchronised',                  'text', '1', 'Database Synchronisation');
