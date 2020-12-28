/*
ts:2019-11-27 12:02:00
*/

ALTER TABLE `plugin_contacts3_contacts`
    CHANGE COLUMN `subtype_id` `subtype_id` TINYINT(4) NOT NULL DEFAULT 0;

UPDATE plugin_contacts3_contacts
SET `subtype_id` = 0
WHERE type = (SELECT contact_type_id
              FROM plugin_contacts3_contact_type
              WHERE `name` = 'organisation')
  AND subtype_id = (SELECT id
                    FROM plugin_contacts3_contacts_subtypes
                    WHERE `subtype` = 'Family');