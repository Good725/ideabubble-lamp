<div class="alert_payment_failed modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Payment Failed</h3>
            </div>
            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>
                <p>Payment has been failed</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Ok</a>
            </div>
        </div>
    </div>
</div>

<div class="alert_no_selected_transaction modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Transaction hasn't selected</h3>
            </div>
            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>
                Please select a transaction.
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Ok</a>
            </div>
        </div>
    </div>
</div>

<div class="alert_changed_transaction modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Stale Data</h3>
            </div>
            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>
                <p>Transaction has already been changed by someone else. Please reload transaction.</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Ok</a>
            </div>
        </div>
    </div>
</div>

<div class="make_payment modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Make a payment for transaction #<span class="transaction_id"></span> </h3>
            </div>
            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>
                <form id="make_payment_modal_form" class="make_payment" method="post" action="/admin/transactions/make_payment">
                    <div class="make_payment_modal_column" id="main_payment_info">
                        <fieldset>
                            <legend>Payment Details</legend>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="modal_make_payment_outstanding">Outstanding balance</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon">&euro;</span>
                                        <input class="form-control outstanding" type="text" readonly="readonly" value="" style="width: 180px;" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="gateway">Payment type</label>
                                <div class="col-sm-8">
                                    <select class="form-control gateway validate[required]" name="gateway" required>
                                        <option value="">Please select type</option>
                                        <?php
                                        foreach (Model_TransactionPayments::get_gateway_handlers() as $gateway_handler) {
                                            if ($gateway_handler->is_ready()) {
                                                ?>
                                                <option value="<?=$gateway_handler->name()?>"><?=$gateway_handler->title()?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="modal_make_payment_amount">Payment Amount</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><select class="validate[required]" name="currency"><option value="EUR" selected="selected">&euro;</option> </select> </span>
                                        <input class="form-control validate[required]"  type="text" id="modal_make_payment_amount" required name="amount" value="" style="width: 180px;"/>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="modal_make_payment_note">Note</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control"  id="modal_make_payment_note" rows="4" cols="6" name="note"></textarea>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <?php
                    foreach (Model_TransactionPayments::get_gateway_handlers() as $gateway_handler) {
                        $gw_inputs = $gateway_handler->get_inputs();
                        if ($gw_inputs != '') {
                            ?>
                            <div class="make_payment_modal_column gateway <?=$gateway_handler->name()?>" style="display: none;"><?=$gw_inputs?></div>
                            <?php
                        }
                    }
                    ?>
                    <input type="hidden" name="transaction_id" value="">
                    <input type="hidden" name="transaction_updated" value="">
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn" data-dismiss="modal" data-content="Do not proceed with this payment">Cancel</a>
                <a class="btn save" data-content="Save the payment and return to the transaction listing">Save Payment</a>
            </div>
        </div>
    </div>
</div>
