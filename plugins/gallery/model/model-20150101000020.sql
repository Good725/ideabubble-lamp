/*
ts:2015-01-01 00:00:20
*/
-- -----------------------------------------------------
-- Table `plugin_gallery_gallery`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_gallery_gallery` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `photo_name` VARCHAR(100) NULL ,
  `category` VARCHAR(100) NULL ,
  `title` TEXT NULL ,
  `order` SMALLINT NOT NULL ,
  `publish` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL ,
  `created_by` INT(10) NOT NULL ,
  `modified_by` INT(10) NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('gallery', 'Gallery', '1', '1', 'gallery');

UPDATE `plugins` SET icon = 'gallery.png' WHERE friendly_name = 'Gallery';
UPDATE `plugins` SET `plugins`.`order` = 10 WHERE friendly_name = 'Gallery';

-- -----------------------------------------------------
-- LP-161 - gallery add image
-- -----------------------------------------------------
INSERT IGNORE INTO `shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`) VALUES
('Gallery', 'gallery', '150', '150', 'fit', '1', '100', '100');

INSERT IGNORE INTO `feeds` (`name`, `date_created`, `date_modified`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
('Gallery', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0', 'gallery', 'Model_Gallery,get_category_images');
