/*
ts:2019-10-07 07:11:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`)
VALUES ('new_booking_org_rep', 'Email sent to an org rep after a successful booking.', 'EMAIL', '1');

UPDATE `plugin_messaging_notification_templates`
set `subject` =
    'Thank you for booking with us',
    `message` = '<p>Dear, $customer</p>\n\n
<p>Thank you for booking with $hostname</p>\n\n
<p>Your booking details are listed below.</p>\n\n
<h2>Booking details</h2>\n\n
<table>\n
\t     <tbody>\n
\t\t       <tr>\n
\t\t\t          <th>Schedule name</th>\n
\t\t\t         <td>$schedule_name</td>\n
\t\t      <tr>\n
\t     </tbody>\n
<table>\n\n
<h2>Delegate details</h2>\n\n
<table>\n
\t  <tbody>\n
        $delegate_details\n
\t  </tbody>\n
<table>',
    `usable_parameters_in_template` = '$customer,$hostname,$schedule_name,$delegate_details'
where `name` = 'new_booking_org_rep';

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`)
VALUES ('new_booking_delegate', 'Email sent to a delegate after an org rep booked them onto a course.', 'EMAIL', '1');

UPDATE `plugin_messaging_notification_templates`
set `subject`                       =
        'You have been booked onto a course',
    `message`                       =
'<p>Dear, $customer</p>\n\n
<p>You have been booked onto a course with $hostname by $delegate_name in $organisation_name</p>\n\n
<p>Your booking details are listed below.</p>\n\n
<h2>Booking details</h2>\n\n
<table>\n
\t    <tbody>\n
\t\t        <tr>\n
\t\t\t          <th>Schedule name</th>\n
\t\t\t          <td>$schedule_name</td>\n
\t\t       <tr>\n
\t    </tbody>\n
<table>\n\n',
    `usable_parameters_in_template` = '$customer,$hostname,$schedule_name,$delegate_name,$organisation_name,$schedule_name'
where `name` = 'new_booking_delegate';