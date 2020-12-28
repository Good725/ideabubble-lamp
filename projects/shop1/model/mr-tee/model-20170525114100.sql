/*
ts:2017-05-25 11:41:00
*/

UPDATE
  `plugin_products_option`
SET
  `message` = '<p>Please note that Ringers are a very snug fit and you may need a larger size. See our <a href=\"/sizeguide.html\">Size Guide</a> for more information.</p>'
WHERE
  `value` IN ('ringer', 'ringer (m)')
;
