/*
ts:2016-05-13 12:40:00
*/

INSERT IGNORE INTO `plugin_products_option_groups` (`group_label`, `group`, `deleted`) VALUES ('Colour', 'color (kma)', '0');

UPDATE IGNORE `plugin_products_option`
SET `group_id` = (SELECT `id` FROM `plugin_products_option_groups` WHERE `group` = 'color (kma)' AND `deleted` = 0 LIMIT 1),
    `date_modified` = CURRENT_TIMESTAMP,
    `modified_by` = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
WHERE `label` IN ('Ireland', 'Antrim', 'Armagh', 'Carlow', 'Cavan', 'Clare', 'Cork', 'Derry', 'Donegal', 'Down', 'Dublin', 'Fermanagh', 'Galway', 'Kerry', 'Kildare', 'Kilkenny', 'Laois', 'Leitrim', 'Limerick', 'Longford', 'Louth', 'Mayo', 'Meath', 'Monaghan', 'Offaly', 'Roscommon', 'Sligo', 'Tipperary', 'Tyrone', 'Waterford', 'Westmeath', 'Wexford', 'Wicklow')
;

UPDATE IGNORE `plugin_products_matrices`
SET `option_2_id` = (SELECT `id` FROM `plugin_products_option_groups` WHERE `group` = 'color (kma)' AND `deleted` = 0 LIMIT 1),
    `last_updated` = CURRENT_TIMESTAMP
WHERE `name` = 'Kiss My Ash!';
