/*
ts:2015-12-10 17:00:00
*/

UPDATE `settings` SET `value_live` = 'https://ideabubble.atlassian.net/' , `value_stage` = 'https://ideabubble.atlassian.net/' , `value_test` = 'https://ideabubble.atlassian.net/' , `value_dev` = 'https://ideabubble.atlassian.net/' WHERE `variable`= 'jira_url';
UPDATE `settings` SET `value_live` = 'Customer' , `value_stage` = 'Customer' , `value_test` = 'Customer' , `value_dev` = 'Customer' WHERE `variable`= 'jira_username';
UPDATE `settings` SET `value_live` = 'customer!951' , `value_stage` = 'customer!951' , `value_test` = 'customer!951' , `value_dev` = 'customer!951' WHERE `variable`= 'jira_password';