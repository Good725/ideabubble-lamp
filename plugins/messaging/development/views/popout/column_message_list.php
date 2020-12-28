    <?php $under_developed_features = $auth->has_access('messaging_see_under_developed_features'); ?>
    <div class="top-head">
        <h3 class="text-primary messaging-sidebar-message-heading messaging-sidebar-messages-heading"></h3>
        <div class="right">
            <ul>
                <li class="border-primary">
                    <a href="javascript:void(0)" class="basic_close"><i class="fa icon-times" aria-hidden="true"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="search-wrap">
        <label class="sr-only" for="messaging-sidebar-search"></label>

        <?php $search_term = isset($filters['parameters']['search']) ? $filters['parameters']['search'] : ''; ?>

        <input type="text" id="messaging-sidebar-search" placeholder="<?= __('Search') ?>" value="<?= $search_term ?>" />

        <button class="search-btn" id="messaging-sidebar-search-btn">
            <span class="fa icon-search" aria-hidden="true"></span>
        </button>
    </div>

    <div class="body-area">
        <div class="toptitle">
            <?php if ($under_developed_features): ?>
                <div class="action">
                    <?= Form::ib_checkbox(__('Select All'), NULL, NULL, FALSE, array('class' => 'checked-all')); ?>
                </div>
            <?php endif; ?>

            <div class="dropdown filter right">
                <button type="button" class="btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <?= __('Filter') ?> <span class="fa icon-angle-down" aria-hidden="true"></span>
                </button>

                <ul class="dropdown-menu pull-right filter--nav">
                    <?php
                    $filter_options = array(
                        'all'       => array('label' => __('All'),    'value' => ''),
                        'sms'       => array('label' => __('SMS'),    'value' => 'sms'),
                        'email'     => array('label' => __('E-mail'), 'value' => 'email'),
                        'dashboard' => array('label' => __('Alerts'), 'value' => 'dashboard')
                    )
                    ?>
                    <?php foreach ($filter_options as $key => $filter_option): ?>
                        <?php
                        if (isset($filters['parameters']) AND isset($filters['parameters']['message_type'])) {
                            $checked = ($filter_option['value'] == $filters['parameters']['message_type']);
                        }
                        else {
                            $checked = ($filter_option['value'] == '');
                        }
                        ?>
                        <li>
                            <input
                                type="radio"
                                name="messaging-sidebar-type_filter"
                                value="<?= $filter_option['value'] ?>"
                                class="messaging-sidebar-type_filter sr-only"
                                id="messaging-sidebar-type_filter-<?= $key ?>"
                                <?= isset($filters['parameters']) AND isset($filters['parameters']['message_type']) ?>
                                <?= $checked ? 'checked="checked"' : '' ?>
                                />

                            <label for="messaging-sidebar-type_filter-<?= $key ?>">
                                <?= $filter_option['label'] ?>
                                <span class="fa icon-check" aria-hidden="true"></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <ul class="messaging-sidebar-actions">
                <?php if ($under_developed_features): ?>
                    <li><a href="#"><i class="fa icon-trash" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa icon-bug" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa icon-star-o" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa icon-envelope" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa icon-envelope-open" aria-hidden="true"></i></a></li>
                <?php endif; ?>
            </ul>
        </div>

        <ul class="medialist">
            <?php if ( ! empty( $messages) AND ! empty($messages['messages'])): ?>
                <?php
                $icons = array(
                    'dashboard' => array('read' => 'icon-bell-o',          'unread' => 'icon-bell'),
                    'email'     => array('read' => 'icon-envelope-open-o', 'unread' => 'icon-envelope'),
                    'sms'       => array('read' => 'flaticon-smartphone',  'unread' => 'flaticon-smartphone-message')
                );
                ?>

                <?php foreach ($messages['messages'] as $message): ?>
                    <li<?= ( ! $message['is_read']) ? ' class="unread"' : '' ?> data-id="<?= $message['id'] ?>">
                        <div class="grid">
                            <ul class="user-action">
                                <li>
                                    <span class="read_icon <?=   $icons[$message['driver']]['read']   ?>" aria-hidden="true"></span>
                                    <span class="unread_icon <?= $icons[$message['driver']]['unread'] ?>" aria-hidden="true"></span>
                                </li>
                                <?php if ($under_developed_features): ?>
                                    <li><?= Form::ib_checkbox(null, null, null, false, array('class' => 'input-field')); ?></li>
                                    <li><span class="fa <?= $message['starred'] ? 'icon-star' : 'icon-star-o' ?>" aria-hidden="true"></span></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="grid second">
                            <div class="messaging-sidebar-show_for_global">
                                <h4>from: <?= $message['from'] ?></h4>
                                <h4>to: <?= $message['to'] ?></h4>
                                <?php if (@$message['cc']) { ?>
                                <h4>cc: <?= $message['cc'] ?></h4>
                                <?php } ?>
                                <?php if (@$message['bcc']) { ?>
                                <h4>bcc: <?= $message['bcc'] ?></h4>
                                <?php } ?>
                            </div>

                            <div class="messaging-sidebar-hide_for_global">
                                <h4><?= $message['from'] ?></h4>
                            </div>

                            <p><?= $message['subject'] ?></p>
                        </div>

                        <span class="grid third"><?= IbHelpers::relative_time_with_tooltip($message['sent_started']) ?></span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="sidebar-no_messages"><?= __('No messages to display.') ?></li>
            <?php endif; ?>
        </ul>
    </div>
    <?php $page_number = isset($args['page_number']) ? $args['page_number'] : 1; ?>

    <div class="pagination-wrap" id="messaging-sidebar-pagination">
        <input type="hidden" id="messaging-sidebar-pagination-number" value="<?= $page_number ?>" />

        <span class="grid-1">
            <?= __(
                'Showing $1 to $2 of $3',
                array(
                    '$1' => $messages['offset'] + 1,
                    '$2' => $messages['offset'] + $messages['total_displayed'],
                    '$3' => $messages['total_all']
                ));
            ?>
        </span>

        <ul class="pagination-btn">
            <li>
                <?php // "Previous" page button; disabled on the first page ?>
                <button type="button"<?= ($page_number == 1) ? ' disabled="disabled"' : '' ?> id="messaging-sidebar-pagination-prev">
                    <span class="fa icon-angle-left" aria-hidden="true"></span>
                </button>
            </li>

            <li>
                <?php // "Next" page button; disabled on the last page ?>
                <button type="button"<?= ($messages['offset'] + $messages['total_displayed'] == $messages['total_all']) ? ' disabled="disabled"' : '' ?> id="messaging-sidebar-pagination-next">
                    <span class="fa icon-angle-right" aria-hidden="true"></span>
                </button>
            </li>
        </ul>
    </div>
