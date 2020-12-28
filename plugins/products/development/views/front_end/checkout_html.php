<?php
if (isset($_SESSION[Model_Checkout::CART_SESSION_ID]->payback_loyalty_discount)) {
    $pb_discount = '&euro; -' . $_SESSION[Model_Checkout::CART_SESSION_ID]->payback_loyalty_discount;
    $pb_discount_label = 'Payback Loyalty discount';
} else {
    $pb_discount = '';
    $pb_discount_label = '';
}
?>

<?php if (!empty($products_list)): ?>
    <script type="text/javascript">var shared_assets = "<?=URL::get_engine_assets_base() ?>";   </script>
    <form id="creditCardForm">
    <input type="hidden" value="<?= Model_Payments::get_thank_you_page(['full_url' => false]); ?>" name="thanks_page" id="thanks_page">
    <input type="hidden" value="Checkout Form" name="subject" id="subject">

    <div class="checkout_wrapper">
    <div id="checkout_messages">
        <?php echo IbHelpers::get_messages(); //The message. example: "Error processing the payment ?>
    </div>
    <div class="min-height">

    <div id="checkoutTable">
        <table class="checkoutTable">
            <tbody>
                <tr>
                    <th class="checkout_title">PRODUCT NAME</th>
                    <th class="priceField"></th>
                    <th class="center"></th>
                    <th class="priceField"></th>
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
                    <td>Postage:</td>
                    <td></td>
                    <td class="priceField postage">&euro;<?= @number_format($shipping_price, 2) ?></td>
                    <td></td>
                </tr>
                <tr class="tr_totalprice product_line_first_td">
                    <td>
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
                    <td>Subtotal:</td>
                    <td></td>
                    <td class="priceField subtotal">&euro;<?= @number_format($subtotal, 2) ?></td>
                    <td></td>
                </tr>
                <? if (!empty($discounts)) { ?>
                    <tr class="tr_totalprice product_line_first_td">
                        <td>Discounts</td>
                        <td></td>
                        <td class="discounts">&euro;<?= @$discounts ?></td>
                        <td></td>
                    </tr>
                <? } ?>
                <tr class="tr_totalprice product_line_first_td">
                    <td>
                        <span class="continue-shopping">
                            <?php if (($last_category = Session::instance()->get('last_category')) !== NULL): ?>
                                <a href="<?php echo $last_category ?>">CONTINUE SHOPPING >></a>
                                <?php Session::instance()->delete('last_category') ?>
                            <?php else: ?>
                                <a href="/<?= isset($products_plugin_page) ? $products_plugin_page : 'products.html' ?>">CONTINUE SHOPPING >></a>
                            <?php endif ?>
                        </span>
                    </td>
                    <td class="highlight_total">Total</td>
                    <td class="highlight_total"></td>
                    <td class="highlight_total priceField totalprice">&euro;<?= @number_format($final_price, 2) ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if (isset($paypal_enabled) AND $paypal_enabled == true): ?>
        <div id="paymentSelect">
            <div>
                <p>Select your Payment Method</p>
                <select id="payment_method_selector">
                    <option value="card" selected>Debit/Credit Card</option>
                    <option value="paypal">Paypal</option>
                </select>
                <span style="display:none;" onclick="changeMethod(this);" class="payment_method grey" id="method_1">method 1</span>
                <span style="display:none;" onclick="changeMethod(this);" class="payment_method grey" id="method_2">method 2</span>
            </div>
        </div>
        <script>
            $("#payment_method_selector").change(function () {
                if ($("#payment_method_selector").val() === "card") {
                    $("#method_1").click();
                }
                else if ($("#payment_method_selector").val() === "paypal") {
                    $("#method_2").click();
                }
            });
            $(document).ready(function () {
                $("#method_1").click();
            });
        </script>
        <br/><br/>
        <div id="paypalButton" class="payment_method_view" style="display: none">
            <a href="javascript:CHECKOUT.checkoutWithPayPal('<?= Model_Payments::get_thank_you_page(); ?>', '<?= URL::site() ?>', function(status,data){ checkout_data.paypal_error(status,data); });"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;"></a>
        </div>
    <?php endif; ?>

    <p class="note">
    <div id="checkoutForm" class="payment_method_view" <?php if (isset($paypal_enabled) AND $paypal_enabled == true) echo 'style="display: none"' ?> >
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
                        <td class="label">Full Name:</td>
                        <td>
                            <input type="text" style="width: 200px;" maxlength="50" name="ccName" id="ccName" value="" class="validate[required] text-input">
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Address:</td>
                        <td>
                            <input type="text" style="width: 200px;" maxlength="70" name="address_1" id="address_1" value="" class="validate[required] text-input">
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Town/City:</td>
                        <td>
                            <input type="text" style="width: 100px;" maxlength="70" name="address_2" id="address_2" value="" class="validate[required] text-input">
                        </td>
                    </tr>
                    <tr>
                        <td class="label">State/County:</td>
                        <td>
                            <input type="text" style="width: 100px;" maxlength="70" name="address_3" id="address_3" value="" class="validate[required] text-input">
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Country:</td>
                        <td>
                            <input type="text" style="width: 100px;" maxlength="70" name="address_4" id="address_4" value="Ireland" class="validate[required] text-input">
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Tel/Mobile:</td>
                        <td>
                            <input type="text" style="width: 100px;" maxlength="70" name="phone" id="phone" value="" class="validate[required] text-input">
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Email:</td>
                        <td>
                            <input type="text" style="width: 180px;" maxlength="70" name="email" id="email" value="" class="validate[required,custom[email]] text-input">
                        </td>
                    </tr>

					<?php if (Settings::instance()->get('checkout_gift_option')): ?>
						<tr>
							<td></td>
							<td colspan="1">
								<label>
									<input
                                        type="checkbox"
                                        name="is_gift"
                                        id="checkout-is_gift" />
                                    <?= __('Mark as a Gift?') ?>
								</label>
							</td>
						</tr>
						<tr class="hidden" id="checkout-gift_card_text-wrapper">
							<td class="label"><label for="checkout-gift_card_text"><?= __('Gift card text') ?>:</label></td>
							<td>
								<textarea
									name="giftcard_text"
									id="checkout-gift_card_text"
									rows="4"
									placeholder="<?= __('Type a message to appear on the gift card') ?>"
									disabled
									></textarea>
							</td>
						</tr>
					<?php endif; ?>

                    <tr>
                        <td class="label comments">Comments:</td>
                        <td><textarea id="comments" rows="3" cols="30" name="comments"></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="checkbox" name="addressCheckbox" id="addressCheckbox" onclick="shippingAddress()" value="1" checked="checked"> ship to the same address
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
                            <td>Name:</td>
                            <td>
                                <input type="text" style="width: 200px;" maxlength="50" name="shipping_name" id="shipping_name" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>Address:</td>
                            <td>
                                <input type="text" style="width: 200px;" maxlength="70" name="shipping_address_1" id="shipping_address_1" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>Town/City:</td>
                            <td>
                                <input type="text" style="width: 100px;" maxlength="70" name="shipping_address_2" id="shipping_address_2" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>State/County:</td>
                            <td>
                                <input type="text" style="width: 100px;" maxlength="70" name="shipping_address_3" id="shipping_address_3" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>Country:</td>
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
                        <td class="label">Card type:</td>
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
                        <td class="label">Card number:</td>
                        <td>
                            <input type="text" style="width: 180px;" maxlength="19" name="ccNum" value="" id="ccNum" class="validate[required,funcCall[luhnTest]] text-input">
                        </td>
                    </tr>
                    <tr>
                        <td class="label">CVV number:</td>
                        <td>
                            <input type="text" style="width: 50px;" maxlength="4" id="ccv" name="ccv" value="" class="validate[required,custom[onlyNumberSp]] text-input">
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Expiry date:</td>
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
                            <img alt="master card image" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/mastercard.png">
                            <img alt="visa card image" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/visa.png">
                            <img alt="master card image" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/realex_icon.png">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="realexmessage">We use a secure certificate for all our payments and Realex our payment partner provide all secure connections for your transaction.</span>
                        </td>
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
                            <input type="checkbox" id="signupCheckbox" value="1" name="signupCheckbox"> Check to add email address so we can contact you
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" id="termsCheckbox" name="termsCheckbox" class="validate[required]">
                            I accept the
                            <a target="_blank" href="<?= URL::site() ?>terms-and-conditions.html">terms and conditions</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkout-recaptcha" style="margin: .5em 0;">
                                <?php
                                $captcha_enabled = Settings::instance()->get('captcha_enabled');
                                if ($captcha_enabled) {
                                    require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                                    $captcha_public_key = Settings::instance()->get('captcha_public_key');
                                    echo recaptcha_get_html($captcha_public_key);
                                }
                                ?>
                            </div>

                            <div id="submit_checkout_button" class="left product_btn submit_checkout_button" onclick="submitCheckout();">
                                <span class="left btn_big_left_bg">&nbsp;</span>
                                <span class="left btn_big_mid_bg"><span class="strong">BUY NOW &raquo;</span></span>
                                <span class="left btn_big_right_bg">&nbsp;</span>
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
    </div>
    </div>
    </form>
    <?php

    // Render CSS Files for THIS View
    if (isset($view_css_files)) {
        foreach ($view_css_files as $css_item_html) echo $css_item_html;
    }
    // Render JS Files for This View
    if (isset($view_js_files)) {
        foreach ($view_js_files as $js_item_html) echo $js_item_html;
    }

endif;
?>
