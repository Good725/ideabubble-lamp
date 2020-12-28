/*
ts:2019-04-16 11:09:00
*/

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