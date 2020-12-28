<div class="dropdown">
    <button class="btn btn-outline-primary btn--full dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <span class="sr-only"><?= __('Actions') ?></span>
        <span class="icon-ellipsis-h" aria-hidden="true"></span>
    </button>

    <ul class="dropdown-menu pull-right">
        <?php if (!empty($stage) && $stage == 'interview'): ?>
            <li>
                <button class="btn-link" data-toggle="modal" data-target="#applications-interviews-edit-modal" data-booking_id="<?= $application->booking_id ?>">
                    <?= __('Edit interview') ?>
                </button>
            </li>
        <?php endif; ?>

        <li>
            <a href="/admin/contacts3?contact=<?= $application->booking->contact_id ?>" target="_blank"><?= __('View contact') ?></a>
        </li>

        <li class="dropdown-submenu-toggle">
            <?php
            echo View::factory('admin/applications/snippets/change_status_dropdown')
                ->set('application', $application)
                ->set('status_groups', $status_groups)
            ?>
        </li>

        <li>
            <a href="/admin/bookings/interview_details_doc?booking_id=<?= $application->booking_id ?>" target="_blank"><?= __('Download Application') ?></a>
        </li>
    </ul>
</div>