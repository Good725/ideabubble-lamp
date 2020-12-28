<style>
    .transactions-history,
    .attendees-table {
        border: 1px solid #eee;
        position: absolute;
        background: #fff;
        width: calc(100% - 2rem);
        left: 1rem;
        padding: 1rem;
    }

    .table.table .dataTables_paginate a {
        padding-left: 15px;
        padding-right: 15px;
    }
</style>

<?php if (Auth::instance()->has_access('contacts3_limited_family_access')): ?>
    <?php
    if (isset($contacts[0])) {
        echo View::factory('frontend/snippets/family_members')
            ->set('family', $contacts[0]['family_id'])
            ->set('attributes', array('class' => 'bookings-select_contact'));
    }
    ?>
<?php else: ?>
    <?php $student_wrapper = 'student_wrapper'; ?>
    <?php $contact = $contacts[0]; ?>
    <h2><?= trim($contact['first_name'].' '.$contact['last_name']) ?>’s bookings</h2>
<?php endif; ?>

<?php foreach ($contacts as $contact): ?>
    <?php if (count($bookings[$contact['id']]) > 0): ?>
        <div class="booking-list" data-contact_id="<?= $contact['id'] ?>">
            <h3><?= __('$1\'s bookings', array('$1' => $contact['first_name'])) ?></h3>

            <div class="table_scroll">
               <table class="table table-striped dataTable table-bookings dataTable-collapse">
                    <thead>
                        <tr>
                            <?php // The columns in the table
                            $num_columns = 0;
                            $columns = [['label' => 'Student', 'permission' => ''], ['label' => 'Course', 'permission' => ''],
                                ['label' => 'Type', 'permission' => ''], ['label' => 'Booking date', 'permission' => ''],
                                ['label' => 'Start date', 'permission' => ''], ['label' => 'Status', 'permission' => 'contacts3_frontend_accounts'],
                                ['label' => 'Outstanding', 'permission' => 'contacts3_frontend_accounts'],
                                ['label' => 'Invoice', 'permission' => 'contacts3_frontend_accounts'],
                                ['label' => 'Actions', 'permission' => '']]; ?>
                            <?php foreach($columns as $column) {
                                if(!empty($column['permission']) && !Auth::instance()->has_access($column['permission'])) {
                                    continue;
                                } else {
                                    echo "<th scope='col'>{$column['label']}</th>";
                                    $num_columns ++;
                                }
                            } ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($bookings[$contact['id']] as $booking): ?>
                            <?php
                            if (!$booking['outstanding']) {
                                $booking['outstanding'] = 0.0;
                            }
                            $billed = ($booking['student_id'] == $booking['paying_contact'] OR $booking['student_family'] == $booking['paying_family']) ? FALSE : TRUE;
                            $status = '';
                            if ($billed && $booking['paying_contact']) {
                                $contact = new Model_Contacts3($booking['paying_contact']);
                                $status .= ' Billed to ' . $contact->get_contact_name();
                            } else {
                                if ($booking['outstanding'] == 0) {
                                    $status = 'Completed';
                                } else if ($booking['outstanding'] > 0) {
                                    $status = ' Outstanding';
                                } else if ($booking['outstanding'] < 0) {
                                    $status = ' Over Payed';
                                }
                            }
                            ?>
                            <tr class="history-dropdown" data-booking_id="<?= $booking['booking_id'] ?>">
                                <td data-label="Student"><?= $booking['first_name']?>

                                    <br /><br />

                                    <?php if(count(@$booking['delegates']) > 0): ?>
                                        <div class="attendees-table toggle-hide-bookings hidden" data-booking_id="<?=$booking['booking_id']?>">
                                            <div class="contact-dt">
                                                <h3>Attendees</h3>
                                            </div>

                                            <table class="table dataTable dataTable-collapse">
                                                <thead>
                                                <tr>
                                                    <th class="sorting_asc">Name</th>
                                                    <th class="sorting">Email</th>
                                                    <th class="sorting">Mobile</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php foreach ($booking['delegates'] as $delegate): ?>
                                                    <tr>
                                                        <td data-label="Name"><?= $delegate['first_name'] . ' ' . $delegate['last_name'] ?></td>
                                                        <td data-label="Email"><?= $delegate['email'] ?></td>
                                                        <td data-label="Mobile"><?= !empty($delegate['country_dial_code']) ? '+'.$delegate['country_dial_code'].$delegate['dial_code'].$delegate['mobile'] : $delegate['mobile']?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>


                                    <?php if(Auth::instance()->has_access('contacts3_frontend_accounts')): ?>
                                        <div class="transactions-history toggle-hide-bookings hidden" data-booking_id="<?=$booking['booking_id']?>">
                                            <div class="contact-dt">
                                                <h3>Transactions</h3>
                                            </div>

                                            <table class="table dataTable dataTable-collapse">
                                                <thead>
                                                <tr>
                                                    <th class="sorting_asc">Date</th>
                                                    <th class="sorting">Type</th>
                                                    <th class="sorting">Price</th>
                                                    <th class="sorting">Fee</th>
                                                    <th class="sorting">Discount</th>
                                                    <th class="sorting">Status</th>
                                                    <th class="sorting">Outstanding</th>
                                                    <th class="sorting">Actions</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php foreach ($booking['transactions'] as $transaction): ?>
                                                    <tr>
                                                        <td data-label="Date"><?= $transaction['created'] ? htmlentities(date('H:i j F Y', strtotime($transaction['created']))) : '' ?></td>
                                                        <td data-label="Type"><?= $transaction['type'] ?></td>
                                                        <td data-label="Price">&euro;<?= $transaction['amount'] ?></td>
                                                        <td data-label="Fee">&euro;<?= $transaction['fee'] ?></td>
                                                        <td data-label="Discount">&euro;<?= $transaction['discount'] ?></td>
                                                        <td data-label="Status"><?= $transaction['outstanding'] == 0 ? 'Completed' : 'Outstanding' ?></td>
                                                        <td data-label="Outstanding">&euro;<?=number_format($transaction['outstanding'], 2, '.', ',')?></td>
                                                        <td data-label="Actions">
                                                            <?php
                                                            if ($transaction['outstanding'] > 0) {
                                                                ?>
                                                                <a href="/pay-online.html?booking_id=<?=$booking['booking_id']?>&contact_id=<?=$booking['student_id']?>&transaction_id=<?=$transaction['id']?>&amount=<?=$transaction['outstanding']?>" ><?=__('Pay Now')?></a>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Course"><?= $booking['course_title'] ?></td>
                                <td data-label="Type"><?= $booking['study_mode'] ?></td>
                                <td data-label="Booking date"><?= $booking['date_created'] ? htmlentities(date('H:i j F Y', strtotime($booking['date_created']))) : '' ?></td>
                                <td data-label="Start date"><?=   $booking['start_date']   ? htmlentities(date('H:i j F Y', strtotime($booking['start_date'])))   : '' ?></td>
                                <?php if(Auth::instance()->has_access('contacts3_frontend_accounts')): ?>
                                    <td data-label="Status"><?= $status ?></td>
                                    <td data-label="Outstanding"><?= $booking['outstanding'] ?></td>
                                <?php endif; ?>
                                <td data-label="Invoice"><?=$contact['id'] == $booking['paying_contact'] ? @$booking['invoice'] : ''?></td>
                                <td data-label="Actions" style="vertical-align: bottom;">
                                    <?php if ($booking['study_mode'] == 'Online'):
                                        $options = [];
                                        if (!empty($booking['schedules']) && !empty($booking['schedules'][0])) {
                                            $schedule = new Model_Course_Schedule($booking['schedules'][0]['id']);
                                            $complete_lessons = $schedule->content->count_user_complete_subsections();
                                            $options[] = ['type' => 'link',   'icon' => 'study-mode', 'title' => $complete_lessons ? 'Continue' : 'Start',   'attributes' => ['class' => 'edit-link', 'href' => "/admin/courses/my_course/{$booking['schedules'][0]['id']}"]];
                                        }

                                        echo View::factory('snippets/btn_dropdown')
                                            ->set('type', 'actions')
                                            ->set('options', $options)->render(); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
        $(document).ready(function(){
            $('.table-bookings .history-dropdown').on('click', function() {
                var booking_id = $(this).data('booking_id');
                $(this).next().removeClass('hidden');

                var $transactions = $(this).find('.transactions-history').removeClass('hidden');
                var $attendees = $(this).find('.attendees-table').removeClass('hidden').css('margin-top', $transactions.outerHeight());

                $(this).find('> td:first-child').css('padding-bottom', $transactions.outerHeight() + $attendees.outerHeight());
            });

            $('.bookings-select_contact').on('change', '.family-member-checkbox', function()
            {
                var contact_id        = $(this).data('contact_id');
                var $contact_bookings = $('.booking-list[data-contact_id="'+contact_id+'"]');

                if (this.checked) {
                    $contact_bookings.removeClass('hidden');
                } else {
                    $contact_bookings.addClass('hidden');
                }
            });
        })
        </script>
    <?php else: ?>
        <div class="booking-list" data-contact_id="<?= $contact['id'] ?>">
            <h3><?= __('$1 has no bookings', array('$1' => $contact['first_name'])) ?></h3>
        </div>
    <?php endif; ?>
<?php endforeach; ?>