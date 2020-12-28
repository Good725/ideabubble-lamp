/*
ts:2017-11-17 18:30:00
*/

ALTER TABLE `engine_site_templates`
ADD COLUMN `header` BLOB NULL  AFTER `type` ,
ADD COLUMN `footer` BLOB NULL  AFTER `header` ;

ALTER TABLE `plugin_pages_layouts`
ADD COLUMN `template_id` INT(11) NULL  AFTER `layout` ;

ALTER TABLE `engine_site_themes`
ADD COLUMN `styles` BLOB NULL AFTER `stub`;

CREATE  TABLE `engine_site_theme_variables` (
  `id`            INT          NOT NULL ,
  `variable`      VARCHAR(100) NOT NULL ,
  `name`          VARCHAR(100) NULL ,
  `default`       VARCHAR(100) NULL ,
  `description`   VARCHAR(255) NULL ,
  `publish`       INT(1)       NOT NULL DEFAULT 1 ,
  `deleted`       INT(1)       NOT NULL DEFAULT 0 ,
  `created_by`    INT(1)       NULL ,
  `modified_by`   INT(1)       NULL ,
  `date_created`  TIMESTAMP    NULL     DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP    NULL ,
  PRIMARY KEY (`id`)
);

CREATE TABLE `engine_site_theme_has_variables` (
  `theme_id`    INT(11)      NOT NULL ,
  `variable_id` INT(11)      NOT NULL ,
  `value`       VARCHAR(100) NULL ,
  PRIMARY KEY (`theme_id`, `variable_id`)
);

ALTER TABLE `engine_site_theme_variables` CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `engine_site_theme_variables`
(`variable`,           `name`,                `default`, `description`) VALUES
('base_color_1',       'Base colour 1',       '#FF8F00', 'Main colour of the theme'),
('base_color_2',       'Base colour 2',       '#4BAF4F', 'Second-most prominent colour in the theme'),
('link_color',         'Link colour',         '#0000EE',  null),
('link_visited_color', 'Visited link Colour', '#551A8B',  null),
('link_hover_color',   'Hover link Colour',   '#0000EE',  null)
;

ALTER TABLE `engine_site_templates`
ADD COLUMN `styles` BLOB NULL  AFTER `header` ;