<?=(isset($alert)) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>

<? if ($events): ?>
    <ul class="nav nav-tabs">
        <?php foreach ($events as $event): ?>
        	<li><a href="#<?=$event['name']?>" data-toggle="tab"><?=$event['description']?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content clearfix">
        <? foreach ($events as $event): ?>
        <div class="tab-pane" id="<?=$event['name']?>">
            <form class="col-sm-9 form-horizontal" id="form_event_edit-<?=$event['name']?>" name="form_event_edit-<?=$event['name']?>" action="/admin/notifications/save/" method="post">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="from">From</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control validate[required]" id="from" name="from" value="<?=$event['from']?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="subject">Subject</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control validate[required]" id="subject" name="subject" value="<?=$event['subject']?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="contacts">Contacts</label>
                    <div class="col-sm-9">
                        <table class="table table-striped" id="contacts">
                            <thead>
                            <tr>
                                <th>First name</th>
                                <th>Last name</th>
                                <th>Email</th>
                                <th>To</th>
                                <th>CC</th>
                                <th>BCC</th>
                            </tr>
                            </thead>
                            <tbody>
                                <? foreach ($contacts as $item): ?>
                                <tr>
                                    <td ><?=$item['first_name']?></td>
                                    <td ><?=$item['last_name']?></td>
                                    <td ><?=$item['email']?></td>
                                    <td class="to" data-id="<?=$item['id']?>" data-email="<?=$item['email']?>" data-event-name="<?=$event['name']?>"><i class="icon-ok"></i></td>
                                    <td class="cc" data-id="<?=$item['id']?>" data-email="<?=$item['email']?>" data-event-name="<?=$event['name']?>"><i class="icon-ok"></i></td>
                                    <td class="bcc" data-id="<?=$item['id']?>" data-email="<?=$item['email']?>" data-event-name="<?=$event['name']?>"><i class="icon-ok"></i></td>
                                </tr>
                                <? endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="to_recipients-<?=$event['name']?>">To</label>
                    <div class="col-sm-9">
						<div class="recipients" id="to_recipients-<?=$event['name']?>">
							<?php foreach ($event['to'] as $recipient): ?>
								<span class="recipient">
                            		<button class="close remove-recipient" type="button">&times;</button>
									<?=Model_Contacts::get_contact_email($recipient)?>
									<input type="hidden" name="to[]" value="<?=$recipient?>">
                        		</span>
							<?php endforeach; ?>
						</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="cc_recipients-<?=$event['name']?>">CC</label>
					<div class="col-sm-9">
						<div class="recipients" id="cc_recipients-<?=$event['name']?>">
							<?php foreach ($event['cc'] as $recipient): ?>
								<span class="recipient">
									<button class="close remove-recipient" type="button">&times;</button>
									<?=Model_Contacts::get_contact_email($recipient)?>
									<input type="hidden" name="cc[]" value="<?=$recipient?>">
								</span>
							<?php endforeach; ?>
						</div>
					</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="bcc_recipients-<?=$event['name']?>">BCC</label>
					<div class="col-sm-9">
						<div class="recipients" id="bcc_recipients-<?=$event['name']?>">
							<?php foreach ($event['bcc'] as $recipient): ?>
								<span class="recipient">
								<button class="close remove-recipient" type="button">&times;</button>
								<?=Model_Contacts::get_contact_email($recipient)?>
								<input type="hidden" name="bcc[]" value="<?=$recipient?>">
							</span>
							<?php endforeach; ?>
						</div>
					</div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="header">Header</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="header" name="header" rows="4"><?=$event['header']?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="footer">Footer</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="footer" name="footer" rows="4"><?=$event['footer']?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-9">
                        <input type="hidden" id="id" name="id" value="<?=$event['id']?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="/admin/notifications" class="btn btn-default">Cancel</a>
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-notification-modal" data-id="<?= $event['id'] ?>">Delete</button>
                </div>
            </form>
        </div>
        <? endforeach; ?>
    </div>
<? endif; ?>

<div class="modal fade" id="delete-notification-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Confirm deletion</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this notification.</p>
				<p>If you do not fully understand what this notification does, you should not proceed.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="confirm-delete-notification">Delete</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
