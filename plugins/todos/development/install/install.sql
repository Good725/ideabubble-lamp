INSERT INTO `plugins` (`name`, `version`, `folder`, `menu`, `type`, `is_frontend`, `is_backend`, `enabled`)
VALUES
	( 'todos', 'development', 'todos', 'Todos', NULL, 0, 1, 1);


  -- ----------------------------
--  Table structure for `plugin_todos`
-- ----------------------------
CREATE  TABLE IF NOT EXISTS `plugin_todos`  (
  `todo_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `details` varchar(2000) COLLATE utf8_bin DEFAULT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) DEFAULT NULL,
  `status_id` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `priority_id` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `related_to_plugin` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `related_to_id` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `related_to_text` varchar(95) COLLATE utf8_bin NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `date_created` date DEFAULT NULL,
  `date_updated` date DEFAULT NULL,
  PRIMARY KEY (`todo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;


INSERT INTO `plugin_todos` (`todo_id`, `title`, `details`, `from_user_id`, `to_user_id`, `status_id`, `priority_id`, `due_date`, `related_to_plugin`, `related_to_id`)
VALUES
	(1, X'62757920636F666665', X'50726566657261626C65204C6F6662657267734C696C61', 32, NULL, 'Open', X'4D656469756D', NULL, NULL, NULL),
	(2, 'Prepare financial report', '', 32, NULL, 'Open', 'Low', NULL, NULL, NULL),
	(3, 'Support Mary with budget', '', 1, 32, 'Open', 'Low', NULL, NULL, NULL);

ALTER TABLE `plugin_todos` ADD COLUMN `related_to_text` varchar(95) NOT NULL AFTER `related_to_id`;
