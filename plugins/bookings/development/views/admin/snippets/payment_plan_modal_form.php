<div id="make_payment_plan_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3><?=$transaction['recurring_payments_enabled'] == 1 ? __('Subscription Automatic Payments') : __('Payment Plan for Transaction')?> #<?= $transaction['id'] ?></h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				<form id="payment_plan_form" method="post" action="">
					<input type="hidden" id="payment_plan_id" name="payment_plan_id"/>
                    <input type="hidden" id="payment_plan_transaction_id" value="<?= $transaction['id'] ?>" />
					<div class="make_payment_modal_column" id="payment_plan_info">
                        <?php if ($payment_plan) { ?>
                        <fieldset>
                            <legend>Plan Start Details</legend>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Outstanding</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" disabled="disabled" value="<?=@$payment_plan['outstanding']?>" />
                                    </div>
                                </div>

                                <label class="col-sm-2 control-label">Term</label>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <select class="form-control" disabled="disabled">
                                            <?=html::optionsFromArray(array(2 => 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12), @$payment_plan['term'] ?: 2)?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <select class="form-control" disabled="disabled">
                                        <option value="month" <?=@$payment_plan['term_type'] != 'custom' ? 'selected="selected"' : ''?>>Month</option>
                                        <option value="custom" <?=@$payment_plan['term_type'] == 'custom' ? 'selected="selected"' : ''?>>Custom</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Deposit</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" disabled="disabled" value="<?=@$payment_plan['deposit'] ?: '0.00'?>" />
                                    </div>
                                </div>

                                <label class="col-sm-2 control-label">Interest</label>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <select class="form-control" disabled="disabled">
                                            <?=html::optionsFromArray(array('Fixed' => '€', 'Percent' => '%'), @$payment_plan['interest_type'] ?: 'Fixed')?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="input-group" id="payment_plan_interest_d">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" id="payment_plan_interest" value="<?=@$payment_plan['interest'] ?: ''?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Start Amount</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" disabled="disabled" value="<?=@$payment_plan['outstanding']?>" />
                                    </div>
                                </div>

                                <label class="col-sm-3 control-label">Start Date</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" disabled="disabled" value="<?=$payment_plan['starts']?>" />
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <?php } ?>

						<fieldset>
							<legend>Plan Details</legend>

							<div class="form-group">
								<label class="col-sm-2 control-label" for="payment_plan_outstanding">Outstanding</label>
								<div class="col-sm-3">
									<div class="input-group">
										<span class="input-group-addon">&euro;</span>
										<input class="form-control" type="text" id="payment_plan_outstanding" readonly="readonly" value="<?=$transaction['outstanding']?>" />
									</div>
								</div>

                                <label class="col-sm-2 control-label" for="payment_plan_terms">Term</label>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <select class="form-control"  id="payment_plan_terms" name="payment_plan_terms">
                                        <?=html::optionsFromArray(array(2 => 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12), @$payment_plan['term'] ?: 2)?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <select class="form-control"  id="payment_plan_term_type" name="">
                                        <option value="month" <?=@$payment_plan['term_type'] == 'month' ? 'selected="selected"' : ''?>>Month</option>
                                        <option value="custom" <?=@$payment_plan['term_type'] == 'custom' ? 'selected="selected"' : ''?>>Custom</option>
                                    </select>
                                </div>
							</div>


                            <div class="form-group">
                                <?php if ($payment_plan) { ?>
                                    <label class="col-sm-2 control-label" for="payment_plan_adjustment">Adjustment</label>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input class="form-control" type="text" id="payment_plan_adjustment" value="0.00" />
                                        </div>
                                    </div>
                                <?php } else { ?>
                                <label class="col-sm-2 control-label" for="payment_plan_deposit">Deposit</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" id="payment_plan_deposit" value="0.00" />
                                    </div>
                                </div>
                                <?php } ?>

                                <label class="col-sm-2 control-label" for="payment_plan_interest" title="Monthly Interest Type/Amount">Interest</label>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <select class="form-control"  id="payment_plan_interest_type" name="payment_plan_interest_type" title="Monthly Interest">
                                            <?=html::optionsFromArray(array('Fixed' => '€', 'Percent' => '%'), @$payment_plan['interest_type'] ?: 'Fixed')?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="input-group" id="payment_plan_interest_d">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" id="payment_plan_interest" value="<?=@$payment_plan['interest'] ?: ''?>" title="Monthly Interest" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="payment_plan_amount">Start Amount</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" id="payment_plan_start_amount" readonly="readonly" value="<?=@$payment_plan['outstanding'] ? $payment_plan['outstanding'] - $payment_plan['deposit'] : '0.00'?>" />
                                    </div>
                                </div>

                                <label class="col-sm-3 control-label" for="payment_plan_starts">Start Date</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="payment_plan_starts" name="payment_plan_starts" value="<?=$payment_plan['starts'] ?: date::today()?>" />
                                    </div>
                                </div>
                            </div>
                            <?php if ($payment_plan) { ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment_plan_newbalance">New Balance</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" id="payment_plan_newbalance" readonly="readonly" value="0.00" />
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="payment_plan_total">Total Inc. Interest</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control" type="text" id="payment_plan_total" readonly="readonly" value="0.00" />
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <button type="button" class="form-control" id="payment_plan_preview_btn"><?=__('PREVIEW SCHEDULE')?></button>
                                </div>
                            </div>
						</fieldset>

                        <fieldset>
                            <legend>Plan Installments Schedule</legend>
                            <div>
                                <table class="table" id="payment_plan_installments">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Net</th>
                                            <th>Interest</th>
                                            <th>Total</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>New Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody><?php
                                    if (!$payment_plan) {
                                    ?><tr><td colspan="7">click "PREVIEW SCHEDULE"</td></tr><?php } else {
                                        $balance = $payment_plan['outstanding'];
                                        foreach ($payment_plan['installments'] as $i => $installment) {
                                            $balance -= $installment['amount'];
                                        ?><tr>
                                            <td><?=$i?></td>
                                            <td><?=$installment['amount']?></td>
                                            <td><?=$installment['interest'] . ($installment['penalty'] > 0 ? ' (+' . $installment['penalty'] . ')' : '')?></td>
                                            <td><?=$installment['total']?></td>
                                            <td><?=$installment['due_date']?></td>
                                            <td><?=$installment['payment_id'] ? 'Paid' : 'Unpaid'?></td>
                                            <td><?=$balance?></td>
                                        </tr><?php
                                        }
                                    } ?></tbody>
                                </table>
                            </div>
                        </fieldset>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" data-content="Do not proceed with this payment plan">Close</a>
                <a href="#" class="btn btn-primary" id="make_payment_plan_modal_btn" data-content="Save the payment plan and return to the transaction listing"><?=$payment_plan ? 'Save' : 'Start Plan'?></a>
                <?php if ($payment_plan) { ?>
                <a href="#" class="btn btn-cancel" id="cancel_payment_plan_modal_btn" data-content="Cancel the payment plan and return to the transaction listing" data-id="<?=$payment_plan['id']?>">Cancel Plan</a>
                <?php } ?>
			</div>
		</div>
	</div>
</div>
