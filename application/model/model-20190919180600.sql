/*
ts:2019-09-11 17:43:00
*/


INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'api_courses', 'API Courses', 'API Courses', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'api'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'api_courses_bookings', 'API Courses Bookings', 'API Courses Bookings', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'api'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'api_chat', 'API Chat', 'API Chat', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'api'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'api_messaging', 'API Messaging', 'API Messaging', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'api'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'api_todos', 'API Todos', 'API Todos', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'api'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'api_events', 'API Events', 'API Events', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'api'));


DELETE FROM engine_settings WHERE `variable` in ('app_enable_courses', 'app_enable_course_bookings', 'app_enable_courses', 'app_enable_chat', 'app_enable_messaging', 'app_enable_todos', 'app_enable_events');

UPDATE engine_site_theme_variables SET `deleted`=1;

select theme_id into @theme_id_20190920_5 from engine_site_theme_has_variables limit 1;
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('primary', 'primary', '#00c6ee');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('secondary', 'secondary', '#f5f5f5');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('success', 'success', '#0e2a6b');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('tertiary', 'tertiary', '#95c813');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('info', 'info', '#17a2b8');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('warning', 'warning', '#ffc107');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('danger', 'danger', '#ff0000');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('visited_link', 'visited_link', '#9267bc');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('menu_inactive', 'menu_inactive', '#787878');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('dark', 'dark', '#333333');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('light', 'light', '#ffffff');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('header_background', 'header_background', '#00c6ee');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('header_text', 'header_text', '#ffffff');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('login_header_background', 'login_header_background', '#00c6ee');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('email_header_background', 'email_header_background', '#00c6ee');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('search_background', 'search_background', '#ffffff');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('fixed_menu_background', 'fixed_menu_background', '#f7f7f7');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('cookie_notice_background', 'cookie_notice_background', '#00c6ee');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('loading_line', 'loading_line', '#0e2a6b');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('tab_marker', 'tab_marker', '#95c813');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('second', 'second', '#95c813');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('chat_box', 'chat_box', '#333333');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('button_color', 'button_color', '#12378f');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('button_color1', 'button_color1', '#12378f');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('submenu_unselected_color', 'submenu_unselected_color', '#');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('submenu_selected_color', 'submenu_selected_color', '#');
INSERT INTO `engine_site_theme_variables` (`variable`, `name`, `default`) VALUES ('alert_text_color', 'alert_text_color', '#');

insert ignore into engine_site_theme_has_variables
(theme_id,variable_id,`value`)
(select st.id,tv.id, tv.`default` from engine_site_themes st join  engine_site_theme_variables tv where st.deleted=0 and tv.deleted = 0);

DELETE FROM engine_settings WHERE `variable` LIKE 'app_theme_color%';
