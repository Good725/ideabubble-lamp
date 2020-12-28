<div class="top-head">
    <?php if (!empty($under_developed_features)): ?>
        <div class="left">
            <ul class="tabs-pills" data-pane="#messaging-sms-pane">
                <li>
                    <a href="javascript:void(0)" rel="#messaging-sms-write" title="<?= __('Message') ?>">
                        <span class="fa icon-pencil-square-o" aria-hidden="true"></span>
                        <span class="hide-small-scr"><?= __('Message') ?></span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" rel="#messaging-sms-schedule" title="<?= __('Schedule') ?>">
                        <span class="fa icon-clock-o" aria-hidden="true"></span>
                        <span class="hide-small-scr"><?= __('Schedule') ?></span>
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?>

    <h3 class="text-primary messaging-sidebar-message-heading"><?= __('Compose SMS') ?></h3>

    <div class="right">
        <ul>
            <?php if (!empty($under_developed_features)): ?>
                <li><a href="javascript:void(0)" rel="messaging-sms-add_link" class="add-btn"><i class="fa icon-link" aria-hidden="true"></i></a></li>
            <?php endif; ?>
            <li><a href="javascript:void(0)" class="basic_close"><i class="fa icon-times" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</div>

<div class="tabs-pills-pane" id="messaging-sms-pane">
    <?php
    $message_type = 'sms';
    require_once('write_sms.php');
    if (!empty($under_developed_features)) {
        include('schedule.php');
    }
    ?>
</div>