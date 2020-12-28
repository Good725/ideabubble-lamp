/*
ts:2017-04-21 13:00:00
*/

-- Record who has read each message
CREATE  TABLE `plugin_messaging_read_by` (
  `message_id` INT         NOT NULL ,
  `user_id`    VARCHAR(45) NOT NULL ,
  `date`       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`message_id`, `user_id`)
);
