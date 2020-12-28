/*
ts:2016-05-16 10:26:00
*/

CREATE TABLE `plugin_contacts_relations`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  relation VARCHAR(50),
  plugin_id INT,
  deleted TINYINT DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_contacts_has_relations`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  contact_1_id  INT,
  contact_2_id  INT,
  relation_id   INT,
  deleted TINYINT DEFAULT 0,

  KEY (contact_1_id),
  KEY (contact_2_id)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_contacts_groups`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `group` VARCHAR(50),
  deleted TINYINT DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_contacts_in_groups`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  contact_id  INT,
  group_id  INT,
  deleted TINYINT DEFAULT 0,

  KEY (contact_id)
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_contacts_users_has_permission`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  contact_id INT,
  user_id INT,

  KEY (contact_id),
  KEY (user_id)
)
ENGINE = InnoDB
CHARSET = UTF8;

ALTER TABLE `plugin_contacts_contact` ADD COLUMN `title` VARCHAR(10);
ALTER TABLE `plugin_contacts_contact` ADD COLUMN `address1` VARCHAR(100);
ALTER TABLE `plugin_contacts_contact` ADD COLUMN `address2` VARCHAR(100);
ALTER TABLE `plugin_contacts_contact` ADD COLUMN `address3` VARCHAR(100);
ALTER TABLE `plugin_contacts_contact` ADD COLUMN `address4` VARCHAR(100);
ALTER TABLE `plugin_contacts_contact` ADD COLUMN `dob` DATE;

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'contacts2', 'Contacts', 'Contacts');
select id into @contacts_resource_id from `engine_resources` o where o.`alias` = 'contacts2';
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'contacts2_index', 'Contacts / Index', 'Contacts List', @contacts_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'contacts2_edit', 'Contacts / Edit', 'Contacts Create / Update', @contacts_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'contacts2_delete', 'Contacts / Delete', 'Contacts Delete', @contacts_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'contacts2_index_limited', 'Contacts / Index : limited', 'Contacts List limited access based on permission', @contacts_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'contacts2_view_limited', 'Contacts / View : limited', 'Contacts View limited access based on permission', @contacts_resource_id);

select id into @admin_role_id from `engine_project_role` r where r.role = 'Administrator';
select id into @contacts_index_resource_id from `engine_resources` o where o.`alias` = 'contacts2_index';
select id into @contacts_edit_resource_id from `engine_resources` o where o.`alias` = 'contacts2_edit';
select id into @contacts_delete_resource_id from `engine_resources` o where o.`alias` = 'contacts2_delete';
select id into @contacts_index_limited_resource_id from `engine_resources` o where o.`alias` = 'contacts2_index_limited';
select id into @contacts_view_limited_resource_id from `engine_resources` o where o.`alias` = 'contacts2_view_limited';

insert into engine_role_permissions (role_id,resource_id) values (@admin_role_id,@contacts_resource_id);
insert into engine_role_permissions (role_id,resource_id) values (@admin_role_id,@contacts_index_resource_id);
insert into engine_role_permissions (role_id,resource_id) values (@admin_role_id,@contacts_edit_resource_id);
insert into engine_role_permissions (role_id,resource_id) values (@admin_role_id,@contacts_delete_resource_id);

