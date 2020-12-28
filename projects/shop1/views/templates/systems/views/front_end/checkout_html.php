<?php
if (isset($_SESSION[Model_Checkout::CART_SESSION_ID]->payback_loyalty_discount)) {
    $pb_discount = '&euro; -' . $_SESSION[Model_Checkout::CART_SESSION_ID]->payback_loyalty_discount;
    $pb_discount_label = 'Payback Loyalty discount';
} else {
    $pb_discount = '';
    $pb_discount_label = '';
}

$stripe['enabled'] = (Settings::instance()->get('stripe_enabled') == 'TRUE');
$realex_enabled = (Settings::instance()->get('enable_realex') != 1) ? FALSE : TRUE;
$sagepay_enabled = (Settings::instance()->get('sagepay') == 1);
$boipa_enabled = (Settings::instance()->get('boipa_enable') == 1);

if ($stripe['enabled']) {
    require_once APPPATH . '/vendor/stripe/lib/Stripe.php';
    $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
    $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
    $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
    Stripe::setApiKey($stripe['secret_key']);
}
$paypal_enabled = (isset($paypal_enabled) AND $paypal_enabled == TRUE);
$payment_method_count = $paypal_enabled ? 1 : 0;
$payment_method_count += $realex_enabled ? 1 : 0;
$payment_method_count += $stripe['enabled'] ? 1 : 0;
$payment_method_count += $sagepay_enabled ? 1 : 0;
$payment_method_count += $boipa_enabled ? 1 : 0;

$default_payment = $stripe['enabled'] ? 'Stripe' :
	($realex_enabled ? 'Realex' :
		($paypal_enabled ? 'PayPal' :
			($boipa_enabled ? 'boipa' : '')));
if($sagepay_enabled){
	$default_payment = 'sagepay';
}

?>

<?php // Horrible inline styling, since there's no shared CSS ?>
<style type="text/css">
    #method_1 {
        background-image: url('/assets/<?=Kohana::$config->load('config')->assets_folder_path;?>/images/pay_with_credit_card_grey.png');
        display: block;
        width: 144px;
        height: 29px;
        float: left;
        font-size: 0;
        margin-left: 5px;
		cursor:pointer;
    }

    #method_1:hover, #method_1.selected {
        background-image: url('/assets/<?=Kohana::$config->load('config')->assets_folder_path;?>/images/pay_with_credit_card.png');
        cursor: hand;
    }

    #method_2 {
        background-image: url('/assets/<?=Kohana::$config->load('config')->assets_folder_path;?>/images/paypal_grey.gif');
        display: block;
        width: 60px;
        height: 38px;
        float: left;
        font-size: 0;
        margin-left: 5px;
		cursor:pointer;
    }

    #method_2:hover, #method_2.selected {
        background-image: url('/assets/<?=Kohana::$config->load('config')->assets_folder_path;?>/images/paypal.gif');
        cursor:pointer;
    }

    #method_3 {
        background-image: url('<?=URL::site();?>assets/shared/img/stripe_logo.png');
        display: block;
        width: 149px;
        height: 55px;
        float: left;
        font-size: 0;
        background-size: 149px 55px;
        margin-left: 5px;
        margin-top: -10px;
		cursor:pointer;
    }

    #method_3 {
        cursor:pointer;
    }
</style>


