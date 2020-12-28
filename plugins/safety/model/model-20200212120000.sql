/*
ts:2020-02-12 12:00:00
*/
CREATE TABLE `plugin_safety_prechecks` (
  `id`            INT        UNSIGNED NOT NULL AUTO_INCREMENT,
  `survey_id`     INT        NOT NULL,
  `assignee_id`   INT        NOT NULL,
  `status_id`     ENUM('Passed', 'Failed') NULL,
  `deleted`       TINYINT(1) NULL DEFAULT 0,
  `date_created`  TIMESTAMP  NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` TIMESTAMP  NULL,
  `date_deleted`  TIMESTAMP  NULL,
  `created_by`    INT NULL,
  `modified_by`   INT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `plugin_safety_precheck_has_survey_results` (
  `id`               INT NOT NULL AUTO_INCREMENT,
  `precheck_id`      INT NULL,
  `survey_result_id` INT NULL,
  PRIMARY KEY (`id`)
);
