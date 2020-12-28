/*
ts:2019-08-23 11:41:00
*/

INSERT INTO plugin_contacts3_contact_type (label)
(SELECT `subtype` from plugin_contacts3_contacts_subtypes where `subtype` != 'Billed Organization');

INSERT INTO `plugin_contacts3_contact_type` (`label`)
VALUES ('Student');

-- Update all general/family members to the new general type
UPDATE plugin_contacts3_contacts
SET `type` = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Family" limit 1)
WHERE id IN (select chr.contact_id
       from plugin_contacts3_contact_has_roles `chr`
                inner join plugin_contacts3_roles `c3r` on (`chr`.role_id = `c3r`.id)
       where `c3r`.name = 'Guardian'
       group by `chr`.id);

UPDATE plugin_contacts3_contacts
SET `type` = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Student" limit 1)
WHERE id IN (select chr.contact_id
             from plugin_contacts3_contact_has_roles `chr`
                      inner join plugin_contacts3_roles `c3r` on (`chr`.role_id = `c3r`.id)
             where  `c3r`.name = 'Child'  or `c3r`.name = 'Mature' group by `chr`.id);

-- Update all teachers to the new general type
UPDATE plugin_contacts3_contacts
SET `type` = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Staff" limit 1)
WHERE id IN (select chr.contact_id
       from plugin_contacts3_contact_has_roles `chr`
                inner join plugin_contacts3_roles `c3r` on (`chr`.role_id = `c3r`.id)
       where `c3r`.name = 'Teacher'
          or `c3r`.name = 'Supervisor'
          or `c3r`.name = 'Admin'
       group by `chr`.id);
-- Update the other subtypes but do not add business as they have their own subtypes

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Host family" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Host family');

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Other Accommodation" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Other Accommodation');

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Agent" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Agent');

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Work Placement" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Work Placement');

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Coordinator" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Coordinator');

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Transport" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Transport');

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Schools" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Schools');

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Suppliers" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Suppliers');

UPDATE plugin_contacts3_contacts
set type = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Tour Provider" limit 1)
where type != (select `contact_type_id` from plugin_contacts3_contact_type where `label` = 'Business')
  and subtype_id = (select `id` from plugin_contacts3_contacts_subtypes where `subtype` = 'Tour Provider');

-- Update the rest of the General contacts
UPDATE plugin_contacts3_contacts
SET `type` = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "Family" limit 1)
WHERE `type` = (select `contact_type_id` from plugin_contacts3_contact_type where `label` = "General" limit 1);

-- Then delete the general contact type
delete from plugin_contacts3_contact_type where `label` = "General";

CREATE TABLE `plugin_contacts3_contact_type_columns`
(
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(45)  NOT NULL,
    `label`        VARCHAR(45)  NULL DEFAULT NULL,
    `table_column` VARCHAR(100) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `name_UNIQUE` (`name` ASC)
);

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('contact_id', 'ID', 'contact.id');
INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('company_name', 'Company', 'business_contact.first_name');
INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('country_name', 'Country', 'countries.name');
INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('full_name', 'Name', 'CONCAT(contact.first_name, \" \" , contact.last_name)' );
INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('mobile', 'Mobile', 'mobile.value');
INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('email', 'Email', 'emails.value');
INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('status', 'Status', 'IF(`contact`.is_inactive = 1, \'Inactive\', \'Active\')');

CREATE TABLE `plugin_contacts3_contact_type_has_columns`
(
    `contact_type_id`        INT NOT NULL,
    `contact_type_column_id` INT NOT NULL,
    `priority` INT NULL DEFAULT 0,
    PRIMARY KEY (`contact_type_id`, `contact_type_column_id`)
);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `label` = 'Agent' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `label` = 'Agent' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'company_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `label` = 'Agent' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'country_name' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `label` = 'Agent' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'full_name' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `label` = 'Agent' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 5);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `label` = 'Agent' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 6);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `label` = 'Agent' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'status' LIMIT 1), 7);

ALTER TABLE `plugin_contacts3_contact_type`
    ADD COLUMN `name` VARCHAR(45) NULL DEFAULT NULL AFTER `contact_type_id`,
    ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC),
    ADD COLUMN `display_name` VARCHAR(45) NULL DEFAULT NULL AFTER `name`,
    ADD COLUMN `publish` TINYINT(1) NULL DEFAULT 1 AFTER `label`,
    ADD COLUMN `deletable` TINYINT(1) NULL DEFAULT 0 AFTER `publish`;


