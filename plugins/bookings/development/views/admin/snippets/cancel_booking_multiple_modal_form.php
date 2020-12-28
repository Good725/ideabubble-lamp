<?php
$booking_object = new Model_Booking_Booking($booking['booking_id']);
?>
<div id="cancel_booking_multiple_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Cancel Booking Schedules</h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				<form id="cancel_booking_multiple_modal_form" method="post" action="/admin/bookings/cancel_booking_multiple">
					<div id="cancel_booking_info">
						<fieldset>
							<legend>Cancel Booking/Schedule Details</legend>

                            <div>
                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Schedule</th>
                                            <th scope="col">Paid</th>
                                            <th scope="col">Outstanding</th>
                                            <th scope="col">Credit</th>
                                            <th scope="col">Cancel</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    <?php
									$transfer_credit = "";
									$no_credit = true;
									$ts_index = 0;
									?>
                                    <?php foreach ($booking['schedules'] as $ts_index => $schedule) { ?>
                                        <?php
										if (in_array($schedule['booking_status'], array(Model_KES_Bookings::CANCELLED, Model_KES_Bookings::COMPLETED))) {
											continue;
										}
                                        $transfer_credit = $schedule['default_transfer_credit'];
										if ($transfer_credit > 0) {
											$no_credit = false;
										}
                                        ?>
                                        <tr>
                                            <td><?=$booking['booking_id'] . '; Schedule #' . $schedule['id'] . '; ' . $schedule['name']?>
                                                <br>
                                                <?= date('D / H:i',strtotime($schedule['start_date']))  ?>
                                            </td>
                                            <td><?=$transfer_credit?></td>
                                            <td><?=$schedule['outstanding']?></td>
                                            <td>
                                                <input type="text"
                                                       name="cancel_booking_schedule[<?=$ts_index?>][credit]"
                                                       class="form-input credit"
													   value="0"
													   data-max="<?=$transfer_credit?>" <?=$transfer_credit == 0 ? ' readonly="readonly"' : ''?>
													   title="<?=$transfer_credit?> has been paid" />
                                            </td>

                                            <td>
                                                <input type="hidden" class="booking_id"
                                                       name="cancel_booking_schedule[<?=$ts_index?>][booking_id]"
                                                       value="<?=$booking['booking_id'];?>" />
                                                <input type="hidden" class="schedule_id"
                                                       name="cancel_booking_schedule[<?=$ts_index?>][schedule_id]"
                                                       value="<?=$schedule['id']?>" />

                                                <?php
                                                echo Form::ib_checkbox(
                                                    null,
                                                    'cancel_booking_schedule[' . $ts_index . '][confirm]',
                                                    1,
                                                    in_array($schedule['id'], $dschedules),
                                                    ['class' => 'confirm']
                                                );
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <?php
                                $has_delegates = $booking_object->has_delegates->find_all();
                                ?>

                                <table class="table dataTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Delegate</th>
                                            <th scope="col">Cancel</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($has_delegates as $has_delegate): ?>
                                            <?php $delegate = $has_delegate->delegate; ?>

                                            <?php if (!$has_delegate->cancelled): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($delegate->get_full_name()) ?></td>
                                                    <td>
                                                        <?php
                                                        echo Form::ib_checkbox(
                                                            null,
                                                            'cancel_booking_delegate[' . $delegate->id . '][confirm]',
                                                            1,
                                                            (bool) $has_delegate->cancelled,
                                                            ['class' => 'confirm']
                                                        )
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label for="booking-cancel-reason control-label">Reason</label>
                                </div>

                                <div class="col-sm-8">
                                    <?php
                                    $reasons =  Model_Lookup::lookupList('Booking cancellation reason', ['public' => 1]);
                                    echo Form::ib_select(
                                        null,
                                        'reason_code',
                                        html::optionsFromRows('value', 'label', $reasons),
                                        null,
                                        ['id' => 'booking-cancel-reason'],
                                        ['please_select' => true]
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="cancel_booking_modal_note">Credit to Family</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" id="credit_to_family_autocomplete" value="" <?=$no_credit ? 'readonly="readonyl"' : ''?> /><span>(Leave empty if you do not want to transfer another family)</span>
                                    <input type="hidden" name="credit_to_family_id" value=""/>
                                </div>
                            </div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="cancel_booking_modal_note">Note</label>
								<div class="col-sm-8">
									<textarea class="form-control" id="cancel_booking_modal_note" cols="5" rows="4" name="note"></textarea>
								</div>
							</div>
						</fieldset>
					</div>
					<input type="hidden" name="booking_id" value="<?=$booking['booking_id'];?>">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn cancel" data-dismiss="modal" data-content="Do not cancel the selected booking">Cancel</button>
				<button type="button" class="btn btn-primary save" id="cancel_booking_multiple_modal_btn" data-content="Cancel selected booking / schedules">Save</button>
			</div>
		</div>
	</div>
</div>
