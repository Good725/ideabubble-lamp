/*
ts:2020-04-29 00:11:00
*/
INSERT INTO `plugin_courses_providers_types`
    (`type`, `publish`, `delete`)
    VALUES ('Accreditation Body', '1', '0');

INSERT INTO `plugin_courses_providers` ( `type_id`,`name` , `address1`, `publish`, `delete`)
    SELECT  `plugin_courses_providers_types`.id, 'Technological University Dublin', ' ', 1,0
    FROM    `plugin_courses_providers_types`
    WHERE   `plugin_courses_providers_types`.`type` = 'Accreditation Body' ;