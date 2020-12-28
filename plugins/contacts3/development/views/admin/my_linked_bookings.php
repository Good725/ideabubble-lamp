<style>
    <?php // Usually every second row is a different colour. Because of the hidden rows, it's every fourth row here ?>
    .dataTable-collapse > tbody > tr.history-dropdown:nth-child(4n + 1) {
        background: #f2f2f2;
    }

    .dataTable-collapse > tbody > tr.history-dropdown:nth-child(4n + 3) {
        background: #fff;
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
    <?php $student_wrapper = 'student_wrapper';
    ?>
    <div class="page-title db-bt-rule">
        <div class="title-left">
            <?php $contact = $contacts[0]; ?>
            <h1>My linked bookings</h1>
        </div>
    </div>
<?php endif; ?>

<?php if (count($bookings) > 0): ?>
    <div class="booking-list">
        <div class="table_scroll">
            <table class="table dataTable table-bookings dataTable-collapse linked-bookings-dataTable">
                <thead>
                    <tr>
                        <th class="sorting">Course</th>
                        <th class="sorting">Schedule</th>
                        <th class="sorting">Student</th>
                        <th class="sorting">School</th>
                        <th class="sorting">Phone</th>
                        <th class="sorting">Start Date</th>
                        <th class="sorting">Host Family</th>
                        <th class="sorting">Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($bookings as $contact_id => $contact_bookings): ?>
                    <?php foreach ($contact_bookings as $contact_booking):
                        $contact_booking_info = $contact_booking['contact_booking_info'];
                        $school_name = !empty($contact_booking['school']) ? $contact_booking['school']['name'] : '';
                        $mobile = $contact_booking['mobile'];
                        $host_family = $contact_booking['host_family']; ?>
                        <tr data-contact_id="<?= $contact_booking['student_id']; ?>" data-booking_id="<?= $contact_booking['booking_id'] ?>">
                            <td data-label="Course"><?= $contact_booking['course_title'] ?></td>
                            <td data-label="Schedule"><?= $contact_booking['schedule_title'] ?></td>
                            <td data-label="Student"><?= htmlspecialchars($contact_booking['student']) ?></td>
                            <td data-label="School"><?= htmlspecialchars($school_name) ?></td>
                            <td data-label="Phone"><?= $mobile; ?></td>
                            <td data-label="Start Date"><?= $contact_booking['start_date']; ?></td>
                            <td data-label="Host Family"><?= htmlspecialchars($host_family['name']) ?></td>
                            <td class data-label="Actions">
                                <?php
                                echo View::factory('snippets/btn_dropdown')
                                    ->set('type', 'actions')
                                    ->set('options', [
                                        ['type' => 'button', 'title' => __('Upload document'),  'attributes' => ['class' => 'upload_document', 'data-id' => $contact_booking['booking_id']]],
                                        ['type' => 'button', 'title' => __('View assessments'), 'attributes' => ['class' => 'view-surveys',    'data-id' => $contact_booking['booking_id']]],
                                        ['type' => 'button', 'title' => __('Submit feedback'),  'attributes' => ['class' => 'add-surveys',     'data-id' => $contact_booking['booking_id']]]
                                    ]);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                
                </tbody>
            </table>
        </div>
        <div id="survey_booking_wrapper" class="survey_booking_wrapper"></div>
    </div>

    <script>
    $(document).ready(function(){
        $('.table-bookings .history-dropdown').on('click', function() {
            var booking_id = $(this).data('booking_id');
            $(this).next().removeClass('hidden');
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
    <div class="booking-list">
        <h3><?= __('You have no linked bookings') ?></h3>
    </div>
<?php endif; ?>