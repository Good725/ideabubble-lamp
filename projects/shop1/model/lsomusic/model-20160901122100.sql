/*
ts:2016-09-01 15:21:00
*/

UPDATE `engine_settings` SET
  `value_dev`   = 'https://vecweb.vecnet.ie/web_musiclimerickcity/webmusic/webbookmusic.html?loccode=lsom',
  `value_test`  = 'https://vecweb.vecnet.ie/web_musiclimerickcity/webmusic/webbookmusic.html?loccode=lsom',
  `value_stage` = 'https://vecweb.vecnet.ie/web_musiclimerickcity/webmusic/webbookmusic.html?loccode=lsom',
  `value_live`  = 'https://vecweb.vecnet.ie/web_musiclimerickcity/webmusic/webbookmusic.html?loccode=lsom'
WHERE
  `variable` = 'course_apply_link';