<?php if ( ! empty($products_list)): ?>
    <script type="text/javascript">
        var shared_assets = "<?=URL::get_engine_assets_base(); ?>";
        var urlBase = "<?= URL::base(); ?>";
		var payPalRedirect = <?= intval(Settings::instance()->get('paypal_payment_mode')); ?>;
    </script>
    <div class="breadcrumb-nav" id="breadcrumb-nav"><?= trim(''.IbHelpers::breadcrumb_navigation()) ?></div>
    <form class="checkout_form" id="creditCardForm">
		<input type="hidden" value="<?= Model_Payments::get_thank_you_page(['full_url' => false]) ?>" name="thanks_page" id="thanks_page">
		<input type="hidden" name="uniqid" value="<?=uniqid();?>" />

		<div class="checkout_wrapper">
			<div id="checkout_messages">
				<?php echo IbHelpers::get_messages(); //The message. example: "Error processing the payment ?>
			</div>
			<div class="min-height">

				<div id="checkoutTable">
					<table class="checkout_table">
						<thead>
							<tr>
								<th scope="col" class="checkout_title">Item</th>
								<th scope="col">Price</th>
								<th scope="col">Qty</th>
								<th scope="col">Total</th>
								<th scope="col">Remove</th>
							</tr>
						</thead>
						<tbody>
							<?= $products_list ?>
						</tbody>
					</table>
					<div class="checkout-settings">
						<div class="checkout-settings-cl checkout-settings-delivery">

							<div class="checkout-settings-row">
								<label for="checkout_delivery_method">Checkout Options</label>
								<select class="validate[required]" id="checkout_delivery_method" name="delivery_method">
									<option value="">-- Please Select --</option>
                                    <option value="pay_and_post">Pay and Ship</option>
                                    <option value="pay_and_collect">Pay and Collect</option>
									<?php if (isset($user_data['credit_account']) AND $user_data['credit_account'] == 1): ?>
										<option value="credit_account">Credit Account</option>
									<?php endif; ?>
								</select>
							</div>
							<div class="checkout-settings-row<?= (count($postage) == 1) ? ' hide' : '' ?>" id="checkout_postal_zone_wrapper">
								<label for="postalZone" class="label-mandatory">Shipping Destination</label>
								<select id="postalZone" class="validate[required]" onchange="changeZone(this.value)" name="zones">
									<?php if (count($postage) != 1): ?>
										<option value="">-- Please Select --</option>
									<?php endif; ?>
									<?php foreach ($postage as $place): ?>
										<option value="<?= $place['id'] ?>"<?php if ($place['id'] == @$zone_id OR count($postage) == 1) echo ' selected="selected"' ?>><?= $place['title'] ?></option>
									<?php endforeach; ?>
								</select>


								<input type="hidden" id="use_postal_methods" value="<?= (isset($postal_methods) AND $postal_methods) ? 1 : 0 ?>" />
							</div>

							<?php if (isset($accept_coupons) AND $accept_coupons): ?>
								<div class="checkout-settings-row">
									<label for="coupon_code">Coupon Code</label>
									<div class="coupon-code-wrapper">
										<input type="text" id="coupon_code" />
										<button type="button" class="button button-default coupon_button" type="button" onclick="validate_coupon()">Validate</button>
									</div>
								</div>
							<?php endif; ?>

							<?php $user = Auth::instance()->get_user(); ?>
							<?php if (isset($user['id']) AND $user['id'] != ''): ?>
								<div class="checkout-settings-row">
									<label for="checkout_purchase_order_reference">Purchase Order No.</label>
									<input type="text" id="checkout_purchase_order_reference" name="purchase_order_reference" />
								</div>
							<?php endif; ?>


							<div class="checkout-settings-row" id="checkout_store_wrapper">
								<label for="checkout_store">Collect location</label>
								<select id="checkout_store" class="validate[required]">
									<?php $stores = Model_Location::get(NULL, array(array('type', '=', 'Shop')));?>
									<option value="">-- Please Select --</option>
									<?php foreach ($stores as $store): ?>
										<option value="<?= $store['id'] ?>">
											<?= $store['title'].
												((trim($store['address_1']) == '') ? '' : ', '.trim($store['address_1'])).
												((trim($store['address_2']) == '') ? '' : ', '.trim($store['address_2'])).
												((trim($store['address_3']) == '') ? '' : ', '.trim($store['address_3'])).
												((trim($store['county']) == '') ? '' : ', '.trim($store['county']));
											?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<?php if (isset($user_data['credit_account']) AND $user_data['credit_account'] == 1): ?>
								<div class="checkout-settings-row" id="checkout_po_number_wrapper" style="display: none;">
									<label for="checkout_po_number"><abbr title="Purchase Order">PO</abbr> Number</label>
									<input type="text" class="validate[required]" id="checkout_po_number" name="po_number" />
								</div>
							<?php endif; ?>

							<span class="continue-shopping">
								<?php if (($last_product_browsing_url = Session::instance()->get('last_product_browsing_url')) != ''): ?>
									<a href="<?= $last_product_browsing_url ?>" class="button button-primary">Continue Shopping</a>
								<?php else: ?>
									<a href="/search.html" class="button button-primary">Continue Shopping</a>
								<?php endif ?>
							</span>
						</div>

						<div class="checkout-settings-cl">
							<?php $subtotal       = isset($subtotal)       ? $subtotal    : 0; ?>
							<?php $discounts      = isset($discounts)      ? $discounts   : 0; ?>
							<?php $shipping_price = isset($shipping_price) ? $subtotal    : 0; ?>
							<?php $subtotal       = isset($subtotal)       ? $subtotal    : 0; ?>
							<?php $subtotal2      = isset($subtotal2)      ? $subtotal2   : 0; ?>
							<?php $final_price    = isset($final_price)    ? $final_price : 0; ?>
							<div class="checkout-settings-row-second">
								<label for="subtotal">Products</label>
								<input type="text" id="subtotal" class="priceField subtotal" value="€<?= number_format($subtotal, 2) ?>" disabled="disabled"/>
							</div>

							<div class="checkout-settings-row-second">
								<label for="checkout_discounts">Discounts</label>
								<div class="priceField discounts" id="discounts">&euro;<?= number_format($discounts, 2) ?></div>
							</div>

							<div class="checkout-settings-row-second">
								<label for="postage">Postage</label>
								<div class="priceField postage" id="postage">&euro;<?= number_format($shipping_price, 2) ?></div>
							</div>

							<div class="checkout-settings-row-second">
								<label for="subtotal">Subtotal</label>
								<input type="text" id="subtotal2" class="priceField subtotal2" value="€<?= number_format($subtotal2, 2) ?>" disabled="disabled" />
							</div>

							<?php if (Settings::instance()->get('vat_rate')): ?>
								<div class="checkout-settings-row-second">
									<label for="vat">VAT</label>
									<input type="text" id="vat" class="priceField vat" value="€<?= number_format($vat, 2); ?>" disabled="disabled" />
								</div>
							<?php endif; ?>

							<div class="checkout-settings-row-second">
								<label for="totalprice" class="postage-label">TOTAL</label>
								<input type="text" class="highlight_total priceField totalprice" id="totalprice" value="&euro;<?= number_format($final_price, 2) ?>" disabled="disabled" />
							</div>
						</div>
					</div>
				</div>

				<?php if ((isset($paypal_enabled) AND $paypal_enabled == true) OR ($stripe['enabled'])): ?>
					<script>
						$("#payment_method_selector").change(function () {
							if ($("#payment_method_selector").val() === "card") {
								$("#method_1").click();
							}
							else if ($("#payment_method_selector").val() === "paypal") {
								$("#method_2").click();
							}
							else if ($("#payment_method_selector").val() === "stripe") {
								$("#method_3").click();
							}
						});

						$(document).ready(function () {
							$("#payment_method_selector option:first").change();
						});
					</script>
				<?php endif; ?>

				<p class="note">
				<div id="checkoutForm">
					<form action="/frontend/payment" method="post" id="creditCardForm" name="creditCardForm" action="">
						<input type="hidden" value="<?=$final_price?>" id="total" name="total">

						<div id="paymentSelect" class="paymentSelect"<?= ($payment_method_count <= 1) ? ' style="display: none;"' : '' ?>>
							<input type="hidden" name="PaRes" id="PaRes" value="" />
							<input type="hidden" name="MD" id="MD" value="" />
							<input type="hidden" name="MDX" id="MDX" value="" />
							<div>
								<?php if ($payment_method_count > 1): ?>
									<h2>Select your Payment Method</h2>
								<?php endif; ?>
								<select id="payment_method_selector" style="display:none;">
									<?php if ($realex_enabled || $sagepay_enabled || $boipa_enabled): ?>
										<option value="card" selected>Debit/Credit Card
										</option><?php endif; ?>
									<?php if ($paypal_enabled): ?>
										<option value="paypal">Paypal
										</option><?php endif; ?>
									<?php if ($stripe['enabled']): ?>
										<option value="stripe">Stripe
										</option><?php endif; ?>
								</select>
								<?php if ($realex_enabled || $sagepay_enabled || $boipa_enabled): ?>
									<span onclick="changeMethod(this);" class="payment_method grey" id="method_1">method 1</span><?php endif; ?>
								<?php if ($paypal_enabled): ?>
									<span onclick="changeMethod(this);" class="payment_method grey" id="method_2">method 2</span><?php endif; ?>
								<?php if ($stripe['enabled']): ?>
									<span onclick="changeMethod(this);" class="payment_method grey" id="method_3">method 3</span><?php endif; ?>
							</div>
							<script type="text/javascript">
								$(document).ready(function(){
									$('#method_1').click()
								});
								function paypal_checkout() {
									if ($('#postalZone').val() == '') {
										alert('Please select a postal region.');
									}
									else {
										CHECKOUT.checkoutWithPayPal(
											"<?= URL::site().'checkout.html/paypal_success'; ?>",
											"<?= URL::site().'checkout.html/paypal_cancel'; ?>",
											function (status, data) {
												checkout_data.paypal_error(status, data);
											}
										);
									}
								}
							</script>
						</div>

						<div id="CartDetailsBox">
							<div class="left">
								<h2 class="orange">Billing Information</h2>
								<table class="CartDetails">
									<tbody>
										<tr>
											<th scope="col" colspan="2"></th>
										</tr>
										<tr>
											<td class="label"><label for="ccName" class="label-mandatory">Full Name:</label></td>
											<td>
												<input type="text" style="width: 200px;" maxlength="50" name="ccName" id="ccName" class="validate[required] text-input"
													   value="<?= trim((isset($user_data['name']) ? $user_data['name'] : '').' '.(isset($user_data['surname']) ? $user_data['surname'] : '')) ?>" />
											</td>
										</tr>
										<tr id="checkout_eircode_wrapper">
											<td class="label"><label for="checkout_eircode" class="label-mandatory">Eircode:</label></td>
											<td>
												<input type="text" name="eircode" id="checkout_eircode" class="text-input" value="<?= isset($user_data['eircode']) ? $user_data['eircode']  : ''; ?>" />
											</td>
										</tr>
										<tr>
											<?php $address = isset($user_data['address']) ? explode("\n", $user_data['address']) : array(); ?>
											<td class="label"><label for="address_1" class="label-mandatory">Address:</label></td>
											<td>
												<textarea style="width: 200px;" rows="5" name="address_1" id="address_1" class="validate[required] text-input"><?= isset($address[0]) ? $address[0] : '' ?></textarea>
											</td>
										</tr>
										<tr>
											<td class="label"><label for="address_2" class="label-mandatory">Town/City:</label></td>
											<td>
												<input type="text" maxlength="70" name="address_2" id="address_2" class="validate[required] text-input"
													   value="<?= isset($address[1]) ? $address[1] : '' ?>">
											</td>
										</tr>
										<tr>
											<td class="label"><label for="address_3" class="label-mandatory">State/County:</label></td>
											<td>
												<input type="text" maxlength="70" name="address_3" id="address_3" class="validate[required] text-input"
													   value="<?= isset($address[2]) ? $address[2] : '' ?>" />
											</td>
										</tr>
										<tr>
											<td class="label"><label for="address_4" class="label-mandatory">Country:</label></td>
											<td>
												<?php $countries = explode("\n", trim(Settings::instance()->get('checkout_countries'))); ?>
												<?php if (sizeof($countries) > 0 AND $countries[0] != ''): ?>
													<select id="address_4" class="validate[required]">
														<?php foreach ($countries as $country): ?>
															<option><?= $country ?></option>
														<?php endforeach; ?>
													</select>
												<?php else: ?>
													<input type="text" maxlength="70" name="address_4" id="address_4" value="Ireland" class="validate[required] text-input"<?= (count($postage) == 1) ? ' readonly="readonly"' : '' ?> />
												<?php endif; ?>
											</td>
										</tr>
										<tr id="checkout_postcode_wrapper" style="display: none;">
											<td class="label"><label for="postcode" class="label-mandatory">Postcode:</label></td>
											<td>
												<input type="text" maxlength="20" name="postcode" id="postcode" value="" class="text-input" />
											</td>
										</tr>
										<tr>
											<td class="label"><label for="phone" class="label-mandatory">Tel/Mobile:</label></td>
											<td>
												<input type="text" maxlength="70" name="phone" id="phone" class="validate[required] text-input"
													   value="<?= isset($user_data['phone']) ? $user_data['phone']  : ''; ?>" />
											</td>
										</tr>
										<tr>
											<td class="label"><label for="email" class="label-mandatory">Email:</label></td>
											<td>
												<input type="text" maxlength="70" name="email" id="email" class="validate[required,custom[email]] text-input"
													   value="<?= isset($user_data['email']) ? $user_data['email']  : ''; ?>" />
											</td>
										</tr>
										<tr>
											<td class="label comments"><label for="comments">Comments:</label></td>
											<td><textarea id="comments" rows="3" cols="30" name="comments"></textarea></td>
										</tr>
									</tbody>
								</table>
								<div id="shipping_heading_wrapper">
									<h2>Shipping Information</h2>
									<label>
										<input type="checkbox" name="addressCheckbox" id="addressCheckbox" onclick="shippingAddress()" value="1"
											   checked="checked"> ship to the billing address
									</label>
								</div>

								<div style="display: none;" id="shippingAddressDiv">
									<table class="CartDetails">
										<tbody>
										<tr>
											<th scope="col" colspan="2"></th>
										</tr>
										<tr>
											<td><label for="shipping_name" class="label-mandatory">Name:</label></td>
											<td>
												<input type="text" style="width: 200px;" maxlength="50" name="shipping_name" id="shipping_name"
													   value="">
											</td>
										</tr>
										<tr>
											<td><label for="shipping_address_1" class="label-mandatory">Address:</label></td>
											<td>
												<textarea style="width: 200px;" maxlength="70" name="shipping_address_1" id="shipping_address_1"
														  class="validate[required]"></textarea>
											</td>
										</tr>
										<tr>
											<td><label for="shipping_address_2" class="label-mandatory">Town/City:</label></td>
											<td>
												<input type="text" style="width: 100px;" maxlength="70" name="shipping_address_2"
													   id="shipping_address_2" value="">
											</td>
										</tr>
										<tr>
											<td><label for="shipping_address_3" class="label-mandatory">State/County:</label></td>
											<td>
												<input type="text" style="width: 100px;" maxlength="70" name="shipping_address_3"
													   id="shipping_address_3" value="">
											</td>
										</tr>
										<tr>
											<td><label for="shipping_address_4" class="label-mandatory">Country:</label></td>
											<td>
												<?php if (sizeof($countries) > 0 AND $countries[0] != ''): ?>
													<select id="shipping_address_4" class="validate[required]">
														<?php foreach ($countries as $country): ?>
															<option><?= $country ?></option>
														<?php endforeach; ?>
													</select>
												<?php else: ?>
													<input type="text" style="width: 100px;" maxlength="70" name="shipping_address_4"
														   id="shipping_address_4" value="Ireland" class="validate[required] text-input">
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td class="label"><label for="shipping_phone" class="label-mandatory">Tel/Mobile:</label></td>
											<td>
												<input type="text" style="width: 100px;" maxlength="70" name="shipping_phone"
													   id="shipping_phone">
											</td>
										</tr>
										<tr>
											<td class="label"><label for="shipping_email">Email:</label></td>
											<td>
												<input type="text" style="width: 180px;" maxlength="70" name="shipping_email"
													   id="shipping_email" value="">
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>

							<div id="CardDetails" class="payment_method_view">
								<table class="CardDetails">
									<tbody>
										<tr>
											<td colspan="2"><h2 class="orange">Credit Card Payment Details</h2></td>
										</tr>
										<tr>
											<td class="label"><label for="ccType" class="label-mandatory">Card type:</label></td>
											<td>
												<select name="ccType" id="ccType" class="validate[required]">
													<option value="">Please select</option>
													<option value="visa">Visa</option>
													<option value="mc">Mastercard</option>
												</select>
											</td>
										</tr>
										<tr>
											<td class="label"><label for="ccNum" class="label-mandatory">Card No.</label></td>
											<td>
												<input type="text" maxlength="70" name="ccNum" value="" id="ccNum" class="validate[required] text-input">
											</td>
										</tr>
										<tr>
											<td class="label"><label for="ccv" class="label-mandatory">CVV No.</label></td>
											<td>
												<input type="text" maxlength="4" id="ccv" name="ccv" value="" class="validate[required] text-input" placeholder="Last three digits from signature strip" />
											</td>
										</tr>
										<tr>
											<td class="label"><label for="ccExpMM" class="label-mandatory">Expiry date</label></td>
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
											<td colspan="2" class="credit_cards">
												<img alt="MasterCard" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/mastercard.png">
												<img alt="Visa" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/visa.png">
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<span class="payment_partner_message">We use a secure certificate for all our payments and our payment partner provide all secure connections for your transaction.</span>
											</td>
										</tr>

									</tbody>
								</table>
								<div id="FinalDetails">
									<table class="FinalDetails">
										<tbody>
										<tr>
											<td>
												<label><input type="checkbox" id="signupCheckbox" value="1" name="signupCheckbox"> I would like to sign up the newsletter</label>
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
										</tbody>
									</table>


								</div>
							</div>

							<div id="payment_button_area">
								<!-- Credit Card -->
								<div id="submit_checkout_button" class="button button-primary payment_method_view submit_checkout_button" onclick="submitCheckout();"<?= ($default_payment != 'Realex' && $default_payment != 'sagepay') ? ' style="display:none;"' : ''; ?>>
									Buy Now
								</div>

								<!-- PayPal -->
								<div id="paypalButton" class="payment_method_view"<?= ($default_payment != 'PayPal') ? ' style="display:none;"' : ''; ?>>
									<a href="<?= "javascript:CHECKOUT.checkoutWithPayPal('" . Model_Payments::get_thank_you_page() . "?clear_cart=1', '" . URL::site() . "', function(status,data){ checkout_data.paypal_error(status,data); });" ?>">
										<img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;">
									</a>
								</div>

								<?php if ($stripe['enabled']): ?>
									<!-- Stripe -->
									<div id="stripeButton" class="payment_method_view"<?= ($default_payment != 'Stripe') ? ' style="display:none;"' : ''; ?>>
										<div style="float:right;width:180px;height:41px;">
											<img src="<?= URL::get_engine_assets_base(); ?>img/stripe.png"/></div>
										<button type="button" style="height: 25px;width: 90px;margin: 5px;" class="payment_button" id="stripe-button" data-key="<?= $stripe['publishable_key']; ?>">Place Order</button>
									</div>
									<script src="https://checkout.stripe.com/checkout.js"></script>
								<?php endif; ?>
							</div>
							<div id="checkoutMessageBar"></div>

						</div>
					</form>
				</div>
			</div>
		</div>
    </form>
    <div id="collect_dialogue" class="ui-dialog" title="Collect in Store" style="display:none;">
        <p>We will notify you by email when your order is ready to be collected.</p>
    </div>
    <?php

    // Render CSS Files for THIS View
    if (isset($view_css_files)) {
        foreach ($view_css_files as $css_item_html) echo $css_item_html;
    }
    // Render JS Files for This View
    if (isset($view_js_files)) {
        foreach ($view_js_files as $js_item_html) echo $js_item_html;
    }
    ?>
