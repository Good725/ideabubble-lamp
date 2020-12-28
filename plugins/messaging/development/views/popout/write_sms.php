<?php
$auth = Auth::instance();
$user = $auth->get_user();
?><div id="messaging-sms-write" class="tabs-pills-content">
    <div class="write-email">
        <form class="send-message-form">
            <input type="hidden" name="operation" value="send" />
            <input type="hidden" name="driver" value="sms" />
            <input type="hidden" name="sms[from]" value="<?= $user['id'] ?>" />
            <input type="hidden" name="message_id" />

            <div class="top">
                <div class="message-compose-field">
                    <div class="message-compose-label">From</div>
                    <div class="message-compose-value">
                        <div class="fields-wrap">
                            <input type="text" disabled value="<?= Settings::instance()->get('twilio_phone_number') ?>" />
                        </div>
                    </div>
                </div>

                <div class="message-compose-field">
                    <div class="message-compose-label">To</div>
                    <div class="message-compose-value">
                        <div class="fields-wrap">
                            <input type="text" class="send-sms-to" id="send-sms-to" placeholder="Type to add a contact or contact list" />
                            <div class="contact-list-labels" id="send-sms-to-contact-list"></div>
                        </div>

                        <?php if ($recipient_finder): ?>
                            <div class="message-compose-search">
                                <button type="button"
                                        class="btn-link message-compose-search-btn"
                                        id="message-compose-search-sms-to"
                                        data-type="sms"
                                        data-recipient_type="To"
                                    >
                                    <span class="icon-search"></span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="messaging-sms-add_link" class="message-compose-field add-files-wrap">
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
                <textarea name="sms[message]" placeholder="<?= __('Type your message here') ?>"></textarea>
            </div>

            <?php if (!isset($action_buttons) || $action_buttons !== false): ?>
                <div class="bottom-btn">
                    <div class="btn-group dropup">
                        <button type="submit" class="btn btn-lg btn-outline-primary messaging-popout-send" id="messaging-sidebar-send-sms"><?= __('Send') ?></button>

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

                    <button type="button" class="btn btn-lg btn-cancel basic_close">Cancel</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
