/*
ts:2016-02-23 16:15:00
*/
INSERT IGNORE INTO `plugin_panels` (`title`, `position`, `type_id`, `image`, `text`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Facebook Feed',
  'content_left',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static' LIMIT 1),
  '0',
  '<div id=\"fb-root\">&nbsp;</div>\n<script>(function(d, s, id) {\n  var js, fjs = d.getElementsByTagName(s)[0];\n  if (d.getElementById(id)) return;\n  js = d.createElement(s); js.id = id;\n  js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5\";\n  fjs.parentNode.insertBefore(js, fjs);\n}(document, \'script\', \'facebook-jssdk\'));</script>\n\n<div class=\"fb-page\" data-adapt-container-width=\"true\" data-hide-cover=\"false\" data-href=\"https://www.facebook.com/GrettalsPetals\" data-show-facepile=\"false\" data-small-header=\"false\" data-tabs=\"timeline\">\n<div class=\"fb-xfbml-parse-ignore\">\n<blockquote cite=\"https://www.facebook.com/GrettalsPetals\"><a href=\"https://www.facebook.com/GrettalsPetals\">Grettals Petals</a></blockquote>\n</div>\n</div>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);
