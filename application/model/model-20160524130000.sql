/*
ts:2016-05-24 15:00:00
*/

UPDATE engine_plugins set friendly_name = 'Shortcuts' WHERE `name` = 'keyboardshortcut';

INSERT IGNORE INTO `engine_settings`
(`variable`,        `name`,            `value_live`, `value_stage`, `value_test`, `value_dev`,  `default`,    `type`, `group`,                     `note`) VALUES
('login_lifetime',  'Login Lifetime',  '1 day',      '1 day',       '1 day',      '1 day',      '1 day',      'text', 'Website Platform Settings', 'The amount of time a user can be signed in before they will be automatically signed out. (e.g. 1 hour 30 minutes)'),
('login_idle_time', 'Login Idle Time', '30 minutes', '30 minutes',  '30 minutes', '30 minutes', '30 minutes', 'text', 'Website Platform Settings', 'The amount of time a user can do nothing, while signed in, before they will be automatically signed out, unless they ticked the &quot;keep me signed in&quot; button. (e.g. 1 hour 30 minutes)');
