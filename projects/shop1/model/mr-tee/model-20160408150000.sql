/*
ts:2016-04-08 15:00:00
*/
UPDATE IGNORE `settings`
SET    `value_live`='0',`value_stage`='0', `value_test`='0', `value_dev`='0'
WHERE  `variable`='sidebar_news_feed';