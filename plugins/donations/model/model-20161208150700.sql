/*
ts:2016-12-08 15:07:00
*/

ALTER TABLE `plugin_donations_donations` MODIFY COLUMN `status`  enum('Processing','Confirmed','Completed','Rejected','Offline');

