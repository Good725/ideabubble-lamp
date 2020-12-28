/*
ts:2018-03-20 09:52:00
*/

UPDATE plugin_contacts3_preferences SET `deleted` = 1 WHERE `label` IN ('SMS Marketing', 'Email Marketing');

INSERT INTO `plugin_contacts3_preferences` (`label`, `stub`, `group`) VALUES ('Marketing Updates', 'marketing_updates', 'notification');


