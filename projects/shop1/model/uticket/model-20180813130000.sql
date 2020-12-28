/*
ts:2018-08-13 13:00:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 0,
  `value_test`  = 0,
  `value_stage` = 0,
  `value_live`  = 0
WHERE
  `variable`    = 'home_page_news_feed'
;