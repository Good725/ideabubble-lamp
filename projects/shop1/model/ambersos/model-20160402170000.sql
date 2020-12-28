/*
ts:2016-04-02 17:00:00
*/
--
-- Table structure for table `plugin_panels`
--

CREATE TABLE IF NOT EXISTS `plugin_panels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `position` varchar(45) DEFAULT NULL,
  `order_no` int(3) unsigned DEFAULT '0',
  `type_id` int(10) unsigned DEFAULT NULL,
  `predefined_id` int(10) unsigned DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `text` text,
  `view` varchar(200) DEFAULT NULL,
  `link_id` int(10) unsigned DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `date_publish` datetime DEFAULT NULL,
  `date_remove` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `publish` tinyint(1) unsigned DEFAULT '1',
  `deleted` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
--  data for table `plugin_panels`
--

INSERT INTO `plugin_panels` ( `page_id`, `title`, `position`, `order_no`, `type_id`, `predefined_id`, `image`, `text`, `view`, `link_id`, `link_url`, `date_publish`, `date_remove`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
( NULL, 'Pain Relief', 'home_content', 0, 2, 0, 'pic1.jpg', '<p style="text-align: center;">Pain Relief</p>\n', '', 22, '', NULL, NULL, NOW(), NOW(), 1, 1, 1, 0),
( NULL, 'Unique Gifts', 'home_content', 0, 2, 0, 'pic2.jpg', '<p style="text-align: center;">Unique Gifts</p>\n', '', 0, '', NULL, NULL,NOW(), NOW(), 1, 1, 1, 0),
( NULL, 'Beauty Benefits', 'home_content', 0, 2, 0, 'pic3.jpg', '<p style="text-align: center;">Beauty Benefits</p>\n', '', 16, '', NULL, NULL, NOW(),NOW(), 1, 1, 1, 0);


-- --------------------------------------------------------

--
-- Table structure for table `plugin_menus`
--

CREATE TABLE IF NOT EXISTS `plugin_menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `link_tag` int(11) NOT NULL,
  `link_url` varchar(500) NOT NULL,
  `has_sub` tinyint(1) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `menu_order` int(11) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `date_modified` datetime NOT NULL,
  `date_entered` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `menus_target` varchar(20) NOT NULL DEFAULT '_top',
  `image_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
--  data for table `plugin_menus`
--

INSERT INTO `plugin_menus` ( `category`, `title`, `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`, `menus_target`, `image_id`) VALUES
( 'main', 'Home', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'main', 'Our Shop', 0, '', 0, 0, 2, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
('main', 'About Us', 0, '', 0, 0, 3, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
('main', 'Blog', 0, '', 0, 0, 4, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
('main', 'Contact Us', 0, '', 0, 0, 5, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
('footer', 'Gift Ideas', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'footer', 'Amber For Children', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'footer', 'Amber For Women', 0, '', 0, 0, 1, 1, 0,NOW(),NOW(), 1, 1, '_self', 0),
( 'footer', 'Amber For Men', 0, '', 0, 0, 1, 1, 0, NOW(),NOW(), 1, 1, '_self', 0),
( 'footer', 'Amber For Pets', 0, '', 0, 0, 1, 1, 0, NOW(),NOW(), 1, 1, '_self', 0),
( 'footer', 'Amber Sets', 0, '', 0, 8, 2, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'footer', 'Raw Amber', 0, '', 0, 8, 4, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'footer', 'Baltic Amber Gemstones', 0, '', 0, 8, 3, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'footer', 'Delivery', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'footer', 'Bespoke Jewellery', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'about us', 'Delivery', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'about us', 'Payment', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'about us', 'Return or Exchange', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'about us', 'Contact us', 0, '', 0, 0, 1, 1, 0, NOW(), NOW(), 1, 1, '_self', 0),
( 'about us', 'Terms & Conditions', 0, '', 0, 0, 1, 1, 0,NOW(), NOW(), 1, 1, '_self', 0),
( 'about us', 'Sitemap', 0, '', 0, 0, 1, 1, 0,NOW(), NOW(), 1, 1, '_self', 0);

--
-- Data table `settings`
--

 UPDATE `settings`
 SET `value_stage` = 'Foxford',`value_live` = 'Foxford',`value_dev` = 'Foxford',`value_test` = 'Foxford' WHERE `variable` = 'addres_line_1';
 UPDATE `settings`
 SET `value_stage` = 'Main Street',`value_live` = 'Main Street',`value_dev` = 'Main Street',`value_test` = 'Main Street' WHERE `variable` = 'addres_line_2';
 UPDATE `settings`
 SET `value_stage` = 'Co. Moyo',`value_live` = 'Co. Moyo',`value_dev` = 'Co. Moyo',`value_test` = 'Co. Moyo' WHERE `variable` = 'addres_line_3';
 UPDATE `settings`
 SET `value_stage` = '094-9257115',`value_live` = '094-9257115',`value_dev` = '094-9257115',`value_test` = '094-9257115' WHERE `variable` = 'telephone';
 UPDATE `settings`
 SET `value_stage` = 'info@ambersos.com',`value_live` = 'info@ambersos.com',`value_dev` = 'info@ambersos.com',`value_test` = 'info@ambersos.com' WHERE `variable` = 'email';
 UPDATE `settings`
 SET `value_stage` = 'www.facebook.com',`value_live` = 'www.facebook.com',`value_dev` = 'www.facebook.com',`value_test` = 'www.facebook.com' WHERE `variable` = 'facebook_url';
 UPDATE `settings`
 SET `value_stage` = 'www.twitter.com',`value_live` = 'www.twitter.com',`value_dev` = 'www.twitter.com',`value_test` = 'www.twitter.com' WHERE `variable` = 'twitter_url';

--
-- Data table `banners`
--

INSERT INTO `plugin_custom_scroller_sequences` ( `title`, `animation_type`, `order_type`, `first_item`, `rotating_speed`, `timeout`, `pagination`, `controls`, `plugin`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
( 'ambersos', 'fade', 'ascending', 1, 17000, 18000, 0, 0, 13, NOW(), NOW(), 1, 1, 1, 0);

INSERT INTO `plugin_custom_scroller_sequence_items` ( `sequence_id`, `image`, `image_location`, `order_no`, `title`, `label`, `html`, `link_type`, `link_url`, `link_target`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
( 1, 'banner-pic.jpg', 'banners', 1, 'banner pic 1', '', '', 'none', '#', 1, NOW(), NOW(), 1, 1, 1, 0);

UPDATE `plugin_pages_pages`
SET `banner_photo` = '3|9|banners|1'  WHERE `name_tag` = 'home.html';
