/*
ts:2016-09-26 11:22:00
*/

UPDATE `plugin_messaging_notification_templates`
SET `message` = '<p>Hi $contact_name,</p>\n

<p>We are contacting you to inform you that your service(s) with us below are due for renewal.</p>\n

<strong>$service_type / $service_name / $date_end / &euro;$subtotal</strong>
<p>
Total to pay: &euro;$total
</p>\n

<p>
If you wish to renew your services can you reply confirming you wish to renew these services <u><strong>before Mon 28th March 2016.</strong></u>
</p>\n

<p>
<u><strong>Please note failure to reply to this email could result in all your web, email services going offline.</strong></u>
</p>\n

<p>
If you wish to renew your services immediately please click on the link below to send payment for these services. An invoice will be issued to you shortly afterwards.
</p>\n

<p>
To renew and pay please go to <a href="https://ideabubble.ie/pay-online.html">https://ideabubble.ie/pay-online.html</a>
</p>\n

<p>
Thank you.
</p>\n

<p>
Accounts Department
</p>\n'
WHERE `name` = 'service-expire-reminder';