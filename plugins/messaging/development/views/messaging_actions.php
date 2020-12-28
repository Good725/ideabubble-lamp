<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="view-dashboard-actions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        Actions <span class="caret"></span>
    </button>

    <ul class="dropdown-menu pull-right">
        <?php $auth = Auth::instance(); ?>

        <?php if ($auth->has_access('messaging_send_system_email')): ?>
            <li><a href="#" data-toggle="modal" data-target="#send-message-modal-email">Compose Email</a></li>
        <?php endif; ?>

        <?php if ($auth->has_access('messaging_send_system_sms')): ?>
            <li><a href="#" data-toggle="modal" data-target="#send-message-modal-sms">Compose SMS</a></li>
        <?php endif; ?>

        <li><a href="#" data-toggle="modal" data-target="#send-message-modal-dashboard">Compose Alert</a></li>
        <li><a href="#" id="mark-messages-as-read">Mark as Read</a></li>
        <li><a href="#" id="mark-messages-as-unread">Mark as Unread</a></li>
        <!-- <li><a href="#" type="button" data-toggle="modal" id="delete-messages-modal-btn"><?= __('Delete selected') ?></a></li> -->
    </ul>
</div>
