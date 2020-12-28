<div id="cancel_booking_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Transaction: Cancel Transaction #<?=$transaction['id'];?></h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
				<form id="cancel_booking_modal_form" method="post" action="">
					<div id="cancel_booking_info">
						<fieldset>
							<legend>Transaction Details</legend>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" for="cancel_booking_modal_transaction_details">Details</label>
								<div class="col-sm-8">
									<textarea class="form-control" id="cancel_booking_modal_transaction_details" cols="5" rows="4" readonly="readonly">Schedule: #<?=$transaction['schedule_id'];?> <?=$transaction['schedule'] . "\n";?>Course: <?=$transaction['course'] . "\n";?>Transaction total: &euro;<?=$transaction['total'] . "\n";?>Amount Payed: &euro;<?=$transaction['payed'];?></textarea>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" for="cancel_booking_modal_payed">Payed Amount</label>
								<div class="col-sm-8">
									<div class="input-prepend">
										<div class="input-group">
											<span class="input-group-addon">&euro;</span>
											<input class="form-control" type="text" id="cancel_booking_modal_payed" readonly="readonly" value="<?=$transaction['payed'];?>" />
										</div>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" for="credit_transaction">Add Credit Balance</label>
								<div class="btn-group col-sm-8" data-toggle="buttons" id="add_credit_balance">
                                    <label class="btn btn-default">
                                        <input type="radio" name="credit_transaction" value="yes" />Yes
                                    </label>
                                    <label class="btn btn-default active">
                                        <input type="radio" name="credit_transaction" value="no" checked/>No
                                    </label>
								</div>
							</div>

                            <div id="cancel_booking_refund_details">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="cancel_booking_modal_credit_amount">Credit Amount:</label>
                                    <div class="col-sm-8">
                                        <div class="input-prepend">
                                            <div class="input-group">
                                                <span class="input-group-addon">&euro;</span>
                                                <input class="form-control" type="text" id="cancel_booking_modal_credit_amount" readonly="readonly" value="0.00" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p>Do you want to add credit to the family or the contact account?</p>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Add Credit</label>
                                    <div class="btn-group col-sm-8"  data-toggle="buttons" id="add_credit_to_type">
                                        <label class="btn btn-default active">
                                            <input type="radio" name="journal_type" value="family" checked />Family
                                        </label>
                                        <label class="btn btn-default">
                                            <input type="radio" name="journal_type" value="contact" />Contact
                                        </label>
                                    </div>
                                </div>

								<div class="form-group family_credit" style="display: none">
									<label class="col-sm-3 control-label" for="cancel_booking_modal_note">Credit to Family</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" id="credit_to_family_autocomplete" value="" /><span>(Leave empty if you do not want to transfer another family)</span>
										<input type="hidden" name="credit_to_family_id" value=""/>
									</div>
								</div>

								<div class="form-group contact_credit" style="display: none">
									<label class="col-sm-3 control-label" for="cancel_booking_modal_note">Credit to Contact</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" id="credit_to_contact_autocomplete" value="" /><span>(Leave empty if you do not want to transfer another contact)</span>
										<input type="hidden" name="credit_to_contact_id" value=""/>
									</div>
								</div>
                            </div>
							<script type="text/javascript">$('#cancel_booking_refund_details').hide();</script>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="cancel_booking_modal_note">Note</label>
								<div class="col-sm-8">
									<textarea class="form-control" id="cancel_booking_modal_note" cols="5" rows="4"></textarea>
								</div>
							</div>
						</fieldset>
					</div>
					<input type="hidden" id="cancel_booking_modal_transaction_id" value="<?=$transaction['id'];?>">
					<input type="hidden" id="cancel_booking_modal_booking_id" value="<?=$transaction['booking_id'];?>">
					<input type="hidden" id="cancel_booking_modal_transaction_balance" value="0.00">
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" data-content="Do not cancel the selected booking">Cancel</a>
				<a href="#" class="btn btn-primary" id="cancel_booking_modal_btn" data-content="Cancel the current booking">Save</a>
			</div>
		</div>
	</div>
</div>
