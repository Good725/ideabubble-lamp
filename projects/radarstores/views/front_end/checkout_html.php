<script type="text/javascript">var shared_assets = "<?=URL::get_engine_assets_base() ?>";</script>
<form id="creditCardForm">
<input type="hidden" value="thanks-for-shopping-with-us.html" name="thanks_page" id="thanks_page">

<div class="checkout_wrapper">
<?php echo IbHelpers::get_messages(); //The message. example: "Error processing the payment ?>
<div class="content min-height">
<?php
$paypal_enabled = (isset($paypal_enabled) AND $paypal_enabled == TRUE);
$realex_enabled = (Settings::instance()->get('enable_realex') == 1 AND Settings::instance()->get('realex_username') != '');
?>

<?php if ($paypal_enabled AND $realex_enabled): ?>
    <div id="paymentSelect">
        <div>
            <p>Select your Payment Method</p>
            <span onclick="changeMethod(this);" class="payment_method credit_card_method" id="method_1">Checkout with Credit Card</span>
            <span onclick="changeMethod(this);" class="payment_method paypal_method" id="method_2">PayPal</span>
        </div>
    </div>

<?php endif; ?>

<div id="checkoutTable">
    <table class="checkoutTable">
        <tbody>
            <tr>
                <th scope="col">Product</th>
                <th scope="col" class="priceField">Price</th>
                <th scope="col" class="center">Qty</th>
                <th scope="col" class="priceField">Total</th>
                <th></th>
            </tr>

            <?= $products_list ?>

            <tr class="tr_destination">
                <td class="product_line_first_td">
                    Select Postal Destination:
                    <select id="postalZone" class="validate[required]" onchange="changeZone(this.value)" name="zones">
                        <option value="">-- Select Postal Zone --</option>
                        <?php foreach ($postage as $place): ?>
                            <option value="<?= $place['id'] ?>"  <?php if ($place['id'] == @$zone_id) echo 'selected="selected" ' ?>><?= $place['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>Postage</td>
                <td></td>
                <td class="priceField postage">€<?= @$shipping_price ?>
                    <input type="hidden" value="Postage + Packaging" name="item_name_2"><input type="hidden" value="1" name="quantity_2"><input type="hidden" value="3.99" name="amount_2">
                </td>
                <td></td>
            </tr>
            <tr class="tr_totalprice product_line_first_td">
                <td rowspan="2">
					<?php if (isset($accept_coupons) AND $accept_coupons): ?>
						<!-- Coupon code -->
						<div class="checkout_option">
							<div class="txt_lable">Coupon code</div>
							<div class="input_lable">
								<input type="text" id="coupon_code"/><input type="button" value="Validate" onclick="validate_coupon()"/>
							</div>
							<div class="checkout_option_description"><br />Enter a code to receive a discount.</div>
						</div>
					<?php endif; ?>
                </td>
                <td>Subtotal</td>
                <td></td>
                <td class="subtotal">&euro;<?= @$subtotal ?></td>
                <td></td>
            </tr>
            <tr class="tr_totalprice product_line_first_td">
                <td>Discounts</td>
                <td></td>
                <td class="discounts">&minus; &euro;<?= @$discounts ?></td>
                <td></td>
            </tr>
            <tr class="tr_totalprice product_line_first_td">
                <td>
                    <span class="continue-shopping">
                        <?php if (($last_category = Session::instance()->get('last_category')) !== NULL): ?>
                            <a href="<?php echo $last_category ?>">CONTINUE SHOPPING >></a>
                            <?php Session::instance()->delete('last_category') ?>
                        <?php else: ?>
                            <a href="products.html">CONTINUE SHOPPING >></a>
                        <?php endif ?>
                    </span>
                </td>
                <td class="highlight_total">Total</td>
                <td class="highlight_total"></td>
                <td class="highlight_total priceField totalprice">€<?= @$final_price ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<?php if (isset($paypal_enabled) AND $paypal_enabled == TRUE): ?>

    <script type="text/javascript">
        $('#method_1').click();
        function paypal_checkout() {
            if ($('#postalZone').val() == '') {
                alert('Please select a postal region.');
            }
            else {
                CHECKOUT.checkoutWithPayPal(
                    '<?=URL::site()?>thanks-for-shopping-with-us.html',
                    '<?=URL::site()?>',
                    function (status, data) {
                        checkout_data.paypal_error(status, data);
                    }
                );
            }
        }
    </script>

    <div id="paypalButton" class="payment_method_view"<?= ($realex_enabled) ? ' style="display: none;"' : '' ?>>
        <a onclick="paypal_checkout()"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"></a>
    </div>
<?php endif; ?>

<p class="note">
<?php if ($realex_enabled): ?>
	<div id="checkoutForm" class="payment_method_view"<?= ($paypal_enabled) ? ' style="display: none;"' : '' ?>>
		<form action="/frontend/payment" method="post" id="creditCardForm" name="creditCardForm" action="">
			<input type="hidden" value="1199" id="total" name="total">

			<div id="CartDetailsBox">
				<div id="CustomerAddress">
					<table class="CartDetails">
						<tbody>
							<tr>
								<td colspan="2"><h2>Name and address</h2></td>
							</tr>
							<tr>
								<td class="label"><label for="ccName">Full Name:</label></td>
								<td>
									<input type="text" style="width: 200px;" maxlength="50" name="ccName" id="ccName" value="" class="validate[required] text-input">
								</td>
							</tr>
							<tr>
								<td class="label"><label for="address_1">Address:</label></td>
								<td>
									<input type="text" style="width: 200px;" maxlength="70" name="address_1" id="address_1" value="" class="validate[required] text-input">
								</td>
							</tr>
							<tr>
								<td class="label"><label for="address_2">Town/City:</label></td>
								<td>
									<input type="text" style="width: 100px;" maxlength="70" name="address_2" id="address_2" value="" class="validate[required] text-input">
								</td>
							</tr>
							<tr>
								<td class="label"><label for="address_3">State/County:</label></td>
								<td>
									<input type="text" style="width: 100px;" maxlength="70" name="address_3" id="address_3" value="" class="validate[required] text-input">
								</td>
							</tr>
							<tr>
								<td class="label"><label for="address_4">Country:</label></td>
								<td>
									<input type="text" style="width: 100px;" maxlength="70" name="address_4" id="address_4" value="Ireland" class="validate[required] text-input">
								</td>
							</tr>
							<tr>
								<td class="label"><label for="phone">Tel/Mobile:</label></td>
								<td>
									<input type="text" style="width: 100px;" maxlength="70" name="phone" id="phone" value="" class="validate[required] text-input">
								</td>
							</tr>
							<tr>
								<td class="label"><label for="email">Email:</label></td>
								<td>
									<input type="text" style="width: 180px;" maxlength="70" name="email" id="email" value="" class="validate[required,custom[email]] text-input">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<label><input type="checkbox" name="addressCheckbox" id="addressCheckbox" onclick="shippingAddress()" value="1" checked="checked"> ship to the same address</label>
								</td>
							</tr>
						</tbody>
					</table>

					<div style="display: none;" id="shippingAddressDiv">
						<table class="CartDetails">
							<tbody>
								<tr>
									<td colspan="2"><h2>Shipping Address</h2></td>
								</tr>
								<tr>
									<td><label for="shipping_name">Name:</label></td>
									<td>
										<input type="text" style="width: 200px;" maxlength="50" name="shipping_name" id="shipping_name" value="">
									</td>
								</tr>
								<tr>
									<td><label for="shipping_address_1">Address:</label></td>
									<td>
										<input type="text" style="width: 200px;" maxlength="70" name="shipping_address_1" id="shipping_address_1" value="">
									</td>
								</tr>
								<tr>
									<td><label for="shipping_address_2">Town/City:</label></td>
									<td>
										<input type="text" style="width: 100px;" maxlength="70" name="shipping_address_2" id="shipping_address_2" value="">
									</td>
								</tr>
								<tr>
									<td><label for="shipping_address_3">State/County:</label></td>
									<td>
										<input type="text" style="width: 100px;" maxlength="70" name="shipping_address_3" id="shipping_address_3" value="">
									</td>
								</tr>
								<tr>
									<td><label for="shipping_address_4">Country:</label></td>
									<td>
										<input type="text" style="width: 100px;" maxlength="70" name="shipping_address_4" id="shipping_address_4" value="">
									</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div id="CardDetails">
					<table class="CardDetails">
						<tbody>
							<tr>
								<td colspan="2"><h2>Credit Card Payment Details</h2></td>
							</tr>
							<tr>
								<td class="label"><label for="ccType">Card type:</label></td>
								<td>
									<select name="ccType" id="ccType" class="validate[required]">
										<option value="">Please select</option>
										<option value="visa">Visa</option>
										<option value="mc">Mastercard</option>
										<option value="laser">Laser</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="label"><label for="ccNum">Card number:</label></td>
								<td>
									<input type="text" style="width: 180px;" maxlength="70" name="ccNum" value="" id="ccNum" class="validate[required] text-input">
								</td>
							</tr>
							<tr>
								<td class="label"><label for="ccv">CVV number:</label></td>
								<td>
									<input type="text" style="width: 180px;" maxlength="4" id="ccv" name="ccv" value="" class="validate[required] text-input">
								</td>
							</tr>
							<tr>
								<td class="label"><label for="ccExpMM">Expiry date:</label></td>
								<td>
									<select name="ccExpMM" id="ccExpMM" class="validate[required]">
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
									<select name="ccExpYY" id="ccExpYY" class="validate[required]">
										<option value="">yyyy</option>
										<?php
										for ($i = date('y'); $i <= (date('y') + 10); $i++) {
											$j = str_pad($i, 2, "0", STR_PAD_LEFT);
											echo "<option value='$j'>20$j</option>\n";
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="credit_cars">
									<img alt="master card image" src="<?= URL::site() ?>assets/default/images/mastercard.png">
									<img alt="visa card image" src="<?= URL::site() ?>assets/default/images/visa.png">
									<img alt="master card image" src="<?= URL::site() ?>assets/default/images/realex_icon.png">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<span class="realexmessage">We use a secure certificate for all our payments and Realex our payment partner provide all secure connections for your transaction.</span>
								</td>
							</tr>
							<tr>
								<td colspan="2"><span class="comments"><label for="comments">Comments:</label></span></td>
							</tr>
							<tr>
								<td colspan="2"><textarea id="comments" rows="3" cols="30" name="comments"></textarea></td>
							</tr>
						</tbody>
					</table>
				</div>

				<div id="checkoutMessageBar"></div>

				<div id="FinalDetails">
					<table class="FinalDetails">
						<tbody>
							<tr>
								<td>
									<label><input type="checkbox" id="signupCheckbox" value="1" name="signupCheckbox"> Check to add email address so we can contact you</label>
								</td>
							</tr>
							<tr>
								<td>
									<label>
										<input type="checkbox" id="termsCheckbox" name="termsCheckbox" class="validate[required]">
										I accept the
										<a target="_blank" href="<?= URL::site() ?>terms-and-conditions.html">terms and conditions</a>
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<div class="submit_checkout_button" style="padding-left: 9px;">
										<input type="button" onclick="submitCheckout()" value="Submit Payment ››" class="button">
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div style="clear: both">&nbsp;</div>
			</div>
		</form>
	</div>
<?php endif; ?>
</div>
</div>
</form>

<link href="<?= URL::site() ?>assets/default/css/checkout.css" rel="stylesheet" type="text/css"/>
<link href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/jqueryui-lightness/jquery-ui-1.10.3.min.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/checkout.js"></script>
<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/jquery.validationEngine2.js"></script>
<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/jquery.validationEngine2-en.js"></script>
<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/jquery-ui-1.10.3.min.js"></script>
