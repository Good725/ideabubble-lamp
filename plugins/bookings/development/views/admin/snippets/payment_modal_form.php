<?php
$payment_plan_installment_to_pay = null;
?>
<div id="make_payment_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<?php if ($booking_id) { ?>
				<h3>Make Payment Booking #<?= $booking_id ?></h3>
				<?php } else { ?>
				<h3>Make Payment<?=($credit==1)?'': ' Journal' ;?>: Transaction #<?= $transaction['id'] ?></h3>
				<?php } ?>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				<form id="make_payment_modal_form" method="post" action="">
					<input type="hidden" id="make_payment_modal_transaction_type" name="credit" value="<?=$transaction['credit'];?>">
					<input type="hidden" id="make_payment_modal_payment_type" name="credit" value="<?=$credit;?>">
					<div class="make_payment_modal_column" id="main_payment_info">
						<fieldset>
							<legend>Payment Details</legend>
							<?php if($credit == 1): ?>
								<?php
								if ($payment_plan) {
									foreach ($payment_plan['installments'] as $ppi => $installment) {
										if ($installment['payment_id'] == null) {
											$payment_plan_installment_to_pay = $installment;
											break;
										}
									}
								}
								?>
								<input type="hidden" name="payment_plan_id" value="<?=@$payment_plan['id']?>" />
								<input type="hidden" name="payment_plan_installment_id" value="<?=@$payment_plan_installment_to_pay['id']?>" />
								<div class="form-group">
									<label class="col-sm-3 control-label" for="modal_make_payment_outstanding"><?= $payment_plan_installment_to_pay ? 'Payment Plan #' . ($ppi + 1). '. Installment ' .  $payment_plan_installment_to_pay['due_date'] : 'Outstanding balance'?></label>
									<div class="col-sm-8">
										<div class="input-group">
											<span class="input-group-addon">&euro;</span>
											<input class="form-control" type="text" id="modal_make_payment_outstanding" readonly="readonly" value="<?=$outstanding?>" style="width: 180px;" />
										</div>
									</div>
								</div>

                                <? // Family Credit ?>
								<div class="form-group" id="make_payment_family_balance_available">
									<label class="col-sm-3 control-label" for="modal_make_payment_family_available_balance">Family Available balance</label>
									<div class="col-sm-8">
										<div class="input-group">
											<span class="input-group-addon">&euro;</span>
											<input class="form-control" type="text" id="modal_make_payment_family_available_balance" readonly="readonly" value="<?=$family_journal['outstanding'] ;?>" style="width: 180px;" />
										</div>
                                        <div class="input-group">
                                            <input type="radio" name="use_credit" value="<?= $family_journal['id'] ;?>" checked> Use Family Credit
                                        </div>
									</div>
								</div>

                                <? // Contact Credit ?>
                                <div class="form-group" id="make_payment_contact_balance_available">
                                    <label class="col-sm-3 control-label" for="modal_make_payment_contact_available_balance">Contact Available balance</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input class="form-control" type="text" id="modal_make_payment_contact_available_balance" readonly="readonly" value="<?=$contact_journal['outstanding'] ;?>" style="width: 180px;" />
                                        </div>
                                        <div class="input-group">
                                            <input type="radio" name="use_credit" value="<?= $contact_journal['id'] ;?>"> Use Contact Credit
                                        </div>
                                    </div>
                                </div>

								<input id="modal_make_payment_credit_transaction" type="hidden" name="credit_transaction" value="<?=isset($journal['id'])?$journal['id']:'';?>">
							<?php endif; ?>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_status">Status</label>
								<div class="col-sm-8">
									<select class="form-control"  id="modal_make_payment_status" name="status">
										<option value="">Please select status</option>
										<?php foreach($statuses as $key=>$status): ?>
											<option value="<?=$status['id'];?>" data-credit="<?=$status['credit']?>"><?=$status['status'];?></option>
										<?php endforeach;?>
									</select>
								</div>
							</div>

							<?php if ($credit == 1): ?>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="make_payment_modal_type">Payment type</label>
									<div class="col-sm-8">
										<select class="form-control"  id="make_payment_modal_type" name="type" >
											<option value="">Please select type</option>
											<option value="cheque">Сheque</option>
											<option value="cash">Cash</option>
											<option value="card">Сard</option>
											<option value="transfer" >Transfer</option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label" for="modal_make_payment_amount">Payment Amount</label>
									<div class="col-sm-8">
										<div class="input-group">
											<span class="input-group-addon">&euro;</span>
											<input class="form-control"  type="text" id="modal_make_payment_amount" required name="amount" value="<?=$payment_plan_installment_to_pay ? $payment_plan_installment_to_pay['amount'] : (count($bookings) > 0 && count($transactions) > 1 ? $outstanding : '0.00')?>" style="width: 180px;"/>
										</div>
									</div>
								</div>
								<?php if (count($bookings)) { ?>
									<div class="form-group">
										<label class="col-sm-3 control-label" for="transactions">Transactions</label>
										<div class="col-sm-8">
											<table class="table col-sm-12">
												<thead>
												<tr><th>Booking Id</th><th>Transaction Id</th><th>Outstanding</th><th>Pay Amount</th></tr>
												</thead>
												<tbody>
												<?php foreach ($transactions as $btransaction) { ?>
													<?php if ($btransaction['outstanding'] > 0) { ?>
													<tr>
														<td>#<?=$btransaction['booking_id']?></td>
														<td>#<?=$btransaction['id'] . ' - ' . $btransaction['schedule']?></td>
														<td><?=$btransaction['outstanding']?></td>
														<td>
															<input class="btransaction_payment_amount" type="text" name="btransaction_payment_amount[<?=$btransaction['id']?>]" value="<?=$btransaction['outstanding']?>" data-outstanding="<?=$btransaction['outstanding']?>" data-transaction_id="<?=$btransaction['id']?>" style="width: 100%;" />
														</td>
													</tr>
													<?php } ?>
												<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								<?php } ?>

								<div class="form-group">
									<label class="col-sm-3 control-label" for="modal_make_payment_bank_fee" <?=
									$payment_plan_installment_to_pay ?
											'title="Interest rate: ' . $payment_plan['interest'] . ' ' . $payment_plan['interest_type']  .
											', Interest:' . $payment_plan_installment_to_pay['interest'] .
											', Adjustment:' . $payment_plan_installment_to_pay['adjustment'] .
											', Penalty:' . $payment_plan_installment_to_pay['penalty'] .
											'"'
											:
											''
									?>>Fee</label>
									<div class="col-sm-8">
										<div class="input-group">
											<span class="input-group-addon">&euro;</span>
											<input class="form-control" type="text" id="modal_make_payment_bank_fee" required name="bank_fee" value="<?=$payment_plan_installment_to_pay ? $payment_plan_installment_to_pay['interest'] + $payment_plan_installment_to_pay['adjustment'] + $payment_plan_installment_to_pay['penalty'] : '0.00'?>" style="width: 100px;"/>
										</div>
									</div>
								</div>
							<?php else: ?>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="modal_make_payment_journal_payment">Payments</label>
									<div class="col-sm-8">
										<select class="form-control"  id="modal_make_payment_journal_payment" name="journal_payment_id">
											<option value="">Please select Payment</option>
											<?php foreach($payments as $key=>$payment): ?>
												<?php if ($payment['credit'] == 1 || $payment['credit'] == -1): ?>
													<option value="<?=$payment['id'];?>" data-amount="<?=$payment['amount'];?>" data-settlement-id="<?=$payment['settlement_id']?>">
														Payment:#<?= $payment['id'];?> - Type: <?=$payment['type'];?> - Amount: €<?=$payment['amount'];?> <?=$payment['settlement_id'] ? '(settlement:' . $payment['settlement_id'] . ')' : ''?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_total_due">Total Due</label>
								<div class="col-sm-8">
									<div class="input-group">
										<span class="input-group-addon">&euro;</span>
										<input class="form-control" type="text" id="modal_make_payment_total_due" readonly="readonly" value="<?=$payment_plan_installment_to_pay ? $payment_plan_installment_to_pay['total'] : (count($bookings) > 0 && count($transactions) > 1 ? $outstanding : '0.00')?>" />
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_note">Note</label>
								<div class="col-sm-8">
									<textarea class="form-control"  id="modal_make_payment_note" rows="4" cols="6" name="note"></textarea>
								</div>
							</div>
							<input type="hidden" id="modal_make_payment_booking_id" name="booking_id" value="<?=$booking_id;?>">
							<input type="hidden" id="modal_make_payment_transaction_id" name="transaction_id" value="<?=@$transaction['id'];?>">
							<input type="hidden" id="modal_make_payment_transaction_balance" name="transaction_balance" value="<?=@$transaction['outstanding'] ;?>">
							<input type="hidden" id="modal_make_payment_booking_balance" name="booking_balance" value="<?=@$booking['outstanding'] ;?>">
						</fieldset>
					</div>
					<?// Credit Card Payment Options ?>
					<div class="make_payment_modal_column" id="card_payment_info" style="display: none;">
						<fieldset>
							<legend>Card Details</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_card_name">Card Name</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="modal_make_payment_card_name" name="ccName">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_card_type">Card Type</label>
								<div class="col-sm-8">
									<select class="form-control" id="modal_make_payment_card_type" name="ccType">
										<option value="">Please select</option>
										<option value="visa">Visa</option>
										<option value="mc">Mastercard</option>
										<option value="laser">Laser</option>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_card_number">Card Number</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="modal_make_payment_card_number" required name="ccNum" />
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_card_cvv">CVV</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="modal_make_payment_card_cvv" maxlength="4" required name="ccv" />
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_card_expired_m">Card expired</label>
								<div class="col-sm-4">
									<select class="form-control" id="modal_make_payment_card_expired_m" name="ccExpMM">
										<option value="">mm</option>
										<option value="01">01</option>
										<option value="02">02</option>
										<option value="03">03</option>
										<option value="04">04</option>
										<option value="05">05</option>
										<option value="06">06</option>
										<option value="07">07</option>
										<option value="08">08</option>
										<option value="09">09</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
									</select>
								</div>
								<div class="col-sm-4">
									<select class="form-control" id="modal_make_payment_card_expired_y" name="ccExpYY">
										<option value="">yyyy</option>
									</select>
								</div>
							</div>
						</fieldset>
					</div>
					<?php //Adding a input box to record name on cheque ?>
					<div class="make_payment_modal_column" id="cheque_options_info" style="display: none;">
						<fieldset>
							<legend>Cheque info</legend>
							<div class="form-group">
								<label class="sr-only" for="modal_make_payment_cheque_record_name">Cheques Options</label>
								<div class="col-sm-3"></div>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="modal_make_payment_cheque_record_name" name="name_cheque" placeholder="Cheque Name Holder" style="width: 200px;"/>
								</div>
							</div>
						</fieldset>
					</div>
					<? //Adding an input box for the credit ?>
					<div class="make_payment_modal_column" id="credit_journal_option" style="display: none;">
						<fieldset>
							<legend><span class="journal_title exceed hidden">The Amount exceeds the Balance</span> <span class="journal_title refund hidden">Refund</span></legend>
							<p id="modal_make_payment_overpay_alert"></p>
							<p>Do you want to use the balance to add credit to the family account?</p>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_payment_create_journal">Create Credit Journal</label>
								<div class="col-sm-8">
									<input type="checkbox" id="modal_make_payment_create_journal" name="create_journal" value=""/>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">Family credit</label>
								<div class="col-sm-8">
									<label><input type="radio" name="journal_type" value="family" checked /> Family Credit</label>
									<br>
									<label><input type="radio" name="journal_type" value="contact" /> Contact Credit</label>
								</div>
							</div>
						</fieldset>
					</div>
					<input type="hidden" id="modal_make_payment_id" name="id"/>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" data-content="Do not proceed with this payment">Cancel</a>
				<a href="#" class="btn btn-primary" id="make_payment_modal_btn" data-content="Save the payment and return to the transaction listing">Save Payment</a>
			</div>
		</div>
	</div>
</div>

<div id="modal_payment_amount_does_not_match" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Transactions amount total is not equal to payment amount</h3>
			</div>
			<div class="modal-body">
				<p>Transactions amount total is not equal to payment amount</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" data-content="Return to the booking form">Cancel</a>
			</div>
		</div>
	</div>
</div>
