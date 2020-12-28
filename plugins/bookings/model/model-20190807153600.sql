/*
ts:2019-08-07 15:36:00
*/
ALTER TABLE `plugin_ib_educate_bookings_has_applications`
    CHANGE COLUMN `application_status` `application_status` ENUM ('Enquiry', 'Pending', 'On hold', 'Accepted') NULL DEFAULT NULL;
