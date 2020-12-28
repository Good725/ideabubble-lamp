/*
ts:2016-12-08 16:20:00
*/
ALTER TABLE `plugin_custom_scroller_sequence_items` ADD COLUMN `overlay_position` ENUM('left', 'right', 'center') NULL DEFAULT 'center'  AFTER `link_target` ;
