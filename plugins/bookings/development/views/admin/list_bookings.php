<?php
/**
 * List the contacts booking under the contacts and family details in the Contacts view
 */
?>
<div class="row-fluid header list_notes_alert">
    <?= (isset($alert)) ? $alert : '' ?>
</div>
<?php if ( ! empty($bookings)): ?>
    <table class="table dataTable dataTable-collapse contact_bookings_table">
        <thead>
            <tr>
                <th scope="col">Booking ID</th>
                <th scope="col">First Name</th>
                <th scope="col">Schedule</th>
                <th scope="col">Course</th>
                <th scope="col">Year</th>
                <th scope="col">Level</th>
                <th scope="col">Category</th>
                <th scope="col">Type</th>
                <th scope="col">Next Timeslot</th>
                <th scope="col">
                    <abbr title="Quantity of delegates">Qty</abbr>
                </th>
                <th scope="col">Booking Total</th>
                <th scope="col">Booking Status</th>
                <th scope="col">Payment Type</th>
                <th scope="col">Booking Outstanding</th>
                <th scope="col">Created</th>
                <th scope="col">Updated</th>
                <th scope="col">Invoice</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';

                foreach($bookings as $key=>$booking):
                    if (!$booking['outstanding']) {
                        $booking['outstanding'] = 0.0;
                    }
                    $billed = ($booking['student_id'] == $booking['paying_contact'] OR $booking['student_family'] == $booking['paying_family']) ? FALSE : TRUE ;

                    $status = '';
                    if ($billed && $booking['paying_contact'])
                    {
                        $contact = new Model_Contacts3($booking['paying_contact']);
                        $status .= ' Billed to ' . $contact->get_contact_name();
                    }
                    else
                    {
                        if ($booking['outstanding'] == 0)
                        {
                            $status = 'Completed';
                        }
                        else if ($booking['outstanding'] > 0)
                        {
                            $status = ' Outstanding' ;
                        }
                        else if ($booking['outstanding'] < 0)
                        {
                            $status = ' Over Payed';
                        }
                    }

            ?>
            <tr data-booking_id= "<?= $booking['booking_id']  ?>" data-multiple_transaction="<?=$booking['multiple_transaction'] ;?>"  data-outstanding="<?=$booking['outstanding']?>" data-status="<?= $status;?>">
                <td data-label="Booking Id"><?=$booking['booking_id'];?></td>
                <td data-label="First name"><?=$booking['first_name'];?></td>
                <td data-label="Schedule"><?=$booking['schedule_title'];?></td>
                <td data-label="Course"><?=$booking['course_title'];?></td>
                <td data-label="Year"><?=$booking['year'];?></td>
                <td data-label="Level"><?=$booking['level'];?></td>
                <td data-label="Category"><?=$booking['category'];?></td>
                <td data-label="Type"><?=$booking['type'];?></td>
                <td data-label="Type"><?=$booking['next_timeslot'];?></td>
                <td data-label="Number of delegates"><?= count(Model_KES_Bookings::get_delegates($booking['booking_id'])) ?></td>
                <td data-label="Booking Total"><?='€' . $booking['total']?></td>
                <td data-label="Status"><?= $booking['status'];?></td>
                <td data-label="Payment Type"><?= @ucfirst($booking['payment_type'])?></td>
                <?php if (Settings::instance()->get('navision_api_booking_outstanding')):?>
                    <td data-label="Booking Outstanding" class="navapi-outstanding">-</td>
                <?php else:?>
                    <td data-label="Booking Outstanding"><?= '€'.$booking['outstanding'];?></td>
                <?php endif?>
                <td data-label="Created"><?= IbHelpers::relative_time_with_tooltip($booking['date_created']) ?></td>
                <td data-label="Updated"><?= IbHelpers::relative_time_with_tooltip($booking['last_modified']) ?></td>
                <td data-label="Invoice"><?=@$booking['nav_invoice']?></td>

            </tr>
            <?php
                endforeach;
            ?>
        </tbody>
    </table>

<?php else: ?>
    <p>There are no bookings.</p>
<?php endif; ?>
