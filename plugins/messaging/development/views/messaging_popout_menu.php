<div class="user-notifications-wrapper">
    <?php if (count($notifications)): ?>
        <div class="user-notifications-list-wrapper">
            <ul class="px-3 user-notifications-list" id="user-notifications-list">
                <?php foreach ($notifications as $key => $notification): ?>
                    <?php
                    switch ($notification['driver'])
                    {
                        case 'sms'   : $item = 'SMS';     $icon = 'mobile';     break;
                        case 'email' : $item = 'email';   $icon = 'envelope-o'; break;
                        case 'dashboard' : $item = 'alert'; $icon = 'bell-o'; break;
                        default      : $item = 'message'; $icon = 'bell-o';  break;
                    }
                    ?>
                    <li class="d-flex<?= $key == 0 ? '' : ' border-top' ?> py-2" data-message-id="<?= $notification['message_id'] ?>" data-message-final-target-id="<?= $notification['id'] ?>">
                        <div class="pr-2">
                            <img src="<?= URL::get_avatar($notification['sender_id']) ?>" alt="" class="rounded-circle" style="width: 100%;" />
                        </div>

                        <div class="w-100">
                            <h4><?= htmlspecialchars($notification['sender']) ?></h4>

                            <p class="my-1 user-notifications-message" style="font-size: 14px;">
                                <?= (!$notification['is_read']) ? '<b>' : '' ?>
                                    <?php
                                    if (($item == 'message' || $item = 'alert' ) && !empty($notification['subject'])) {
                                        echo htmlentities($notification['subject']);
                                    } else {
                                        echo htmlentities(__('You have received a new $1', ['$1' => __($item)]));
                                    }
                                    ?>
                                <?= (!$notification['is_read']) ? '</b>' : '' ?>
                            </p>

                            <pre hidden>
                                <?php var_dump($notification) ?>
                            </pre>

                            <?php if ($notification['sent_started']): ?>
                                <div class="nowrap">
                                    <time class="user-notification-time" datetime="<?= $notification['sent_started'] ?>" title="<?= $notification['sent_started'] ?>">
                                        <?//= IbHelpers::formatted_time() ?>
                                    </time>
                                </div>
                            <?php endif; ?>

                            <p class="my-1">
                                <button class="btn-link p-0 user-notifications-read" style="font-size: 14px; color: var(--primary)!important;"><?= __('Read message') ?> <span class="icon-angle-right w-auto"></span></button>
                            </p>
                        </div>

                        <?php if ($notification['sent_started']): ?>
                            <div class="nowrap">
                                <time class="user-notification-time" datetime="<?= $notification['sent_started'] ?>" title="<?= $notification['sent_started'] ?>">
                                    <?= IbHelpers::relative_time($notification['sent_started']) ?>
                                </time>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="user-notifications-empty_notice pt-2 px-2 pb-1 text-center">
            <div class="mb-2">
                <svg class="svg-sprite" style="height: 4em;;">
                    <use xlink:href="#sprite-notification"></use>
                </svg>
            </div>
            <p><?= __('You have no notifications.') ?></p>
        </div>
    <?php endif; ?>

    <div class="user-notifications-footer border-top text-center">
        <a class="d-block p-1" href="/admin/messaging"><?= __('More') ?></a>
    </div>
</div>