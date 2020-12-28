/*
ts:2016-08-22 22:00:00
*/

UPDATE `plugin_messaging_notification_templates`
SET `message` = '<table width="100%" border="0" cellspacing="0" cellpadding="0">\n<tr>\n<td align="center">\n<img src="$logosrc" alt="uTicket" />\n</td>\n</tr>\n</table>\n<br />\n<p>Hello $firstname $lastname</p>\n

<p>Thank you for your order.</p>\n<p>Please find your tickets and receipt attached.</p>\n<p>You may download your tickets to your phone as a mobile ticket or you may print at home.</p>\n<p>Our tickets are 100% mobile friendly so save the trees and go mobile!</p>\n

<h2>Your details</h2>\n<p>Name: $firstname $lastname</p>\n<p>Email: $email</p>\n

<p>\nAddress:\n<br />\n$address_1\n<br />\n$address_2\n<br />\n$city\n<br />\n$country\n</p>\n

<h2>Order</h2>\n$orders_table\n<br />\n

<p>\nPrice: $currency$booking_price<br />\nBooking Fee: $currency$booking_fee<br />\nDiscount: $currency$discount<br />\nTotal: $currency$total\n</p>

<p>\nNote:\n</p>\n

<p>\nTo edit any your details please log in to your <a href="$profile_url">Profile</a><br />\nFor help just reply to this message or visit our <a href="$buyer_help_url">Ticket Buyer Help page</a><br />\nThank you for using our service.\nThe team at uTicket\n</p>\n

<p>\n<a href="www.uticket.ie">www.uticket.ie</a>\n</p>'
WHERE `name` = 'ticket-purchased-buyer';