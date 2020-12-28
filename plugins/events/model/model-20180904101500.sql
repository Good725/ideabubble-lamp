/*
ts:2018-09-04 10:15:00
*/

ALTER TABLE plugin_events_orders_payments_has_partial_payments ADD COLUMN paymentplan_id INT;
ALTER TABLE plugin_events_orders_items ADD COLUMN sleeping INT;
INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `driver`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('event-partial-payment-completed', 'EMAIL', 'Event Payment Done', 'Thank you for contributing to your ticket for ($eventname, $eventdate) booked by ($buyer)\r\n\r\nYou have paid ($paid)\r\n\r\nFinal balance of ($balance) is due by ($due_date). Once this is paid then all you have to do is decide what you\'re going to wear!\r\n\r\nYour tickets are non refundable or exchangeable as per our (terms and conditions).\r\n\r\nIf you have any issues please contact support@uticket.ie', 'Events', '$eventname,$eventdate,$buyer,$payer,$paid,$balance,$due_date', 'events');

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `driver`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('event-partial-payment-completed-nobalance', 'EMAIL', 'Event Payment Done', 'Thank you for contributing to your ticket for ($eventname, $eventdate) booked by ($buyer)\r\n\r\nYou have paid ($paid)\r\n\r\nAll you have to do is decide what you\'re going to wear!\r\n\r\nYour tickets are non refundable or exchangeable as per our (terms and conditions).\r\n\r\nIf you have any issues please contact support@uticket.ie', 'Events', '$eventname,$eventdate,$buyer,$payer,$paid,$balance,$due_date', 'events');


INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `driver`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('event-paymentplan-group-booking-created', 'EMAIL', 'Event Booking', 'Thank you for booking your tickets for ($eventname, $eventdate) \r\n\r\nYou can now share this link with others in your group so they can contribute to the price.\r\n\r\n$links\r\n\r\nYour next instalment of ($nextpayment) is due by ($next_due_date). Please make sure that this amount is paid on or before this date to avoid your tickets being cancelled without refund.\r\n\r\nFinal balance of ($finalpayment) is due by ($final_due_date). Once this is paid then all you have to do is decide what you\'re going to wear!\r\n\r\nYour tickets are non refundable or exchangeable as per our (terms and conditions).\r\n\r\nIf you have any issues please contact support@uticket.ie', 'Events', '$eventname,$eventdate,$buyer,$payer,$nextpayment,$next_due_date,$finalpayment,$final_due_date', 'events');


UPDATE `plugin_messaging_notification_templates` SET `message`='Hey $payer<br />\r\n<br />\r\n$buyer has invited you to contribute to your ticket for $eventname, $eventdate<br />\r\n<br />\r\n$comment<br />\r\n<br />\r\nTo contribute your share simply follow this <a href=\"$link\">link</a> and complete the booking process.<br />\r\n<br />\r\nThe next instalment total of $amount is due by $due_date. Please make sure that this amount is paid on or before this date to avoid your tickets being cancelled without refund.<br />\r\n<br />\r\nYour tickets are non refundable or exchangeable as per our (terms and conditions).<br />\r\n<br />\r\nIf you have any issues please contact support@uticket.ie\r\n', `usable_parameters_in_template`='$buyer,$payer,$email,$due_date,$eventname,$eventdate,$amount,$link,$comment' WHERE (`name`='event-partial-payment');
UPDATE `plugin_messaging_notification_templates` SET `message`='Thank you for contributing to your ticket for $eventname, $eventdate booked by $buyer<br />\r\n<br />\r\nYou have paid $paid<br />\r\n<br />\r\nFinal balance of $balance is due by $due_date. Once this is paid then all you have to do is decide what you\'re going to wear!<br />\r\n<br />\r\nYour tickets are non refundable or exchangeable as per our (terms and conditions).<br />\r\n<br />\r\nIf you have any issues please contact support@uticket.ie' WHERE (`name`='event-partial-payment-completed');
UPDATE `plugin_messaging_notification_templates` SET `message`='Thank you for contributing to your ticket for $eventname, $eventdate booked by $buyer<br />\r\n<br />\r\nYou have paid $paid<br />\r\n<br />\r\nAll you have to do is decide what you\'re going to wear!<br />\r\n<br />\r\nYour tickets are non refundable or exchangeable as per our (terms and conditions).<br />\r\n<br />\r\nIf you have any issues please contact support@uticket.ie' WHERE (`name`='event-partial-payment-completed-nobalance');
UPDATE `plugin_messaging_notification_templates` SET `message`='Thank you for booking your tickets for $eventname, $eventdate <br />\r\n<br />\r\nYou can now share this link with others in your group so they can contribute to the price.<br />\r\n<br />\r\n$links<br />\r\n<br />\r\nYour next instalment of $nextpayment is due by $next_due_date. Please make sure that this amount is paid on or before this date to avoid your tickets being cancelled without refund.<br />\r\n<br />\r\nFinal balance of ($finalpayment) is due by ($final_due_date). Once this is paid then all you have to do is decide what you\'re going to wear!<br />\r\n<br />\r\nYour tickets are non refundable or exchangeable as per our (terms and conditions).<br />\r\n<br />\r\nIf you have any issues please contact support@uticket.ie' WHERE (`name`='event-paymentplan-group-booking-created');

ALTER TABLE plugin_events_orders_payments_has_partial_payments ADD COLUMN commission_total DECIMAL(10, 2);
ALTER TABLE plugin_events_orders_payments_has_partial_payments ADD COLUMN vat_total DECIMAL(10, 2);
ALTER TABLE plugin_events_orders_payments_has_partial_payments ADD COLUMN total DECIMAL(10, 2);
