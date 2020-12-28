/*
ts:2020-01-21 12:26:00
*/

CREATE TABLE `plugin_todos_todos2_categories`
(
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(200),
    `published`     TINYINT(1)   NULL DEFAULT 1,
    `deleted`       TINYINT(1)   NULL DEFAULT 0,
    `date_created`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modified` TIMESTAMP    NULL,
    `created_by`    INT          NULL,
    `modified_by`   INT          NULL,
    PRIMARY KEY (`id`)
);

ALTER TABLE `plugin_todos_todos2`
    ADD COLUMN `category_id` INT(11) NULL AFTER `datetime`;
