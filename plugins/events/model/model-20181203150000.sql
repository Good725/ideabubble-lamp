/*
ts:2018-12-03 15:00:00
*/

UPDATE
  `plugin_messaging_notification_templates`
SET
  `date_updated`                  = CURRENT_TIMESTAMP,
  `subject`                       = '$project Order No. $order_id | Group Booking Created',
  `usable_parameters_in_template` = '$address_1, $address_2, $base_url, $buyer, $buyer_help_url, $city, $country, $county, $email, $eventname, $eventdate, $firstname, $logosrc, $order_id, $organiser_name, $organiser_email, $payer, $profile_url, $project, $telephone',
  `message`                       = '
<div style="background-color: #ebebeb; padding-bottom: 50px;">
\n  <table cellspacing="0" cellpadding="0" style="margin: auto">
\n    <tr>
\n      <td align="center">
\n        <img src="$logosrc" style="padding-bottom: 5px;padding-top:1em" alt="$project" />
\n      </td>
\n    </tr>
\n  </table>
\n
\n  <table cellspacing="0" cellpadding="0" style="border: 1px solid #ccc; background-color: #fff; margin: auto; max-width: 540px; font-size: 13px">
\n    <tr>
\n      <td style="padding: 37px 40px;color: #fff; font-size: 26px; font-weight: 200; background-color: #00aa87;">
\n        Your group booking has been created
\n      </td>
\n    </tr>
\n
\n    <tr>
\n      <td style="padding: 10px 40px 40px;">
\n        <p>Hi $firstname.</p>
\n
\n        <p>Thank you for booking with $project. Your order details are shown below for your reference.</p>
\n
\n        <p>You have booked for $eventname on $eventdate.</p>
\n
\n        <p>You can share this link with others in your group so that they may contribute to the payment.
\n
\n        <h2 style="color: #00aa87;font-size: 16px;margin: .5em 0;">Your details</h2>
\n
\n        <table cellspacing"=0" cellpadding="5" style="margin: 0 -5px;">
\n           <tr>
\n             <th scope="row" style="font-weight: bold; padding: 5px; text-align: left;">Name</th>
\n             <td style="padding: 5px;">$buyer</td>
\n           </tr>
\n
\n           <tr>
\n             <th scope="row" style="font-weight: bold; padding: 5px; text-align: left;">Email</th>
\n             <td style="padding: 5px;">$email</td>
\n           </tr>
\n
\n           <tr>
\n             <th scope="row" style="font-weight: bold; padding: 5px; text-align: left;">Tel.</th>
\n             <td style="padding: 5px;">$telephone</td>
\n           </tr>
\n
\n           <tr>
\n             <th scope="row" style="font-weight: bold; padding: 5px; text-align: left;">Address</th>
\n             <td style="padding: 5px;">$address_1 $address_2 $city $county $country</td>
\n           </tr>
\n        </table>
\n
\n        <p>You may edit your details in <a style="color: #00aa87;text-decoration:none" href="$profile_url">your profile</a>.</p>
\n
\n        <h2 style="color: #00aa87;font-size: 16px;margin: .5em 0;">Organiser details</h2>
\n
\n        <table cellspacing"=0" cellpadding="5" style="margin: 0 -5px;">
\n           <tr>
\n             <th scope="row" style="font-weight: bold; padding: 5px; text-align: left;">Name</th>
\n             <td style="padding: 5px;">$organiser_name</td>
\n           </tr>
\n
\n           <tr>
\n             <th scope="row" style="font-weight: bold; padding: 5px; text-align: left;">Email</th>
\n             <td style="padding: 5px;"><a href="mailto:$organiser_email" style="color: #00aa87; text-decoration: none;">$organiser_email</a></td>
\n           </tr>
\n        </table>
\n
\n        <h2 style="color: #00aa87; font-size: 16px; margin: .5em 0;">Payments</h2>
\n
\n        $links
\n
\n        <p>&nbsp;</p>
\n
\n        <p>Your next instalment of $nextpayment is due by $next_due_date. Please make sure that this amount is paid on or before this date to avoid your tickets being cancelled without refund.</p>
\n
\n        <p>Final balance of $finalpayment is due by $final_due_date. Once this is paid then all you have to do is decide what you&#39;re going to wear!</p>
\n
\n        <p>Your tickets are non refundable or exchangeable.</p>
\n
\n        <p>If you need help just reply to this email or visit <a style="color: #00aa87;text-decoration:none" href="$buyer_help_url">Ticket Buyer Help</a>.</p>
\n
\n        <p>Thanks for using $project!</p>
\n
\n        <p style="text-align: center">
\n          <a style="color: #00aa87;text-decoration:none" href="$base_url">$base_url</a>
\n        </p>
\n      </td>
\n    </tr>
\n  </table>
\n</div>'
WHERE
  `name` = 'event-paymentplan-group-booking-created'
;

UPDATE
  `plugin_messaging_notification_templates`
SET
  `date_updated`                  = CURRENT_TIMESTAMP,
  `subject`                       = '$project Order No. $order_id | Payment Invitation',
  `usable_parameters_in_template` = '$amount, $buyer, $comment, $due_date, $email, $eventdate, $eventname, $link, $logosrc, $order_id, $payer, $project',
  `message`                       = '
