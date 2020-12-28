/*
ts:2016-09-01 12:40:00
*/

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`) VALUES ('content-wide');

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`) VALUES ('content-socialmedia');

-- Add the facebook panel, if it doesn't already exist
INSERT INTO `plugin_panels` (`title`, `position`, `type_id`, `text`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
SELECT
  'Facebook Feed',
  'content_right',
  `id`,
  '<div id=\"fb-root\">&nbsp;</div> <script>(function(d, s, id) {   var js, fjs = d.getElementsByTagName(s)[0];   if (d.getElementById(id)) return;   js = d.createElement(s); js.id = id;   js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4\";   fjs.parentNode.insertBefore(js, fjs); }(document, \'script\', \'facebook-jssdk\'));</script>  <div class=\"fb-page\" data-adapt-container-width=\"true\" data-height=\"400\" data-hide-cover=\"false\" data-href=\"https://www.facebook.com/limerickschoolofmusic\" data-show-facepile=\"false\" data-small-header=\"true\" data-tabs=\"timeline\" data-width=\"242\"> <div class=\"fb-xfbml-parse-ignore\"> <blockquote cite=\"https://www.facebook.com/limerickschoolofmusic\"><a href=\"https://www.facebook.com/limerickschoolofmusic\">Limerick School of Music</a></blockquote> </div> </div> ',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
FROM `plugin_panels_types`
WHERE
	`name` = 'static'
AND
	NOT EXISTS (SELECT `id` FROM `plugin_panels` WHERE `title` = 'Facebook Feed' AND `deleted` = 0)
LIMIT 1;

UPDATE `plugin_panels` SET `position`='content_right' WHERE `title`='Facebook Feed';


INSERT IGNORE INTO `plugin_pages_layouts` (`layout`) VALUES ('content-facebook');

INSERT IGNORE INTO `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES
(
  'News',
  'news',
  '900',
  '1600',
  'fit',
  '1',
  '225',
  '400',
  'fit',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);
