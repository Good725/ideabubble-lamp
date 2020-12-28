<?php
$auth = Auth::instance();
$user = $auth->get_user();
$access_own_mail = Auth::instance()->has_access('messaging_access_own_mail');
?>
<div id="messaging-email-write" class="tabs-pills-content">
    <div class="write-email">
        <form class="send-message-form">
            <input type="hidden" name="operation" value="send" />
            <input type="hidden" name="driver" value="email" />
            <input type="hidden" name="email[from]" value="<?= $access_own_mail ? $user['id'] : Settings::instance()->get('default_email_sender') ?>" />
            <input type="hidden" name="message_id" />

            <div class="top">
                <div class="message-compose-field">
                    <div class="message-compose-label">From</div>
                    <div class="message-compose-value">
                        <?php
                        if (isset($from) && $from = 'phpmail_from_email') {
                            $sender_options['phpmail_from_email'] =  Settings::instance()->get('phpmail_from_email');
                        }
                        else {
                            $sender_options = [];
                            $sender_options['user'] = $access_own_mail ? $user['email'] : Settings::instance()->get('default_email_sender');
                            if (Auth::instance()->has_access('messaging_access_system_mail')) {
                                $sender_options['phpmail_from_email'] = Settings::instance()->get('phpmail_from_email');
                            }
                        }
                        ?>
                        <div class="fields-wrap">
                            <?= Form::ib_select(null, 'email[from]', $sender_options, 'user', $attributes); ?>
                        </div>
                    </div>
                </div>

                <div class="message-compose-field">
                    <div class="message-compose-label">To</div>
                    <div class="message-compose-value">
                        <div class="fields-wrap">
                            <input type="text" class="send-email-to" id="send-email-to" placeholder="Type to add a contact or contact list" />
                            <div class="contact-list-labels" id="send-email-to-contact-list"></div>
                        </div>

                        <?php if ($recipient_finder): ?>
                            <div class="message-compose-search">
                                <button type="button"
                                        class="btn-link message-compose-search-btn"
                                        id="message-compose-search-email-to"
                                        data-type="email"
                                        data-recipient_type="To"
                                    >
                                    <span class="icon-search"></span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="message-compose-cc-toggles fields-wrap">
                            <a class="add-btn message-compose-cc-toggle" rel="cc" href="javascript:void(0)">CC...</a>
                            <a class="add-btn message-compose-cc-toggle" rel="bcc" href="javascript:void(0)">BCC...</a>
                        </div>
                    </div>
                </div>

                <div id="cc" class="message-compose-field add-files-wrap">
                    <div class="message-compose-label">CC</div>

                    <div class="message-compose-value">
                        <div class="fields-wrap">
                            <input type="text" class="send-email-cc" id="send-email-cc" placeholder="Type to add a contact or contact list" />
                            <div class="contact-list-labels" id="send-email-cc-contact-list"></div>
                        </div>

                        <?php if ($recipient_finder): ?>
                            <div class="message-compose-search">
                                <button type="button"
                                        class="btn-link message-compose-search-btn"
                                        id="message-compose-search-email-cc"
                                        data-type="email"
                                        data-recipient_type="CC"
                                    >
                                    <span class="icon-search"></span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="bcc" class="message-compose-field add-files-wrap">
                    <div class="message-compose-label">BCC</div>

                    <div class="message-compose-value">
                        <div class="fields-wrap">
                            <input type="text" class="send-email-bcc" id="send-email-bcc" placeholder="Type to add a contact or contact list" />
                            <div class="contact-list-labels" id="send-email-bcc-contact-list"></div>
                        </div>

                        <?php if ($recipient_finder): ?>
                            <div class="message-compose-search">
                                <button type="button"
                                        class="btn-link message-compose-search-btn"
                                        id="message-compose-search-email-bcc"
                                        data-type="email"
                                        data-recipient_type="BCC"
                                    >
                                    <span class="icon-search"></span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="message-compose-field">
                    <div class="message-compose-label">Template</div>
                    <div class="message-compose-value">
                        <div class="fields-wrap">
                            <?php
                                $filter = [];
                                if (@$last_sent_all != true){
                                    $filter = ['last_sent' => ['op' => 'is not', 'value' => null]];
                                }
                                $notification_templates = Model_Messaging::notification_template_list(true,
                                    $filter,
                                    ['column' => 'last_sent', 'direction' => 'desc']);
                                $select_options = ['' => ''];
                                foreach ($notification_templates as $notification_template) {
                                    if ($notification_template['driver'] === 'email') {
                                        $select_options[$notification_template['id']] = $notification_template['name'];
                                    }
                                }
                                echo Form::ib_select(null, 'message_template_select', $select_options,
                                    null, [
                                        'class' => 'ib-combobox form-control ib-event-handler',
                                        'id' => 'message_template_select'
                                    ]);
                            ?>
                        </div>
                    </div>
                </div>

                <div class="message-compose-field">
                    <div class="message-compose-label">Subject</div>
                    <div class="message-compose-value">
                        <div class="fields-wrap">
                            <input type="text" name="email[subject]" placeholder="Subject">
                        </div>
                    </div>
                </div>

                <div id="messaging-email-add_link" class="message-compose-field add-files-wrap">
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


                <div id="messaging-email-add_link" class="message-compose-field add-files-wrap">
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
                <?php $signature = isset($user['default_messaging_signature']) ? trim($user['default_messaging_signature']) : ''; ?>
                <textarea class="ckeditor-email" id="messaging-sidebar-email-message" name="email[message]" placeholder="Type your message here"><?= $signature ?></textarea>
            </div>

            <?php if (!isset($action_buttons) || $action_buttons !== false): ?>
                <div class="bottom-btn">
                    <div class="btn-group dropup">
                        <button type="submit" class="btn btn-lg btn-outline-primary messaging-popout-send" id="messaging-sidebar-send-email">Send</button>

                        <?php if (!empty($under_developed_features) || $auth->has_access('messaging_access_drafts')): ?>
                            <button type="button" class="btn btn-lg btn-primary dropdown-toggle" data-toggle="dropdown">
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

                    <button type="button" class="btn btn-lg btn-cancel basic_close">Cancel</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
