/*
ts:2019-10-17 12:43:00
*/

CREATE TABLE `plugin_contacts3_tags`
(
    `id`      INT          NOT NULL AUTO_INCREMENT,
    `label`   VARCHAR(255) NULL,
    `name`    VARCHAR(255) NOT NULL,
    `publish` TINYINT(1)   NOT NULL DEFAULT 1,
    `delete`  TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name_UNIQUE` (`name` ASC)
);

CREATE TABLE `plugin_contacts3_contact_has_tags`
(
    `contact_id` INT NOT NULL,
    `tag_id`     INT NOT NULL,
    PRIMARY KEY (`contact_id`, `tag_id`)
);


INSERT INTO `plugin_contacts3_tags` (`label`, `name`)
VALUES ('Newsletter signup', 'newsletter_signup');
INSERT INTO `plugin_contacts3_tags` (`label`, `name`)
VALUES ('Contact us enquiry ', 'contact_us_enquiry');
INSERT INTO `plugin_contacts3_tags` (`label`, `name`)
VALUES ('Other form registration', 'other_form_registration');
