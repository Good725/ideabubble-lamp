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
                <?php for ($i = 0; $i < 15; $i++){ ?>
                <option value="<?= date('y') + $i ?>"><?= date('Y') + $i ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</fieldset>