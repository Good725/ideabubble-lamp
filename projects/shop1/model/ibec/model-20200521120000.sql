/*
ts:2020-05-21 12:00:00
*/

/** News details **/
DELIMITER ;;

-- Update capitalisation
UPDATE `plugin_news_categories` SET `category` = 'Emerging Trends' WHERE `category` = 'Emerging trends';;

UPDATE `plugin_pages_pages` SET `title` = 'Emerging Trends' WHERE `title` = 'Emerging trends';;

-- Turn off terms text on the subscription form.
UPDATE `engine_settings` SET `value_dev` = '0', `value_test` = '0', `value_stage` = '0', `value_live` = '0' WHERE `variable` = 'newsletter_signup_terms';;

-- Set content for "Article" news items
UPDATE
  `plugin_news`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `content` = '<p>{addthis_toolbox-}</p>
\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Risus feugiat in ante metus dictum at. Nunc mi ipsum faucibus vitae aliquet nec ullamcorper sit. Lobortis scelerisque fermentum dui faucibus in ornare quam viverra. Sed viverra tellus in hac habitasse. Posuere lorem ipsum dolor sit amet consectetur adipiscing elit duis. Turpis tincidunt id aliquet risus feugiat. Vel elit scelerisque mauris pellentesque pulvinar. Orci dapibus ultrices in iaculis nunc sed. Vitae suscipit tellus mauris a diam maecenas. Pellentesque habitant morbi tristique senectus et netus et. Sed arcu non odio euismod lacinia at. Pellentesque habitant morbi tristique senectus. At urna condimentum mattis pellentesque. Tortor at auctor urna nunc id cursus metus. At volutpat diam ut venenatis tellus in metus vulputate eu. Mollis aliquam ut porttitor leo a diam sollicitudin tempor. Urna nec tincidunt praesent semper feugiat nibh sed. Amet massa vitae tortor condimentum lacinia.</p>
\n<p>Amet dictum sit amet justo. Leo integer malesuada nunc vel risus commodo viverra. Sed risus ultricies tristique nulla aliquet enim tortor at auctor. Sit amet risus nullam eget felis eget nunc lobortis. Venenatis tellus in metus vulputate eu. Tincidunt nunc pulvinar sapien et ligula ullamcorper. Interdum velit euismod in pellentesque massa. Egestas fringilla phasellus faucibus scelerisque. Arcu cursus vitae congue mauris rhoncus. Euismod in pellentesque massa placerat duis ultricies lacus sed. Turpis nunc eget lorem dolor sed viverra ipsum nunc aliquet.</p>
\n<figure>
\n    <img src="/shared_media/ibec/media/photos/content/team2.jpg" alt="" width="730" height="382" />
\n    <figcaption>Caption for image in article</figcaption>
\n</figure>
\n<p>Vulputate mi sit amet mauris commodo quis imperdiet massa. Nunc sed augue lacus viverra vitae congue. Arcu bibendum at varius vel pharetra vel. Diam in arcu cursus euismod quis viverra nibh cras pulvinar. Ac odio tempor orci dapibus ultrices in iaculis nunc. Feugiat in fermentum posuere urna nec tincidunt praesent semper feugiat. Sed nisi lacus sed viverra tellus in hac habitasse. Bibendum arcu vitae elementum curabitur. Quam quisque id diam vel quam elementum. Quis varius quam quisque id diam vel quam elementum pulvinar.</p>
\n<p>Tristique risus nec feugiat in. Vitae justo eget magna fermentum iaculis. Mi quis hendrerit dolor magna. Interdum varius sit amet mattis vulputate enim nulla. Bibendum neque egestas congue quisque egestas diam in arcu. Nunc sed id semper risus in hendrerit gravida rutrum quisque. Pretium quam vulputate dignissim suspendisse in est ante in nibh. Sed arcu non odio euismod lacinia at. Porta nibh venenatis cras sed felis eget velit. Scelerisque felis imperdiet proin fermentum. Leo vel fringilla est ullamcorper eget nulla facilisi. Nisl condimentum id venenatis a condimentum vitae. Quis blandit turpis cursus in hac habitasse platea dictumst. Ullamcorper sit amet risus nullam eget felis. Nunc lobortis mattis aliquam faucibus purus in. Natoque penatibus et magnis dis parturient. A pellentesque sit amet porttitor eget dolor morbi non arcu. A diam maecenas sed enim ut sem viverra aliquet. Urna cursus eget nunc scelerisque viverra. Varius morbi enim nunc faucibus.</p>
\n<p>In metus vulputate eu scelerisque felis imperdiet proin fermentum leo. At risus viverra adipiscing at in tellus integer feugiat scelerisque. Adipiscing diam donec adipiscing tristique risus nec. Sapien nec sagittis aliquam malesuada bibendum arcu vitae. Tincidunt vitae semper quis lectus. Mi proin sed libero enim sed faucibus. Sed velit dignissim sodales ut eu sem integer vitae. Turpis cursus in hac habitasse platea. Scelerisque purus semper eget duis at tellus at urna. Parturient montes nascetur ridiculus mus mauris vitae ultricies leo integer. Quis viverra nibh cras pulvinar mattis nunc sed blandit libero. Vitae turpis massa sed elementum tempus egestas sed sed risus. Quam vulputate dignissim suspendisse in est ante in.</p>
\n',
  `seo_footer` = '<p>{download_brochure-}</p>
\n
\n<p>{get_started-}</p>
\n'
WHERE
  `title` like 'Title of article. Emerging trend%';;


-- Set content for "Video" news items
UPDATE
  `plugin_news`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `content` = '<p>{addthis_toolbox-}</p>
\n<p>{video-NycTraffic.mp4}</p>
\n
\n<p>Description of video if required. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Risus feugiat in ante metus dictum at. Nunc mi ipsum faucibus vitae aliquet nec ullamcorper sit. Lobortis scelerisque fermentum dui faucibus in ornare quam viverra. Sed viverra tellus in hac habitasse. Posuere lorem ipsum dolor sit amet consectetur adipiscing elit duis.</p>
\n',
  `seo_footer` = '<p>{download_brochure-}</p>
\n
\n<p>{get_started-}</p>'
WHERE
  `title` like 'Title of video. Video item%';;


-- Set content for "Audio" news items
UPDATE
  `plugin_news`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `content` = '<p>{addthis_toolbox-}</p>
\n<p>{audio-ride_of_the_valkyries.mp3}</p>
\n
\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Risus feugiat in ante metus dictum at. Nunc mi ipsum faucibus vitae aliquet nec ullamcorper sit. Lobortis scelerisque fermentum dui faucibus in ornare quam viverra. Sed viverra tellus in hac habitasse. Posuere lorem ipsum dolor sit amet consectetur adipiscing elit duis.</p>
\n',
  `seo_footer` = '<p>{download_brochure-}</p>
\n
\n<p>{get_started-}</p>'
WHERE
  `title` like 'Title of podcast%';;