<div style="background-color: #ebebeb; padding-bottom: 50px;">
\n  <table cellspacing="0" cellpadding="0" style="margin: auto">
\n    <tr>
\n      <td align="center">
\n        <img src="$logosrc" style="padding-bottom: 5px;padding-top:1em" alt="$project" />
\n      </td>
\n    </tr>
\n  </table>
\n
\n  <table cellspacing="0" cellpadding="0" style="border: 1px solid #ccc; background-color: #fff; margin: auto; max-width: 540px; font-size: 13px">
\n    <tr>
\n      <td style="padding: 37px 40px;color: #fff; font-size: 26px; font-weight: 200; background-color: #00aa87;">
\n        Invitation to contribute
\n      </td>
\n    </tr>
\n
\n    <tr>
\n      <td style="padding: 10px 40px 40px;">
\n        <p>Hey $payer</p>
\n        <p>$buyer has invited you to contribute to your ticket for $eventname, $eventdate</p>
\n
\n        <p>$comment</p>
\n
\n        <p>To contribute your share simply follow this <a href=\"$link\">link</a> and complete the booking process.</p>
\n
\n        <p>The next instalment total of $amount is due by $due_date. Please make sure that this amount is paid on or before this date to avoid your tickets being cancelled without refund.</p>
\n
\n        <p>Your tickets are not refundable or exchangeable.</p>
\n
\n        <p>If you have any issues please contact support@uticket.ie</p>
\n      </td>
\n    </tr>
\n  </table>
\n</div>'
WHERE
  `name` = 'event-partial-payment'
;


UPDATE
  `plugin_messaging_notification_templates`
SET
  `date_updated`                  = CURRENT_TIMESTAMP,
  `subject`                       = '$project Order No. $order_id | Payment Complete',
  `usable_parameters_in_template` = '$balance, $buyer, $due_date, $eventdate, $eventname, $firstname, $logosrc, $order_id, $paid, $payer, $payment_id, $project',
  `message`                       = '
<div style="background-color: #ebebeb; padding-bottom: 50px;">
\n  <table cellspacing="0" cellpadding="0" style="margin: auto">
\n    <tr>
\n      <td align="center">
\n        <img src="$logosrc" style="padding-bottom: 5px;padding-top:1em" alt="$project" />
\n      </td>
\n    </tr>
\n  </table>
\n
\n  <table cellspacing="0" cellpadding="0" style="border: 1px solid #ccc; background-color: #fff; margin: auto; max-width: 540px; font-size: 13px">
\n    <tr>
\n      <td style="padding: 37px 40px;color: #fff; font-size: 26px; font-weight: 200; background-color: #00aa87;">
\n        Payment Complete
\n      </td>
\n    </tr>
\n
\n    <tr>
\n      <td style="padding: 10px 40px 40px;">
\n         <p>Hello $firstname.</p>
\n
\n         <p>Thank you for contributing to your ticket for $eventname on $eventdate, booked by $buyer.</p>
\n
\n         <p>You have paid $paid.</p>
\n
\n         <p>Order reference number: $order_id<br />Payment reference number: $payment_id</p>
\n
\n         <p>Final balance of $balance is due by $due_date. Once this is paid, all you have to do is decide what you&#39;re going to wear!</p>
\n
\n         <p>Your tickets are not refundable or exchangeable.</p>
\n
\n         <p>If you have any issues, please contact <a href="mailto:support@uticket.ie">support@uticket.ie</a>.</p>
\n      </td>
\n    </tr>
\n  </table>
\n</div>'
WHERE
  `name` = 'event-partial-payment-completed'
;

UPDATE
  `plugin_messaging_notification_templates`
SET
  `date_updated`                  = CURRENT_TIMESTAMP,
  `subject`                       = '$project Order No. $order_id | Final Payment Complete',
  `usable_parameters_in_template` = '$balance, $buyer, $due_date, $eventdate, $eventname, $firstname, $logosrc, $order_id, $paid, $payer, $payment_id, $project',
  `message`                       = '
<div style="background-color: #ebebeb; padding-bottom: 50px;">
\n  <table cellspacing="0" cellpadding="0" style="margin: auto">
\n    <tr>
\n      <td align="center">
\n        <img src="$logosrc" style="padding-bottom: 5px;padding-top:1em" alt="$project" />
\n      </td>
\n    </tr>
\n  </table>
\n
\n  <table cellspacing="0" cellpadding="0" style="border: 1px solid #ccc; background-color: #fff; margin: auto; max-width: 540px; font-size: 13px">
\n    <tr>
\n      <td style="padding: 37px 40px;color: #fff; font-size: 26px; font-weight: 200; background-color: #00aa87;">
\n        Final Payment Complete
\n      </td>
\n    </tr>
\n
\n    <tr>
\n      <td style="padding: 10px 40px 40px;">
\n         <p>Hello $firstname.</p>
\n
\n         <p>Thank you for contributing to your ticket for $eventname on $eventdate, booked by $buyer.</p>
\n
\n         <p>You have paid $paid.</p>
\n
\n         <p>Order reference number: $order_id<br />Payment reference number: $payment_id</p>
\n
\n         <p>Now that your final payment has been made, all you have to do is decide what you&#39;re going to wear!</p>
\n
\n         <p>Your tickets are not refundable or exchangeable.</p>
\n
\n         <p>If you have any issues, please contact <a href="mailto:support@uticket.ie">support@uticket.ie</a>.</p>
\n      </td>
\n    </tr>
\n  </table>
\n</div>'
WHERE
  `name` = 'event-partial-payment-completed-nobalance'
;