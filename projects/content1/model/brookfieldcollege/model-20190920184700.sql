/*
ts:2019-09-20 18:47:00
*/

update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#5f1026'
	where engine_site_theme_variables.variable = 'primary' /* 2 */;

update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#f5f5f5'
	where engine_site_theme_variables.variable = 'secondary' /* 2 */;

update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#bfb8bf'
	where engine_site_theme_variables.variable = 'success' /* 2 */;


update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#17a2b8'
	where engine_site_theme_variables.variable = 'info' /* 2 */;

update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#ffc107'
	where engine_site_theme_variables.variable = 'warning' /* 2 */;

update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#dc3545'
	where engine_site_theme_variables.variable = 'danger' /* 2 */;

update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#5d0024'
	where engine_site_theme_variables.variable = 'email_header_background' /* 2 */;

update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#5d0024'
	where engine_site_theme_variables.variable = 'login_header_background' /* 2 */;

update engine_site_theme_has_variables
	inner join engine_site_theme_variables on engine_site_theme_has_variables.variable_id = engine_site_theme_variables.id
	set engine_site_theme_has_variables.`value` = '#ffffff'
	where engine_site_theme_variables.variable = 'search_background' /* 2 */;

