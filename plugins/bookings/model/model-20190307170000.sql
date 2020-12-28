/*
ts:2019-03-07 17:00:00
*/

ALTER TABLE `plugin_ib_educate_bookings_has_applications`
  CHANGE COLUMN `interview_status`   `interview_status` ENUM('Pending', 'No show', 'Interviewed')                                  NULL DEFAULT NULL,
  ADD COLUMN    `application_status`                    ENUM('Pending', 'On hold', 'Accepted')                                     NULL DEFAULT NULL AFTER `student`,
  ADD COLUMN    `offer_status`                          ENUM('Pending', 'Offered', 'Waiting list', 'No offer')                     NULL DEFAULT NULL AFTER `interview_status`,
  ADD COLUMN    `registration_status`                   ENUM('Pending', 'Deposit paid', 'Awaiting docs', 'Registered', 'Deferred') NULL DEFAULT NULL AFTER `offer_status`,
  ADD COLUMN    `date_modified` TIMESTAMP NULL AFTER `registration_status`
;

ALTER TABLE `plugin_ib_educate_bookings_has_applications` DROP COLUMN `date_modified`;

CREATE TABLE `plugin_ib_educate_bookings_has_applications_history` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `booking_id`  INT(11)      NULL,
  `column`      VARCHAR(50)  NULL,
  `value`       VARCHAR(255) NULL,
  `modified_by` INT(11)      NULL,
  `timestamp`   TIMESTAMP    NULL,
  `deleted`     INT(1)       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);

