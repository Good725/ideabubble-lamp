/*
ts:2016-04-18 15:20:00
*/
INSERT IGNORE INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('events', 'Events', '1', '0');


CREATE TABLE IF NOT EXISTS `plugin_events_events` (
  `id`             INT          UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name`           VARCHAR(255) NOT NULL ,
  `logo_id`        INT(11)      NULL ,
  `venue_id`       INT(11)      NULL ,
  `category_id`    INT(11)      NULL ,
  `start_datetime` TIMESTAMP    NULL ,
  `end_datetime`   TIMESTAMP    NULL ,
  `description`    TEXT         NULL ,
  `organizer`      VARCHAR(255) NULL ,
  `tickets`        INT(11)      NULL ,
  `publish`        INT(1)       NOT NULL DEFAULT 1 ,
  `deleted`        INT(1)       NOT NULL DEFAULT 0 ,
  `date_created`   TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified`  TIMESTAMP    NULL ,
  `created_by`     INT(11)      NULL ,
  `modified_by`    INT(11)      NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) );

CREATE TABLE IF NOT EXISTS `plugin_events_venues` (
  `id`             INT          NOT NULL ,
  `name`           VARCHAR(255) NOT NULL ,
  `street`         VARCHAR(255) NULL ,
  `country_id`     INT(11)      NULL ,
  `county_id`      INT(11)      NULL ,
  `city`           VARCHAR(255) NULL ,
  `eircode`        VARCHAR(8)   NULL ,
  `telephone`      VARCHAR(255) NULL ,
  `website`        VARCHAR(255) NULL ,
  `facebook_url`   VARCHAR(255) NULL ,
  `twitter_url`    VARCHAR(255) NULL ,
  `publish`        INT(1)       NOT NULL DEFAULT 1 ,
  `deleted`        INT(1)       NOT NULL DEFAULT 0 ,
  `date_created`   TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified`  TIMESTAMP    NULL ,
  `created_by`     INT(11)      NULL ,
  `modified_by`    INT(11)      NULL ,
  PRIMARY KEY (`id`) );

ALTER IGNORE TABLE `plugin_events_events`
ADD    COLUMN `date`                        DATE NULL DEFAULT NULL  AFTER category_id,
CHANGE COLUMN `start_datetime` `start_time` TIME NULL DEFAULT NULL ,
CHANGE COLUMN `end_datetime`   `end_time`   TIME NULL DEFAULT NULL
;
