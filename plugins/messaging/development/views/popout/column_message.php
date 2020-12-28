<?php $under_developed_features = $auth->has_access('messaging_see_under_developed_features'); ?>
<?php // Read message ?>
    <div class="messaging-sidebar-message mail-wrap hidden" id="messaging-sidebar-message--view"></div>

<?php // Send SMS ?>
<?php if ($auth->has_access('messaging_send_system_sms')): ?>
    <div class="messaging-sidebar-message content-box hidden" id="send-sms">
        <?php require_once('message_sms.php') ?>
    </div>
<?php endif; ?>
<?php // Send email
    if ($auth->has_access('messaging_send_system_email')): ?>
        <div class="messaging-sidebar-message content-box hidden" id="send-email">
            <?php require_once('message_email.php') ?>
        </div>
    <?php endif;
    if ($auth->has_access('messaging_send_alerts')): ?>
        <div class="messaging-sidebar-message content-box hidden" id="send-alert">
            <?php require_once('message_alert.php') ?>
        </div>
    <?php endif; ?>