<?php else: ?>
    <div class="alert">Your shopping cart is empty.</div>
<?php endif; ?>
<script>
    $(document).ready(function () {
        update_county_list();
		$('#postalZone').trigger('change');
    });
    $('#postalZone').on('change', function () {
        if ($('#postalZone').find(':selected').html().toLowerCase() == 'collect in store') {
            $('#collect_dialogue').dialog({
                resizable: false,
                modal: true,
                buttons: { OK: function () {
                    $(this).dialog('close');
                } }
            });
        }
        update_county_list();
    });

    function update_county_list()
	{
        var zone = $('#postalZone').find(':selected').html();
        if (document.getElementById('use_postal_methods').value == 1)
		{
            zone = document.getElementById('address_4').value;
        }

        $.ajax({url: '/frontend/products/ajax_update_county_list/',
			data: { zone: zone},
			type: 'POST',
			dataType: 'json'
		}).done(function (results)
		{
            if (results != '')
			{
                $('[for="address_3"]').html('County');
                $('#address_3').parent().html('<select id="address_3" name="address_3">' + results + '</select>');
                $('[for="shipping_address_3"]').html('County');
                $('#shipping_address_3').parent().html('<select id="shipping_address_3" name="shipping_address_3">' + results + '</select>');
            }
            else
			{
                $('[for="address_3"]').html('State/County');
                $('#address_3').parent().html('<input type="text" maxlength="70" name="address_3" id="address_3" value="">');
                $('[for="shipping_address_3"]').html('State/County');
                $('#shipping_address_3').parent().html('<input type="text" maxlength="70" name="shipping_address_3" id="shipping_address_3" value="">');
            }
        });
    }
</script>
