/*
ts:2019-02-28 12:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'testimonials',
  `value_test`  = 'testimonials',
  `value_stage` = 'testimonials',
  `value_live`  = 'testimonials'
WHERE
  `variable`    = 'home_page_feed_1'
;