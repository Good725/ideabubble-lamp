<div id="make_transaction_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Make Transaction</h3>
			</div>
			<div class="modal-body form-horizontal">
				<form id="make_transaction_modal_form" method="post" action="">
					<div class="alert-area"></div>

					<div class="field">
						<fieldset>
							<legend>Contact</legend>
							<div style="display: inline-flex">
								<div class="text_label">Name</div>
								<input class="form-control" type="text" id="modal_make_transaction_contact_name" readonly="readonly" style="width: 200px;">
								<div class="text_label">Booking</div>
								<select class="form-control" id="modal_make_transaction_booking" name="booking_id" style="width: 150px;">
									<option value="">Select Booking</option>
								</select>
							</div>
                            <div style="display: inline-flex">
                                <div class="text_label">Schedule</div>
                                <select class="form-control" id="modal_make_transaction_booking_schedule" name="schedule_id" style="width: 150px;">
                                    <option value="">Select Schedule</option>
                                </select>
                            </div>
						</fieldset>
					</div>

					<div class="field">
						<fieldset>
							<legend>Transaction</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_transaction_type">Type</label>
								<div class="col-sm-8">
									<select class="form-control" id="modal_make_transaction_type" name="type">
										<option value="">Please select type</option>
										<?php
										$transaction = ORM::factory('Kes_Transaction');
										$types = $transaction->get_transaction_types();
										foreach($types as $key=>$type){
											echo '<option value="'. $type['id'] .'">'. $type['type'] .'</option>';
										}
										?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_transaction_billed">Billing</label>
								<div class="col-sm-8">
									<input type="checkbox" id="modal_make_transaction_billed" name="transaction_billed" value="billed">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_transaction_amount">Amount</label>
								<div class="col-sm-8">
									<div class="input-group">
										<span class="input-group-addon">&euro;</span>
										<input class="form-control" type="text" id="modal_make_transaction_amount" required name="amount" value="0.00" />
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_transaction_discount">Discount</label>
								<div class="col-sm-8">
									<div class="input-group">
										<span class="input-group-addon">&euro;</span>
										<input class="form-control" type="text" id="modal_make_transaction_discount" name="discount" value="0.00" />
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_transaction_admin_fee">Admin Fee</label>
								<div class="col-sm-8">
									<div class="input-group">
										<span class="input-group-addon">&euro;</span>
										<input class="form-control" type="text" id="modal_make_transaction_admin_fee" name="fee" value="0.00" />
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_transaction_total">Total</label>
								<div class="col-sm-8">
									<div class="input-group">
										<span class="input-group-addon">&euro;</span>
										<input class="form-control" type="text" id="modal_make_transaction_total" readonly="readonly" required name="total" value="0.00" />
									</div>
								</div>
							</div>
						</fieldset>
					</div>
					<input type="hidden" id="modal_make_transaction_id" name="id">
					<input type="hidden" id="modal_make_transaction_contact_id" name="contact_id">
					<input type="hidden" id="modal_make_transaction_family_id" name="family_id">
					<div id="bill_payer_information" class="field" style="display: none">
						<fieldset>
							<legend>Bill Payer Information</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="modal_make_transaction_bill_payer_name">Search Contact</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" id="modal_make_transaction_bill_payer_name" class="ui-autocomplete-input"
										   placeholder="Type to search contacts" value="" name="payer_name" autocomplete="off">
									<input type="hidden" id="madal_make_transaction_bill_payer_id" name="payer_id" value="">
								</div>
							</div>
						</fieldset>
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<a href="#" id="make_transaction_modal_btn" class="btn btn-primary" data-content="Save the current transaction and return to the list">Save Transaction</a>
				<a href="#" class="btn" data-dismiss="modal" data-content="Return to the transaction list without saving the content">Cancel</a>
			</div>
		</div>
	</div>
</div>