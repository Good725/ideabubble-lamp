/*
ts:2019-03-19 12:20:00
*/

/* Setup the Facebook feed */
UPDATE
  `engine_settings`
SET
  `value_dev`   = 'limerickschoolofmusic',
  `value_test`  = 'limerickschoolofmusic',
  `value_stage` = 'limerickschoolofmusic',
  `value_live`  = 'limerickschoolofmusic'
WHERE
  `variable`    = 'facebook_url';

UPDATE
  `engine_settings`
SET
  `value_dev`   = '1',
  `value_test`  = '1',
  `value_stage` = '1',
  `value_live`  = '1'
WHERE
  `variable`    = 'footer_facebook_feed';

/* Merge the "Applications" menu into the "Useful Links" menu to make room for the Facebook feed. */
UPDATE `plugin_menus`
SET    `parent_id` = (SELECT `id` FROM (SELECT `id` FROM `plugin_menus` WHERE `title` = 'Useful Links' AND `deleted` = 0 LIMIT 1) `m2`)
WHERE  `title`     = 'Apply For a Course'
AND    `category`  = 'footer'
AND    `deleted`   = 0;

UPDATE `plugin_menus`
SET    `publish`  = 0
WHERE  `title`    = 'Applications'
AND    `category` = 'footer'
AND    `deleted`  = 0;