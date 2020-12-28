<?= (isset($alert)) ? $alert : ''; ?>
<form class="educate-form form-horizontal" id="kes_booking_edit_form" method="post" action="<?=URL::site();?>admin/bookings/process_booking">
    <fieldset>
        <legend>Payments</legend>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="payment_type_selector">Select Payment Method</label>
            <div class="col-sm-8">
                <select id="payment_type_selector">
                    <option value="">Please select a payment type</option>
                    <?php foreach($payment_types as $key=>$payment_type): ?>
                        <option value="<?=$payment_type['id'];?>"><?=$payment_type['title'];?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-success">Add</button>
            </div>
        </div>

        <table id="payments_table" class="table table-striped">
            <thead>
                <tr>
                    <th>Payment Type</th>
                    <th>Payment Amount</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </fieldset>
</form>

<div id="cash_payment_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Cash Payment Modal</h3>
			</div>
			<div class="modal-body">
				<p>
					Amount &euro;:<input type="text" id="cash_payment_amount" value=""/>
				</p>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true" data-content="Do not procedd with the selected payment">Close</button>
				<button class="btn btn-primary" id="add_cash_payment" data-content="Proceed with the payment on the selected transaction">Save changes</button>
			</div>
		</div>
	</div>
</div>