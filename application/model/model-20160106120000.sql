/*
ts:2016-01-06 12:00:00
*/

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`) VALUES
('help_links','Help Links','','','','','','both','List the help links for the CMS','textarea','Help Menu','');

UPDATE `settings` SET `note` = 'List the help links for the CMS/nUse the format:/nText to display{http://myurl.com}[_blank]' WHERE `variable` = 'help_links';
UPDATE `settings` SET `value_live` = 'User Guide{https://ideabubble.atlassian.net/wiki/display/PUB/Public+Documentation}[_blank]', `value_stage` = 'User Guide{https://ideabubble.atlassian.net/wiki/display/PUB/Public+Documentation}[_blank]', `value_test` = 'User Guide{https://ideabubble.atlassian.net/wiki/display/PUB/Public+Documentation}[_blank]', `value_dev` = 'User Guide{https://ideabubble.atlassian.net/wiki/display/PUB/Public+Documentation}[_blank]', `default` = 'User Guide{https://ideabubble.atlassian.net/wiki/display/PUB/Public+Documentation}[_blank]' WHERE `variable` = 'help_links';