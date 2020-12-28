/*
ts:2016-04-06 15:55:00
*/

INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
('Property deals feed', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0', 'property_deals', 'Model_Propman,render_deals');

UPDATE IGNORE `engine_feeds` SET `function_call` = 'Model_Propman,render_deals_feed' WHERE `name` = 'Property deals feed';