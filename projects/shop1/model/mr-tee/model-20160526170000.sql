/*
ts:2016-05-25 17:00:00
*/

UPDATE IGNORE `plugin_pages_pages` SET `publish` = 0, `deleted` = 1 WHERE `name_tag` IN ('online-returns', 'online-returns.html');

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'online-returns.html',
  'Online Returns',
  '<h1>Returns/Exchanges</h1>  <p>We are happy to exchange your t-shirt for another of different size or design (provided the one you return is in saleable condition and is sent to us within 7 days of your receipt of same). All you have to do is <strong>fill in the form below</strong> and send the t-shirt back to us at this address: Mr-Tee Limited, PO Box 107, Clonmel, Co. Tipperary, Ireland and <strong>include your invoice in your package</strong>.</p>  <p>Shipping costs on any non-damage or non-flawed returns will be the responsibility of the customer. Upon receipt of the returned garment, we will refund you or issue your replacement as soon as possible.</p>  <p>Refunds may take several weeks to process, depending on your payment method.</p>  <p>Replacements can only be dispatched upon payment of &quot;Exchange Shipping Costs&quot; below&nbsp;(Ireland -&nbsp;&euro;3.99 and Worldwide -&nbsp;&euro;4.99).</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
);
