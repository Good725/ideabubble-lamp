<?php $recipient_finder = Settings::instance()->get('messaging_recipient_finder'); ?>
<div data-popup="messaging-sidebar" class="messaging-sidebar hidden" id="messaging-sidebar">
    <div id="messaging-sidebar-sms">
        <div class="message-wrapper">
            <div class="container">
                <div class="message-close">
                    <a href="javascript:void(0)" class="popup-close" data-popup-close="messaging-sidebar">
                        <span class="fa icon-times" aria-hidden="true"></span>
                        <span>ESC</span>
                    </a>
                </div>

                <div class="messaging-sidebar-columns">

                    <?php // column 1: Main ?>
                    <div class="messaging-sidebar-column message--nav">
                        <?php require_once('popout/column_main.php'); ?>
                    </div>

                    <?php // column 2: Message listing ?>
                    <div class="messaging-sidebar-column messaging-sidebar-messages grayBg hidden border-0" id="messaging-sidebar-messages">
                    </div>

                    <?php // column 3: Message ?>
                    <div class="messaging-sidebar-column">
                        <?php require_once('popout/column_message.php'); ?>
                    </div>

                    <div class="messaging-sidebar-column last">
                        <?php if ($recipient_finder): ?>
                            <div class="content-box" id="messaging-compose-search" style="display: none;">
                                <?php include('popout/find_a_contact.php') ?>
                            </div>
                        <?php endif; ?>

                        <div class="content-box" id="add-attachment" style="display: none;">
                            <?php include('popout/add_attachment.php'); ?>
                        </div>
                        <div id="messaging-sidebar-attachments-view" class="content-box attachment--view" style="display: none;"></div>
                    </div>
                </div>
            </div>
            <!-- popup html -->
            <?php require_once('snippets/sms/popup_slider.php'); ?>
            <!--  popup end  -->
        </div>
    </div>
</div>
<div class="alert-area" id="list-messages-alert-area"></div>