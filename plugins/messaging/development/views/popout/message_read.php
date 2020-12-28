<div class="top-head">
    <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
		<div class="left">
			<ul>
				<li><a href="#"><span class="fa icon-reply" aria-hidden="true"></span></a></li>
				<li><a href="#"><span class="fa icon-share" aria-hidden="true"></span></a></li>
			</ul>
		</div>
    <?php endif; ?>

    <h3 class="text-primary messaging-sidebar-message-heading"><?= __('View Message') ?></h3>

    <div class="right">
        <ul>
            <?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
                <li>
                    <a href="javascript:void(0)" class="add-btn" rel="check-link">
                        <span class="fa icon-link" aria-hidden="true"></span>
                    </a>
                </li>
            <?php endif; ?>
            <li><a href="javascript:void(0)" class="basic_close"><span class="fa icon-times" aria-hidden="true"></span></a></li>
        </ul>
    </div>
</div>
<?php
$user_model = new Model_Users();
$user = $user_model->get_user_by_email($message['sender']);
$avatar_link = ($user) ? URL::get_avatar($user['id']) : URL::get_avatar('-1');
?>
<div class="user-info" data-id="<?= $message['id'] ?>">
    <div class="left-section">
        <figure class="imgbox">
            <img src="<?= $avatar_link; ?>" alt="Profile image" />
        </figure>
        <span class="fa icon-envelope" aria-hidden="true"></span>
    </div>

    <div class="right-section">
        <ul>
            <li><label>From:</label> <?= $message['sender_d'] ?></li>
            <li class="time"><?= IbHelpers::relative_time_with_tooltip($message['sent_started']) ?></li>
        </ul>
        <br clear="both" />
        <h5>
            <label>To:</label>
            <ul>
                <?php foreach ($message['targets'] as $recipient) { ?>
                    <?php if ($recipient['x_details'] == 'to' || $recipient['x_details'] == '') { ?>
                    <li><?=$recipient['target_d'] ? $recipient['target_d'] . ' <' . $recipient['email'] . '' . $recipient['sms'] . '>' : $recipient['target']?></li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </h5>
        <br clear="both" />
        <h5>
            <label>CC:</label>
            <ul>
                <?php foreach ($message['targets'] as $recipient) { ?>
                    <?php if ($recipient['x_details'] == 'cc') { ?>
                        <li><?=$recipient['target_d'] ? $recipient['target_d'] . ' <' . $recipient['email'] . '' . $recipient['sms'] . '>' : $recipient['target']?></li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </h5>
        <br clear="both" />
        <h5>
            <label>BCC:</label>
            <ul>
                <?php foreach ($message['targets'] as $recipient) { ?>
                    <?php if ($recipient['x_details'] == 'bcc') { ?>
                        <li><?=$recipient['target_d'] ? $recipient['target_d'] . ' <' . $recipient['email'] . '' . $recipient['sms'] . '>' : $recipient['target']?></li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </h5>
        <br clear="both" />
        <?php if (!isset($message['driver']['driver']) || $message['driver']['driver'] != 'sms'): ?>
            <h5><label>Subject:</label> <?= $message['subject'] ?></h5>
        <?php endif; ?>
    </div>
</div>

<?php if ($auth->has_access('messaging_see_under_developed_features')): ?>
    <div id="check-link" class="add-files-wrap read--links">
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
<?php endif; ?>
<div class="descbody">
    <?php
    if (strpos($message['message'], '<body') > 0)
    {
        // If the email is formatted as a full HTML document, only echo the content inside the body tags
        preg_match("~<body.*?>(.*?)<\/body>~is", $message['message'], $match);
        if (isset($match[1]))
        {
            echo $match[1];
        }
    }
    else
    {
        echo $message['message'];
    }
    ?>
</div>