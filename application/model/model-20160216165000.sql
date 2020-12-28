/*
ts:2016-01-16 16:50:00
*/

UPDATE IGNORE `plugins` SET `icon` = 'accounts'      WHERE `name` = 'accounts';
UPDATE IGNORE `plugins` SET `icon` = 'articles'      WHERE `name` = 'articles';
UPDATE IGNORE `plugins` SET `icon` = 'bookings'      WHERE `name` IN ('bookings', 'todos');
UPDATE IGNORE `plugins` SET `icon` = 'claims'        WHERE `name` = 'claims';
UPDATE IGNORE `plugins` SET `icon` = 'contacts'      WHERE `name` IN ('contacts', 'contacts2', 'contacts3');
UPDATE IGNORE `plugins` SET `icon` = 'courses'       WHERE `name` = 'courses';
UPDATE IGNORE `plugins` SET `icon` = 'EDocuments'    WHERE `name` = 'technopath/list_lots';
UPDATE IGNORE `plugins` SET `icon` = 'extra'         WHERE `name` = 'extra';
UPDATE IGNORE `plugins` SET `icon` = 'files'         WHERE `name` = 'files';
UPDATE IGNORE `plugins` SET `icon` = 'gallery'       WHERE `name` = 'gallery';
UPDATE IGNORE `plugins` SET `icon` = 'locations'     WHERE `name` = 'locations';
UPDATE IGNORE `plugins` SET `icon` = 'media'         WHERE `name` = 'media';
UPDATE IGNORE `plugins` SET `icon` = 'menus'         WHERE `name` = 'menus';
UPDATE IGNORE `plugins` SET `icon` = 'msds_files'    WHERE `name` = 'technopath/list_msds';
UPDATE IGNORE `plugins` SET `icon` = 'news'          WHERE `name` = 'news';
UPDATE IGNORE `plugins` SET `icon` = 'notifications' WHERE `name` IN ('keyboardshortcut', 'messaging', 'notifications');
UPDATE IGNORE `plugins` SET `icon` = 'options'       WHERE `name` = 'insuranceoptions';
UPDATE IGNORE `plugins` SET `icon` = 'panels'        WHERE `name` IN ('dashboards', 'formbuilder', 'panels', 'surveys');
UPDATE IGNORE `plugins` SET `icon` = 'pages'         WHERE `name` = 'pages';
UPDATE IGNORE `plugins` SET `icon` = 'policy'        WHERE `name` = 'insurance';
UPDATE IGNORE `plugins` SET `icon` = 'products'      WHERE `name` = 'products';
UPDATE IGNORE `plugins` SET `icon` = 'projects_2'    WHERE `name` = 'projects';
UPDATE IGNORE `plugins` SET `icon` = 'properties'    WHERE `name` = 'propman';
UPDATE IGNORE `plugins` SET `icon` = 'reagent_info'  WHERE `name` = 'technopath/select_reagent_lot';
UPDATE IGNORE `plugins` SET `icon` = 'reports'       WHERE `name` IN ('reports', 'reports2');
UPDATE IGNORE `plugins` SET `icon` = 'settings_3'    WHERE `name` = 'settings';
UPDATE IGNORE `plugins` SET `icon` = 'testimonials'  WHERE `name` = 'testimonials';
