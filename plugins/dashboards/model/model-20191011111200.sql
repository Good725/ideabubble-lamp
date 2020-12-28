/*
ts:2019-10-11 11:12:00
*/

UPDATE engine_users
set `default_home_page` = '/admin'
where `default_home_page` = '/dashboard.html';

UPDATE `plugin_dashboards`
set `title` = 'Website bookings'
where `title` = 'Welcome' OR (`title` = 'Welcome to CourseCo' and `description` = 'Your Website and Course booking platform');