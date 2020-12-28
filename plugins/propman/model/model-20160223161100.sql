/*
ts:2016-02-23 16:11:00
*/

UPDATE plugin_messaging_notification_templates
  SET
    `message`='A new booking has been made.\n<h2>Booking details</h2>\n\n<table>\n	<tbody>\n		<tr>\n			<th scope=\"row\">Property name</th>\n			<td>$property_name</td>\n		</tr>\n		<tr>\n			<th scope=\"row\">Refcode</th>\n			<td>$ref_code\n				(<a href=\"$property_url_frontend\">view</a>\n				&middot;\n				<a href=\"$property_url_backend\">edit</a>)\n			</td>\n		</tr>\n		<tr>\n			<th scope=\"row\">Check in date</th>\n			<td>$checkin</td>\n		</tr>\n		<tr>\n			<th scope=\"row\">Check out date</th>\n			<td>$checkout</td>\n		</tr>\n		<tr>\n			<th scope=\"row\">Number of guests</th>\n			<td>$guests</td>\n		</tr>\n	</tbody>\n</table>\n\n\n<h2>Billing information</h2>\n\n<table>\n	<tbody>\n		<tr>\n			<th scope=\"row\">Amount</th>\n			<td>$amount_paid</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Name</th>\n			<td>$billing_name</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Address</th>\n			<td>$billing_address</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Town / City</th>\n			<td>$billing_town</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">State / County</th>\n			<td>$billing_county</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Country</th>\n			<td>$billing_country</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Telephone</th>\n			<td>$billing_phone</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Email</th>\n			<td>$billing_email</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Comments</th>\n			<td><pre>$comments</pre></td>\n		</tr>\n	</tbody>\n</table>\nThis email was generated at $time on $hostname.\n',
    `create_via_code`='New Booking',
    `usable_parameters_in_template`='$property_name,$ref_code,$property_url_frontend,$property_url_backend,$checkin,$checkout,$guests,$amount_paid,$guests,$customer,$billing_name,$billing_address,$billing_town,$billing_county,$billing_country,$billing_phone,$billing_email,$comments,$time,$hostname,$property_contact,$property_phone,$property_mobile'
  WHERE `name` = 'new_booking_admin';

UPDATE plugin_messaging_notification_templates
  SET
    `message`='<p>Dear, $customer</p>\n\n<p>Thank you for booking with $hostname</p>\n\n<p>Your details are listed below.</p>\n\n<h2>Booking details</h2>\n\n<table>\n	<tbody>\n		<tr>\n			<th scope=\"row\">Property name</th>\n			<td>$property_name <a href=\"$property_url_frontend\">view</a></td>\n		</tr>\n		<tr>\n        <th scope=\"row\">Property Host Contact Details</th>\n        <td>\n            Host Name: $property_contact<br>\n            Phone: $property_phone<br>\n            Mobile: $property_mobile\n        </td>\n    </tr>\n		<tr>\n			<th scope=\"row\">Check in date</th>\n			<td>$checkin</td>\n		</tr>\n		<tr>\n			<th scope=\"row\">Check out date</th>\n			<td>$checkout</td>\n		</tr>\n		<tr>\n			<th scope=\"row\">Number of guests</th>\n			<td>$guests</td>\n		</tr>\n	</tbody>\n</table>\n\n\n<h2>Billing information</h2>\n\n<table>\n	<tbody>\n		<tr>\n			<th scope=\"row\">Amount</th>\n			<td>$amount_paid</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Name</th>\n			<td>$billing_name</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Address</th>\n			<td>$billing_address</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Town / City</th>\n			<td>$billing_town</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">State / County</th>\n			<td>$billing_county</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Country</th>\n			<td>$billing_country</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Telephone</th>\n			<td>$billing_phone</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Email</th>\n			<td>$billing_email</td>\n		</tr>\n\n		<tr>\n			<th scope=\"row\">Comments</th>\n			<td><pre>$comments</pre></td>\n		</tr>\n	</tbody>\n</table>\nThis email was generated at $time on $hostname.\n',
    `create_via_code`='New Booking',
    `usable_parameters_in_template`='$property_name,$ref_code,$property_url_frontend,$property_url_backend,$checkin,$checkout,$guests,$amount_paid,$guests,$customer,$billing_name,$billing_address,$billing_town,$billing_county,$billing_country,$billing_phone,$billing_email,$comments,$time,$hostname,$property_contact,$property_phone,$property_mobile'
  WHERE `name` = 'new_booking_customer';
