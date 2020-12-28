/*
ts:2019-04-03 13:00:00
*/

CREATE TABLE `engine_settings_microsite_overwrites` (
  `id`           INT         NOT NULL AUTO_INCREMENT,
  `setting`      VARCHAR(64) NULL,
  `microsite_id` VARCHAR(64) NULL,
  `environment`  VARCHAR(64) NULL,
  `value`        LONGTEXT    NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `overwrite_UNIQUE` (`setting` ASC, `microsite_id` ASC, `environment` ASC));