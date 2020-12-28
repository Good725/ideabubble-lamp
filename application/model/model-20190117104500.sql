/*
ts:2019-01-17 10:45:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
VALUES (
  (SELECT IFNULL(`id`, 2) FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0),
  'fei@ideabubble.ie',
  'dd22e11bc5451c43f6d6042663ee90077ee6d4993343cc6c53d550db3df5a97b',
  'Fei',
  '',
  CURRENT_TIMESTAMP(),
  1,
  1,
  0,
  1
),
(
  (SELECT IFNULL(`id`, 2) FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0),
  'rowan@ideabubble.ie',
  '6166322341133eda8e8947c7fc919de57a84cca1e639c0849e51149bd03c6ae5',
  'Rowan',
  '',
  CURRENT_TIMESTAMP(),
  1,
  1,
  0,
  1
);

UPDATE `engine_users`
SET    `role_id` = (SELECT IFNULL(`id`, 2) FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0)
WHERE  `email`   = 'mary@ideabubble.ie';
