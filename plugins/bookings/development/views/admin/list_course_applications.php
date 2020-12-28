<?php
/**
 * List the contacts booking under the contacts and family details in the Contacts view
 */
?>
<div class="row-fluid header list_notes_alert">
    <?= (isset($alert)) ? $alert : '' ?>
</div>
<?php if ( ! empty($applications)): ?>
    <table class="table dataTable contact_applications_table dataTable-collapse">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Course</th>
                <?php if (!empty($has_instrument_column)): ?>
                    <th scope="col">Instrument</th>
                <?php endif; ?>
                <th scope="col">Status</th>
                <th scope="col">Updated</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>

        <tbody style="cursor: pointer;">
            <?php
            $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';

            foreach ($applications as $application) {
            $status = '';
            ?>
                <tr data-application_id="<?= $application['id'] ?>">
                    <td data-label="ID"><?= $application['id']; ?></td>
                    <td data-label="Course"><?= htmlentities($application['course']) ?></td>
                    <?php if (!empty($has_instrument_column)): ?>
                        <td data-label="Instrument"><?= htmlentities(@$application['subject']) ?></td>
                    <?php endif; ?>
                    <td data-label="Status"><?= $application['interview_status'] == null ? $application['status'] : 'Interview ' . $application['interview_status']; ?></td>
                    <td data-label="Updated"><?= date($date_format, strtotime($application['modified_date'])); ?></td>
                    <td data-label="Actions">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn--full dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <span class="sr-only"><?= __('Actions') ?></span>
                                <span class="icon-ellipsis-h" aria-hidden="true"></span>
                            </button>

                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a class="edit" data-application_id="<?= $application['id'] ?>"><?= __('Edit') ?></a>
                                </li>
                                <?php if(Settings::instance()->get('accreditation_application_page')
                                    && (!empty(@$application['form_id']) &&  @$application['form_id']== 'accreditation_application')):?>
                                <?else:?>
                                <li>
                                    <a href="/admin/bookings/interview_details_doc?application_id=<?= $application['application_id'] ?>"
                                       target="_blank"><?= __('Download') ?></a>
                                </li>

                                <li>
                                    <button type="button" class="btn-link"><?= __('Print') ?></button>
                                </li>

                                <li class="dropdown-submenu-toggle">
                                    <?php
                                    $status_groups = [
                                        'application'  => ['label' => 'Application',  'name' => 'application_status_id',  'statuses' => [1 => 'Enquiry',  3 => 'On hold',       5 => 'Accepted']],
                                        'interview'    => ['label' => 'Interview',    'name' => 'interview_status_id',    'statuses' => [1 => 'Pending',  6 => 'No Show',       7 => 'Interviewed']],
                                        'offer'        => ['label' => 'Offer',        'name' => 'offer_status_id',        'statuses' => [1 => 'Pending',  8 => 'Offered',       2 => 'Waiting list',   4 => 'No offer']],
                                        'registration' => ['label' => 'Registration', 'name' => 'registration_status_id', 'statuses' => [1 => 'Pending', 10 => 'Deposit Paid', 11 => 'Awaiting docs', 12 => 'Registered', 13 => 'Deferred']]
                                    ];

                                    if (@$application['application_status'] != null) {
                                        echo View::factory('admin/applications/snippets/change_status_dropdown')
                                            ->set('item_id', $application['id'])
                                            ->set('status_groups', $status_groups)
                                            ->set('application', $application);
                                    }
                                    ?>
                                </li>
                                <?php endif?>

                                <li>
                                    <button
                                        type="button"
                                        data-toggle="modal"
                                        data-target="#application-cancel-confirm-modal"
                                        data-application_id="<?= $application['id'] ?>"
                                    ><?= __('Cancel') ?></button>
                                </li>
                            </ul>
                        </div>

                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php else: ?>
    <p><?= __('There are no applications.') ?></p>
<?php endif; ?>

<div id="fulltime-course-application-wrapper">

</div>

<?php ob_start(); ?>
    <button type="button" class="btn btn-primary" data-dismiss="modal" id="application-cancel-modal-confirm">Yes</button>
    <button type="button" class="btn btn-cancel" data-dismiss="modal">No</button>
<?php $cancel_buttons = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id', 'application-cancel-confirm-modal')
    ->set('title', 'Confirm cancellation')
    ->set('body',  '<p>Are you sure you want to cancel this application?</p>')
    ->set('footer', $cancel_buttons);
?>

<div id="course_application_saved_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Application has been updated.</h3>
            </div>

            <div class="modal-body">
                <p>Application has been updated.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" data-content="">Okay</button>
            </div>
        </div>
    </div>
</div>