UPDATE `plugin_contacts3_contact_type`
SET `name` = 'billed', `display_name` = 'Billed Organizations'
WHERE (`label` = 'Billed Organization');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'business', `display_name` = 'Businesses'
WHERE (`label` = 'Business');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'department', `display_name` = 'Departments'
WHERE (`label` = 'Department');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'family', `display_name` = 'Families'
WHERE (`label` = 'Family');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'staff', `display_name` = 'Staff'
WHERE (`label` = 'Staff');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'other_accommodation', `display_name` = 'Other accommodations'
WHERE (`label` = 'Other Accommodation');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'host', `display_name` = 'Hosts'
WHERE (`label` = 'Host Family');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'agent', `display_name` = 'Agents'
WHERE (`label` = 'Agent');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'work_placement', `display_name` = 'Work placement'
WHERE (`label` = 'Work Placement');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'coordinator', `display_name` = 'Coordinators'
WHERE (`label` = 'Coordinator');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'transport', `display_name` = 'Transports'
WHERE (`label` = 'Transport');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'school', `display_name` = 'Schools'
WHERE (`label` = 'Schools');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'supplier', `display_name` = 'Suppliers'
WHERE (`label` = 'Suppliers');
UPDATE `plugin_contacts3_contact_type`
SET `name` = 'tour_provider', `display_name` = 'Tour providers'
WHERE (`label` = 'Tour Provider');
UPDATE `plugin_contacts3_contact_type`
SET `name`         = 'student',
    `display_name` = 'Students'
WHERE (`label` = 'Student');

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('workplace_name', 'Workplace', 'business_department_contact.first_name');

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('department_contact_name', 'Main Contact', 'CONCAT(main_department_business_contact.first_name, \' \', main_department_business_contact.last_name)' );

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('business_name', 'Business',
        'contact.first_name');

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'work_placement' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'work_placement' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'workplace_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'work_placement' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'department_contact_name' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'work_placement' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'business_name' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'work_placement' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 5);


INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('position', 'Position',
        'role.name');

-- Coordinator columns
INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'coordinator' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'coordinator' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'full_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'coordinator' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'position' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'coordinator' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'coordinator' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 5);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'coordinator' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'status' LIMIT 1), 6);

-- Transport columns
INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'transport' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'transport' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'business_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'transport' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'transport' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'transport' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'status' LIMIT 1), 5);

-- Staff columns
INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'staff' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'staff' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'full_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'staff' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'position' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'staff' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'staff' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 5);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'staff' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'status' LIMIT 1), 6);

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('county', 'County',
        'counties.name');

-- School columns
INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'school' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'school' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'full_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'school' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'county' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'school' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'school' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 5);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'school' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'status' LIMIT 1), 6);

-- Supplier columns
INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'supplier' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'supplier' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'business_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'supplier' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'supplier' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'supplier' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'status' LIMIT 1), 5);

-- Tour provider columns
INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'tour_provider' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'tour_provider' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'business_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'tour_provider' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'tour_provider' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'tour_provider' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'status' LIMIT 1), 5);

-- student columns
INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('schedule_name', 'Schedule', 'schedule.name');

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('schedule_length_days', 'Length', 'CONCAT(DATEDIFF(schedule.end_date ,schedule.start_date), \' days\')');

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('school_name', 'School', 'school.name');

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('type_name', 'Type', 'course_category.category');

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'student' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'student' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'full_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'student' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'school_name' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'student' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'type_name' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'student' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 5);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'student' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 6);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'student' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'status' LIMIT 1), 7);

-- Add setting to change which filters you want in the submenu
INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`, `options`, `config_overwrite`, `value_dev`,
                               `value_test`, `value_stage`, `value_live`, `default`)
VALUES ('display_filter_contact_types', 'Display filter contact types', 'Display which contact types display on the submenu in the contacts plugin.',
        'multiselect', 'Contacts', 'Model_Settings,get_filter_contact_types', 0, 'a:0:{}', 'a:0:{}', 'a:0:{}',
        'a:0:{}', 'a:0:{}');

ALTER TABLE `plugin_survey`
    CHANGE COLUMN `contact3_subtype_id` `contact_id` INT(11) NULL DEFAULT NULL;

-- Hide contacts2 sidebar if contacts3 is enabled
update engine_plugins
set show_on_dashboard = 0
where (select count(rp.role_id)
       from engine_role_permissions `rp`
                inner join engine_resources `r` on rp.resource_id = r.id
                inner join engine_project_role `pr` on rp.role_id = pr.id
       where pr.role = "Administrator"
         and r.alias = 'contacts3') = 1
  and name = 'contacts2';

-- Show contacts3 sidebar if contacts3 is enabled
update engine_plugins
set show_on_dashboard = 1, friendly_name     = 'Contacts'
where (select count(rp.role_id)
       from engine_role_permissions `rp`
                inner join engine_resources `r` on rp.resource_id = r.id
                inner join engine_project_role `pr` on rp.role_id = pr.id
       where pr.role = "Administrator"
         and r.alias = 'contacts3') = 1
  and name = 'contacts3';

UPDATE `plugin_contacts3_contact_type` t
SET t.`name`         = 'organisation',
    t.`display_name` = 'Organisations',
    t.`label`        = 'Organisation'
WHERE t.`name` = 'business';