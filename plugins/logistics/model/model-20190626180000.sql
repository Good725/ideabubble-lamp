/*
ts:2019-06-26 18:00:00
*/
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `overwrite_cms_message`, `date_created`, `created_by`)
VALUES (
  'logistics_transfer_created',
  'Email sent to student when a transfer is created',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'Transfer created',
  '<p>Hello $name</p>

  <p>A transfer has been scheduled as follows:</p>

  <p>Pick up: $pickup_location / $scheduled_date<br />Drop off: $droppoff_location</p>

  <p>Regards</p>
  ',
  '1',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0)
);

UPDATE
  `plugin_messaging_notification_templates`
SET
  `message` = '<p>Hello $name</p>

  <p>A transfer has been scheduled as follows:</p>

  <p>Pick up: $pickup_location / $scheduled_date<br />Drop off: $dropoff_location</p>

  <p>Regards</p>'
WHERE
  `name` = 'logistics_transfer_created';