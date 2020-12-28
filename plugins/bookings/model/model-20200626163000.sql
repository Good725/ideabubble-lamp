/*
ts:2020-06-26 16:30:00
*/

ALTER TABLE `plugin_ib_educate_bookings_has_delegates`
ADD COLUMN `cancelled` INT(1) NOT NULL DEFAULT 0 AFTER `contact_id`;

-- Add cancellation reason to booking schedules and booking delegates
ALTER TABLE `plugin_ib_educate_bookings_has_delegates`
ADD COLUMN `cancel_reason_code` VARCHAR(100) NOT NULL DEFAULT 0 AFTER `contact_id`;

ALTER TABLE `plugin_ib_educate_booking_has_schedules`
ADD COLUMN `cancel_reason_code` VARCHAR(100) NOT NULL DEFAULT 0 AFTER `booking_status`;


-- Lookup values aren't necessarily numeric
ALTER TABLE `engine_lookup_values` CHANGE COLUMN `value` `value` VARCHAR(255) NULL DEFAULT NULL ;

-- Add new lookup group
INSERT INTO `engine_lookup_fields` (`name`) VALUES ('Booking cancellation reason');

-- Add values to the group
INSERT INTO `engine_lookup_values` (`value`, `label`, `field_id`) VALUES
  ('BAD DEBTS',  'Bad debt write off',                          (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('COURSE CRE', 'Course cancelled - credit note',              (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('COURSE MOV', 'Course cancelled - move to future course',    (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('COURSE REF', 'Course cancelled - refund due',               (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('CREDIT',     'Credit to be left on account',                (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('DELEG CRED', 'Delegate cancelled - credit note',            (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('DELEG MOVE', 'Delegate cancelled - moved to future course', (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('DELEG REFU', 'Delegate cancelled - refund due',             (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('DISCOUNT',   'Discount applied',                            (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('PRICE',      'Change in value',                             (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('VATEXEMPT',  'Company VAT exempt',                          (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('DESC',       'Incorrect description',                       (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('DUP',        'Duplicate invoice/booking',                   (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('RATE',       'Charged non-member rate',                     (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1)),
  ('DELETE',     'Credit note, but no refund due',              (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1));

-- Make fields public
UPDATE `engine_lookup_values`
SET`public` = '1'
WHERE `field_id` = (SELECT `id` FROM `engine_lookup_fields` WHERE `name` = 'Booking cancellation reason' LIMIT 1);

-- Remove "0" default from columns
ALTER TABLE `plugin_ib_educate_bookings_has_delegates` CHANGE COLUMN `cancel_reason_code` `cancel_reason_code` VARCHAR(100) NULL;
ALTER TABLE `plugin_ib_educate_booking_has_schedules`  CHANGE COLUMN `cancel_reason_code` `cancel_reason_code` VARCHAR(100) NULL;

UPDATE `plugin_ib_educate_bookings_has_delegates` SET `cancel_reason_code` = NULL WHERE `cancel_reason_code` = '0';
UPDATE `plugin_ib_educate_booking_has_schedules`  SET `cancel_reason_code` = NULL WHERE `cancel_reason_code` = '0';

-- Add "booking" as a type of item whose activity can be tracked, if it has not already been added.
INSERT IGNORE INTO `engine_activities_item_types` (`stub`, `name`, `table_name`) VALUES ('booking', 'Booking', 'plugin_ib_educate_bookings');

-- Add "cancel delegate" as a trackable activity action.
INSERT IGNORE INTO `engine_activities_actions` (`stub`, `name`) VALUES ('cancel_delegate', 'Cancel delegate');

-- Track the date a delegate is cancelled
ALTER TABLE `plugin_ib_educate_bookings_has_delegates` ADD COLUMN `date_cancelled` TIMESTAMP NULL AFTER `cancelled`;
