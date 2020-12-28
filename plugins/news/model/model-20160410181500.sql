/*
ts:2016-04-10 18:15:00
*/

CREATE OR REPLACE
  SQL SECURITY INVOKER
  VIEW `pnews_view_news_list_admin` AS
  (select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news`.`category_id` AS `category_id`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`alt_text` AS `alt_text`,`plugin_news`.`title_text` AS `title_text`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`order` AS `order`,`plugin_news`.`date_created` AS `date_created`,`plugin_news`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_news`.`publish` AS `publish`,`plugin_news`.`seo_title` AS `seo_title`,`plugin_news`.`seo_keywords` AS `seo_keywords`,`plugin_news`.`seo_description` AS `seo_description`,`plugin_news`.`seo_footer` AS `seo_footer` from (((((`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) left join `engine_users` `users_create` on((`plugin_news`.`created_by` = `users_create`.`id`))) left join `engine_users` `users_modify` on((`plugin_news`.`modified_by` = `users_modify`.`id`))) left join `engine_project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `engine_project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_news`.`deleted` = 0));

