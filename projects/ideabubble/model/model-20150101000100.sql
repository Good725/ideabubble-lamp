/*
ts:2015-01-01 00:01:00
*/

INSERT IGNORE INTO plugin_notifications_event (`name`,`description`,`from`,`subject`) VALUES('successful-payment-seller','Successful Payment Seller','payments@ideabubble.ie','Payment Received'),('successful-payment-customer','Successful Payment Customer','payments@ideabubble.ie','Thank you for your payment.');

INSERT IGNORE INTO ppages_layouts (`layout`) VALUES ('ideabubble_layout5');

-- IBOC-220
DELETE engine_dalm_statement
	FROM engine_dalm_statement
		INNER JOIN engine_dalm_model ON engine_dalm_statement.model_id = engine_dalm_model.id
	WHERE engine_dalm_model.type = 4 AND engine_dalm_model.`name` <> 'extra';
DELETE FROM engine_dalm_model WHERE engine_dalm_model.type = 4 AND engine_dalm_model.`name` <> 'extra';
ALTER IGNORE TABLE `plugin_products_product` DROP FOREIGN KEY `fk_plugin_products_product_plugin_products_category1`;
ALTER IGNORE TABLE `plugin_products_product` DROP COLUMN `category`;
ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `category_id` INT;

-- IBOC-218
insert into `ppages` set `name_tag` = 'registration-successful.html', `title` = 'Registration Successful', `content` = '', `banner_photo` = null, `seo_keywords` = '', `seo_description` = '', `footer` = '', `date_entered` = NOW(), `last_modified` = NOW(), `created_by` = 1, `modified_by` = null, `publish` = '1', `deleted` = '0', `include_sitemap` = '1', `layout_id` = '7', `category_id` = '1';
insert into `ppages` set `name_tag` = 'reset-password.html', `title` = 'Reset Password', `content` = '', `banner_photo` = null, `seo_keywords` = '', `seo_description` = '', `footer` = '', `date_entered` = NOW(), `last_modified` = NOW(), `created_by` = 1, `modified_by` = null, `publish` = '1', `deleted` = '0', `include_sitemap` = '1', `layout_id` = '7', `category_id` = '1';
insert into `ppages` set `name_tag` = 'reset-password-sent.html', `title` = 'Reset Password Sent', `content` = '', `banner_photo` = null, `seo_keywords` = '', `seo_description` = '', `footer` = '', `date_entered` = NOW(), `last_modified` = NOW(), `created_by` = 1, `modified_by` = null, `publish` = '1', `deleted` = '0', `include_sitemap` = '1', `layout_id` = '7', `category_id` = '1';
insert into `ppages` set `name_tag` = 'new-password.html', `title` = 'New Password', `content` = '', `banner_photo` = null, `seo_keywords` = '', `seo_description` = '', `footer` = '', `date_entered` = NOW(), `last_modified` = NOW(), `created_by` = 1, `modified_by` = null, `publish` = '1', `deleted` = '0', `include_sitemap` = '1', `layout_id` = '7', `category_id` = '1';

INSERT INTO `page_redirects` (`from`, `to`, `type`) VALUES ('/login.html', 'customer-login.html', 302);

-- IBCMS-711
DELETE FROM `user_group` WHERE `user_group` IN ('Abbott Pharmaceuticals', 'Technopath', 'aaa');
