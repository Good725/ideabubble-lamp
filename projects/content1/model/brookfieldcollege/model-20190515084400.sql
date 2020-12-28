/*
ts:2019-05-15 08:44:00
*/

INSERT IGNORE INTO `engine_settings_microsite_overwrites`
  (`setting`,                          `microsite_id`,            `environment`, `value`)
VALUES
  ('host_application',               'brookfieldlanguage',      'dev',   '1'),
  ('host_application',               'brookfieldinternational',      'dev',   '1'),
  ('host_application',               'brookfieldlanguage',      'test',   '1'),
  ('host_application',               'brookfieldinternational',      'test',   '1'),
  ('host_application',               'brookfieldlanguage',      'stage',   '1'),
  ('host_application',               'brookfieldinternational',      'stage',   '1'),
  ('host_application',               'brookfieldlanguage',      'live',   '1'),
  ('host_application',               'brookfieldinternational',      'live',   '1');
