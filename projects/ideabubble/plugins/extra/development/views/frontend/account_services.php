<h2>Your Services</h2>
<form class="service-form">
    <table class="zebra services-table">
        <thead>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Type</th>
                <th scope="col">Expiry</th>
                <th scope="col">Validity</th>
                <th scope="col">Billable</th>
                <th scope="col">Price</th>
                <th scope="col">Your price</th>
                <th scope="col">Status</th>
                <th scope="col">Pay</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; ?>
            <?php foreach ($services as $service): ?>
                <?php
                $price = ($service['price'] - $service['discount']);
                ?>
                <tr data-item_id="<?= $service['id']; ?>">
                    <td><?= $service['url'] ?></td>
                    <td><?= $service['service_type'] ?></td>
                    <td style="white-space:nowrap;">
                        <?php
                        if ($service['date_end'] != '0000-00-00 00:00:00') {
                            echo date('Y-m-d', strtotime($service['date_end']));
                        } else {
                            echo 'n/a';
                        }
                        ?>
                    </td>
                    <td></td>
                    <td><?= $service['billing_frequency'] ?></td>
                    <td><?= $service['price'] ?></td>
                    <?php
                    $payment_required = false;
                    $due = 0;
                    foreach ($service['invoices'] as $invoice) {
                        if ($invoice['status'] == 'Unpaid') {
                            $payment_required = true;
                            $due += $invoice['amount'];
                        }
                    }
                    ?>
                    <td><?= (!$payment_required) ? 'Not Due' : number_format($due, 2) ?></td>
                    <td><?= $service['status'] ?></td>
                    <td>
                    <?php
                    if (count($service['invoices']) > 0) {
                        foreach ($service['invoices'] as $invoice) {
                            if (strtotime($invoice['date_to']) < time()) {
                                continue;
                            }
                            ?>
                            <label for="pay_invoice_<?= $service['id'] ?>" class="hide">Pay service</label>
                            <?php
                            if ($payment_required) {
                                $total += $invoice['amount'];
                                ?>
                                <input id="pay_invoice_<?= $invoice['id'] ?>" type="checkbox" name="pay_invoice[]"
                                       value="<?= $invoice['id'] ?>" data-price="<?= $invoice['amount'] ?>" checked/>
                                <?php
                            }
                        }
                    } else {
                        echo 'contact accounts to create an invoice &amp; enable';
                    }
                    ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" style="border:0;text-align:right;">
                    <label for="service_payment_total">Payment Total:</label><input id="service_payment_total" type="text" disabled="disabled" value="<?= number_format($total, 2) ?>"/>
                </td>
            </tr>
        </tfoot>
    </table>
    <script>
    $("[name='pay_invoice[]']").on("change", function(){
        var total = 0.0;
        var $prices = $("[name='pay_invoice[]']");
        for(var i = 0 ; i < $prices.length ; ++i){
            if($prices[i].checked){
                total += parseFloat($($prices[i]).data('price'));
            }
        }
        $("#service_payment_total").val(total.toFixed(2));
    });
    </script>
    <label class="hide" for="service_payment_comments">Comments</label>
    <textarea id="service_payment_comments" placeholder="Enter any comments to help us process your payment"></textarea>

    <fieldset class="form-block" <?=$total == 0 ? 'style="display:none;"' : ''?>>
        <legend>Your payment details</legend>
        <div>
            <label for="payment_fullname" class="form-label">Full Name</label>
            <input id="payment_fullname" name="fullname" type="text"/>
        </div>

        <div>
            <label for="payment_phone" class="form-label">Phone</label>
            <input id="payment_phone" name="phone" type="text"/>
        </div>

        <div>
            <label for="payment_email" class="form-label">Email</label>
            <input id="payment_email" name="email" type="text"/>
        </div>

        <img src="<?= URL::site() ?>assets/default/images/secure-payment-2.png" alt="visa"/>
    </fieldset>

    <fieldset class="form-block" <?=$total == 0 ? 'style="display:none;"' : ''?>>
        <legend>Credit Card Payment</legend>

        <?php if(count($cards) > 0){ ?>
        <div id="select-card">
        <div>
            <label for="realvault_card_id" class="form-label">Use a saved card</label>
            <select id="realvault_card_id" name="realvault_card_id">
                <option value=""></option>
                <?php foreach($cards as $card){ ?>
                <option value="<?=$card['id']?>"><?=$card['card_number'] . ' (' . date('m/Y', strtotime($card['expdate'])) . ')'?></option>
                <?php } ?>
            </select>
            <script>
            $("#realvault_card_id").on("change", function(){
                if(this.selectedIndex > 0){
                    $("#new-card").hide();
                } else {
                    $("#new-card").show();
                }
            });
            </script>
        </div>
        </div>
        <?php } ?>
        <div id="new-card">
        <div>
            <label for="cc_card_type" class="form-label">Card Type</label>
            <select id="cc_card_type" name="card_type">
                <option value="">-- Select Type --</option>
                <option value="visa">Visa</option>
                <option value="mc">Mastercard</option>
                <option value="laser">Laser</option>
            </select>
        </div>

        <div>
            <label for="cc_name_on_card" class="form-label">Name on Card</label>
            <input id="cc_name_on_card" name="name_on_card" type="text"/>
        </div>

        <div>
            <label for="cc_card_number" class="form-label">Card Number</label>
            <input id="cc_card_number" name="card_number" type="text" maxlength="19"/>
        </div>

        <div>
            <label for="cc_ccv_number" class="form-label">CCV Number</label>
            <input id="cc_ccv_number" name="ccv_number" type="text" maxlength="4"/>
        </div>

        <div>
            <label class="form-label" for="cc_ExpMM">Expiry</label>
            <select id="cc_ExpMM" name="ccExpMM" data-validation-engine="validate[required]">
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
            <label for="cc_ExpYY" class="hide">Expiry Year</label>
            <select id="cc_ExpYY" name="ccExpYY" data-validation-engine="validate[required]">
                <option value="">yyyy</option>
                <?php
                for ($i = date('y'); $i <= (date('y') + 10); $i++) {
                    $j = str_pad($i, 2, "0", STR_PAD_LEFT);
                    echo "<option value='$j'>20$j</option>\n";
                }
                ?>
            </select>
        </div>
        <div>
            <label class="form-label" for="save_card">Save Card</label>
            <input type="checkbox" name="save_card" id="save_card" /><span style="font-size: 8px;">(* stored securely on real vault)</span>
        </div>
        </div>
    </fieldset>

    <button type="button" id="submit_payment">Next</button>
</form>