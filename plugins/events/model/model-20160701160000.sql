/*
ts:2016-06-17 16:00:00
*/

CREATE TABLE IF NOT EXISTS `plugin_events_topics` (
  `id`           INT         NOT NULL AUTO_INCREMENT ,
  `name`         VARCHAR(45) NOT NULL ,
  `created_by`   INT(11)     NULL ,
  `modified_by`  INT(11)     NULL ,
  `date_created` TIMESTAMP   NULL     DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP   NULL ,
  `publish`      INT(1)      NOT NULL DEFAULT 1 ,
  `deleted`      INT(1)      NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`) ,
UNIQUE INDEX `name_UNIQUE` (`name` ASC) );


SELECT `id` INTO @ut165_super_user_id FROM `engine_users` WHERE `email` = 'super@ideabubble.ie';

INSERT IGNORE INTO `plugin_events_topics` (`name`, `created_by`, `modified_by`, `date_created`, `date_modified`) VALUES
('Auto, Boat & Air',            @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Business & Professional',     @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Charity & Causes',            @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Community & Culture',         @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Family & Education',          @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Fashion & Beauty',            @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Film, Media & Entertainment', @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Food & Drink',                @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Government & Politics',       @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Health & Wellness',           @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Hobbies & Special Interests', @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Home & Lifestyle',            @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Music',                       @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Other',                       @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Performing & Visual Arts',    @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Religion & Spirituality',     @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Science & Technology',        @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Seasonal',                    @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Sports & Fitness',            @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Travel & Outdoor',            @ut165_super_user_id, @ut165_super_user_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

ALTER IGNORE TABLE `plugin_events_events` ADD COLUMN `topic_id` INT(11) NULL  AFTER `category_id` ;


ALTER IGNORE TABLE `plugin_events_events` ADD COLUMN `ticket_note` VARCHAR(1023) NULL DEFAULT NULL  AFTER `email_note` ;
