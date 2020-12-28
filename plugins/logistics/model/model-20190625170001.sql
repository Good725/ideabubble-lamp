/*
ts:2019-06-25 17:00:01
*/
INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('logistics', 'Logistics', '1', '0');

CREATE TABLE `plugin_logistics_transfers` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `title`          VARCHAR(255) NOT NULL,
  `type`           ENUM('Arrival', 'Departure') NOT NULL,
  `passenger_id`   INT(11) NULL,
  `driver_id`      INT(11) NULL,
  `pickup_id`      INT(11) NULL,
  `dropoff_id`     INT(11) NULL,
  `scheduled_date` TIMESTAMP NULL,
  `date_created`   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified`  TIMESTAMP NULL,
  `created_by`     INT(11) NULL DEFAULT NULL ,
  `modified_by`    INT(11) NULL,
  `publish`        INT(1) NOT NULL DEFAULT 1,
  `deleted`        INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
;
