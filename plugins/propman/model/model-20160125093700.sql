/*
ts:2016-01-25 09:38:00
*/

ALTER TABLE plugin_propman_ratecards_weeks ADD COLUMN `arrival` ENUM ('Any', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
