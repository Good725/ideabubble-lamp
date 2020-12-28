/*
ts:2016-12-05 22:40:00
*/

UPDATE `engine_project_role` SET `publish` = 0, `deleted` = 1 WHERE `role` IN ('External User', 'Basic', 'Parent/Guardian', 'Student');

INSERT INTO engine_project_role SET `role` = 'Donor', `description` = 'Donor', `publish` = 1, `deleted` = 0;

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Donor') AND e.alias IN ('contacts2_index_limited', 'contacts2_view_limited'));

INSERT INTO engine_plugins_per_role
  (plugin_id,role_id,enabled)
  (SELECT p.id, r.id, 1 FROM `engine_project_role` r JOIN engine_plugins p WHERE r.role IN ('Donor') AND p.name IN ('contacts2'));

UPDATE plugin_contacts_mailing_list SET `publish` = 0, `deleted` = 1 WHERE `name` IN ('trainer', 'Parent/Guardian', 'Student', 'Event Organizer');

