/*
ts:2017-02-16 15:33:00
*/


CREATE TABLE if not EXISTS `engine_contextual_linking_references` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(250) NOT NULL,
  `link_column_name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),

  UNIQUE KEY `table_name` (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE  if not EXISTS `engine_contextual_linking_data` (
  `src_id` int(10) unsigned NOT NULL,
  `src_type` int(10) unsigned NOT NULL,
  `dst_id` int(10) unsigned NOT NULL,

  `dst_type` int(10) unsigned NOT NULL,
  UNIQUE KEY `src_id` (`src_id`,`src_type`,`dst_id`,`dst_type`),
  KEY `src_id_2` (`src_id`,`src_type`,`dst_type`),
  KEY `ref_id` (`dst_id`,`dst_type`),
  KEY `src_type` (`src_type`),
  KEY `dst_type` (`dst_type`),
  CONSTRAINT `engine_contextual_linking_data_ibfk_1` FOREIGN KEY (`src_type`) REFERENCES `engine_contextual_linking_references` (`id`),
  CONSTRAINT `engine_contextual_linking_data_ibfk_2` FOREIGN KEY (`dst_type`) REFERENCES `engine_contextual_linking_references` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
