/*
ts:2020-06-12 07:07:01
*/

UPDATE engine_settings SET value_live='8516c9f0-3886-40f1-a1de-57a903a34636', value_stage='8516c9f0-3886-40f1-a1de-57a903a34636', value_test='8516c9f0-3886-40f1-a1de-57a903a34636' WHERE `variable` = 'cdsapi_client_id';
UPDATE engine_settings SET value_live='/FM[L1eH2]h?CIDzNSh5HmaA7epi=Cn:', value_stage='/FM[L1eH2]h?CIDzNSh5HmaA7epi=Cn:', value_test='/FM[L1eH2]h?CIDzNSh5HmaA7epi=Cn:' WHERE `variable` = 'cdsapi_client_secret';
UPDATE engine_settings SET value_live='https://ibeccustomermastersandbox.crm4.dynamics.com/.default', value_stage='https://ibeccustomermastersandbox.crm4.dynamics.com/.default', value_test='https://ibeccustomermastersandbox.crm4.dynamics.com/.default' WHERE `variable` = 'cdsapi_scope';
UPDATE engine_settings SET value_live='https://login.microsoftonline.com/92cf37ec-f728-4610-b625-c6b3211e8bbf/oauth2/v2.0/token', value_stage='https://login.microsoftonline.com/92cf37ec-f728-4610-b625-c6b3211e8bbf/oauth2/v2.0/token', value_test='https://login.microsoftonline.com/92cf37ec-f728-4610-b625-c6b3211e8bbf/oauth2/v2.0/token' WHERE `variable` = 'cdsapi_ms_auth_url';
UPDATE engine_settings SET value_live='https://ibeccustomermastersandbox.crm4.dynamics.com', value_stage='https://ibeccustomermastersandbox.crm4.dynamics.com', value_test='https://ibeccustomermastersandbox.crm4.dynamics.com' WHERE `variable` = 'cdsapi_api_url';


INSERT IGNORE INTO engine_role_permissions
  (role_id, resource_id)
  VALUES
  (
    (SELECT id FROM engine_project_role WHERE role IN ('Administrator')),
    (SELECT id FROM engine_resources WHERE alias IN ('cdsapi'))
  );
