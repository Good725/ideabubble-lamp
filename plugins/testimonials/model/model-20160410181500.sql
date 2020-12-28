/*
ts:2016-04-10 18:15:00
*/

CREATE OR REPLACE
  SQL SECURITY INVOKER
  VIEW `ptestimonials_view_testimonials_list_admin` AS
  ((select `plugin_testimonials`.`id` AS `id`,`plugin_testimonials`.`title` AS `title`,`plugin_testimonials`.`category_id` AS `category_id`,`plugin_testimonials_categories`.`category` AS `category`,`plugin_testimonials`.`summary` AS `summary`,`plugin_testimonials`.`content` AS `content`,`plugin_testimonials`.`image` AS `image`,`plugin_testimonials`.`event_date` AS `event_date`,`plugin_testimonials`.`item_signature` AS `item_signature`,`plugin_testimonials`.`item_company` AS `item_company`,`plugin_testimonials`.`date_created` AS `date_created`,`plugin_testimonials`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_testimonials`.`date_modified` AS `date_modified`,`plugin_testimonials`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_testimonials`.`publish` AS `publish`,`plugin_testimonials`.`item_website` AS `item_website` from (((((`plugin_testimonials` left join `plugin_testimonials_categories` on((`plugin_testimonials`.`category_id` = `plugin_testimonials_categories`.`id`))) left join `engine_users` `users_create` on((`plugin_testimonials`.`created_by` = `users_create`.`id`))) left join `engine_users` `users_modify` on((`plugin_testimonials`.`modified_by` = `users_modify`.`id`))) left join `engine_project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `engine_project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_testimonials`.`deleted` = 0)));

