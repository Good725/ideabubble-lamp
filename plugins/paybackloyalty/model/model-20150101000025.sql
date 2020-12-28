/*
ts:2015-01-01 00:00:25
*/
insert IGNORE into `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUE ( 'paybackloyalty' , 'Payback Loyalty' , '0' , '0' );

insert IGNORE into `plugins_per_role` select `plugins`.`id`, `project_role`.`id`, 1 from `plugins` , `project_role` WHERE `plugins`.`name` = 'paybackloyalty' AND `project_role`. `role` != 'External User';

insert IGNORE into `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
              values (
                     'ws_url',
                     'WS URL',
                     '',
                     '',
                     '',
                     '',
                     '',
                     'both',
                     '',
                     'text',
                     'Payback Loyalty',
                     '0',
                     '') , ( 'partner_id' , 'Partner ID' , '' , '' , '' , '' , '' , 'both' , '' , 'text' , 'Payback Loyalty' , '0' , '' ) , ( 'request_id' , 'Request ID' , '' , '' , '' , '' , '' , 'both' , '' , 'text' , 'Payback Loyalty' , '0' , '' ) , ( 'store_id' , 'Store ID' , '' , '' , '' , '' , '' , 'both' , '' , 'text' , 'Payback Loyalty' , '0' , '' ) , ( 'user_id' , 'User ID' , '' , '' , '' , '' , '' , 'both' , '' , 'text' , 'Payback Loyalty' , '0' , '' );

-- GBS-234 Added new notification for admin success notice - removed as not all project require this.
-- INSERT INTO `plugin_notifications_event` VALUES ('6', 'successful-payback-new-member-admin', 'New Member', 'info@garretts.ie', 'Rewards Club - Registration confirmation (admin notice)',NULL,NULL);

UPDATE `plugins` SET icon = 'paybackLoyalty.png' WHERE friendly_name = 'Payback Loyalty';
UPDATE `plugins` SET `plugins`.`order` = 14 WHERE friendly_name = 'Payback Loyalty';
