/*
ts:2015-01-01 00:01:08
*/

INSERT INTO `project_role` (`role`, `description`)
VALUES ('Card Holder', 'Employees that require a business card.'), ('Manager', 'People managing business cards.');


INSERT IGNORE INTO `plugin_notifications_event` (`name`,`description`,`from`, `subject`) VALUES ('printing','Printers','','Regeneron Card Delivery');

-- --------------------------------------------------------------
-- REG-42 Notification for administrator when an order is placed.
-- --------------------------------------------------------------
INSERT IGNORE INTO `plugin_notifications_event` (`name`,`description`,`from`, `subject`) VALUES ('new_card_created','New Card Created','','Card Created');
