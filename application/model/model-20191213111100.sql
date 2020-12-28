/*
ts:2019-12-13 11:11:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `note`, `type`, `group`, `required`, `options`)
VALUES ('cms_platform', 'CMS Platform', '',
        'Selecting an option may change the behavior of the CMS. e.g. Some inputs may be required or hidden depending on the platform Training companies may not need "Academic Year" in contacts3',
        'dropdown', 'Engine', '0',
        '{"":"\-\- Please Select \-\-","secondary_school_grinds":"Secondary school/grinds provider","training_company":"Training company","language_school":"Language school"}');
