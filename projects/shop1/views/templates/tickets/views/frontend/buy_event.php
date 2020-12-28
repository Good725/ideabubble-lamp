<?php require_once Kohana::find_file('template_views', 'header') ?>
<?php
$currencies = Model_Currency::getCurrencies(true);
$base_url = URL::base();
$logged_in_user = Auth::instance()->get_user();
$is_logged_in = ( ! empty($logged_in_user));

if ( ! empty($_POST['event_id']))
{
    $session_instance = Session::instance();
    $session_instance->set('event_checkout_post_data', $_POST);
}
?>

<?php if ($order['error']): ?>
	<div class="row row--checkout">
		<p><?=$order['error']?></p>
	</div>
<?php else: ?>
	<?php $currency = $currencies[$order['currency']]['symbol']?>
	<script>
        if (typeof fbq === 'function') {
            fbq('track', 'InitiateCheckout');
        }
	</script>
	<input type="hidden" id="checkout-country_json" value="<?= htmlentities(json_encode(Model_Event::getCountryMatrix())) ?>" />
	<div class="row row--checkout">
		<form class="columns small-12" id="checkout" method="post">
			<h2 class="text-primary event_heading"><?= __('Confirm Booking') ?></h2>
			<?php
			if ( ! empty($countdown_seconds)){
			?>	
			<div class="booking_timer">
				<div class="tooltip_timer">
					<span id="hms_timer"></span>
					<span class="tooltiptext">Please complete your purchase within this time.</span>
				</div>
			</div>
			<?php
			}
			?>
            <input type="hidden" name="partial_payment_id" value="<?= @$partial_payment['partial_payment']['id'] ?>" />
            <input type="hidden" name="partial_payment_id" value="<?= @$partial_payment['partial_payment']['id'] ?>" />
			<input type="hidden" name="ticket_id" value="<?= $order['ticket_id'] ?>" />
			<input type="hidden" name="event_id" value="<?= $order['event_id'] ?>" />
            <input type="hidden" name="currency" value="<?= $order['currency'] ?>" />
			<input type="hidden" name="total" value="<?= $order['total'] ?>" />
            <input type="hidden" id="saveCheckout" name="saveCheckout" value="0" />

			<div class="widget">
				<table class="table table--checkout">
					<thead>
						<tr>
							<th scope="col" class="item"><?= __('Item') ?></th>
							<th scope="col"class="price"><?= __('Price') ?></th>
							<th scope="col" class="total"><?= __('Total') ?></th>
							<th scope="col"class="quantity"><?= __('Quantity') ?></th>
						</tr>
					</thead>
					<tbody id="event-checkout-items">
						<?php foreach ($order['items'] as $itemIndex => $item): ?>
							<?php
							foreach ($item['dates'] as $dtIndex => $dateId) {
                                $starts = null;
                                foreach ($event['dates'] as $dtDetails) {
                                    if ($dtDetails['id'] == $dateId) {
                                        $starts = $dtDetails['starts'];
                                    }
                                }
							?>
							<?php foreach ($event['ticket_types'] as $ticketType): ?>
								<?php if ($ticketType['id'] == $item['ticket_type_id']): ?>
									<tr>
										<td><a style="color:inherit;" href="/event/<?= $event['url'] ?>"><?= $event['name'] ?></a> &ndash; <?= $ticketType['name'] . ($event['one_ticket_for_all_dates'] == 0 ? date(' F j g:i a', strtotime($starts)) : '') ?></td>
										<td>
                                            <?php if ($ticketType['type'] != 'Donation'): ?>
                                                <?= $currency.number_format($item['total'] + $item['discount'], ($item['total'] + $item['discount'] == floor($item['total'] + $item['discount']) ? 0 : 2))?>
                                            <?php else: ?>
                                                <label class="input-with-icon">
                                                    <span class="input-icon"><?= $currency ?></span>
                                                    <input type="text" class="item_donation" style="text-indent: 2em;" name="item[<?=$itemIndex?>][donation]" value="<?=$item['donation'] ? $item['donation'] : '0.00'?>" data-old_value="<?=$item['donation'] ? $item['donation'] : '0.00'?>" />
                                                </label>
                                            <?php endif; ?>
										</td>

										<td>
											<input type="hidden" class="item_total" name="item[<?=$itemIndex?>][total]" value="<?= $currency.number_format(($item['quantity'] * ($item['total'] + $item['discount'])), 2) ?>" data-single-base="<?= $ticketType['price'] ?>" data-single-total="<?=($item['total'] + $item['discount'])?>" data-currency="<?=$currency?>" />
											<span class="item_total-display"><?= $currency.number_format(($item['quantity'] * ($item['total'] + $item['discount'])), (($item['quantity'] * ($item['total'] + $item['discount'])) == floor(($item['quantity'] * ($item['total'] + $item['discount']))) ? 0 : 2)) ?></span>
										</td>

										<td>
                                            <?php if ($partial_payment) { ?>
                                                <?=number_format($partial_payment['partial_payment']['payment_amount'], 2)?>
                                            <?php } else { ?>
                                                <button type="button" class="button--adjust minus">&minus;</button>
                                                &nbsp;<span class="qty"><?= $item['quantity'] ?></span>&nbsp;
                                                <button type="button" class="button--adjust plus">+</button>
                                                <input class="ticket_type" type="hidden" name="item[<?= $itemIndex ?>][ticket_type_id]" value="<?= $item['ticket_type_id'] ?>" />
                                                <input class="qty" type="hidden" name="item[<?= $itemIndex ?>][quantity]" value="<?= $item['quantity'] ?>" data-min="<?=$ticketType['min_per_order'] > 1 ? $ticketType['min_per_order'] : 1?>" data-max="<?=$ticketType['max_per_order']?>"/>
                                                <?php if ($event['one_ticket_for_all_dates'] == 0) { ?>
                                                <input class="dt" type="hidden" name="item[<?= $itemIndex ?>][dates][]" value="<?= $dateId ?>" />
                                                <?php } else { ?>
                                                <?php foreach ($event['dates'] as $dtDetails) { ?>
                                                <input class="dt" type="hidden" name="item[<?= $itemIndex ?>][dates][]" value="<?= $dtDetails['id'] ?>" />
                                                <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
										</td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
							<?php
								if ($event['one_ticket_for_all_dates'] == 1) {
									break;
								}
							}
							?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

            <?php if ($partial_payment) { ?>
                <div class="row row--event collapse">
                    <div class="columns small-12 large-offset-4 large-8 text-right checkout-prices">
                        <div class="row checkout-prices-subtotal">
                            <div class="columns small-6 medium-9">Partial Payment</div>
                            <div class="columns small-6 medium-3"><?= $currency ?><span id="subtotal-display"><?= number_format($partial_payment['partial_payment']['payment_amount'], 2)?></span></div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
			<div class="row row--event collapse">
				<div class="columns small-12 large-offset-4 large-8 text-right checkout-prices">
					<div class="row checkout-prices-subtotal">
						<div class="columns small-6 medium-9">Sub Total</div>
						<div class="columns small-6 medium-3"><?= $currency ?><span id="subtotal-display"><? $subtotal = $order['total'] - $order['vat'] - $order['commission'] + $order['discount']; echo number_format($subtotal, $subtotal == floor($subtotal) ? 0 : 2)?></span></div>
					</div>

                    <?php if ($order['has_discounts'] ): ?>
						<div class="row checkout-promo_code-wrapper">
							<label class="columns small-12 medium-3 small-text-left" for="checkout-promo_code"><strong><?= __('Promo Code') ?></strong></label>
							<div class="columns small-6 medium-5">
								<input type="text" class="form_field checkout-promo_code" id="checkout-promo_code" name="discount_code" autocomplete="off" value="<?= $order['discount_code'] ?>" placeholder="<?= __('Promo Code') ?>" />
							</div>
							<div class="columns small-6 medium-4">
								<button type="button" class="button secondary text-uppercase button--full" id="checkout-discount-apply"><?= __('Apply') ?></button>
							</div>
						</div>

                        <div<?= ($order['discount'] > 0) ? '' : ' class="hidden"' ?> id="checkout-discount-wrapper">
                            <hr />
                            <div class="row checkout-prices-booking_fee">
                                <div class="columns small-6 medium-9" id="checkout-discount-label">
                                    <?php if ( ! empty($order['discount_type']) && ! empty($order['discount_type_amount'])): ?>
                                        <?= ($order['discount_type'] == 'Fixed') ? $currency.$order['discount_type_amount'] : ($order['discount_type_amount'] + 0).'%' ?> Discount
                                    <?php else: ?>
                                        <?= __('Discount') ?>
                                    <?php endif; ?>
                                </div>
                                <div class="columns small-6 medium-3">
                                    -<?= $currency ?><span id="discount-display"><?= number_format($order['discount'], $order['discount'] == floor($order['discount']) ? 0 : 2)?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

					<hr />
					<div class="row checkout-prices-booking_fee">
						<div class="columns small-6 medium-9"><?= __('Booking Fees') ?></div>
						<div class="columns small-6 medium-3"><?= $currency ?><span id="fees-display"><?= number_format($order['commission'], $order['commission'] == floor($order['commission']) ? 0 : 2)?></span></div>
					</div>

					<hr />

					<div class="row checkout-prices-booking_fee">
						<div class="columns small-6 medium-9"><?= __('VAT') ?></div>
						<div class="columns small-6 medium-3"><?= $currency ?><span id="vat-display"><?= number_format($order['vat'], $order['vat'] == floor($order['vat']) ? 0 : 2)?></span></div>
					</div>

					<hr />

					<div class="row checkout-prices-total">
						<div class="columns small-6 medium-9"><strong><?= __('Total') ?></strong></div>
						<div class="columns small-6 medium-3"><strong><?= $currency ?><span id="total-display"><?= number_format($order['total'], $order['total'] == floor($order['total']) ? 0 : 2) ?></span></strong></div>
					</div>

				</div>
			</div>
            <?php } ?>

            <?php if (count($event['ticket_paymentplans']) > 0 && $partial_payment == null) { ?>
                <div class="row row--checkout-details">
                    <div class="widget widget--checkout widget--checkout-billing">
                        <div class="widget-heading">
                            <div class="form-group">
                                <div class="col-sm-3 control-label"><?= __('Payment Plan') ?></div>
                                <div class="col-sm-9">
                                    <?= html::toggle_button('use_payment_plan',	__('Yes'), __('No'), false, $event['id']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="widget-body">
                            <table class="table hidden" id="paymentplan">
                                <thead>
                                <tr><th>Title</th><th>Amount</th><th>Due Date</th><th></th></tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($event['ticket_paymentplans'] as $paymentplan) {
                                ?>
                                    <tr>
                                        <td><?=$paymentplan['title']?></td>
                                        <td><?=number_format(round($order['total'] * ($paymentplan['payment_percent'] / 100.0), 2), 2)?></td>
                                        <td><?=$paymentplan['due_date'] ?: 'Now'?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if ($event['enable_multiple_payers'] == 'YES' && $partial_payment == null) { ?>
                <div class="row row--checkout-details">
                    <div class="widget widget--checkout widget--checkout-billing">
                        <div class="widget-heading">
                            <div class="form-group">
                                <div class="col-sm-3 control-label"><?= __('Group Payment') ?></div>
                                <div class="col-sm-9">
                                    <?= html::toggle_button('enable_multiple_payers',	__('Yes'), __('No'), false) ?>
                                </div>
                            </div>
                        </div>

                        <div class="widget-body">
                            <table class="table hidden" id="multiple_payers">
                                <thead>
                                    <tr><th>Name</th><th>Email</th><th>Amount to Pay</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <tr class="hidden">
                                        <td><input type="text" name="payer[index][name]" value="" class="name" /> </td>
                                        <td><input type="text" name="payer[index][email]" value="" class="email" /> </td>
                                        <td><input type="text" name="payer[index][amount]" value="" class="amount" readonly="readonly" /> </td>
                                        <td><button type="button" class="btn remove" onclick="$(this).parent().parent().remove();get_multiple_payer_amounts();">Remove</button> </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"><button type="button" class="payer_add" onclick="add_multiple_buyer();">Add Payer</button> </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="row row--checkout-details">
				<div class="columns small-12 medium-6">
					<div class="widget widget--checkout widget--checkout-billing">
						<div class="widget-heading">
							<h3 class="widget-title"><?= __('Billing Information') ?></h3>
						</div>

						<div class="widget-body">
                            <?php if (empty($user['id'])): ?>
                                <div class="checkout-user_options">
                                    <label class="checkout-user_option">
                                        <input type="radio" name="login_option" value="new_user" checked="checked">
                                        <span><?= __('New User') ?></span>
                                    </label>
                                    <label class="checkout-user_option">
                                        <input type="radio" name="login_option" value="registered_user">
                                        <span><?= __('Registered User') ?></span>
                                    </label>
                                </div>

                            <?php endif; ?>
                            <div class="checkout-toggleable checkout-toggleable--new_user">
                                <div id="checkoutDetails">
                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-firstname"><?= __('First Name') ?></label>
                                        <div class="columns small-8">
                                            <input type="text" class="form_field validate[required]" id="checkout-firstname" name="firstname" value="<?= $checkoutDetails['firstname'] ?>"/>
                                        </div>
                                    </div>

									<div class="form-group">
										<label class="columns small-4" for="checkout-lastname"><?= __('Last Name') ?></label>
										<div class="columns small-8">
											<input type="text" class="form_field validate[required]" id="checkout-lastname" name="lastname" value="<?= $checkoutDetails['lastname'] ?>"/>
										</div>
									</div>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-address"><?= __('Address') ?></label>
                                        <div class="columns small-8">
                                            <textarea class="form_field validate[required]" id="checkout-address" name="address" rows="3"><?= $checkoutDetails['address'] ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-city"><?= __('Town/&#8203;City') ?></label>
                                        <div class="columns small-8">
                                            <input type="text" class="form_field validate[required]" id="checkout-city" name="city" value="<?= $checkoutDetails['city'] ?>"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="columns small-4 large-nowrap" for="checkout-county"><?= __('State/&#8203;County') ?></label>
                                        <div class="columns small-8">
                                            <input type="text" class="form_field validate[required]" id="checkout-county" name="county" value="<?= $checkoutDetails['county'] ?>"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-country"><?= __('Country') ?></label>
                                        <div class="columns small-8">
                                            <div class="select">
                                                <select class="form_field validate[required]" id="checkout-country" name="country_id">
                                                    <option></option>
                                                    <?= html::optionsFromRows('id', 'name', Model_Event::getCountryMatrix(), $checkoutDetails['country_id']) ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-postcode"><?= __('Postcode') ?></label>
                                        <div class="columns small-8">
                                            <input type="text" class="form_field" id="checkout-postcode" name="postcode" value="<?= $checkoutDetails['postcode'] ?>"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-telephone"><?= __('Tel/Mobile') ?></label>
                                        <div class="columns small-8">
                                            <input type="text" class="form_field" id="checkout-telephone" name="telephone" value="<?= $checkoutDetails['telephone'] ?>"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-email"><?= __('Email') ?> *</label>
                                        <div class="columns small-8">
                                            <input type="text" class="form_field validate[required,custom[email]]" id="checkout-email" name="email" value="<?= $checkoutDetails['email'] ?>"/>
                                        </div>
                                    </div>

                                    <?php if ( ! $is_logged_in): ?>
                                        <div class="form-group">
                                            <label class="columns small-4" for="checkout-password"><?= __('Password') ?> *</label>
                                            <div class="columns small-8">
                                                <input type="password" class="form_field validate[required,minSize[8]]" id="checkout-password" name="password" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="columns small-4 large-nowrap" for="checkout-mpassword"><?= __('Confirm Password') ?> *</label>
                                            <div class="columns small-8">
                                                <input type="password" class="form_field validate[required,equals[checkout-password]" id="checkout-mpassword" name="mpassword" />
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-comments"><?= __('Comments') ?></label>
                                        <div class="columns small-8">
                                            <textarea class="form_field" id="checkout-comments" name="comments" rows="3"><?= $checkoutDetails['comments'] ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="checkout-toggleable checkout-toggleable--registered_user hidden">

                                <div id="checkout-login">
                                    <input type="hidden" name="redirect" value="/checkout.html" />
                                    <input type="hidden" name="action" value="register"/>

                                    <div class="checkout-login-errors" id="checkout-login-errors"></div>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-login-email"><?= __('Email') ?> *</label>
                                        <div class="columns small-8">
                                            <input type="email" class="form_field validate[required,custom[email]]" id="checkout-login-email" name="login_email" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>" placeholder="<?= __('Email') ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="columns small-4" for="checkout-login-password"><?= __('Password') ?> *</label>
                                        <div class="columns small-8">
                                            <input type="password" class="form_field validate[required]" id="checkout-login-password" name="login_password" value="<?= isset($_POST['password']) ? $_POST['password'] : '' ?>" placeholder="<?= __('Password') ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="columns small-12 large-12" style="text-align:center;">
                                            <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="columns small-12 text-right">
                                            <a class="checkout-forgot_password" href="/admin/login/forgot_password" target="_blank">
                                                <strong><?= __('I forgot my password') ?></strong>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="form-group login-buttons">
                                        <div class="col-sm-12">
                                            <div class="columns small-offset-1 small-10 large-offset-2 large-8">
                                                <button type="submit" id="checkout-login-submit" class="button secondary button--full text-uppercase"><?= __('Log in to proceed') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</div>

				<div class="columns small-12 medium-6 checkout-toggleable checkout-toggleable--new_user">
					<div class="widget widget--checkout widget--checkout-billing payment-section payment-cc<?= ($order['total'] == 0) ? ' hidden' : '' ?>">
						<div class="widget-heading">
							<h3 class="widget-title"><?= __('Credit Card Payment Details') ?></h3>
						</div>

						<div class="widget-body">
							<div class="form-group">
								<label class="columns small-4" for="checkout-ccType"><?= __('Card Type') ?></label>
								<div class="columns small-8">
									<div class="select">
										<select class="form_field validate[required]" name="ccType" id="checkout-ccType">
											<option value=""></option>
											<option value="visa"><?= __('VISA') ?></option>
											<option value="mc"><?= __('MasterCard') ?></option>
										</select>
									</div>
								</div>
							</div>


							<div class="form-group">
								<label class="columns small-4 large-nowrap" for="checkout-ccNum"><?= __('Card Number') ?></label>
								<div class="columns small-8">
									<input type="text" class="form_field validate[required]" id="checkout-ccNum" name="ccNum" maxlength="20" />
								</div>
							</div>

							<div class="form-group">
								<label class="columns small-4" for="checkout-ccv"><?= __('CCV No.') ?></label>
								<div class="columns small-8">
									<input type="text" class="form_field validate[required]" id="checkout-ccv" name="ccCVC" maxlength="4" />
								</div>
							</div>


							<div class="form-group checkout-expiration">
								<label class="columns small-4" for="checkout-ccYear"><?= __('Expiry') ?></label>

								<div class="columns small-8">
									<label class="show-for-sr" for="checkout-ccMonth"><?= __('Expiration Month') ?></label>
									<div class="select">
										<select class="form_field validate[required]" id="checkout-ccMonth" name="ccMonth">
											<option value="">MM</option>
											<?php for ($i = 1 ; $i <= 12 ; ++$i): ?>
												<option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>"><?= $i ?></option>
											<?php endfor; ?>
										</select>
									</div>

									<label class="show-for-sr" for="checkout-ccYear"><?= __('Expiration Year') ?></label>
									<div class="select">
										<select class="form_field validate[required]" id="checkout-ccYear" name="ccYear">
											<option value="">YYYY</option>
											<?php for ($i = 0 ; $i < 20 ; ++$i): ?>
												<option value="<?= date('Y') + $i ?>"><?= date('Y') + $i ?></option>
											<?php endfor; ?>
										</select>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="columns small-12 medium-offset-4 medium-8 large-offset-3 large-9 credit-card-image">
									<img src="/assets/<?= $assets_folder_path ?>/images/credit-card-icons.png" alt="Accepted card types: VISA, MasterCard, American Express, Discover" />
								</div>
							</div>

							<div style="font-size: .9em;">
								<div class="clearfix">
                                    <div class="columns small-12">
                                        <p>We use a secure certificate for all our payments and Realex or Stripe, our payment partners, provide all secure connections for your transactions.</p>
                                    </div>
								</div>



							</div>

						</div>
					</div>

					<div class="widget widget--checkout widget--checkout-billing payment-section" >
						<div class="widget-heading">
							<h3 class="widget-title"><?= __('Complete Booking') ?></h3>
						</div>

						<div class="widget-body">
							<div class="row--checkout">
								<div class="form-group">
									<label class="columns small-12 medium-offset-3 medium-9 checkout-checkbox-wrapper">
										<input type="checkbox" class="form-checkbox" name="signup_newsletter" value="1" id="test1" checked="checked" />
										<label for="test1"></label>
										<?= __('I would like to sign up to newsletter') ?>
									</label>
								</div>

								<div class="form-group">
									<label class="columns small-12 medium-offset-3 medium-9 checkout-checkbox-wrapper">
										<input type="checkbox" class="form-checkbox validate[required]" name="terms_accepted" value="1" id="test2" />
										<label for="test2"></label>
										I accept the <a href="/terms-and-conditions" target="_blank">terms and conditions</a>
									</label>
								</div>
							</div>

							<div class="form-group">
								<div class="columns small-offset-1 small-10 large-offset-2 large-8">
									<button type="submit" class="button secondary button--full text-uppercase" name="action" value="buy"><?= __('Confirm your booking') ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<input type="hidden" id="checkout-success-redirect" value="<?= isset($success_redirect) ? $success_redirect : '/thanks-for-shopping-with-us.html' ?>" />
			<input type="hidden" name="skip_duplicate_test" id="skip_duplicate_test" value="0" />

		</form>
	</div>
    <script>
        $(document).on('ready', function(){
            $('body').on('click', '#checkout-login-submit', function(ev)
            {
                ev.preventDefault();

                var form_data = ($('#checkout-login').find(':input').serialize()+'&ajax=1')
                    .replace('?login_email=',    '?email=')
                    .replace('&login_email=',    '&email=')
                    .replace('?login_password=', '?password=')
                    .replace('&login_password=', '&password=');

                $.post('/admin/login', form_data).done(function(data)
                {
                    console.log(data);
                    data = JSON.parse(data);
                    if (data.error)
                    {
                        $('#checkout-login-errors').html('<div class="alert checkout-alert">'+data.error+'</div>');
                    }
                    else if (data.redirect)
                    {
                        // Take note of the discount field
                        if (window.localStorage) {
                            localStorage.setItem('checkout_promo_code', $('#checkout-promo_code').val());
                        }

                        window.location = data.redirect+'?login=1';
                    }

                });
            });

            $('[name="login_option"]').on('change', function()
            {
                var choice = $('[name="login_option"]:checked').val();
                $('.checkout-toggleable').addClass('hidden');
                $('.checkout-toggleable--'+choice).removeClass('hidden');
            });

        });
    </script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script src="<?= URL::get_engine_plugin_assets_base('events') ?>js/buy_ticket.js?ts=<?= filemtime(ENGINEPATH.'plugins/events/development/assets/js/buy_ticket.js') ?>"></script>
	<script src="<?= URL::get_engine_plugin_assets_base('events') ?>js/jquery.countdownTimer.js"></script>

	<?php
		/* booking timer count down, explode the timer to pass values here */
		if ( ! empty($countdown_seconds)) {
			$countdown_time_hrs     = floor($countdown_seconds / 3600);
			$countdown_time_minutes = floor(($countdown_seconds % 3600) / 60);
			$countdown_time_sec     = $countdown_seconds % 60;
		?>
			<script>
			var timeout_url = '<?=$base_url."/timeout"; ?>';
			$(function(){
				$('#hms_timer').countdowntimer({
					hours : <?= $countdown_time_hrs ?>,
					minutes :<?= $countdown_time_minutes ?>,
					seconds : <?= $countdown_time_sec ?>,
					timeUp : redirect_to_home   
				});
			});

			function redirect_to_home() {
				window.location.href = '/event/<?=$event['url']?>?timeout=1';
			}
			</script>
	<?php
		}
	endif
	?>

<div class="reveal" id="checkout_error_modal" data-reveal>
	<h3 class="text-secondary"><?= __('Checkout error') ?></h3>
	<div id="checkout-error_modal-message"></div>

	<button type="button" class="button primary" data-close><?= __('Review') ?></button>

	<button class="close-button" data-close aria-label="Close modal" type="button">
		<span aria-hidden="true">&times;</span>
	</button>
</div>

<div class="reveal" id="checkout_save_details_modal" data-reveal>
    <p><?= __('Would you like to save your updated contact details for speedy checkout next time?') ?></p>

    <div class="modal-buttons">
        <button type="button" class="button primary" data-saveCheckout="1"><?= __('Yes') ?></button>
        <button type="button" class="button secondary" data-saveCheckout="0"><?= __('No') ?></button>
    </div>
</div>

<div class="reveal" id="checkout_duplicate_modal" data-reveal>
	<p class="duplicate_item"></p>
	<p><?= __('Do you want to continue')?></p>

	<div class="modal-buttons">
		<button type="button" class="button secondary yes"><?= __('Yes') ?></button>
		<button type="button" class="button primary no"><?= __('No') ?></button>
	</div>
</div>

<?php require_once Kohana::find_file('template_views', 'footer') ?>
