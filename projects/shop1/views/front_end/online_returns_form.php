<style>
	.formrt label {color: #000;text-align: left;width:85px;}
	.formrt li > label{font-weight: bold;}
	.formrt label small{color:#7e7e7e;display:block;}
	.formrt label:not([for]):after, .formrt [for="returns_form_postcode"]:after, .formrt [for="returns_form_refund_message"]:after{display:none;}
	.formrt .messagebox, .formrt .messagebox_label, .formrt .return_action_label{clear:both;text-align:left;width:100%;}
	.formrt legend{border:none;margin-left:0;margin-bottom: 5px;padding-top:10px;}
	.return_action_label + div{padding-left:20px;}
	.shipping_costs_selection label, .payment_method_selector label{float:none;margin-right:15px;width:auto;}
	.online_returns_submit{text-transform:uppercase;font-weight:bold;}
	.formrt .validation_label:not(:empty):after{content:':*';}
	.formrt input[type="text"], .formrt textarea {width: 200px;}
	.formrt .messagebox{height:8em;}
	#online_form_return_fieldset label {
		float: none;
		width: auto;
	}
	.payment-method-option [type="radio"] {
		position: absolute;
		opacity: 0;
	}
	.payment-method-option img {
		height: 24px;
	}
	.payment-method-option [type="radio"]:focus + img,
	.payment-method-option [type="radio"]:checked + img {
		outline: 2px solid skyblue;
	}
</style>
<div class="formrt">
	<form action="frontend/formprocessor/" enctype="multipart/form-data" id="online_returns_form" method="post" name="online_returns_form">
		<input type="hidden" name="redirect" value="7">
		<input type="hidden" name="failpage" value="7">
		<input type="hidden" id="redirect" name="redirect" value="thank-you.html">
		<input type="hidden" id="trigger" name="trigger" value="custom_form">
		<input type="hidden" id="email_template" name="email_template" value="return_order">
		<input type="hidden" id="form_identifier" name="form_identifier">
		<input type="hidden" id="event" name="event"value="returns">

		<ul>
			<li>
				<fieldset id="online_form_details_fieldset">
					<legend>Your Details</legend>

					<ul>
						<li>
							<label for="returns_form_order_no" class="validation_label">Order No.</label>
							<input type="text" class="validate[required]" id="returns_form_order_no" name="order_no" />
						</li>

						<li>
							<label for="returns_form_first_name" class="validation_label">First Name</label>
							<input type="text" class="validate[required]" id="returns_form_first_name" name="first_name" />
						</li>

						<li>
							<label for="returns_form_surname" class="validation_label">Surname</label>
							<input type="text" class="validate[required]" id="returns_form_surname" name="surname" />
						</li>

						<li>
							<label for="returns_form_address" class="validation_label">Address</label>
							<textarea id="returns_form_address" class="validate[required]" name="address" rows="4"></textarea>
						</li>

						<li>
							<label for="returns_form_county" class="validation_label">County</label>
							<input type="text" class="validate[required]" id="returns_form_county" name="county" />
						</li>

						<li>
							<label for="returns_form_country" class="validation_label">Country</label>
							<select class="validate[required]" id="returns_form_country" name="country" style="width: 180px;">
								<option value="">-- Select a country --</option>
								<?= Model_Country::get_country_as_options('IE'); ?>
							</select>
						</li>

						<li>
							<label for="returns_form_postcode">Postcode:<br /><small>(if applicable)</small></label>
							<input type="text" id="returns_form_postcode" name="postcode" />
						</li>

						<li>
							<label for="email" class="validation_label">Email</label>
							<input class="validate[required]" id="email" name="email" type="text" />
						</li>

						<li>
							<label for="phone" class="validation_label">Tel/Mobile</label>
							<input class="validate[required]" id="phone" name="phone" type="text" />
						</li>
					</ul>
				</fieldset>
			</li>

			<li>
				<fieldset id="online_form_return_fieldset">
					<legend>Action Required</legend>
					<ul>
						<li>
							<label class="return_action_label">
								<input type="radio" name="return_action" value="refund" />
								<strong>I want a refund.</strong>
							</label>
							<div>
								<textarea class="messagebox" id="returns_form_refund_message" name="refund_message"></textarea>
								<label for="returns_form_refund_message" class="messagebox_label">Please tell us why you want to refund this item.</label>
							</div>
						</li>

						<li>
							<label class="return_action_label">
								<input type="radio" name="return_action" value="replacement" />
								<strong>I would like to exchange an item</strong>
							</label>

							<div>
								<p>Please choose a reason for this exchange:</p>
								<p>
									<label>
										<input type="radio" name="replacement_reason" value="wrong_size" />
										Wrong Size
									</label>
									<label>
										Correct size to be:
										<input type="text" name="correct_size" style="width:84px;"/>
									</label>
								</p>
								<p>
									<label>
										<input type="radio" name="replacement_reason" value="other" />
										Other (Please Specify):
									</label>
									<textarea name="other_replacement_reason" class="messagebox" id="returns_form_other_replacement_reason" style="width:10.2em;height:4.5em;"></textarea>
								</p>
							</div>
						</li>

						<li style="padding-left: 20px;">
							<h3>Exchange Shipping Costs:</h3>
							<p>This payment covers the delivery of your replacement item.</p>

							<div class="shipping_costs_selection">
								<label>
									<input type="radio" name="ship_to" value="Ireland" />
									<strong>Ireland</strong> - &euro;3.99
								</label>

								<label>
									<input type="radio" name="ship_to" value="Ireland" />
									<strong>Worldwide</strong> - &euro;4.99
								</label>
							</div>
						</li>
					</ul>
				</fieldset>

				<fieldset style="padding-left: 20px;">
					<legend>Select your Method of Payment</legend>

					<div class="payment_method_selector">
						<label class="payment-method-option">
							<input type="radio" name="payment_method" value="paypal" />
							<img src="<?= URL::overload_asset('images/paypal_logo.png') ?>" alt="PayPal" />
						</label>
						<label class="payment-method-option">
							<input type="radio" name="payment_method" value="master_card" />
							<img src="<?= URL::overload_asset('images/mastercard.png') ?>" alt="MasterCard" />
						</label>
						<label class="payment-method-option">
							<input type="radio" name="payment_method" value="visa" />
							<img src="<?= URL::overload_asset('images/visa.png') ?>" alt="Visa" />
						</label>
					</div>
					<input type="hidden" name="ignore_captcha" value="true" />

				</fieldset>

				<button class="online_returns_submit" id="online_returns_submit" type="submit">Submit</button>

			</li>

		</ul>
	</form>
</div>
<script>
	$('#online_returns_submit').on('click', function (ev)
	{
		ev.preventDefault();
		var valid = $('#online_returns_form').validationEngine('validate');
		if (valid)
		{
			$('#online_returns_form').submit();
		}
		else
		{
			setTimeout('removeBubbles()', 5000);
		}
	});
</script>