/*
ts:2019-08-20 15:19:00
*/

INSERT INTO `engine_resources`
    (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES (1, 'timetables_view_planner', 'Timetables View Planner', 'Timetables View Planner',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timetables'));

INSERT IGNORE INTO engine_role_permissions
    (role_id, resource_id)
    (SELECT r.id, e.id
     FROM `engine_project_role` r
              JOIN engine_resources e
     WHERE r.role = 'Administrator'
       AND e.alias = 'timetables_view_planner');
