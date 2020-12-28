<?
// Get notification headers and footers for email content
$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('booking'));
$notifications_model = new Model_Notifications($event_id);
?>

<html>
    <body>

        <p>We received payment from our website : <?= $_SERVER['HTTP_HOST'] ?> please see details below.</p>

        <p>Please see a copy of the email issued to the visitor below:</p>


        --------------------- Email Issued to Visitor ------------------------------------

        <?= $notifications_model->get_header(); ?>

        <p>Thank you for your payment through our site : <?= $_SERVER['HTTP_HOST'] ?> please see details below.</p>

        <h2>Payment details:</h2>

        <table>
			<?php if (isset($checkout->payment_ref)): ?>
				<tr>
					<td>Payment Ref No: <?= $checkout->payment_ref; ?></td>
				</tr>
			<?php endif; ?>

			<?php if (isset($checkout->course_name)): ?>
				<tr>
					<td colspan="2">Course Name : <?= $checkout->course_name; ?></td>
				</tr>
			<?php endif; ?>

            <tr>
                <td>Booked: <?= date('F j,  Y,  g:i a') ?> </td>
				<?php if (isset($checkout->location)): ?>
					<td>Venue: <?= $checkout->location; ?></td>
				<?php else: ?>
					<td></td>
				<?php endif; ?>
            </tr>

			<?php if (isset($checkout->student_name)): ?>
				<tr>
					<td colspan="2">Name of Student: <?= $checkout->student_name; ?></td>
				</tr>
			<?php endif; ?>

            <tr>
                <td>Parent Name: <?= isset($checkout->name) ? $checkout->name : ''; ?></td>
                <td>Parent Mobile: <?= isset($checkout->phone) ? $checkout->phone : ''; ?></td>
            </tr>

			<?php if (isset($checkout->email)): ?>
				<tr>
					<td>E-mail: <?= $checkout->email; ?></td>
				</tr>
			<?php endif; ?>

			<?php if (isset($checkout->comments)): ?>
				<tr>
					<td colspan="2">Comments:    <?= is_null($checkout->comments) ? '' : nl2br($checkout->comments) ?></td>
				</tr>
			<?php endif; ?>

            <tr>
                <td>Date of Pay: <?= date('F j,  Y,  g:i a') ?></td>
                <td>Amount Paid: <?= $checkout->payment_total; ?></td>
            </tr>
        </table>

        <?= $notifications_model->get_footer(); ?>

        <h5>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> CMS Engine</h5>

    </body>
</html>
