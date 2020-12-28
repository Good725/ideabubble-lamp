/*
ts:2016-08-29 10:25:00
*/

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'default'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` IN ('01');

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '2col'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` = '02';

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'home_wide'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` IN ('03', '05', '06', '07', '08', '10', '13', '15', '22');


UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'a'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` IN ('04', '09', '11', '12', '14', '19', '24', '25');

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'course'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` = '16';

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'systems'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` = '17';

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'books'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` IN ('20', '21');

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'accommodation'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` = '23';

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'courses2'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` = '26';

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'courses2'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` = '27';

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'b'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` = '28';

UPDATE IGNORE `engine_site_themes`
SET
  `template_id`   = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = 'tickets'),
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `stub` IN ('29');
