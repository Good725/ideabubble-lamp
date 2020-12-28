<?php $user = $auth->get_user(); ?>
<div class="top-head">
    <?php if (!empty($under_developed_features)): ?>
        <div class="left">
            <ul class="tabs-pills" data-pane="#messaging-alert-pane">
                <li>
                    <a href="javascript:void(0)" rel="#messaging-alert-write" title="<?= __('Message') ?>">
                        <span class="fa icon-pencil-square-o" aria-hidden="true"></span>
                        <span class="hide-small-scr"><?= __('Message') ?></span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" rel="#messaging-alert-schedule" title="<?= __('Schedule') ?>">
                        <span class="fa icon-clock-o" aria-hidden="true"></span>
                        <span class="hide-small-scr"><?= __('Schedule') ?></span>
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?>

    <h3 class="text-primary messaging-sidebar-message-heading"><?= __('Compose Alert') ?></h3>

    <div class="right">
        <ul>
            <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
                <li><a href="javascript:void(0)" rel="messaging-alert-add_link" class="add-btn"><i class="fa icon-link" aria-hidden="true"></i></a></li>
            <?php endif; ?>
            <li><a href="javascript:void(0)" class="basic_close"><i class="fa icon-times" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</div>

<div class="tabs-pills-pane" id="messaging-alert-pane">
    <div id="messaging-alert-write" class="tabs-pills-content">
        <div class="write-email">
            <form class="send-message-form">
                <input type="hidden" name="operation" value="send" />
                <input type="hidden" name="driver" value="dashboard" />
                <input type="hidden" name="dashboard[from]" value="<?= $user['id'] ?>" />
                <input type="hidden" name="message_id" />

                <div class="top">
                    <div class="message-compose-field">
                        <div class="message-compose-label">From</div>
                        <div class="message-compose-value">
                            <?php
                            $sender_options = [];
                            $sender_options['user'] = $user['email'];
                           ?>
                            <div class="fields-wrap">
                                <?= Form::ib_select(null, 'alert[from]', $sender_options, $selected, $attributes); ?>
                            </div>
                        </div>
                    </div>
                    <div class="message-compose-field">
                        <div class="message-compose-label">To</div>
                        <div class="message-compose-value">
                            <div class="fields-wrap">
                                <input type="text" class="send-dashboard-to" id="send-dashboard-to" placeholder="Type to add contact or contact list" />
                                <div class="contact-list-labels" id="send-dashboard-to-contact-list"></div>
                            </div>
                        </div>
                    </div>

                    <div id="messaging-alert-add_link" class="message-compose-field add-files-wrap">
                        <div class="message-compose-label">Link to</div>
                        <div class="message-compose-value">
                            <div class="fields-wrap">
                                <div class="link-wrap">
                                    <div class="grid">Select</div>
                                    <div class="grid">
                                        <?= Form::ib_select(NULL, NULL, array('contacts' => __('Contacts'))); ?>
                                    </div>
                                    <div class="grid">
                                        <a href="#" class="btn btn-lg btn-primary-outline d-block p-2">Add</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="middle">
                    <?= Form::ib_textarea(null, 'dashboard[message]', '', array('rows' => '3', 'placeholder' => 'Type your message here')) ?>
                </div>

                <div class="bottom-btn dropup">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-lg btn-outline-primary messaging-popout-send">Send</button>

                        <?php if (!empty($under_developed_features) || $auth->has_access('messaging_access_drafts')): ?>
                            <button type="button" class="btn btn-lg btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <?php if (!empty($under_developed_features)): ?>
                                    <li><a href="#">Save as Template</a></li>
                                <?php endif; ?>

                                <?php if ($auth->has_access('messaging_access_drafts')): ?>
                                    <li><button type="button" class="messaging-popout-save_as_draft">Save as Draft</button></li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>

                    </div>

                    <button type="button" class="btn-cancel basic_close">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (!empty($under_developed_features))
    {
        $message_type = 'alert';
        include('schedule.php');
    }
    ?>
</div>