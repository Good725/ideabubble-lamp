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
<?php
$currencies = Model_Currency::getCurrencies(true);
?>
<style>
	.dataTable tr.odd {
		background-color: #f4f4f4;
	}
	.list-events-table td:nth-child(3) div {white-space: nowrap;}
</style>

<table class="table dataTable table-condensed list-events-table" id="list-events-table">
	<thead>
		<tr>
			<!-- WARNING! TABLE COLUMNS ORDER ARE IMPORTANT FOR RESPONSIVE BEHAVIOR!-->
                        <!-- Have a look at project.css -->
			<th scope="col" style="min-width: 200px;"><?= __('Event') ?></th>
			<th scope="col" class="hidden-phone"><?= __('Seller') ?></th>
			<th scope="col" class="hidden-phone"><?= __('Date') ?></th>
			<th scope="col" class="hidden-phone"><?= __('Time') ?></th>
            <!-- <th scope="col" class="visible-phone"><?= __('Time') ?></th> -->
			<th scope="col"><?= __('Status') ?></th>
			<th scope="col" class="hidden-phone"><?= __('Allocation') ?></th>
			<th scope="col"><?= __('Sold') ?></th>
			<th scope="col" class="hidden-phone"><?= __('Price') ?></th>
			<th scope="col">
                <span class="visible-phone"><?= __('Sales') ?></span>
                <span class="hidden-phone"><?= __('Total&nbsp;Sales') ?></span>
            </th>
			<th scope="col"><?= __('Actions') ?></th>
			<th scope="col"><?= __('Publish') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $media_path = URL::get_engine_plugin_assets_base('events') ?>

		<?php foreach ($events as $event): ?>
			<tr data-id="<?= $event['id'] ?>" data-date_id="<?= $event['date_id']?>">
				<td><a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link"><?= $event['name'] ?></a></td>
				<td class="hidden-phone"><?= $event['contact_full_name'] ?></td>
				<td class="hidden-phone"><a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link">
					<?php
					if ($event['dates']) {
						$event['dates'] = explode(',', $event['dates']);
						foreach ($event['dates'] as $i => $date) {
							$date = explode('|', $date);
							$date['starts'] = $date[0];
							$date['ends'] = $date[1];
							$event['dates'][$i] = $date;
						}
					} else {
						$event['dates'] = array();
					}

					foreach ($event['dates'] as $date) {
					?>
					<span class="hidden"><?= $date['starts'] ?></span>
					<div><?= date('j F Y',strtotime($date['starts'])) ?></div>
					<?php
					}
					?>
					</a>
				</td>
				<td class="hidden-phone">
				   <a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link">
					<?php
					foreach ($event['dates'] as $date) {
						?>
						<span class="hidden"><?= $date['starts'] ?></span>
						<div><?= date('H:i',strtotime($date['starts'])) ?></div>
						<?php
					}
					?>
					</a>
				</td>
                <td class="visible-phone">
                    <a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link">
                        <?php foreach ($event['dates'] as $date) : ?>
                            <span class="hidden"><?= $date['starts'] ?></span>
                            <div>
                                <?= date('j M Y',strtotime($date['starts'])) ?>
                                <br/>
                                <?= date('H:i',strtotime($date['starts'])) ?>
                            </div>
                        <?php endforeach; ?>
                    </a>
                </td>
				<td>
                    <a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link">
                        <?php
                        if ($event['status'] == 'Live' && $event['quantity'] - $event['sold'] <= 0)
                            echo __('Sold Out');
                        else if ($event['status'] == 'Live' && $event['is_onsale'] == 1)
							if ($event['date_onsale'] == 1) echo __('On Sale');
							else echo __('Sale Ended');
                        else
                            echo $event['status'];
                        ?>
                    </a>
                </td>
				<td class="hidden-phone"><a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link"><?= ($event['quantity'] - $event['sold']).' / '.$event['quantity']?></a></td>
				<td><a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link"><?= $event['sold']?></a></td>
				<td class="hidden-phone">
					<?php if (count($event['tickets']) == 1): ?>
						<a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link">
                            <?php if ($event['tickets'][0]['type'] == 'Donation'): ?>
                                Donation
                            <?php else: ?>
                                <?= $currencies[$event['currency']]['symbol'] . number_format($event['tickets'][0]['base_price'], 2) ?>
                            <?php endif; ?>
                        </a>
					<?php elseif (count($event['tickets']) > 1): ?>
						<a
							role="button"
							tabindex="0"
							class="text-uppercase"
							data-toggle="popover"
							data-html="true"
							data-trigger="hover touchend mouseup"
							data-placement="top"
							data-content="<table><tbody>
							<?php foreach ($event['tickets'] as $ticket): ?>
								<tr>
									<td><?= $ticket['name'] ?></td>
									<td>&nbsp;<?= $currencies[$event['currency']]['symbol'] . number_format($ticket['base_price'], 2) ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody></table>"
							>Multiple</a>
					<?php endif; ?>
					
				</td>

                <td>
                   <a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link"><?= $currencies[$event['currency']]['symbol'] . number_format($event['totalsold'], 2, '.', ',') ?></a>
                </td>

				<td>
					<div class="dropdown">
						<button class="btn btn-default dropdown-toggle btn-actions" type="button" data-toggle="dropdown">
							<span class="hidden-phone"><?= __('Actions') ?></span>
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu pull-right">
                            <?php if (Auth::instance()->has_access('events_edit') || Auth::instance()->has_access('events_edit_limited')){ ?>
							<li>
								<a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link">
									<span class="icon-pencil"></span> <?= __('Edit') ?>
								</a>
							</li>
							<li>
								<a href="/admin/events/duplicate_event?id=<?= $event['id'] ?>" class="edit-link">
									<span class="icon-copy"></span> <?= __('Duplicate') ?>
								</a>
							</li>
							<li>
								<button type="button" class="btn-link list-sale-end-button"
                                        onclick="$('#sale-event-modal [name=id]').val(<?= $event['id'] ?>); $('#sale-event-modal [name=date_id]').val(<?= $event['date_id'] ?>)"
                                        data-toggle="modal"
                                        data-target="#sale-event-modal"
                                        data-id="<?= $event['id'] ?>"
										data-date_id="<?=$event['date_id']?>">
									<span class="icon-calendar-times-o"></span> <?= __('End Ticket Sales') ?>
								</button>
							</li>
							<li>
								<?php if ($event['status'] != 'Postponed'): ?>
									<button type="button" class="btn-link list-postpone-button"
											onclick="$('#status-event-modal [name=status]').val('Postponed'); $('#status-event-modal [name=id]').val(<?= $event['id'] ?>)"
											data-toggle="modal"
											data-target="#status-event-modal"
											data-id="<?= $event['id'] ?>">
										<span class="icon-calendar-minus-o"></span> <?= __('Postpone Event') ?>
									</button>
								<?php else: ?>
									<button type="button" class="btn-link list-reinstated-button"
											onclick="$('#status-event-modal').find('[name=status]').val('Live'); $('#status-event-modal').find('[name=id]').val(<?= $event['id'] ?>)"
											data-toggle="modal"
											data-target="#status-event-modal"
											data-id="<?= $event['id'] ?>">
										<span class="icon-calendar-minus-o"></span> <?= __('Reinstate') ?>
									</button>
								<?php endif; ?>
							</li>
							<li>
								<button type="button" class="btn-link list-cancel-button"
                                        onclick="$('#status-event-modal [name=status]').val('Cancelled'); $('#status-event-modal [name=id]').val(<?= $event['id'] ?>)"
                                        data-toggle="modal"
                                        data-target="#status-event-modal"
                                        data-id="<?= $event['id'] ?>">
									<span class="icon-ban"></span> <?= __('Cancel Event') ?>
								</button>
							</li>
							<?php if ($event['status'] == 'Sale Ended') { ?>
                                <?php if ($event['invoice_id'] == null) { ?>
							<li>
								<a class="btn-link list-print-button" href="/admin/events/invoice_generate?event_id=<?=$event['id']?>">
									<span class="icon-print"></span> <?= __('Generate Invoice') ?>
								</a>
							</li>
                                <?php } else { ?>
                            <li>
                                <a class="btn-link list-print-button" href="/admin/events/invoice_download?invoice_id=<?=$event['invoice_id']?>">
                                    <span class="icon-print"></span> <?= __('Download Invoice') ?>
                                </a>
                            </li>
                                <?php } ?>
                            <li>
                                <a class="btn-link list-print-button" href="/admin/events/statement_view?event_id=<?=$event['id']?>">
                                    <span class="icon-print"></span> <?= __('View Statement') ?>
                                </a>
                            </li>
                                <?php } ?>
                            <?php } ?>
							<li>
                                <button
                                    class="btn-link download-attendees"
                                    data-toggle="modal"
                                    data-target="#download-attendees-modal"
                                    data-id="<?= $event['id'] ?>"
                                    data-date_id="<?= $event['date_id']?>"
                                >
									<select class="ticket_types_data" style="display: none;"><?=html::optionsFromRows('id', 'name', $event['tickets'])?></select>
                                    <span class="icon-download"></span> <?= __('Download Attendees') ?>
                                </button>
							</li>
							<li>
								<button
									class="btn-link"
									data-toggle="modal"
									data-target="#email-attendees-modal"
									data-id="<?= $event['id'] ?>"
                                    data-date_id="<?= $event['date_id']?>"
									>
									<span class="icon-envelope-o"></span> <?= __('Email Attendees') ?>
								</button>
							</li>
                            <?php if (Auth::instance()->has_access('events_delete') || Auth::instance()->has_access('events_delete_limited')){ ?>
							<li>
								<button
									type="button"
									class="btn-link list-delete-button"
									title="<?= __('Delete') ?>"
									data-toggle="modal"
									data-target="#delete-event-modal"
									data-id="<?= $event['id'] ?>"
									>
									<span class="icon-times"></span> <?= __('Delete') ?>
								</button>
							</li>
                            <?php } ?>
						</ul>
					</div>
				</td>
				<td>
					<button type="button" class="btn-link publish-btn" data-id="<?= $event['id'] ?>">
						<span class="hidden publish-value"><?= $event['publish'] ?></span>
						<span class="publish-icon icon-<?= $event['publish'] ? 'ok' : 'ban-circle' ?>"></span>
					</button>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php if (Auth::instance()->has_access('events_delete') || Auth::instance()->has_access('events_delete_limited')){ ?>
<div class="modal fade" tabindex="-1" role="dialog" id="delete-event-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="/admin/events/delete_event" method="post">
				<input type="hidden" name="id" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete event') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this event?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger" id="delete-event-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php } ?>

<?php if (Auth::instance()->has_access('events_edit') || Auth::instance()->has_access('events_edit_limited')){ ?>
	<div class="modal fade" tabindex="-1" role="dialog" id="status-event-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="/admin/events/event_status_set" method="post" id="status-event-form">
					<input type="hidden" name="id" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><?= __('Update Event') ?></h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
                            <label class="form-label" for="event_status">Status</label>
                            <input class="form-control" type="text" readonly name="status" value="" style="border:none;"/>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?=__('Reason')?></label>
                            <textarea class="form-control" name="status_reason"></textarea>
                        </div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-danger" id="update-email-event-button"><?= __('Update & Email') ?></button>
						<button type="submit" class="btn btn-danger" id="update-event-button"><?= __('Update') ?></button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>

<?php if (Auth::instance()->has_access('events_edit') || Auth::instance()->has_access('events_edit_limited')){ ?>
	<div class="modal fade" tabindex="-1" role="dialog" id="download-attendees-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="/admin/events/download_attendee_csv" method="post" id="download-attendees-form">
					<input type="hidden" name="id" />
                    <input type="hidden" name="date_id" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><?= __('Download Attendees') ?></h4>
					</div>
					<div class="modal-body">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label class="form-label" for="csv_filter_ticket_type_id">Ticket Types</label>
                                <select name="csv_filter[ticket_type_id][]" id="csv_filter_ticket_type_id" multiple="multiple"></select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="row">
                                <div class="col-lg-12 text-center"><h4><?= __('Display fields') ?></h4></div>
                            </div>
                            <div class="form-group col-lg-6">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="csv_fields[item_id]" value="1">Ticket #</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="csv_fields[order_firstname]" value="1">Customer Name</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="csv_fields[order_email]" value="1">Email</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="csv_fields[order_id]" value="1">Order No.</label>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="csv_fields[item_total]" value="0">Total</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="csv_fields[order_status]" value="0">Status</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="csv_fields[order_created]" value="0">Created</label>
                                </div>
								<div class="checkbox">
									<label><input type="checkbox" name="csv_fields[ticket_type]" value="1">Ticket Type</label>
								</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 text-center"><h4><?= __('Order') ?></h4></div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="order_by_lastname" value="1">Order by customer last name</label>
                                </div>
                            </div>
                        </div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-danger" id="update-event-button"><?= __('Download') ?></button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>

<?php if (Auth::instance()->has_access('events_edit') || Auth::instance()->has_access('events_edit_limited')){ ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="sale-event-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/admin/events/event_sale_end" method="post">
                    <input type="hidden" name="id" />
					<input type="hidden" name="date_id" />
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?= __('End Sale') ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?= __('Are you sure you want to end sales for this event?') ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" id="end-sale-event-button"><?= __('End Sale') ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>


<div class="modal fade email-attendees-modal" id="email-attendees-modal" tabindex="-1" role="dialog" aria-labelledby="email-attendees-modal-label">
	<div class="modal-dialog" role="document">
		<form class="modal-content" action="/admin/events/email_attendees" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="email-attendees-modal-label"><?= __('Email Attendees') ?></h4>
			</div>
			<div class="modal-body clearfix">
				<input type="hidden" name="event_id" id="email-attendees-event_id" />
                <input type="hidden" name="date_id" id="email-attendees-date_id" />

				<div class="form-group">
					<label class="col-sm-12"><?= __('Subject') ?>
						<input type="text" class="form-control" name="subject" />
					</label>
				</div>

				<div class="form-group">
					<label class="col-sm-12"><?= __('BCC') ?>
						<input
							type="text"
							class="form-control"
							id="email-attendees-bcc"
							readonly="readonly"
							data-toggle="popover"
							data-html="true"
							data-trigger="hover touchend mouseup"
							data-placement="bottom"
							data-content=""
							/>
					</label>
				</div>

				<div class="form-group">
					<label class="col-sm-12"><?= __('Message') ?>
						<textarea class="form-control ckeditor-simple" id="email-attendees-message" name="message" rows="5"></textarea>
					</label>
				</div>
			</div>
			<div class="modal-footer">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary"><?= __('Send') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<style>
	@media screen and (min-width: 830px)
	{
		.email-attendees-modal .modal-dialog {
			width: 800px;
		}
	}
</style>
<script>
	$(document).on('ready', function(){
		$('#download_cms').on('click', function(e){
			confirm('Wha?');
			e.preventDefault();
		});
	});
</script>
