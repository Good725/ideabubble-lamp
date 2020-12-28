/*
ts:2016-02-04 10:55:00
*/

ALTER IGNORE TABLE `plugin_propman_properties` ADD COLUMN `rooms_ensuite` INT(11) UNSIGNED NULL AFTER `beds_bunks`;
ALTER IGNORE TABLE `plugin_propman_properties` ADD COLUMN `rooms_bathrooms` INT(11) UNSIGNED NULL AFTER `rooms_ensuite`;