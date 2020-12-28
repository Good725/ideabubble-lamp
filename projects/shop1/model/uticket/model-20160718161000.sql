/*
ts:2016-07-18 16:10:00
*/

SELECT id INTO @ut_206_user_id FROM `engine_users` WHERE `email` = 'super@ideabubble.ie';

INSERT IGNORE INTO `plugin_menus` (`category`, `title`, `link_tag`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) VALUES
('main',   'Pricing',                null, '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @ut_206_user_id, @ut_206_user_id),
('footer', 'Home',                   '-1', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @ut_206_user_id, @ut_206_user_id),
('footer', 'About',                  null, '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @ut_206_user_id, @ut_206_user_id),
('footer', 'News',                   null, '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @ut_206_user_id, @ut_206_user_id),
('footer', 'Sell With Us',           null, '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @ut_206_user_id, @ut_206_user_id),
('footer', 'Terms &amp; Conditions', null, '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @ut_206_user_id, @ut_206_user_id);