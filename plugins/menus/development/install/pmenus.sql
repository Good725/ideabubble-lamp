/*
 Navicat MySQL Data Transfer

 Source Server         : Test Server
 Source Server Version : 50149
 Source Host           : 192.168.2.14
 Source Database       : wpp_test_alpha

 Target Server Version : 50149
 File Encoding         : utf-8

 Date: 07/27/2012 09:50:57 AM
*/

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `pmenus`
-- ----------------------------
DROP TABLE IF EXISTS `pmenus`;
CREATE TABLE `pmenus` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

