/*
ts:2016-04-10 18:15:00
*/

CREATE OR REPLACE
  SQL SECURITY INVOKER
  VIEW `ppanels_view_panels_list_admin` AS
  (select `plugin_panels`.`id` AS `id`,`plugin_panels`.`page_id` AS `page_id`,`plugin_panels`.`title` AS `title`,`plugin_panels`.`position` AS `position`,`plugin_panels`.`order_no` AS `order_no`,`plugin_panels`.`image` AS `image`,`plugin_panels`.`text` AS `text`,`plugin_panels`.`link_id` AS `link_id`,`plugin_panels`.`link_url` AS `link_url`,`plugin_panels`.`date_publish` AS `date_publish`,`plugin_panels`.`date_remove` AS `date_remove`,`plugin_panels`.`date_created` AS `date_created`,`plugin_panels`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_created`.`role` AS `created_by_role`,`plugin_panels`.`date_modified` AS `date_modified`,`plugin_panels`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modified`.`role` AS `modified_by_role`,`plugin_panels`.`publish` AS `publish` from ((((`plugin_panels` left join `engine_users` `users_create` on((`plugin_panels`.`created_by` = `users_create`.`id`))) left join `engine_project_role` `roles_created` on((`users_create`.`role_id` = `roles_created`.`id`))) left join `engine_users` `users_modify` on((`plugin_panels`.`modified_by` = `users_modify`.`id`))) left join `engine_project_role` `roles_modified` on((`users_modify`.`role_id` = `roles_modified`.`id`))) where (`plugin_panels`.`deleted` = 0));

