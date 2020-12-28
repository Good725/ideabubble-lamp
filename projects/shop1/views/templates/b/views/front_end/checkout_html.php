<?php
if (isset($_SESSION[Model_Checkout::CART_SESSION_ID]->payback_loyalty_discount))
{
	$pb_discount = '&euro; -'.$_SESSION[Model_Checkout::CART_SESSION_ID]->payback_loyalty_discount;
	$pb_discount_label = 'Payback Loyalty discount';
}
else
{
	$pb_discount = '';
	$pb_discount_label = '';
}

$stripe['enabled'] = (Settings::instance()->get('stripe_enabled') == 'TRUE');
$realex_enabled = (Settings::instance()->get('enable_realex') == 1 AND Settings::instance()->get('realex_username') != '');

if ($stripe['enabled'])
{
	require_once APPPATH.'/vendor/stripe/lib/Stripe.php';
	$stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
	$stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
	$stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
	Stripe::setApiKey($stripe['secret_key']);
}
$paypal_enabled = (isset($paypal_enabled) AND $paypal_enabled == TRUE);
$payment_method_count = $paypal_enabled ? 1 : 0;
$payment_method_count += $realex_enabled ? 1 : 0;
$payment_method_count += $stripe['enabled'] ? 1 : 0;

$default_payment = $stripe['enabled'] ? 'Stripe' : ($realex_enabled ? 'Realex' : ($paypal_enabled ? 'PayPal' : NULL));

?>

<?php //Horrible Inline Styling Since There's No Shared CSS //?>
<style type="text/css">
	#method_1 {
		background-image: url('/assets/<?=Kohana::$config->load('config')->assets_folder_path;?>/images/pay_with_credit_card_grey.png');
		display: block;
		width: 144px;
		height: 29px;
		float: left;
		font-size: 0;
		margin-left: 5px;
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
	}

	#method_2:hover, #method_2.selected {
		background-image: url('/assets/<?=Kohana::$config->load('config')->assets_folder_path;?>/images/paypal.gif');
		cursor: hand;
	}

	#method_3 {
		border: 1px solid #000;
		border-radius: 5px;
		background-image: url('<?= URL::site() ?>assets/shared/img/stripe_logo.png');
		cursor: pointer;
		display: block;
		width: 149px;
		height: 55px;
		float: left;
		font-size: 0;
		background-size: 149px 55px;
		margin-left: 5px;
		margin-top: -10px;
	}

	#method_3.gray {
		background-color: #e5e5e5;
		border: 1px solid #666;
	}
	.DeliveryDeals{
		display:none;
	}

</style>


