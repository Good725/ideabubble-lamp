/*
ts:2020-02-18 07:24:00
*/


ALTER TABLE `plugin_courses_schedules`
    ADD COLUMN `allow_credit_card` TINYINT(1) NULL DEFAULT 1 AFTER `allow_purchase_order`;