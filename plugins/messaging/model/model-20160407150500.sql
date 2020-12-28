/*
ts:2016-04-07 15:05:00
*/
INSERT IGNORE INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `sender`, `message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
(
  'product-review-posted',
  'Email sent to the administrator when a product review is posted',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'New Product Review',
  'testing@websitecms.ie',
  '<p>A new comment has been posted on the product <a href=\"$product_url\">$product_title</a>.</p>\n\n<p> Rating: $rating<br />\nTitle: $title<br />\nAuthor: $author<br />\nEmail: $email </p>\n\n<h3>Review</h3>\n$review\n\n<p><a href=\"$edit_review_link\">Approve this review.</a></p>',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie'),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);
