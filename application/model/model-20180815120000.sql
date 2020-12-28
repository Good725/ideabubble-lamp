/*
ts:2018-08-15 12:00:00
*/

UPDATE
  `plugin_messaging_notification_templates`
SET
  `message` = REPLACE(
      `message`,
      '<p>HOST: $host</p><p>REFERER: $referer</p>',
      '<p>This email was sent from <a href="http://$host">$host</a> and was issued from <a href="$referer">$referer_path</a>.</p>'
  )
WHERE
  `name` = 'newsletter-signup'
;
