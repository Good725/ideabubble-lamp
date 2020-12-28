/*
ts:2019-02-01 16:47:00
*/

ALTER TABLE `plugin_ib_educate_bookings_has_applications` MODIFY COLUMN `interview_status`  enum('Scheduled','No Follow Up','Interviewed','Accepted','Rejected','Cancelled','Not Scheduled','On Hold');

