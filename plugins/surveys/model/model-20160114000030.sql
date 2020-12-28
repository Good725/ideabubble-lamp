/*
ts:2016-01-14 18:54:45
*/
DROP TABLE IF EXISTS `plugin_survey_result`;

CREATE TABLE `plugin_survey_result` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) DEFAULT NULL,
  `starttime` int(11) DEFAULT NULL,
  `endtime` bigint(20) DEFAULT NULL,
  `user_ip` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `plugin_survey_answer_result`;

CREATE TABLE `plugin_survey_answer_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) DEFAULT NULL,
  `survey_result_id` int(11) DEFAULT NULL,
  `answer_id` int(11) DEFAULT NULL,
  `question_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;