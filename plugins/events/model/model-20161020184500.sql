/*
ts:2016-10-20 18:45:00
*/

UPDATE `plugin_messaging_notification_templates`
SET `message` = '
<div style="background-color: #ebebeb; padding-bottom: 50px;">
\n  <table cellspacing="0" cellpadding="0" style="margin: auto">
\n    <tr>
\n      <td align="center">
\n        <img src="$logosrc" style="padding-bottom: 5px;padding-top:1em" alt="uTicket" />
\n      </td>
\n    </tr>
\n  </table>
\n  <table cellspacing="0" cellpadding="0" style="border: 1px solid #ccc; background-color: #fff; margin: auto; max-width: 540px; font-size: 13px">
\n    <tr>
\n    <td style="padding: 37px 40px;color: #fff; font-size: 26px; font-weight: 200; background-color: #00aa87;">
\n      Your order is complete
\n    </td>
\n    </tr>
\n    <tr>
\n      <td style="padding: 40px; padding-top:10px">
\n        <p>
\n          Hi $firstname.
\n        </p>
\n
\n        <p>
\n         Your recent order on uTicket has been completed. Your order details are shown below for your reference.
\n        </p>
\n
\n        <p>
\n          <strong>Your tickets and receipt are attached to this email.</strong>
\n        </p>
\n
\n        <p>
\n          You can save your tickets to your mobile device or download and print at home.
\n        </p>
\n
\n        $orders_table
\n
\n        <h2 style="color: #00aa87;font-size: 16px;margin: .5em 0;">Your details</h2>
\n
\n        <p>
\n          Name: $firstname $lastname
\n        </p>
\n        <p>
\n          Email: <a href="mailto:$customer_email">$customer_email</a>
\n        </p>
\n        <p>
\n          Tel: $customer_phone
\n        </p>
\n        <p>
\n          Address: $address_1 $address_2 $city $county $country
\n        </p>
\n
\n        <h2 style="color: #00aa87;font-size: 16px;margin: .5em 0;">Organiser details</h2>
\n
\n        <p>
\n          Name: $organiser_name
\n        </p>
\n
\n        <p>
\n          Email: <a href="$organiser_email">$organiser_email</a>
\n        </p>
\n
\n        <p></p>
\n
\n        <p>
\n          Please go to your <a style="color: #00aa87;text-decoration:none" href="$profile_url">profile</a> to edit your details.
\n        </p>
\n
\n        <p>
\n          If you need help just reply to this email or visit <a style="color: #00aa87;text-decoration:none" href="$buyer_help_url">Ticket Buyer Help</a>.
\n        </p>
\n
\n        <p>
\n          Thanks for using uTicket!
\n        </p>
\n
\n        <p style="text-align: center">
\n          <a style="color: #00aa87;text-decoration:none" href="www.uticket.ie">www.uticket.ie</a>
\n        </p>
\n      </td>
\n    </tr>
\n  </table>
\n</div>',
`date_updated` = CURRENT_TIMESTAMP
WHERE `name` = 'ticket-purchased-buyer';

UPDATE `plugin_messaging_notification_templates`
SET `message` = '
<div style="background-color: #ebebeb; padding-bottom: 50px;">
\n  <table cellspacing="0" cellpadding="0" style="margin: auto">
\n    <tr>
\n      <td align="center">
\n        <img src="$logosrc" style="padding-bottom: 5px;padding-top:1em" alt="uTicket" />
\n      </td>
\n    </tr>
\n  </table>
\n  <table cellspacing="0" cellpadding="0" style="border: 1px solid #ccc;background-color: #fff; margin: auto; max-width: 540px; font-size: 13px;">
\n    <tr>
\n    <td style="padding: 37px 40px;color: #fff; font-size: 26px; font-weight: 200; background-color: #00aa87;">
\n      You have received an order
\n    </td>
\n    </tr>
\n    <tr>
\n      <td style="padding: 40px; padding-top:10px">
\n        <p>
\n          Hi $organiser_name.
\n        </p>
\n
\n        <p>
\n          You have received an order for:
\n        </p>
\n
\n        $orders_table
\n
\n        <h2 style="color: #00aa87;font-size: 16px;margin: .5em 0;">Customer details</h2>
\n
\n        <p>
\n          Name: $firstname $lastname
\n        </p>
\n        <p>
\n          Email: <a href="mailto:$customer_email">$customer_email</a>
\n        </p>
\n        <p>
\n          Tel: $customer_phone
\n        </p>
\n        <p>
\n          Address: $address_1 $address_2 $city $county $country
\n        </p>
\n
\n        <h2 style="color: #00aa87;font-size: 16px;margin: .5em 0;">Your details</h2>
\n
\n        <p>
\n          Name: $organiser_name
\n        </p>
\n
\n        <p>
\n          Email: <a href="$organiser_email">$organiser_email</a>
\n        </p>
\n
\n        <p></p>
\n
\n        <p>
\n          Click <a style="color: #00aa87;text-decoration:none" href="$orders_url">here</a> to view your orders.
\n        </p>
\n
\n        <p>
\n          Please go to your <a style="color: #00aa87;text-decoration:none" href="$profile_url">profile</a> to edit your details.
\n        </p>
\n
\n        <p>
\n          If you need help just reply to this email or visit <a style="color: #00aa87;text-decoration:none" href="$organizer_help_url">Event Organiser Help</a>.
\n        </p>
\n
\n        <p>
\n          Thanks for using uTicket!
\n        </p>
\n
\n        <p style="text-align: center">
\n          <a style="color: #00aa87;text-decoration:none" href="www.uticket.ie">www.uticket.ie</a>
\n        </p>
\n      </td>
\n    </tr>
\n  </table>
\n</div>',
`date_updated` = CURRENT_TIMESTAMP
WHERE `name` = 'ticket-purchased-seller';
