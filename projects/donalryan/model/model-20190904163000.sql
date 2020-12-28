/*
ts:2019-09-04 16:30:00
*/
UPDATE `plugin_pages_pages`
SET `content` = REPLACE(`content`, '"/media/', '"/shared_media/donalryan/media/')
WHERE `content` LIKE '%/media/%' AND `deleted` = 0
;