/*
ts:2016-05-17 11:43:00
*/

select id into @courses_plugin_id_z from engine_plugins where `name` = 'courses';
INSERT INTO `plugin_contacts_relations` (`relation`, `plugin_id`) VALUES ('Parent/Guardian', @courses_plugin_id_z);

INSERT IGNORE INTO `plugin_contacts_mailing_list` (`name`) VALUES ('trainer');
INSERT IGNORE INTO `plugin_contacts_mailing_list` (`name`) VALUES ('Parent/Guardian');
INSERT IGNORE INTO `plugin_contacts_mailing_list` (`name`) VALUES ('Student');

INSERT INTO `engine_project_role` (`role`, `description`,`publish`,`deleted`) VALUES ('Parent/Guardian', 'Parent Guardian', 1, 0);
INSERT INTO `engine_project_role` (`role`, `description`,`publish`,`deleted`) VALUES ('Student', 'Student', 1, 0);

select id into @contacts_plugin_id_c from `engine_plugins` p where p.name = 'contacts2';
select id into @pg_role_id_c from `engine_project_role` r where r.role = 'Parent/Guardian';
select id into @st_role_id_c from `engine_project_role` r where r.role = 'Student';
select id into @contacts_resource_id_c from `engine_resources` o where o.`alias` = 'contacts2';
select id into @contacts_index_resource_id_c from `engine_resources` o where o.`alias` = 'contacts2_index';
select id into @contacts_edit_resource_id_c from `engine_resources` o where o.`alias` = 'contacts2_edit';
select id into @contacts_delete_resource_id_c from `engine_resources` o where o.`alias` = 'contacts2_delete';
select id into @contacts_index_limited_resource_id_c from `engine_resources` o where o.`alias` = 'contacts2_index_limited';
select id into @contacts_view_limited_resource_id_c from `engine_resources` o where o.`alias` = 'contacts2_view_limited';

insert into engine_role_permissions (role_id,resource_id) values (@pg_role_id_c,@contacts_index_limited_resource_id_c);
insert into engine_role_permissions (role_id,resource_id) values (@st_role_id_c,@contacts_index_limited_resource_id_c);
insert into engine_role_permissions (role_id,resource_id) values (@st_role_id_c,@contacts_view_limited_resource_id_c);
replace into engine_plugins_per_role (plugin_id,role_id,enabled) values (@contacts_plugin_id_c, @pg_role_id_c, 1);
replace into engine_plugins_per_role (plugin_id,role_id,enabled) values (@contacts_plugin_id_c, @st_role_id_c, 1);

CREATE TABLE `plugin_courses_schedules_has_students`
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  schedule_id INT,
  contact_id INT,
  status ENUM('Pending', 'Registered', 'Cancelled'),
  notes TEXT,
  deleted TINYINT DEFAULT 0,
  created_by INT,
  created DATETIME,
  updated_by INT,
  updated DATETIME,

  KEY (schedule_id),
  KEY (contact_id)
)
ENGINE = InnoDB
CHARSET = UTF8;


CREATE TABLE `plugin_courses_students`
(
  contact_id INT PRIMARY KEY,
  dob DATE,
  gender ENUM('Male', 'Female'),
  other_fam ENUM('Y', 'N'),
  beginner ENUM('Y', 'N'),
  on_prev_crs ENUM ('Y', 'N'),
  education VARCHAR(50),
  cur_sch VARCHAR(100)
)
  ENGINE = InnoDB
  CHARSET = UTF8;