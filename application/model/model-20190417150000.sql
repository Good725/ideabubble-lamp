/*
ts:2019-04-17 15:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `group`, `type`, `options`)
VALUES
(
  'page_change_on_sidebar_submenu_open',
  'Page change on sidebar submenu open',
  '0',
  '0',
  '0',
  '0',
  '0',
  'When &quot;On&quot;, if a sidebar link with a submenu is clicked, the page changes to open the plugin for the clicked submenu.<br />When &quot;Off&quot;, only the submenu will open.',
  'Engine',
  'toggle_button',
  'Model_Settings,on_or_off'
);