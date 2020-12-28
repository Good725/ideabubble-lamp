/*
ts:2016-01-12 16:05:00
*/

INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
('Special offers feed', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), '1', '0', 'specialoffers', 'Model_Product,render_special_offers');