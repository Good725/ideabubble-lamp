<div class="top-head">
    <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
        <div class="left">
            <ul class="tabs-pills" data-pane="#messaging-email-pane">
                <li>
                    <a href="javascript:void(0)" rel="#messaging-email-write" title="<?= __('Message') ?>">
                        <i class="fa icon-pencil-square-o" aria-hidden="true"></i>
                        <span class="hide-small-scr"><?= __('Message') ?></span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" rel="#messaging-email-schedule" title="<?= __('Schedule') ?>">
                        <i class="fa icon-clock-o" aria-hidden="true"></i>
                        <span class="hide-small-scr"><?= __('Schedule') ?></span>
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?>

    <h3 class="text-primary messaging-sidebar-message-heading"><?= __('Compose Email') ?></h3>

    <div class="right">
        <ul>
            <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
                <li><a href="javascript:void(0)" class="add-btn" rel="messaging-email-add_link"><i class="fa icon-link" aria-hidden="true"></i></a></li>
            <?php endif; ?>
            <li><a href="javascript:void(0)" class="add-btn" rel="add-attachment"><i class="fa icon-paperclip" aria-hidden="true"></i></a></li>
            <li><a href="javascript:void(0)" class="basic_close"><i class="fa icon-times" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</div>
<!-- write email  -->

<div class="tabs-pills-pane" id="messaging-email-pane">
    <?php
    $message_type = 'email';
    require_once('write_email.php');
    if ($auth->has_access('messaging_see_under_developed_features'))
    {
        include('schedule.php');
    }
    ?>
</div>