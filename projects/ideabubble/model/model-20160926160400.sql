/*
ts:2016-09-26 16:04:00
*/

INSERT IGNORE INTO `plugin_messaging_notification_templates`
(`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `overwrite_cms_message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
  (
    'service-expire-reminder-0-days',
    'EMAIL',
    (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
    'Service expire reminder',
    'accounts@ideabubble.ie',
    '<p>Dear $contact_name ($company_title).</p>\n

  <p>This is a reminder email as your services have not been renewed.</p>\n

  <p>Renew & Pay Online - Secure your Services</p>\n

  <p>You can login here to view and pay renewal invoices. <a href="http://www.ideabubble.ie/customer-payment.html">http://www.ideabubble.ie/customer-payment.html</a></p>\n

  <p>Please note: if services are not renewed this may result in your website or email going offline.</p>\n',
    '0',
    CURRENT_TIMESTAMP,
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    CURRENT_TIMESTAMP,
    '1',
    '0'
  );

INSERT IGNORE INTO `plugin_messaging_notification_templates`
(`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `overwrite_cms_message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
  (
    'service-expire-reminder-10-days',
    'EMAIL',
    (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
    'Service expire reminder',
    'accounts@ideabubble.ie',
    '<p>Dear $contact_name ($company_title).</p>\n

  <p>This is a reminder email as your services have not been renewed.</p>\n

  <p>Renew & Pay Online - Secure your Services</p>\n

  <p>You can login here to view and pay renewal invoices. <a href="http://www.ideabubble.ie/customer-payment.html">http://www.ideabubble.ie/customer-payment.html</a></p>\n

  <p>Please note: if services are not renewed this may result in your website or email going offline.</p>\n',
    '0',
    CURRENT_TIMESTAMP,
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    CURRENT_TIMESTAMP,
    '1',
    '0'
);

INSERT IGNORE INTO `plugin_messaging_notification_templates`
(`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `overwrite_cms_message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
  (
    'service-expire-reminder-30-days',
    'EMAIL',
    (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
    'Service expire reminder',
    'accounts@ideabubble.ie',
    '<p>Dear $contact_name ($company_title)</p>\n

<p>This is a notice that an invoice has been generated on $today.</p>\n

<p>Renew & Pay Online - Secure your Services</p>\n

<p>You can login here to view and pay renewal invoices. <a href="http://www.ideabubble.ie/customer-payment.html"/>http://www.ideabubble.ie/customer-payment.html</a></p>\n

<p>Amount Due: &euro;$total EUR</p>\n

<p>Due Date: $date_end</p>\n

<p>Invoice Items</p>\n

<p>Domain - $domain $service_type &euro;$total EUR</p>\n

<p>
------------------------------------------------------
</p>\n

<p>Sub Total: &euro;$subtotal EUR</p>\n

<p>23.00% VAT: &euro;$vat EUR</p>\n

<p>Credit: &euro;$credit EUR</p>\n

<p>Total: &euro;$total EUR</p>\n

<p>
------------------------------------------------------
</p>\n

<p>Please note: if services are not renewed this may result in your website or email going offline.</p>\n',
    '0',
    CURRENT_TIMESTAMP,
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    CURRENT_TIMESTAMP,
    '1',
    '0'
  );