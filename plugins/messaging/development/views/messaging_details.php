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
<style>
	#messaging_details_table dt {
		padding-bottom: 10px;
	}
</style>
<div id="messaging_details">
	
    <dl class="dl-horizontal" id="messaging_details_table">
		<dt>Sender</dt>
		<dd><?=$details['sender_d'] ? $details['sender_d'] : 'default'?></dd>

        <dt>Reply-To</dt>
		<dd><?=$details['replyto']?></dd>

		<dt>Schedule</dt>
		<dd><?=$details['schedule']?></dd>

		<dt>Status</dt>
		<dd><?=$details['status']?></dd>

		<dt>Sent Started</dt>
		<dd><?=$details['sent_started']?></dd>

		<dt>Sent Completed</dt>
		<dd><?=$details['sent_completed']?></dd>

		<?php if ($details['send_interrupted']): ?>
			<dt>Send Interrupted</dt>
			<dd><?=$details['send_interrupted']?></dd>
		<?php endif; ?>

		<?php if ($details['ip_address']): ?>
			<dt><?= __('IP address') ?></dt>
			<dd><?= $details['ip_address'] ?></dd>
		<?php endif; ?>

		<?php if ($details['user_agent']): ?>
			<dt><?= __('User Agent') ?></dt>
			<dd><?= $details['user_agent'] ?></dd>
		<?php endif; ?>

        <?php
        if (count($details['attachments'])){
        ?>
        <dt>Attachments</dt>
        <dd>
            <ul>
            <?php
            foreach ($details['attachments'] as $attachment) {
            ?>
            <li><a href="/admin/messaging/download_attachment/<?=$attachment['id']?>" target="_blank"><?=$attachment['name']?></a> </li>
            <?php
            }
            ?>
            </ul>
        </dd>
        <?php
        }
        ?>
		<dt>Subject</dt>
		<dd><?=$details['subject']?></dd>
    </dl>

		<h2>Message</h2>
			<div>
				<?php $form_data = json_decode($details['form_data']); ?>
				<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 0;">
					<li role="presentation" class="active"><a href="#message-details-view-visual" aria-controls="message-details-view-visual" role="tab" data-toggle="tab">Details</a></li>
					<li role="presentation"><a href="#message-details-view-source" aria-controls="message-details-view-source" role="tab" data-toggle="tab">Source</a></li>
					<?php if ( ! is_null($form_data)): ?>
						<li role="presentation"><a href="#message-details-view-form_data" aria-controls="message-details-view-form_data" role="tab" data-toggle="tab">Form data</a></li>
					<?php endif; ?>
				</ul>

				<div class="tab-content clearfix" style="border: solid #ddd; border-width: 0 1px 1px; padding: 1em;">
					<div role="tabpanel" class="tab-pane active" id="message-details-view-visual">
                        <iframe
                            src="/admin/messaging/view_message/<?= $details['id'] ?>"
                            class="border-0 w-100"
                            id="message-details-view-visual-iframe"
                            onload="resize_iframe(this)"
                        ></iframe>
					</div>
					<div role="tabpanel" class="tab-pane" id="message-details-view-source">
						<pre><?=htmlentities($details['message'], ENT_COMPAT, 'utf-8')?></pre>
					</div>
					<?php if ( ! is_null($form_data)): ?>
						<div role="tabpanel" class="tab-pane" id="message-details-view-form_data">
							<dl class="dl-horizontal" style="overflow-y: scroll;">
								<?php foreach ($form_data as $label => $value): ?>
                                    <?php
                                    if (is_string($value))
                                    {
                                        $decode = json_decode($value);
                                        $value = (json_last_error() == JSON_ERROR_NONE) ? $decode : $value;
                                    }
                                    ?>
									<dt><?= htmlspecialchars($label) ?></dt>
									<dd>
										<?php if (is_object($value) || is_array($value)): ?>
											&nbsp;<dl>
												<?php foreach ($value as $label2 => $value2): ?>
													<dt><?= htmlspecialchars($label2) ?></dt>
													<dd><?= is_object($value2) || is_array($value2) ? json_encode($value2) : nl2br(htmlspecialchars($value2)) ?></dd>
												<?php endforeach; ?>
											</dl>
										<?php else: ?>
											<?= nl2br(htmlspecialchars($value)) ?>
										<?php endif; ?>
									</dd>
								<?php endforeach; ?>
							</dl>
						</div>
					<?php endif; ?>
				</div>
			</div>


		<h3>To</h3>
		    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 0;">
                <li role="presentation" class="active"><a href="#messaging_recipient_list_panel" aria-controls="messaging_recipient_list_panel" role="tab" data-toggle="tab">Recipients</a></li>
                <li role="presentation"><a href="#messaging_recipient_status" aria-controls="messaging_recipient_status" role="tab" data-toggle="tab">Detailed Status</a></li>
            </ul>

            <div class="tab-content" style="border: solid #ddd; border-width: 0 1px 1px; padding: 1em;">

                <div role="tabpanel" class="tab-pane active" id="messaging_recipient_list_panel">
                    <table id="messaging_recipient_list" class="table table-striped">
                        <tbody>
                            <?php foreach($details['targets'] as $target){ ?>
                                <tr>
                                    <td><?=$target['target_type']?></td>
                                    <td><?
                                    echo $target['target'];
                                    if ($target['target_d']) {
                                        echo '; ' . htmlentities($target['target_d']);
                                    } else {
                                        foreach ($details['final_targets'] as $final_target){
                                            if ($final_target['target_id'] == $target['id']){
                                                echo '; ' . htmlentities($final_target['target']) . '<br />';
                                            }
                                        }
                                    }
                                    ?></td>
                                    <td><?=$target['custom_subject']?></td>
                                    <td><?=$target['custom_message']?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane active" id="messaging_recipient_status">
                    <table class="table table-striped">
                        <thead>
                            <tr><th>Recipient</th><th>Driver Remote Id</th><th>Status</th><th>Details</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($details['final_targets'] as $final_target){
                            ?>
                            <tr>
                                <td><?=htmlentities($final_target['target'])?></td>
                                <td><?=htmlentities($final_target['driver_remote_id'])?></td>
                                <td><?=htmlentities($final_target['delivery_status'])?></td>
                                <td><?=htmlentities($final_target['delivery_status_details'])?></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

			</div>
</div>

<script>
    // Ensure an iframe height matches its parent
    function resize_iframe(iframe)
    {
        // Extra 2px is to accommodate for borders that counter margin-collapsing
        iframe.height = (iframe.contentWindow.document.body.scrollHeight + 2) + 'px';
    }
</script>