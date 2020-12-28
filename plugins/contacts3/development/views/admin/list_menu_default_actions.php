<div class="btn-group pull-right m-1">
    <a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle">Actions<span class="caret"></span></a>

    <ul class="dropdown-menu">
        <?php if (@$tab == 'applications'): ?>
            <?php if (Settings::instance()->get('courses_enable_bookings') == 1): ?>
                <li>
                    <?php if (Kohana::$config->load('config')->fulltime_course_booking_enable): ?>
                        <a class="new_fulltime_application">Create Fulltime Application</a>
                    <?php else: ?>
                        <a class="new_application">Create Application</a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php elseif ($contact == 'true'): ?>
            <li>
                <a href="/admin/bookings/add_edit_booking/<?= ($contact_id == '') ? 'new' : '?contact='.$contact_id ?>"
                   target="_blank" class="add_contact_booking" data-contact-id="<?=$contact_id?>">Add Booking</a>
            </li>

            <li>
                <a href="/admin/bookings/add_edit_booking/?contact=<?=$contact_id ?>&transfer_booking_id=all"
                   target="_blank" class="add_contact_booking" data-contact-id="<?=$contact_id?>">Transfer Bookings</a>
            </li>

            <?php if (Auth::instance()->has_access('show_accounts_tab_bookings')): ?>
                <li>
                    <a class="make_booking_payment">Make Payment</a>
                </li>
            <?php endif?>
        <?php endif; ?>

        <?php if ($contact == 'true' || $tab == 'notes'): ?>
            <?php if (Auth::instance()->has_access('contacts3_notes')): ?>
                <li>
                    <a href="#" class="add_note_btn" id="add_family_member_note_btn" data-table="contacts">Add note</a>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($contact == 'true' || $tab == 'todos'): ?>
            <?php if (Auth::instance()->has_access('contacts3_tasks')): ?>
                <li>
                    <a href="#" id="add_family_member_todo_btn">Add task</a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>