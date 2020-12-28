/*
ts:2015-01-01 00:00:01
*/
-- -----------------------------------------------------
-- Table `ppages_categorys`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `ppages_categorys` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_categorys_pages1` (`id` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `ppages_layouts`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `ppages_layouts` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `layout` VARCHAR(25) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_layouts_pages` (`id` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `ppages`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `ppages` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name_tag` VARCHAR(255) NULL DEFAULT NULL ,
  `title` VARCHAR(255) NULL DEFAULT NULL ,
  `content` TEXT NULL DEFAULT NULL ,
  `banner_photo` VARCHAR(500) NULL DEFAULT NULL ,
  `seo_keywords` VARCHAR(500) NULL DEFAULT NULL ,
  `seo_description` VARCHAR(500) NULL DEFAULT NULL ,
  `footer` VARCHAR(500) NULL DEFAULT NULL ,
  `date_entered` DATETIME NULL DEFAULT NULL ,
  `last_modified` DATETIME NULL DEFAULT NULL ,
  `created_by` INT(11) NULL DEFAULT NULL ,
  `modified_by` INT(11) NULL DEFAULT NULL ,
  `publish` TINYINT(1) NOT NULL DEFAULT '0' ,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0' ,
  `include_sitemap` TINYINT(1) NOT NULL DEFAULT '0' ,
  `layout_id` INT(10) UNSIGNED NOT NULL ,
  `category_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ppages_ppages_layouts_idx` (`layout_id` ASC) ,
  INDEX `fk_ppages_ppages_categorys1_idx` (`category_id` ASC) ,
  CONSTRAINT `fk_ppages_ppages_categorys1`
  FOREIGN KEY (`category_id` )
  REFERENCES `ppages_categorys` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ppages_ppages_layouts`
  FOREIGN KEY (`layout_id` )
  REFERENCES `ppages_layouts` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

CREATE OR REPLACE VIEW `view_ppages` AS select `ppages`.`id` AS `id`,`ppages`.`name_tag` AS `name_tag`,`ppages`.`title` AS `title`,`ppages`.`content` AS `content`,`ppages`.`banner_photo` AS `banner_photo`,`ppages`.`category_id` AS `category_id`,`ppages`.`layout_id` AS `layout_id`,`ppages`.`seo_keywords` AS `seo_keywords`,`ppages`.`seo_description` AS `seo_description`,`ppages`.`date_entered` AS `date_entered`,`ppages`.`footer` AS `footer`,`ppages`.`last_modified` AS `last_modified`,`ppages`.`modified_by` AS `modified_by`,`ppages`.`created_by` AS `created_by`,`ppages`.`publish` AS `publish`,`ppages`.`deleted` AS `deleted`,`ppages_categorys`.`category` AS `category`,`ppages_layouts`.`layout` AS `layout`,`ppages`.`include_sitemap` AS `include_sitemap` from ((`ppages` left join `ppages_layouts` on((`ppages`.`layout_id` = `ppages_layouts`.`id`))) left join `ppages_categorys` on((`ppages`.`category_id` = `ppages_categorys`.`id`)));

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('pages', 'Pages', '1', '0', NULL);



-- -----------------------------------------------------
-- add system page categories, layouts and system pages that are required
-- -----------------------------------------------------

INSERT IGNORE INTO `ppages_categorys` VALUES
    ('1', 'Default');

INSERT IGNORE INTO `ppages_layouts` VALUES
    ('1', 'home'),
    ('2', 'content'),
    ('3', 'newslisting'),
    ('4', 'newsdetail'),
    ('5', 'contactform');

INSERT IGNORE INTO `ppages` ( `id`, `name_tag`, `title`, `content`, `banner_photo`, `seo_keywords`,`seo_description`, `footer`, `date_entered`, `last_modified`,`created_by`, `modified_by`, `publish`,`deleted`,`include_sitemap`,`layout_id`,`category_id`) VALUES
    (null, 'sitemap.html', 'Site Map Page', '<h1>Site Map</h1>\n', null, '', '', NULL, null, null, '1', '1', '1', '0', '1', '1', '1'),
    (null, 'error404.html', '404 Page', '<h1>404 - Page not found</h1>\n', null, '', '', NULL, null, null, '1', '1', '1', '0', '1', '1', '1');

-- ----------------------------
-- TOS-137 - Add Default ans System - Page Categories
-- ----------------------------
INSERT IGNORE INTO ppages_categorys (id, category) values(1, 'Default') ON DUPLICATE KEY UPDATE category = 'Default';
INSERT IGNORE INTO ppages_categorys (id, category) values(2, 'System') ON DUPLICATE KEY UPDATE category = 'System';

UPDATE `plugins` SET icon = 'pages.png' WHERE friendly_name = 'Pages';
UPDATE `plugins` SET `plugins`.`order` = 2 WHERE friendly_name = 'Pages';

-- ----------------------------
-- WPPROD-340 - Add Cache Settings - Page Categories
-- ----------------------------
INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
	('enable_caching', 'Enable Caching', '', '', '', '', 'false', 'site', 'Tick to enable caching on a public website', 'checkbox', 'General Settings', '1', '');

-- ----------------------------
-- WPPROD-409 - New Google Map Banner Option
-- ----------------------------
CREATE TABLE IF NOT EXISTS `plugin_pages_maps` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `html` BLOB NOT NULL ,
  `created_by` INT(11) NULL ,
  `modified_by` INT(11) NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` INT(1) NOT NULL DEFAULT '1' ,
  `deleted` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

-- --------------------------------------------------------------
-- IBCMS-252 Members Registration, Approval & Management (front end part)
-- --------------------------------------------------------------
  INSERT IGNORE INTO `ppages` (`id`, `name_tag`, `title`, `content`, `banner_photo`, `seo_keywords`, `seo_description`, `footer`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES (NULL, 'register-account.html', 'Register Account',
'<h2>Register Account</h2>
<form action="" class="formrt" id="account_registration_form" method="post">
<ul>
	<li><label for="register_name">Name</label> <input id="register_name" name="name" type="text" /> <label class="accessible-hide" for="register_surname">Last name</label> <input id="register_surname" name="surname" style="width: 137px;" type="text" /></li>
	<li><label for="register_email1">Email</label> <input class="validate[required]" id="register_email1" name="email1" type="text" /> <label for="register_email2">@</label> <input class="validate[required]" id="register_email2" name="email2" placeholder="regeneron.com" style="width: 181px;" type="text" /></li>
	<li><label for="register_phone">Phone</label> <input id="register_phone" name="phone" type="text" /></li>
	<li><label for="register_mobile">Mobile</label> <input id="register_mobile" name="mobile" type="text" /></li>
	<li><label for="register_address">Address</label> <input id="register_address" name="address" type="text" /></li>
	<li><label for="register_address_2">Address 2</label> <input id="register_address_2" name="address_2" type="text" /></li>
	<li><label for="register_address_3">Address 3</label> <input id="register_address_3" name="address_3" type="text" /></li>
	<li><label for="register_country">Country</label> <input id="register_country" name="country" type="text" /></li>
	<li><label for="register_county">County</label> <input id="register_county" name="county" type="text" /></li>
	<li><label for="register_company">Company</label> <input id="register_company" name="company" type="text" /></li>
	<li><label for="register_password">Create Password</label> <input class="validate[required,minSize[8]]" id="register_password" name="password" type="password" /></li>
	<li><label for="register_password2">Reenter Password</label> <input class="validate[required,minSize[8]]" id="register_password2" name="password2" type="password" /></li>
	<li><button class="primary_button" type="submit">Create Account</button></li>
</ul>
</form>
<p><strong>Please note you will receive an email to verify your account upon registration.</strong></p>',
'', '', '', '', '2014-11-26 14:16:47', '2015-02-13 16:53:22', '2', '2', '1', '0', '1', '1', '1');

INSERT IGNORE INTO `ppages` (`id`, `name_tag`, `title`, `content`, `banner_photo`, `seo_keywords`, `seo_description`, `footer`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES (NULL, 'login.html', 'Login',
'<h2>Log in</h2>
<p>Regeneron account <a href="/frequently-asked-questions.html">What&#39;s this?</a></p>
<p>Please enter your <strong>email address</strong>&nbsp;and <strong>password</strong> below&nbsp;to access the system, for example <strong>firstname.lastname@regeneron.com&nbsp;</strong>could be your email address.</p>
<form action="frontend/users/login" class="formrt" enctype="multipart/form-data" id="login_form" method="POST">
<ul>
	<li><label for="login_email">Email</label> <input class="validate[required,custom[email]]" id="login_email" name="email" placeholder="yourname@regeneron.com" type="text" /></li>
	<li><label for="login_password">Password</label> <input class="validate[required]" id="login_password" name="password" placeholder="Password" type="password" /></li>
	<li><input id="login_stay_signed_in" name="stay_signed_in" type="checkbox" /> <label for="login_stay_signed_in">Keep me signed in</label></li>
	<li><input class="primary_button" id="login_button" type="submit" value="Log in" /></li>
</ul>
</form>
<p>Don&#39;t have a Regeneron account? <a href="/register-account.html" title="Register Account">Create one now</a></p>',
'', '', '', '', '2014-11-26 14:16:36', '2015-01-29 17:48:09', '2', '2', '1', '0', '1', '1', '1');


ALTER IGNORE TABLE `ppages` ADD COLUMN `theme` VARCHAR(100) NULL AFTER `category_id` ;

CREATE OR REPLACE VIEW `view_ppages` AS
    SELECT
        `ppages`.`id` AS `id`,
        `ppages`.`name_tag` AS `name_tag`,
        `ppages`.`title` AS `title`,
        `ppages`.`content` AS `content`,
        `ppages`.`banner_photo` AS `banner_photo`,
        `ppages`.`category_id` AS `category_id`,
        `ppages`.`layout_id` AS `layout_id`,
        `ppages`.`theme` AS `theme`,
        `ppages`.`seo_keywords` AS `seo_keywords`,
        `ppages`.`seo_description` AS `seo_description`,
        `ppages`.`date_entered` AS `date_entered`,
        `ppages`.`footer` AS `footer`,
        `ppages`.`last_modified` AS `last_modified`,
        `ppages`.`modified_by` AS `modified_by`,
        `ppages`.`created_by` AS `created_by`,
        `ppages`.`publish` AS `publish`,
        `ppages`.`deleted` AS `deleted`,
        `ppages_categorys`.`category` AS `category`,
        `ppages_layouts`.`layout` AS `layout`,
        `ppages`.`include_sitemap` AS `include_sitemap`
    FROM
        ((`ppages`
        LEFT JOIN `ppages_layouts` ON ((`ppages`.`layout_id` = `ppages_layouts`.`id`)))
        LEFT JOIN `ppages_categorys` ON ((`ppages`.`category_id` = `ppages_categorys`.`id`)));

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`)
VALUES ('default_page_layout', 'Default Page Layout', 'content', 'content', 'content', 'content', 'content', 'both', 'The page layout to be used when the specified layout does not exist.', 'select', 'Website', 'Model_Page_Layout,get_as_options');
