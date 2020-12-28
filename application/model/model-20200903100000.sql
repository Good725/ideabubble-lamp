/*
ts:2020-09-03 10:00:00
*/

-- Setting for changing the default HTML for the AddThis toolbox
INSERT INTO `engine_settings`
(`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`) VALUES
(
  'addthis_toolbox_html',
  'AddThis toolbox HTML',
  '',
  '',
  '',
  '',
  '',
  'both',
  'HTML to use for the AddThis toolbox. Leave blank to use the default from the codebase.',
  'html_editor',
  '0',
  'Social Media',
  '0'
);

-- Setting for automatically prefixing news items with the AddThis toolbox
INSERT INTO `engine_settings`
(`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'auto_addthis_on_news',
  'Auto-AddThis toolbox on news',
  '',
  '',
  '',
  '',
  '',
  'both',
  'Automatically add the AddThis toolbox to the top of news pages. This will not affect news items that have otherwise placed it by putting <code>{addthis_toolbox-}</code> in their content.',
  'toggle_button',
  '0',
  'Social Media',
  '0',
  'Model_Settings,on_or_off'
);

-- Setting for automatically prefixing pages with the AddThis toolbox
INSERT INTO `engine_settings`
(`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'auto_addthis_on_pages',
  'Auto-AddThis toolbox on pages',
  '',
  '',
  '',
  '',
  '',
  'both',
  'Automatically add the AddThis toolbox to the top of pages. This will not affect pages that have otherwise placed it by putting <code>{addthis_toolbox-}</code> in their content.',
  'toggle_button',
  '0',
  'Social Media',
  '0',
  'Model_Settings,on_or_off'
);

-- Re-purpose the setting to only affect content pages.
-- This is not desirable for home, checkout, course list, news, etc.
UPDATE
  `engine_settings`
SET
  `name` = 'Auto-AddThis toolbox on content pages',
  `note` = 'Automatically add the AddThis toolbox to the top of content pages. This will not affect pages that have otherwise placed it by putting <code>{addthis_toolbox-}</code> in their content.'
WHERE
  `variable` = 'auto_addthis_on_pages';