<?php
$auth = Auth::instance();
$user = $auth->get_user();
$list_users = Model_Users::get_visible_users();
$access_own_mail    = $auth->has_access('messaging_access_own_mail');
$access_system_mail = $auth->has_access('messaging_access_system_mail');
$access_others_mail = $auth->has_access('messaging_access_others_mail');
?>

<div class="messaging-sidebar-user clearfix">
    <div class="dropdown">
        <button class="btn-link dropdown-toggle" id="messaging-sidebar-select_user-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span id="messaging-sidebar-user-name"><?php
                if ($access_own_mail) {
                    echo trim($user['name'].' '.$user['surname']);
                } else if ($access_system_mail) {
                    echo __('System');
                } else {
                    echo '&nbsp;';
                }
                ?></span>
            <span class="icon-chevron-down"></span>
        </button>

        <ul class="dropdown-menu" aria-labelledby="messaging-sidebar-select_user-btn">
            <?php if ($access_own_mail): ?>
                <li>
                    <label>
                        <span class="messaging-sidebar-select_user-name"><?= trim($user['name'].' '.$user['surname']) ?></span>
                        <input class="messaging-sidebar-select_user-option sr-only" type="radio" name="messaging-sidebar-selected_user" value="<?= $user['id'] ?>" checked="checked" />
                        <span class="fa icon-check" aria-hidden="true"></span>
                    </label>
                </li>
            <?php endif; ?>

            <?php if ($access_system_mail): ?>
                <li>
                    <label>
                        <span class="messaging-sidebar-select_user-name"><?= __('System') ?></span>
                        <input class="messaging-sidebar-select_user-option sr-only" type="radio" name="messaging-sidebar-selected_user" value="system"<?= $access_own_mail ? '' : ' checked="checked"' ?> />
                        <span class="fa icon-check" aria-hidden="true"></span>
                    </label>
                </li>
            <?php endif; ?>

            <?php if ($access_others_mail): ?>
                <?php foreach ($list_users as $list_user): ?>
                    <?php $name = trim($list_user['name'].' '.$list_user['surname']); ?>
                    <?php if ($name): ?>
                        <li>
                            <label>
                                <span class="messaging-sidebar-select_user-name"><?= $name ?></span>
                                <input class="messaging-sidebar-select_user-option sr-only" type="radio" name="messaging-sidebar-selected_user" value="<?= $list_user['id'] ?>" />
                                <span class="fa icon-check" aria-hidden="true"></span>
                            </label>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <?php if($auth->has_access('messaging_send_system_email') ||
        $auth->has_access('messaging_send_system_sms') ||
        $auth->has_access('messaging_send_alerts')): ?>
    <div class="dropdown messaging-sidebar-expand_icon">
        <button class="button--plain dropdown-toggle nounderline" id="messaging-sidebar-send_dropdown-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span class="fa icon-plus-circle" aria-hidden="true"></span>
            <span><?= __('Compose') ?></span>
        </button>

        <ul class="dropdown-menu pull-right" aria-labelledby="messaging-sidebar-send_dropdown-btn">
            <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
                <li><a href="#">Template <span class="fa icon-check" aria-hidden="true"></span></a></li>
            <?php endif; ?>
            <?php if ($auth->has_access('messaging_send_system_email')): ?>
                <li><a class="detail-btn" href="javascript:void(0)" rel="send-email">Compose Email</a></li>
            <?php endif; ?>
            <?php if ($auth->has_access('messaging_send_system_sms')): ?>
                <li><a class="detail-btn" href="javascript:void(0)" rel="send-sms">Compose SMS</a></li>
            <?php endif; ?>
            <?php if ($auth->has_access('messaging_send_alerts')): ?>
                <li><a class="detail-btn" href="javascript:void(0)" rel="send-alert">Compose Alert</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<div class="messaging-sidebar-body">
    <ul>
        <li>
            <a href="/admin/messaging/all" class="messaging-sidebar-open_list" data-name="all" data-params="{}">
                <span class="fa icon-envelope-o" aria-hidden="true"></span>
                <span class="counts"></span>
                <?= __('All') ?>
            </a>
        </li>

        <li>
            <a href="/admin/messaging/inbox" class="messaging-sidebar-open_list" data-name="inbox" data-params="<?= htmlspecialchars(json_encode(array('inbox' => 1))) ?>">
                <span class="fa icon-paper-plane" aria-hidden="true"></span>
                <span class="counts"></span>
                <?= __('Inbox') ?>
            </a>
        </li>

        <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
            <li>
                <a href="/admin/messaging/starred" class="messaging-sidebar-open_list" data-name="starred" data-params="<?= htmlspecialchars(json_encode(array('starred' => '1'))) ?>">
                    <span class="fa icon-star" aria-hidden="true"></span>
                    <span class="counts"></span>
                    <?= __('Starred') ?>
                </a>
            </li>
        <?php endif; ?>

        <li>
            <a href="/admin/messaging/sent" class="messaging-sidebar-open_list" data-name="sent" data-params="<?= htmlspecialchars(json_encode(array('sent' => 1, 'is_draft' => 0))) ?>">
                <span class="fa icon-paper-plane" aria-hidden="true"></span>
                <span class="counts"></span>
                <?= __('Sent') ?>
            </a>
        </li>

        <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
            <li>
                <a href="/admin/messaging/scheduled" class="messaging-sidebar-open_list" data-name="scheduled" data-params=<?= htmlspecialchars(json_encode(array('scheduled' => TRUE))) ?>>
                    <span class="fa icon-calendar" aria-hidden="true"></span>
                    <span class="counts"></span>
                    <?= __('Scheduled') ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($auth->has_access('messaging_access_drafts')): ?>
            <li>
                <a href="/admin/messaging/drafts" class="messaging-sidebar-open_list" data-name="drafts" data-params="<?= htmlspecialchars(json_encode(array('is_draft' => 1))) ?>">
                    <span class="fa icon-file-text-o" aria-hidden="true"></span>
                    <span class="counts"></span>
                    <?= __('Drafts') ?>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
            <li>
                <a href="/admin/messaging/spam" class="messaging-sidebar-open_list" data-name="spam" data-params="<?= htmlspecialchars(json_encode(array('is_spam' => 1))) ?>">
                    <span class="fa icon-bug" aria-hidden="true"></span>
                    <span class="counts"></span>
                    <?= __('Spam') ?>
                </a>
            </li>

            <li>
                <a href="/admin/messaging/mutes">
                    <span class="fa icon-users" aria-hidden="true"></span><?= __('Muted Senders') ?>
                </a>
            </li>

            <li>
                <a href="/admin/messaging/notification_templates">
                    <span class="fa icon-trash" aria-hidden="true"></span><?= __('Trash') ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
        <hr />

        <ul>
            <li>
                <a href="/admin/messaging/settings">
                    <span class="fa icon-cog"  aria-hidden="true"></span><?= __('Settings') ?>
                </a>
            </li>

            <li>
                <a href="/admin/messaging/drivers">
                    <span class="fa icon-wrench" aria-hidden="true"></span>
                    <?= __('Drivers') ?>
                </a>
            </li>
        </ul>

        <hr />

        <ul>
            <li class="dropdown-nav">
                <a class="pullBtn" href="javascript:void(0)">Templates</a>
                <ul class="toggle-box sub--nav">
                    <li><a href="#">Template 1</a></li>
                    <li><a href="#">Template 2</a></li>
                    <li><a href="#">Template 3</a></li>
                    <li><a href="#">Template 4</a></li>
                </ul>
            </li>
        </ul>
    <?php endif; ?>
</div>