<div class="row-fluid header list_notes_alert">
    <?= (isset($alert)) ? $alert : '' ?>
    <?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
</div>
<?php if ( ! empty($messages)): ?>
    <table class="table dataTable educate_messages_table">
        <thead>
            <tr>
                <th scope="col">Type</th>
                <th scope="col">Subject</th>
                <th scope="col">Sender</th>
                <th scope="col">Status</th>
                <th scope="col">Last Activity</th>
                <th scope="col">Message</th>
                <th scope="col">Details</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message){ ?>
                <tr data-id="<?= $message['id']; ?>">
                    <td><?= $message['driver']; ?></td>
                    <td><?= ($message['driver'] === 'dashboard') ? 'Alert notification' : htmlentities($message['subject']); ?></td>
                    <td><?= $message['sender']; ?></td>
                    <td><?= $message['status']; ?></td>
                    <td><?= IbHelpers::relative_time(max(strtotime($message['date_created']), strtotime($message['date_updated']))); ?></td>

                    <td>
                        <?php // Get HTML for the message and a list of attachments. Put the result in a string, so it can be used as a popover ?>
                        <?php ob_start(); ?>

                        <?php // Remove <html></html> and <body></body> tags from the message, if any  ?>
                        <?= preg_replace('/<\/?html[^>]*\>/i', '', preg_replace('/<\/?body[^>]*\>/i', '', $message['custom_message'] ? $message['custom_message'] : $message['message'])); ?>
                        <?php if (!empty($message['attachments'])): ?>
                            <h3>Attachments</h3>

                            <ul style="margin-left: 1em;">
                                <?php foreach ($message['attachments'] as $attachment): ?>
                                    <li><a href="/admin/messaging/download_attachment/<?= $attachment['id'] ?>"><?= $attachment['name'] ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php $view_popover = ob_get_contents(); ?>
                        <?php ob_end_clean(); ?>

                        <button
                            type="button"
                            class="btn-link"
                            data-id="<?= $message['id'] ?>"
                            data-toggle="popover" data-placement="top" data-trigger="click" data-html="true"
                            data-content="<?= htmlentities($view_popover) ?>"
                            >
                            view
                        </button>
                    </td>

                    <td>
                        <?php ob_start(); ?>
                        <dl class="dl-horizontal">
                            <dt>Created</dt>
                            <dd><?= $message['date_created'] ?></dd>

                            <dt>Started</dt>
                            <dd><?= $message['sent_started'] ?></dd>

                            <dt>Interrupted</dt>
                            <dd><?= ($message['send_interrupted'] != '') ? $message['send_interrupted'] : 'n/a' ?></dd>

                            <dt>Scheduled</dt>
                            <dd><?= ($message['schedule'] != '') ? $message['schedule'] : 'n/a' ?></dd>

                            <dt>Service</dt>
                            <dd><?= $message['provider'] ?></dd>
                        </dl>
                        <?php $info_popover = ob_get_contents(); ?>
                        <?php ob_end_clean(); ?>

                        <button
                            type="button"
                            class="btn-link"
                            data-id="<?= $message['id'] ?>"
                            data-toggle="popover" data-placement="top" data-trigger="click" data-html="true"
                            data-content="<?= htmlentities($info_popover) ?>"
                            >
                            <span class="icon-info-circle"></span>
                        </button>
                    </td>

                </tr>
            <?php } ?>
        </tbody>
    </table>


    <style>
        .educate_messages_table .dl-horizontal dt {
            width: 80px;
            text-align: left;
        }
        .educate_messages_table .dl-horizontal dd {
            margin-left: 85px;
        }
    </style>
    <script>
        $('.educate_messages_table').find('[data-toggle="popover"]').popover();

        // Dismiss the popover when clicked away from
        $(document).on('click', function (ev) {
            var $target = $(ev.target);
            if ($target.data('toggle') !== 'popover' && $target.parents('[data-toggle="popover"]').length === 0 && $target.parents('.popover.in').length === 0) {
                $('.educate_messages_table').find('[data-toggle="popover"]').popover('hide');
            }
        });
    </script>
<?php else: ?>
    <p>There are no messages.</p>
<?php endif; ?>
