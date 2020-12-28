/*
ts:2016-08-22 22:10:00
*/

UPDATE `plugin_messaging_notification_templates`
SET `message` = '<table width="100%" border="0" cellspacing="0" cellpadding="0">\n<tr>\n<td align="center">\n<img src="$logosrc" alt="uTicket" />\n</td>\n</tr>\n</table>\n<br />\n<p>Hello $firstname $lastname</p>\n

<p>\nHello.\n</p>\n
<p>\nYou have received an order from:\n</p>\n

<p>\nName: $firstname $lastname</p>\n<p>Email: $email\n</p>\n

<p>\nAddress:\n<br />\n$address_1\n<br />\n$address_2\n<br />\n$city\n<br />\n$country\n</p>\n

<h2>Order</h2>\n$orders_table\n<br />\n

<p>\nDiscount: $currency$discount<br />\nTotal: $currency$total\n</p>\n

<p>\nTo view full details of this order and any other order visit you <a href="$orders_url">Orders page</a>\n</p>\n
<p>\nTo edit any your details please log in to your <a href="$profile_url">Profile</a>\n</p>\n
<p>\nFor help just reply to this message or visit our <a href="$organizer_help_url">Event Organiser Help page</a>\n</p>\n
<p>Thank you for using our service.
The team at uTicket
</p>
<p>\n<a href="www.uticket.ie">www.uticket.ie</a>\n</p>'
WHERE `name` = 'ticket-purchased-seller';