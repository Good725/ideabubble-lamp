/*
ts:2016-09-29 22:00:00
*/

UPDATE `plugin_messaging_notification_templates`
SET `message` = '
<div style="background-color: #ebebeb">
  <table cellspacing="0" cellpadding="0" style="margin: auto">
    <tr>
      <td align="center">
        <img src="$logosrc" style="padding-bottom: 5px;padding-top:1em" alt="uTicket" />
      </td>
    </tr>
  </table>
  <table cellspacing="0" cellpadding="0" style="background-color: #fff; margin: auto; font-size: 10px">
    <tr>
    <td style="padding: 20px;padding-left: 40px; color: #fff; font-size: 16px; background-color: #00aa87;">
      Your order is complete
    </td>
    </tr>
    <tr>
      <td style="padding: 40px; padding-top:10px">
        <p>
          Hi $firstname.
        </p>

        <p>
          Thank you for your order.
        </p>

        <p>
          <strong>Your tickets and receipt are attached to this email.</strong>
        </p>

        <p>
          You can save your tickets to your mobile device or download and print at home.
        </p>

        $orders_table

        <h2 style="color: #00aa87;font-size: 10px">Your details</h2>

        <p>
          Name: $firstname $lastname
        </p>
        <p>
          Email: <a href="mailto:$customer_email">$customer_email</a>
        </p>
        <p>
          Tel: $customer_phone
        </p>
        <p>
          Address: $address_1 $address_2 $city $county $country
        </p>

        <h2 style="color: #00aa87;font-size: 10px">Organiser details</h2>

        <p>
          Name: $organiser_name
        </p>

        <p>
          Email: <a href="$organiser_email">$organiser_email</a>
        </p>

        <p></p>

        <p>
          Please go to your <a style="color: #00aa87;text-decoration:none" href="$profile_url">profile</a> to edit your details.
        </p>

        <p>
          If you need help just reply to this email or visit <a style="color: #00aa87;text-decoration:none" href="$buyer_help_url">Ticket Buyer Help</a>.
        </p>

        <p>
          Thanks for using uTicket!
        </p>

        <p style="text-align: center">
          <a style="color: #00aa87;text-decoration:none" href="www.uticket.ie">www.uticket.ie</a>
        </p>
      </td>
    </tr>
  </table>
</div>'
WHERE `name` = 'ticket-purchased-buyer';

UPDATE `plugin_messaging_notification_templates`
SET `message` = '
<div style="background-color: #ebebeb">
  <table cellspacing="0" cellpadding="0" style="margin: auto">
    <tr>
      <td align="center">
        <img src="$logosrc" style="padding-bottom: 5px;padding-top:1em" alt="uTicket" />
      </td>
    </tr>
  </table>
  <table cellspacing="0" cellpadding="0" style="background-color: #fff; margin: auto; font-size: 10px">
    <tr>
    <td style="padding: 20px;padding-left: 40px; color: #fff; font-size: 16px; background-color: #00aa87;">
      Your have received an order
    </td>
    </tr>
    <tr>
      <td style="padding: 40px; padding-top:10px">
        <p>
          Hi $organiser_name.
        </p>

        <p>
          You have received an order for:
        </p>

        $orders_table

        <h2 style="color: #00aa87;font-size: 10px">Customer details</h2>

        <p>
          Name: $firstname $lastname
        </p>
        <p>
          Email: <a href="mailto:$customer_email">$customer_email</a>
        </p>
        <p>
          Tel: $customer_phone
        </p>
        <p>
          Address: $address_1 $address_2 $city $county $country
        </p>

        <h2 style="color: #00aa87;font-size: 10px">Your details</h2>

        <p>
          Name: $organiser_name
        </p>

        <p>
          Email: <a href="$organiser_email">$organiser_email</a>
        </p>

        <p></p>

        <p>
          Click <a style="color: #00aa87;text-decoration:none" href="$orders_url">here</a> to view your orders.
        </p>

        <p>
          Please go to your <a style="color: #00aa87;text-decoration:none" href="$profile_url">profile</a> to edit your details.
        </p>

        <p>
          If you need help just reply to this email or visit <a style="color: #00aa87;text-decoration:none" href="$organizer_help_url">Event Organiser Help</a>.
        </p>

        <p>
          Thanks for using uTicket!
        </p>

        <p style="text-align: center">
          <a style="color: #00aa87;text-decoration:none" href="www.uticket.ie">www.uticket.ie</a>
        </p>
      </td>
    </tr>
  </table>
</div>'
WHERE `name` = 'ticket-purchased-seller';