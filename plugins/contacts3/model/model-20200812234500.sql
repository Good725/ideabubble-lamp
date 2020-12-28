/*
ts:2020-08-12 23:45:00
*/

-- Add the "User / Profile / Organisation / Edit billing address" permission.
INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
  (
    '1',
    'user_profile_organisation_edit_billing_address',
    'User / Profile / Organisation / Edit billing address',
    'Access the organisation section of the profile',
    (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'user')
  );
