/*
ts:2017-09-24 10:16:00
*/

update engine_settings set `linked_plugin_name` = 'payments' where `variable` like '%boipa%';
update engine_settings set `linked_plugin_name` = 'messaging' where `variable` like '%bongo%';
update engine_settings set `linked_plugin_name` = 'messaging' where `variable` like '%sparkpost%';
update engine_settings set `linked_plugin_name` = 'payments' where `variable` like 'enable_mobile_payments';
update engine_settings set `linked_plugin_name` = 'courses' where (linked_plugin_name is null or linked_plugin_name = '') and `group` like '%course%';
update engine_settings set `linked_plugin_name` = 'sitc' where (linked_plugin_name is null or linked_plugin_name = '') and `group` like '%SITC%';
update engine_settings set `linked_plugin_name` = 'testimonials' where (linked_plugin_name is null or linked_plugin_name = '') and `group` like '%Testimonials%';
update engine_settings set `linked_plugin_name` = 'products' where (linked_plugin_name is null or linked_plugin_name = '') and `group` like '%Products%';
