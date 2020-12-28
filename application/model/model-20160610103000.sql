/*
ts:2016-06-10 10:30:00
*/

INSERT IGNORE INTO `engine_settings`
(`name`,                            `variable`,                        `type`,          `group`,        `options`) VALUES
('Enable Twitter API access',       'twitter_api_access',              'toggle_button', 'Social Media', 'Model_Settings,on_or_off'),
('Twitter API Consumer Key',        'twitter_api_consumer_key',        'text',          'Social Media', NULL),
('Twitter API Secret Consumer Key', 'twitter_api_secret_consumer_key', 'text',          'Social Media', NULL),
('Twitter API Access Token',        'twitter_api_access_token',        'text',          'Social Media', NULL),
('Twitter API Secret Access Token', 'twitter_api_secret_access_token', 'text',          'Social Media', NULL);

INSERT IGNORE INTO `engine_settings`
(`name`,                            `variable`,                        `type`,          `group`,        `options`) VALUES
('Enable Facebook API access',      'facebook_api_access',             'toggle_button', 'Social Media', 'Model_Settings,on_or_off'),
('Facebook API App ID',             'facebook_api_app_id',             'text',          'Social Media', NULL),
('Facebook API Secret ID',          'facebook_api_secret_id',          'text',          'Social Media', NULL),
('Facebook API Access Token',       'facebook_api_access_token',       'text',          'Social Media', NULL);

INSERT IGNORE INTO `engine_settings`
(`name`,                      `variable`,          `type`,          `group`,        `options`                 ) VALUES
('Enable Slaask integration', 'slaask_api_access', 'toggle_button', 'Social Media', 'Model_Settings,on_or_off'),
('Slaask API key',            'slaask_api_key',    'text',          'Social Media', NULL                      );

ALTER IGNORE TABLE `engine_users`
ADD COLUMN `avatar`       VARCHAR(255) NULL                AFTER `company` ,
ADD COLUMN `use_gravatar` INT(1)       NOT NULL DEFAULT 1  AFTER `avatar` ;
