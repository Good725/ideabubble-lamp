/*
ts:2016-09-01 13:00:00
*/

UPDATE IGNORE `engine_plugins` SET `flaticon` = 'id-card'        WHERE `name` = 'cardbuilder';
UPDATE IGNORE `engine_plugins` SET `flaticon` = 'car'            WHERE `name` = 'cars';
UPDATE IGNORE `engine_plugins` SET `flaticon` = 'distribution'   WHERE `name` = 'articles';
UPDATE IGNORE `engine_plugins` SET `flaticon` = 'business-card'  WHERE `name` IN ('contacts', 'contacts2', 'contacts3');
UPDATE IGNORE `engine_plugins` SET `flaticon` = 'info'           WHERE `name` = 'extra';
UPDATE IGNORE `engine_plugins` SET `flaticon` = 'family'         WHERE `name` = 'families';
UPDATE IGNORE `engine_plugins` SET `flaticon` = 'report'         WHERE `name` IN ('report', 'reports2');
UPDATE IGNORE `engine_plugins` SET `flaticon` = 'todo'           WHERE `name` = 'todos';
UPDATE IGNORE `engine_plugins` SET `flaticon` = 'accounting'     WHERE `name` = 'transactions';

UPDATE IGNORE `engine_plugins` SET `show_on_dashboard` = '1'     WHERE `name` IN ('cars', 'families', 'locations', 'todos');
