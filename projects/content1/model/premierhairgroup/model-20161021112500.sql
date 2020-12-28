/*
ts:2016-10-21 12:25:00
*/

UPDATE IGNORE `plugin_panels`
SET `publish` = 0
WHERE `title` = 'GALLERY';

UPDATE IGNORE `plugin_panels`
SET `position` = 'footer'
WHERE `title` = 'LOCATION';

UPDATE IGNORE `plugin_panels`
SET `title` = 'REQUEST A PRICE LIST'
WHERE `title` = 'GET A BROCHURE';

UPDATE IGNORE `plugin_menus`
SET `title` = 'About Us'
WHERE `title` = 'About';

UPDATE IGNORE `plugin_menus`
SET `menu_order` = 1
WHERE `title` = 'About Us';

UPDATE IGNORE `plugin_menus`
SET `menu_order` = 2
WHERE `title` = 'Hair Loss';

UPDATE IGNORE `plugin_menus`
SET `menu_order` = 3
WHERE `title` = 'Our Services';

INSERT INTO `plugin_menus`
(`category`, `title`, `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`)
  VALUE
  ('main',
    'Consultation',
    (SELECT id FROM `plugin_pages_pages` WHERE `name_tag` = 'book-consultation' LIMIT 1),
    '',
    0,
    0,
    4,
    1,
    0,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
   1,
   1
  );

UPDATE IGNORE `plugin_menus`
SET `menu_order` = 5
WHERE `title` = 'Blog';
