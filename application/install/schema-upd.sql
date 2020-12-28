drop table users_req;

CREATE TABLE `users_req` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `e-mail` varchar(45) DEFAULT NULL,
  `forename` varchar(45) DEFAULT NULL,
  `surname` varchar(45) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  `mob` varchar(15) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'N',
  `users_id` int(11) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(100) DEFAULT NULL,
  `fax` varchar(15) DEFAULT NULL,
  `company` varchar(45) DEFAULT NULL,
  `department` varchar(45) DEFAULT NULL,
  `role` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_req_users` (`users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Update Table `plugins`:
-- Add two new Fields: requires_media and media_folder
-- -----------------------------------------------------
ALTER TABLE plugins
ADD COLUMN requires_media TINYINT NOT NULL DEFAULT 0 AFTER enabled,
ADD COLUMN media_folder VARCHAR(50) NULL DEFAULT NULL AFTER requires_media;