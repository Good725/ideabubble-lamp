/*
ts:2016-12-15 15:00:00
*/

-- Move the "Facebook" menu item to the "About Us" column
UPDATE `plugin_menus` `m1`
JOIN `plugin_menus` `m2` ON (`m2`.`title` = 'About Us' AND `m2`.`category` = 'footer')
SET
  `m1`.`parent_id`= `m2`.`id`,
  `m1`.`date_modified` = CURRENT_TIMESTAMP,
  `m1`.`modified_by` = (SELECT `id`FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
WHERE `m1`.`title`='Facebook' AND `m1`.`category` = 'footer';

-- Change the "Join Us" column to a column for "PSI House"
UPDATE `plugin_menus`
SET
  `title`='PSI House',
  `link_url`='http://www.thepsi.ie/gns/home.aspx',
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by` = (SELECT `id`FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
WHERE `title`='Join Us' AND `category` = 'footer';

-- Add new menu items
SELECT `id` INTO @hpg_188_psi_menu_id FROM `plugin_menus` WHERE `title` = 'PSI House' AND `category` = 'footer' LIMIT 1;
SELECT `id` INTO @hpg_188_superuser_id FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1;

INSERT INTO `plugin_menus` (`category`, `title`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) VALUES
('footer',  'Fenian Street',          @hpg_188_psi_menu_id, '1', '1', '0',  CURRENT_TIMESTAMP,  CURRENT_TIMESTAMP,  @hpg_188_super_id,  @hpg_188_superuser_id),
('footer',  'Dublin 2',               @hpg_188_psi_menu_id, '2', '1', '0',  CURRENT_TIMESTAMP,  CURRENT_TIMESTAMP,  @hpg_188_super_id,  @hpg_188_superuser_id),
('footer',  'Phone: +353 1 2184000',  @hpg_188_psi_menu_id, '3', '1', '0',  CURRENT_TIMESTAMP,  CURRENT_TIMESTAMP,  @hpg_188_super_id,  @hpg_188_superuser_id),
('footer',  'Fax: +353 1 2837678',    @hpg_188_psi_menu_id, '4', '1', '0',  CURRENT_TIMESTAMP,  CURRENT_TIMESTAMP,  @hpg_188_super_id,  @hpg_188_superuser_id),
('footer',  'Email: info@psi.ie',     @hpg_188_psi_menu_id, '5', '1', '0',  CURRENT_TIMESTAMP,  CURRENT_TIMESTAMP,  @hpg_188_super_id,  @hpg_188_superuser_id);
