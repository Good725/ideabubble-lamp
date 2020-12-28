/*
ts:2018-10-10 12:28:00
*/

ALTER TABLE `plugin_events_checkout_details`
MODIFY COLUMN `ccName`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `owner_id`,
MODIFY COLUMN `address`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `ccName`,
MODIFY COLUMN `city`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `address`,
MODIFY COLUMN `county`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `city`,
MODIFY COLUMN `country_id`  int(100) NULL AFTER `county`,
MODIFY COLUMN `postcode`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `country_id`,
MODIFY COLUMN `telephone`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `postcode`,
MODIFY COLUMN `email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `telephone`,
MODIFY COLUMN `comments`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `email`,
MODIFY COLUMN `firstname`  varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `updated_by`,
MODIFY COLUMN `lastname`  varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `firstname`;