<?php if (!empty($products_list)): ?>
	<script type="text/javascript">
		var shared_assets = "<?=URL::get_engine_assets_base() ?>";
		var urlBase = "<?= URL::base(); ?>";
		var payPalRedirect = <?= intval(Settings::instance()->get('paypal_payment_mode')); ?>;
	</script>
	<form id="creditCardForm">
	<input type="hidden" value="<?= Model_Payments::get_thank_you_page(['full_url' => false]) ?>" name="thanks_page" id="thanks_page">
	<input type="hidden" value="" name="template_name" id="template_name">

	<div class="checkout_wrapper">
	<div id="checkout_messages">
		<?= IbHelpers::get_messages(); //The message. example: "Error processing the payment ?>
	</div>
	<div class="min-height">

	<div id="checkoutTable">
		<h2>Your Order Details</h2>
		<table class="checkoutTable">
			<thead>
				<tr>
					<th scope="col" class="checkout_title"><?= __('Item') ?></th>
					<th scope="col"><?= __('Price') ?></th>
					<th scope="col"><?= __('Qty') ?></th>
					<th scope="col"><?= __('total') ?></th>
					<th scope="col"><?= __('Remove') ?></th>
				</tr>
			</thead>
			<tbody>
			<?= $products_list ?>
           </tbody>
		</table>

		<div class="clearfix">
			<div class="checkout-options">
				<div id="checkout_postal_zone_wrapper">
					<h2><label for="postalZone" class="label-mandatory"><?= __('Select your postal destination') ?></label></h2>

					<select id="postalZone" class="validate[required]" onchange="changeZone(this.value)" name="zones">
						<option value="">
							<?= (isset($postal_methods) AND $postal_methods) ? __('-- Select Delivery Method --') : __('-- Select Postal Zone --') ?>
						</option>
						<?php foreach ($postage as $place): ?>
							<option value="<?= $place['id'] ?>"<?php if ($place['id'] == @$zone_id) echo ' selected="selected" ' ?>><?= $place['title'] ?></option>
						<?php endforeach; ?>
					</select>

					<input type="hidden" id="use_postal_methods" value="<?= (isset($postal_methods) AND $postal_methods) ? 1 : 0 ?>" />
				</div>

				<?php if (isset($accept_coupons) AND $accept_coupons): ?>
					<div>
						<h2><label for="coupon_code"><?= __('Enter your coupon code') ?></label></h2>

						<input type="text" class="checkout-input" id="coupon_code"/>
						<input class="coupon_button" type="button" value="<?= __('Validate') ?>" onclick="validate_coupon()" />
					</div>
				<?php endif;?>
			</div>
			<table class="checkout-totals-table">
				<tbody>
					<tr class="tr_totalprice product_line_first_td">
						<td></td>
						<td><?= __('Subtotal') ?></td>
						<td>
							<div class="checkout-input priceField subtotal">&euro;<?= number_format(@$subtotal, 2) ?></div>
						</td>
					</tr>
					<tr class="tr_destination">
						<td class="product_line_first_td">
							<style>
								.checkout_control_group {
									clear: both;
								}

								.checkout_label {
									width: 9em;
									display: block;
								}

								.checkout_controls select {
									width: 200px;
								}

								.tr_destination {
									vertical-align: bottom;
								}

								@media only screen and (min-width:600px) {
									.checkout_label {
										float: left;
									}

									.checkout_controls {
										margin-left: 9.5em;
									}
								}

							</style>
							<?php if (Settings::instance()->get('checkout_delivery_options') == 1): ?>
								<h3><?= __('Delivery Method') ?></h3>
								<div class="checkout_control_group">
									<div class="checkout_label">
										<label for="checkout_delivery_method"><?= __('Select Method') ?></label>
									</div>
									<div class="checkout_controls">
										<select class="validate[required]" id="checkout_delivery_method" name="delivery_method">
											<option value=""><?= __('-- Please Select --') ?></option>
											<option value="reserve_and_collect"><?= __('Reserve and Collect') ?></option>
											<option value="pay_and_post"><?= (Kohana::$config->load('config')->get('db_id') == 'grettalspetals') ? __('Pay and Deliver') : __('Pay and Post') ?></option>
										</select>
									</div>
								</div>

								<div class="checkout_control_group" id="checkout_store_wrapper">
									<div class="checkout_label">
										<label for="checkout_store"><?= __('Select Store') ?></label>
									</div>
									<div class="checkout_controls">
										<select class="validate[required]" id="checkout_store" name="store_id">
											<?php $stores = Model_Location::get(NULL, array(array('type', 'in', array('Pharmacy','Store'))));?>
											<option value=""><?= __('-- Please Select --') ?></option>
											<?php foreach ($stores as $store): ?>
												<option value="<?= $store['id'] ?>">
													<?= $store['title'].
													((trim($store['address_1']) == '') ? '' : ', '.trim($store['address_1'])).
													((trim($store['address_2']) == '') ? '' : ', '.trim($store['address_2'])).
													((trim($store['address_3']) == '') ? '' : ', '.trim($store['address_3'])).
													((trim($store['county'])    == '') ? '' : ', '.trim($store['county']));
													?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							<?php endif; ?>
						</td>
						<td><?= __('Postage') ?></td>
						<td>
							<div class="checkout-input priceField postage">&euro;<?= @number_format($shipping_price, 2) ?></div>
						</td>
					</tr>
                    <tr id="">
						<td colspan="5"  id="checkout_discount_applied"></td>
						<!--<div id="checkout_discount_applied" class="chkout_options">Loading...</div><br />-->
					</tr>	
					<tr class="tr_totalprice product_line_first_td"<?= (empty($discounts)) ? 'style="display:none;"' : '' ?>>
						<td>&nbsp;</td>
						<td><?= __('Discounts') ?></td>
						<td>
							<div class="checkout-input priceField discounts">&minus;&nbsp;&euro;<?= number_format(@$discounts, 2) ?></div>
						</td>
					</tr>

					<tr class="tr_totalprice product_line_first_td">
					<td></td>
					<td class="highlight_total"><?= __('Total') ?></td>
					<td class="highlight_total">
						<div class="checkout-input priceField totalprice">&euro;<?= @number_format($final_price, 2) ?></div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php if ((isset($paypal_enabled) AND $paypal_enabled == TRUE) OR ($stripe['enabled'])): ?>
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
	<input type="hidden" value="1199" id="total" name="total">

	<?php if (Settings::instance()->get('checkout_delivery_date') == 1): ?>
		<div class="delivery_time_wrapper" id="delivery_time_wrapper">
			<div class="checkout_control_group">
				<label class="checkout_label" for="checkout_delivery_time"><?= __('Delivery Time') ?></label>
				<div class="checkout_controls">
					<input type="text" class="validate[required] datetimepicker" id="checkout_delivery_time" name="delivery_time" />
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div class="DeliveryDeals" id="DeliveryDeals">Delivery Deals:</div>
	<div id="checkout_discount_options" class="chkout_options"></div>
	<div class="continue-shopping">
		<?php
		$continue_shopping_url = Session::instance()->get('last_product_browsing_url');
		$continue_shopping_url = ($continue_shopping_url == '') ? '/products.html' : $continue_shopping_url;
		?>
		<a class="continue-shopping-btn" href="<?= $continue_shopping_url ?>"><?= __('Continue Shopping') ?></a>
	</div>
	<!-- cart discount options end here-->
	<div id="CartDetailsBox">
		<div id="paymentSelect" class="paymentSelect"<?= ($payment_method_count <= 1) ? ' style="display: none;"' : '' ?>>
			<div>
				<h2><?= __('Select your Payment Method') ?></h2>
				<select id="payment_method_selector" style="display:none;">
					<?php if ($realex_enabled): ?>
						<option value="card" selected><?= __('Debit/Credit Card') ?></option>
					<?php endif; ?>
					<?php if ($paypal_enabled): ?>
						<option value="paypal"><?= __('Paypal') ?></option>
					<?php endif; ?>
					<?php if ($stripe['enabled']): ?>
						<option value="stripe"><?= __('Stripe') ?></option>
					<?php endif; ?>
				</select>
				<?php if ($realex_enabled): ?>
					<span onclick="changeMethod(this);" class="payment_method grey" id="method_1">Checkout With Credit Card</span><?php endif; ?>
				<?php if ($paypal_enabled): ?>
					<span onclick="changeMethod(this);" class="payment_method grey" id="method_2">Checkout With Paypal</span><?php endif; ?>
				<?php if ($stripe['enabled']): ?>
					<span onclick="changeMethod(this);" class="payment_method grey" id="method_3">Checkout With Stripe</span><?php endif; ?>
			</div>
		</div>
		
		
	<div id="CustomerAddress">
        <div class="checkout-address-wrapper">
            <h2><?= __('Billing Information') ?></h2>
            <table class="CartDetails">
                <tbody>
                <tr>
                    <th scope="col" colspan="2"></th>
                </tr>
                <tr id="full_name_row">
                    <td class="label"><label for="ccName" class="label-mandatory"><?= __('Full Name') ?>:</label></td>
                    <td>
                        <input type="text" style="width: 200px;" maxlength="50" name="ccName" id="ccName" value="" class="validate[required] checkout-input" />
                    </td>
                </tr>
                <tr id="first_name_row">
                    <td class="label"><label for="ccFirstName" class="label-mandatory"><?= __('First Name') ?>:</label></td>
                    <td>
                        <input type="text" style="width: 200px;" maxlength="50" name="ccFirstName" id="first_name" value="" class="checkout-input" />
                    </td>
                </tr>
                <tr id="last_name_row">
                    <td class="label"><label for="ccLastName" class="label-mandatory"><?= __('Last Name') ?>:</label></td>
                    <td>
                        <input type="text" style="width: 200px;" maxlength="50" name="ccLastName" id="last_name" class="checkout-input" value="" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="address_1" class="label-mandatory"><?= __('Address') ?>:</label></td>
                    <td>
                        <textarea style="width: 200px;" maxlength="70" name="address_1" id="address_1" class="validate[required] checkout-input"></textarea>
                    </td>
                </tr>
                <script type="text/javascript">
                    $('#first_name_row').hide();
                    $('#last_name_row').hide();
                </script>
                <tr>
                    <td class="label"><label for="address_2" class="label-mandatory"><?= __('Town/City') ?>:</label></td>
                    <td>
                        <input type="text" maxlength="70" name="address_2" id="address_2" value="" class="validate[required] checkout-input">
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="address_3" class="label-mandatory" data-string-state_county="<?= __('State/County') ?>" data-string-county="<?= __('County') ?>"><?= __('State/County') ?>:</label></td>
                    <td>
                        <select id="address_3" name="address_3">
                            <?= $counties ?>
                        </select>
                    </td>
                </tr>
                <tr id="postcode_tr" style="display: none;">
                    <td class="label"><label for="postcode">Postcode:</label></td>
                    <td>
                        <input type="text" maxlength="70" name="postcode" id="postcode" value="" class="validate[required] checkout-input" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="address_4" class="label-mandatory"><?= __('Country') ?>:</label></td>
                    <td>
                        <?php $countries = explode("\n", trim(Settings::instance()->get('checkout_countries'))); ?>
                        <?php if (sizeof($countries) > 0 AND $countries[0] != ''): ?>
                            <select id="address_4" class="validate[required]">
                                <?php foreach ($countries as $country): ?>
                                    <option><?= $country ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" maxlength="70" name="address_4" id="address_4"
                                   value="<?= __('Ireland') ?>" class="validate[required] checkout-input">
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="phone" class="label-mandatory"><?= __('Tel/Mobile') ?>:</label></td>
                    <td>
                        <input type="text" maxlength="70" name="phone" id="phone" value="" class="validate[required] checkout-input">
                    </td>
                </tr>
                <tr id="email_row">
                    <td class="label"><label for="email" class="label-mandatory"><?= __('Email') ?>:</label></td>
                    <td>
                        <input type="text" style="width: 180px;" maxlength="70" name="email" id="email" value="" class="validate[required,custom[email]] checkout-input" />
                    </td>
                </tr>
                <tr>
                    <td class="label comments"><label for="comments"><?= __('Comments') ?>:</label></td>
                    <td><textarea id="comments" class="checkout-input" rows="3" cols="30" name="comments"></textarea></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="checkout-address-wrapper">

            <div class="shipping_heading_wrapper" id="shipping_heading_wrapper">
                <h2><?= Model_Localisation::get_ctag_translation(Settings::instance()->get('shipping_information_string'), I18n::$lang) ?></h2>
                <label>
                    <input type="checkbox" name="addressCheckbox" id="addressCheckbox" onchange="shippingAddress()" value="1" checked="checked"> <?= __('ship to the billing address') ?>
                </label>
            </div>

            <div style="display: none;" id="shippingAddressDiv">
                <table class="CartDetails">
                    <tbody>
                        <tr>
                            <th scope="col" colspan="2"></th>
                        </tr>
                        <tr>
                            <td><label for="shipping_name" class="label-mandatory"><?= __('Name') ?>:</label></td>
                            <td>
                                <input type="text" style="width: 200px;" maxlength="50" name="shipping_name" id="shipping_name" class="checkout-input" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td><label for="shipping_address_1" class="label-mandatory"><?= __('Address') ?>:</label></td>
                            <td>
                                <textarea style="width: 200px;" maxlength="70" name="shipping_address_1" id="shipping_address_1"
                                          class="validate[required] checkout-input"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="shipping_address_2" class="label-mandatory"><?= __('Town/City') ?>:</label></td>
                            <td>
                                <input type="text" style="width: 100px;" maxlength="70" name="shipping_address_2"
                                       id="shipping_address_2" class="checkout-input" value="">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="shipping_address_3"><?= __('State/County') ?>:</label></td>
                            <td>
                                <select id="shipping_address_3" name="shipping_address_3">
                                    <?= $counties ?>
                                </select>
                            </td>
                        </tr>

                        <tr id="shipping_postcode_tr">
                            <td><label for="shipping_postcode">Postcode:</label></td>
                            <td>
                                <input type="text" maxlength="70" name="shipping_postcode" id="shipping_postcode" value=""/>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="shipping_address_4" class="label-mandatory"><?= __('Country') ?>:</label></td>
                            <td>
                                <?php if (sizeof($countries) > 0 AND $countries[0] != ''): ?>
                                    <select id="shipping_address_4" class="validate[required]">
                                        <?php foreach ($countries as $country): ?>
                                            <option><?= __($country) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" style="width: 100px;" maxlength="70" name="shipping_address_4" id="shipping_address_4" value="<?= __('Ireland') ?>" class="validate[required] checkout-input">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><label for="shipping_phone" class="label-mandatory"><?= __('Tel/Mobile') ?>:</label></td>
                            <td>
                                <input type="text" style="width: 100px;" maxlength="70" name="shipping_phone" id="shipping_phone" class="checkout-input">

                                <div class="shipping_details_phone_notice"><?= __('Please provide a phone number for the shipping address') ?></div>
                            </td>
                        </tr>
                        <tr id="row_shipping_email">
                            <td class="label"><label for="shipping_email"><?= __('Email') ?>:</label></td>
                            <td>
                                <input type="text" style="width: 180px;" maxlength="70" name="shipping_email" id="shipping_email" class="checkout-input" value="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
	    </div>
    </div>

	

	<?php if ($realex_enabled): ?>
        <div id="CardDetails"
             class="payment_method_view"<?= ($default_payment != 'Realex') ? ' style="display: none"' : '' ?>>
            <table class="CardDetails">
                <tbody>
					<tr>
						<td colspan="2"><h2><?= __('Credit Card Payment Details') ?></h2></td>
					</tr>
					<tr>
						<td class="label"><label for="ccType" class="label-mandatory"><?= __('Card type') ?>:</label></td>
						<td>
							<select name="ccType" id="ccType" class="validate[required]">
								<option value=""><?= __('Please select') ?></option>
								<option value="visa"><?= __('Visa') ?></option>
								<option value="mc"><?= __('Mastercard') ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label"><label for="ccNum" class="label-mandatory"><?= __('Card number') ?>:</label></td>
						<td>
							<input type="text" style="width: 200px;" maxlength="19" name="ccNum" value="" id="ccNum" class="validate[required,funcCall[luhnTest]] checkout-input">
						</td>
					</tr>
					<tr>
						<td class="label"><label for="ccv" class="label-mandatory"><?= __('CVV number') ?>:</label></td>
						<td>
							<input type="text" style="width: 200px;" maxlength="4" id="ccv" name="ccv" value="" class="validate[required,custom[onlyNumberSp]] checkout-input" placeholder="<?= __('Last 3 digits from signature strip') ?>" />
						</td>
					</tr>
					<tr>
						<td class="label"><label for="ccExpMM" class="label-mandatory"><?= __('Expiry date') ?>:</label></td>
						<td>
							<select name="ccExpMM" id="ccExpMM" class="validate[required]">
								<option value=""><?= __('MM') ?></option>
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
								<option value=""><?= __('YYYY') ?></option>
								<?php
								for ($i = date('y'); $i <= (date('y') + 10); $i++)
								{
									$j = str_pad($i, 2, "0", STR_PAD_LEFT);
									echo "<option value='$j'>20$j</option>\n";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="credit_cars">
							<img alt="master card image" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/mastercard.png" />
							<img alt="visa card image" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/visa.png" />
							<img alt="master card image" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/realex_icon.png" />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="realexmessage"><?= __('We use a secure certificate for all our payments and Realex our payment partner provide all secure connections for your transaction.') ?></span>
						</td>
					</tr>
                </tbody>
            </table>
        </div>
	<?php endif; ?>

	<div id="checkoutMessageBar"></div>

	<div id="FinalDetails"> 
		<table class="FinalDetails">
			<tbody>
				<tr>
					<td>
						<label>
							<input type="checkbox" id="signupCheckbox" value="1" name="signupCheckbox"<?= Settings::instance()->get('checkout_signup_default') ? ' checked="checked"' : '' ?> />
							<?= __('I would like to sign up the newsletter') ?>
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<label>
							<input type="checkbox" id="termsCheckbox" name="termsCheckbox" class="validate[required]">
							<?= __('I accept the $items', array('$items' => '<a target="_blank" href="/terms-and-conditions.html">'.__('terms and conditions').'</a>')) ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<div id="payment_button_area">
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
			<!-- Credit Card -->
			<button type="button" id="submit_checkout_button"
					class="left product_btn payment_method_view submit_checkout_button"
					onclick="submitCheckout();"<?= ($default_payment != 'Realex') ? ' style="display:none;"' : ''; ?>>
				<?= __('Checkout') ?>
			</button>

			<!-- PayPal -->
			<div id="paypalButton"
				 class="payment_method_view"<?= ($default_payment != 'PayPal') ? ' style="display:none;"' : ''; ?>>
				<a href="<?= "javascript:CHECKOUT.checkoutWithPayPal('".Model_Payments::get_thank_you_page()."?clear_cart=1', '".URL::site()."', function(status,data){ checkout_data.paypal_error(status,data); });" ?>">
					<img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;">
				</a>
			</div>

			<?php if ($stripe['enabled']): ?>
				<!-- Stripe -->
				<div id="stripeButton"
					 class="payment_method_view"<?= ($default_payment != 'Stripe') ? ' style="display:none;"' : ''; ?>>
					<div style="float:right;width:180px;height:41px;">
						<img src="<?= URL::get_engine_assets_base(); ?>img/stripe.png"/></div>
					<button type="button" style="height: 25px;width: 90px;margin: 5px;" class="payment_button"
							id="stripe-button" data-key="<?= $stripe['publishable_key']; ?>"><?= __('Place Order') ?>
					</button>
				</div>
				<script src="https://checkout.stripe.com/checkout.js"></script>
			<?php endif; ?>
		</div>

	</div>
	<div style="clear: both;">&nbsp;</div>
	</div>
	</form>
	</div>
	</div>
	</div>
	</form>
	<div id="collect_dialogue" class="ui-dialog" title="Collect in Store" style="display:none;">
		<p><?= __('We will contact you when your purchases are ready for collection.') ?></p>
	</div>
	<?php

	// Render CSS Files for THIS View
	if (isset($view_css_files))
	{
		foreach ($view_css_files as $css_item_html) echo $css_item_html;
	}
	// Render JS Files for This View
	if (isset($view_js_files))
	{
		foreach ($view_js_files as $js_item_html) echo $js_item_html;
	}
	?>
	
	<script>
	/* ajax call to display the discount summary*/
	$.ajax({
		type: "POST",
		url: '/frontend/products/ajax_get_discount_html',
		success: function(html){
			
			if($(html).filter('.offer-con').html()){
			  $('#DeliveryDeals').show();
			  $('#checkout_discount_options').html(html);
			}else{
				$('#DeliveryDeals').hide();
			    $('#checkout_discount_options').html('');
		    }	
		}
	});

	/* ajax call to display applied discounts*/
	$.ajax({
		type: "POST",
		url: '/frontend/products/ajax_get_applied_discount_html',
		success: function(html){
			$('#checkout_discount_applied').html(html);
		}
	});
</script>
<?php else: ?>
	<div class="alert"><?= __('Your shopping cart is empty.') ?></div>
<?php endif; ?>
<script>
	$(document).ready(function () {
		update_county_list();

		if (jQuery().datepicker)
		{
			$(".datepicker").datepicker();
			$(".datepicker").datepicker( "option", "dateFormat", 'dd-mm-yy');
		}

	});
	$('#postalZone').on('change', function ()
	{
		var destination = $('#postalZone').find(':selected').html().toLowerCase();
		if (destination == 'collect in store' || destination == 'for collection')
		{
			// Shipping destination not needed, when the user is collecting
			$('#addressCheckbox').prop('checked', true).trigger('change');
			$('#shipping_heading_wrapper').hide();

			// Display message
			$('#collect_dialogue').dialog({
				resizable: false,
				modal: true,
				buttons: {
					OK: function () {
						$(this).dialog('close');
					}
				}
			});
		}
		else
		{
			$('#shipping_heading_wrapper').show();
		}
		update_county_list();

		// Toggle between county or postcode input, depending on postal zone
		if ($("#postalZone").val() != 1 && $("#postalZone").val() != 2)
		{
			$('#address_3').parents('tr').hide().find('select').prop('disabled', true);
			$('#shipping_address_3').parents('tr').hide().find('select').prop('disabled', true);
			$('#postcode_tr, #shipping_postcode_tr').show().find('input[type="text"]').prop('disabled', false);
		}
		else
		{
			$('#postcode_tr, #shipping_postcode_tr').hide().find('input[type="text"]').prop('disabled', true);
			$('#address_3').parents('tr').show().find('select').prop('disabled', false);
			$('#shipping_address_3').parents('tr').show().find('select').prop('disabled', false);
		}

		// Show/hide country selector, depending on whether or not Ireland is the postal zone
		if ($('#postalZone').find(':selected').html().substr(0, 7) == 'Ireland')
		{
			$('#address_4, #shipping_address_4').val('').parents('tr').hide();
		}
		else
		{
			$('#address_4, #shipping_address_4').parents('tr').show();
		}
	}).trigger('change');

	function update_county_list() {
		var zone = $('#postalZone').find(':selected').html();
		if (document.getElementById('use_postal_methods').value == 1) {
			zone = document.getElementById('address_4').value;
		}

		$.ajax({
			url: '/frontend/products/ajax_update_county_list/',
			data: { zone: zone},
			type: 'POST',
			dataType: 'json'
		}).done(function (results) {
                var $address3_label = $('[for="address_3"]');
                var $shipping_address_label = $('[for="shipping_address_3"]');

				if (results != '') {
                    $address3_label.html($address3_label.attr('data-county'));
					$('#address_3').parent().html('<select id="address_3" name="address_3">' + results + '</select>');
                    $shipping_address_label.html($shipping_address_label.attr('data-county'));
					$('#shipping_address_3').parent().html('<select id="shipping_address_3" name="shipping_address_3">' + results + '</select>');
				}
				else {
                    $address3_label.html($address3_label.attr('string_state_county'));
					$('#address_3').parent().html('<input type="text" style="width: 100px;" maxlength="70" name="address_3" id="address_3" value="">');
                    $shipping_address_label.html($shipping_address_label.attr('string_state_county'));
					$('#shipping_address_3').parent().html('<input type="text" style="width: 100px;" maxlength="70" name="shipping_address_3" id="shipping_address_3" value="">');
				}
			});
	}
</script>
