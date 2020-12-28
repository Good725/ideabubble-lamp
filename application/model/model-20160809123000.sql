/*
ts:2016-08-09 12:30:00
*/


ALTER IGNORE TABLE `engine_plugins` ADD COLUMN `flaticon` VARCHAR(255) NOT NULL DEFAULT 0  AFTER `icon` ;

UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'address-book' WHERE `name` = 'contacts2';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'course-book'  WHERE `name` = 'courses';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'dashboard'    WHERE `name` = 'dashboards';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'calendar'     WHERE `name` = 'events';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'folder'       WHERE `name` = 'files';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'files-text'   WHERE `name` = 'invoices';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'media'        WHERE `name` = 'media';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'speech'       WHERE `name` = 'messaging';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'menu'         WHERE `name` = 'menus';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'newspaper'    WHERE `name` = 'news';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'speech'       WHERE `name` = 'notifications';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'document'     WHERE `name` = 'pages';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'report'       WHERE `name` = 'reports';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'cog'          WHERE `name` = 'settings';


UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'receipt'      WHERE `name` = 'bookings';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'coins'        WHERE `name` = 'currency';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'folder'       WHERE `name` = 'documents';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'form'         WHERE `name` = 'formbuilder';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'layout'       WHERE `name` = 'gallery';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'homework'     WHERE `name` = 'homework';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'invoice'      WHERE `name` = 'events/invoices';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'broken-link'  WHERE `name` = 'linkchecker';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'location'     WHERE `name` = 'locations';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'padnote'      WHERE `name` = 'events/orders';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'panels'       WHERE `name` = 'panels';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'barcode'      WHERE `name` = 'products';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'project'      WHERE `name` = 'projects';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'houses'       WHERE `name` = 'propman';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'chart'        WHERE `name` = 'surveys';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'testimonial'  WHERE `name` = 'testimonials';

UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'business-card' WHERE `name` = 'contacts2';
UPDATE IGNORE  `engine_plugins` SET `flaticon` = 'next'          WHERE `name` = 'keyboardshortcut';

