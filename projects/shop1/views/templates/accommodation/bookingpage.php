<?php include 'template_views/header.php' ?>
<?php if (isset($property_data) AND $property_data->id AND isset($query) AND trim($query['guests']) AND trim($query['check_in']) AND trim($query['check_out'])): ?>

	<?php
	$bookingPrice = Model_Propman::calculatePrice(
			$property_data->id,
			$query['check_in'],
			$query['check_out'],
			$query['guests']
	);
    if (@$bookingPrice['error']) {
    ?>
        <div class="notice notice-error">
            <strong><?= __('Error') ?></strong>: Invalid submission. <?php // Vague, but will do for now ?>
        </div>
    <?php
    } else {
        ?>


        <form class="checkout-wrapper" action="/frontend/propman/process_booking"
              id="booking-checkout-form" method="post">
            <input type="hidden" name="property_id" value="<?= $property_data->id ?>"/>
            <input type="hidden" name="check_in" value="<?= $query['check_in'] ?>"/>
            <input type="hidden" name="check_out" value="<?= $query['check_out'] ?>"/>
            <input type="hidden" name="booking_id" value=""/>

            <div class="col-xsmall-12">
                <h1 class="checkout-title"><?= $property_data->name ?></h1>

                <ul class="property-quick-details">
                    <li><?= $property_data->building_type->name ?></li>
                    <li>
                        From <?= date('d M Y', strtotime($query['check_in'])) ?> &ndash; <?= date('d M Y', strtotime($query['check_out'])) ?></li>
                    <li><?= $query['guests'] ?> <?= ($query['guests'] == 1) ? 'Guest' : 'Guests' ?></li>
                    <li><?= $property_data->count_beds() ?> Beds</li>
                </ul>
            </div>

            <div class="col-xsmall-12 checkout-group">
                <table class="booking-table">
                    <thead>
                    <tr>
                        <th scope="col"><?= __('Item') ?></th>
                        <th scope="col"><?= __('Price') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $property_data->name ?> / <?= $property_data->get_county() ?>
                            #<?= $property_data->ref_code ?></td>
                        <td>&euro;<?= number_format(@$bookingPrice['fee'], 2) ?></td>
                    </tr>
                    <tr>
                        <td><?= __('Booking Fee') ?></td>
                        <td>&euro;<?= number_format(@$bookingPrice['bookingfee'], 2) ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="checkout-group checkout-group-pricing">
				<div class="col-xsmall-12 col-small-4 col-medium-6">
					<img class="image-full" alt="" src="<?= $property_data->get_thumbnail() ?>" />
				</div>

                <div class="col-xsmall-12 col-small-8 col-medium-6">
                    <div class="form-group">
                        <label class="col-xsmall-5 form-label" for="booking-subtotal"><?= __('Subtotal') ?></label>

                        <div class="col-xsmall-7">
                            <div class="input-with-addon">
                                <div class="input-addon">&euro;</div>
                                <input type="text" class="input-styled" id="booking-subtotal" disabled="disabled" value="<?= number_format((@$bookingPrice['fee']+@$bookingPrice['bookingfee']), 2) ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xsmall-5 form-label" for="booking-subtotal"><?= __('Discounts') ?></label>

                        <div class="col-xsmall-7">
                            <div class="input-with-addon">
                                <div class="input-addon">&euro;</div>
                                <input type="text" class="input-styled" id="booking-subtotal" disabled="disabled" value="<?= number_format(@$bookingPrice['discount'], 2) ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xsmall-5 form-label" for="booking-totaldue"><?= __('Total Due') ?></label>

                        <div class="col-xsmall-7">
                            <div class="input-with-addon">
                                <div class="input-addon">&euro;</div>
                                <input type="text" class="input-styled" id="booking-totaldue" disabled="disabled" value="<?= number_format(@$bookingPrice['total'], 2) ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xsmall-5 form-label" for="booking-totaldue"><?= __('Payment Options') ?></label>
                        <?php
                            $now = time();
                            $weeks8 = strtotime('+8 weeks', $now);
                            $checkin = strtotime($query['check_in']);
                            $full_pay = $checkin < $weeks8;
                        ;?>
                        <div class="col-xsmall-7">
                            <div class="form-group">
                                <label class="form-label">
                                    <input type="radio" name="pay" value="deposit" <?= $full_pay ? ' disabled="disabled"': ' checked="checked" ' ?> onclick="$('#booking-amount').val('<?= number_format(Settings::instance()->get('propman_min_deposit'), 2) ?>');$('#booking-balance').val('<?= number_format(@$bookingPrice['total'] - Settings::instance()->get('propman_min_deposit'), 2) ?>');"/> <?= __('Pay Deposit Now') ?>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <input type="radio" name="pay" value="full" <?= $full_pay ? ' checked="checked" ' : ''?> onclick="$('#booking-amount').val('<?= number_format(@$bookingPrice['total'], 2) ?>');$('#booking-balance').val('0');"/> <?= __('Pay Full Amount') ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xsmall-5 form-label" for="booking-amount"><strong><?= __('Payment Amount') ?></strong></label>

                        <div class="col-xsmall-7">
                            <div class="input-with-addon">
                                <div class="input-addon">&euro;</div>
                                <input type="text" class="input-styled" id="booking-amount" disabled="disabled"
                                       value="<?=  $full_pay ? number_format(@$bookingPrice['total'], 2) :  Settings::instance()->get('propman_min_deposit') ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xsmall-5 form-label" for="booking-balance"><strong><?= __('Balance') ?></strong></label>

                        <div class="col-xsmall-7">
                            <div class="input-with-addon">
                                <div class="input-addon">&euro;</div>
                                <input type="text" class="input-styled" id="booking-balance" disabled="disabled"
                                       value="<?= $full_pay ? 0 : number_format(@$bookingPrice['total'] - Settings::instance()->get('propman_min_deposit'), 2) ?>"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="checkout-group">
                <div class="col-xsmall-12 col-small-6 col-medium-4">
                    <h3><?= __('Party details') ?></h3>

                    <div class="form-group">
                        <label class="col-xsmall-6 col-small-5 form-label" for="temp-guests"><?= __('Guests') ?></label>

                        <div class="col-xsmall-6 col-small-4">
                            <div class="select">
                                <select class="input-styled" id="temp-guests" name="guests">
                                    <?php
                                    $maxGuests = $property_data->max_occupancy;
                                    for ($i = 1; $i <= $maxGuests; ++$i) {
                                        echo '<option value="' . $i . '" ' . ($i == $query['guests'] ? 'selected="selected"' : '') . '>' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xsmall-6 col-small-5 form-label" for="temp-adults"><?= __('Adults') ?></label>

                        <div class="col-xsmall-6 col-small-4">
                            <div class="select">
                                <select class="input-styled" id="temp-adults" name="adults">
                                    <?php
                                    for ($i = 1; $i <= $maxGuests; ++$i) {
                                        echo '<option value="' . $i . '" ' . ($i == $query['guests'] ? 'selected="selected"' : '') . '>' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xsmall-6 col-small-5 form-label"
                               for="temp-children"><?= __('Children') ?></label>

                        <div class="col-xsmall-6 col-small-4">
                            <div class="select">
                                <select class="input-styled" id="temp-children" name="children">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xsmall-6 col-small-5 form-label"
                               for="temp-infants"><?= __('Infants') ?></label>

                        <div class="col-xsmall-6 col-small-4">
                            <div class="select">
                                <select class="input-styled" id="temp-infants" name="infants">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xsmall-12 col-small-6 col-medium-4">
                    <h3><?= ('Property details') ?></h3>

                    <p><?= $property_data->name ?>, <?= $property_data->get_county() ?></p>

                    <p>Ref: <?= $property_data->ref_code ?></p>

                    <p>From: <?= date('d M Y', strtotime($query['check_in'])) ?></p>

                    <p>To: <?= date('d M Y', strtotime($query['check_out'])) ?></p>
                </div>

                <?php $suitabilities = $property_data->suitability_types->order_by('sort')->find_all_published(); ?>

                <div class="col-xsmall-12 col-small-12 col-medium-4">
                    <h3><?= __('Suitability') ?></h3>
                    <?php foreach ($suitabilities as $key => $suitability): ?>
                        <p><?= $suitability->name ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="col-xsmall-12">
					<h4>Additional costs payable on arrival: Towels: &euro;3 per set / Pets: &euro;20 per dog (max of 2 dogs) / Cots &euro;5 & High Chairs &euro;5 (must be pre-booked and subject to availability</h4>
                </div>
            </div>

            <div class="checkout-group">
                <div class="col-xsmall-12 col-small-6">
                    <div>
                        <h3>Billing Information</h3>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-firstname"><?= __('First Name') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-firstname" name="firstName"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-lastname"><?= __('Last Name') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-lastname" name="lastName"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-address"><?= __('Address') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-address" name="address"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-town"><?= __('Town / City') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]" id="billing-information-town"
                                       name="town"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4"
                                   for="billing-information-county"><?= __('State / County') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-county" name="county"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-country"><?= __('Country') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-country" name="country"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4"
                                   for="billing-information-telephone"><?= __('Tel / Mobile') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-telephone" name="telephone"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-email"><?= __('Email') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-email" name="email"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-comments"><?= __('Comments') ?></label>

                            <div class="col-xsmall-8">
                                <textarea class="input-styled" id="billing-information-comments"
                                          name="comments"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xsmall-0 col-small-0 col-medium-1">&nbsp;</div>
                <div class="col-xsmall-12 col-small-6 col-medium-5">
                    <div>
                        <h3><?= __('Payment Details') ?></h3>
                        <?php
                        $realex_enabled = (Settings::instance()->get('enable_realex') AND Settings::instance()->get('realex_username') != '');
                        $paypal_enabled = (Settings::instance()->get('enable_paypal') AND Settings::instance()->get('paypal_email'));
                        ?>

                        <div class="form-group">
                            <?php if ($realex_enabled): ?>
                                <label
                                    class="col-xsmall-12 col-small-6 payment-method-select payment-method-select-card">
                                    <input type="radio" name="payment_select" data-method="credit-card"
                                           checked="checked" value="realex"/>
                                    <span><?= __('Credit Card') ?></span>
                                </label>
                            <?php endif; ?>

                            <?php if (Settings::instance()->get('enable_paypal') AND Settings::instance()->get('paypal_email')): ?>
                                <label
                                    class="col-xsmall-12 col-small-6 payment-method-select payment-method-select-paypal">
                                    <input type="radio" name="payment_select"
                                           data-method="paypal"<?= (!$realex_enabled) ? ' checked="checked"' : '' ?>
                                           value="paypal"/>
                                    <span>Paypal</span>
                                </label>
                            <?php endif; ?>
                        </div>

                        <?php if ($realex_enabled): ?>
                            <div class="payment-option-fields payment-option-fields-credit-card">
                                <div class="form-group">
                                    <label class="col-xsmall-4" for="checkout-ccType"><?= __('Card Type') ?></label>

                                    <div class="col-xsmall-8 col-medium-4">
                                        <div class="select">
                                            <select class="input-styled validate[required]" id="checkout-ccType"
                                                    name="ccType">
                                                <option value=""><?= __('Select...') ?></option>
                                                <option value="visa">Visa</option>
                                                <option value="mc">MasterCard</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xsmall-4" for="checkout-ccNum"><?= __('Card No.') ?></label>

                                    <div class="col-xsmall-8">
                                        <input type="text" class="input-styled validate[required]" id="checkout-ccNum"
                                               name="ccNum"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xsmall-4" for="checkout-expiry-mm"><?= __('Expiry') ?></label>

                                    <div class="col-xsmall-4">
                                        <div class="select">
                                            <select class="input-styled validate[required]" id="checkout-expiry-mm"
                                                    name="ccExpMM">
                                                <option value=""><?= __('Month...') ?></option>
                                                <option value="01">01 (Jan)</option>
                                                <option value="02">02 (Feb)</option>
                                                <option value="03">03 (Mar)</option>
                                                <option value="04">04 (Apr)</option>
                                                <option value="05">05 (May)</option>
                                                <option value="06">06 (Jun)</option>
                                                <option value="07">07 (Jul)</option>
                                                <option value="08">08 (Aug)</option>
                                                <option value="09">09 (Sep)</option>
                                                <option value="10">10 (Oct)</option>
                                                <option value="11">11 (Nov)</option>
                                                <option value="12">12 (Dec)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xsmall-4">
                                        <div class="select">
                                            <label class="sr-only"
                                                   for="checkout-expiry-yy"><?= __('Expiration year') ?></label>
                                            <select class="input-styled validate[required]" id="checkout-expiry-yy"
                                                    name="ccExpYY">
                                                <option value=""><?= __('Year...') ?></option>
                                                <?php $y = date('y'); ?>
                                                <?php for ($i = 0; $i < 10; $i++, $y++): ?>
                                                    <option value="<?= $y ?>"><?= $y ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xsmall-4" for="checkout-ccv"><?= __('CCV No.') ?></label>

                                    <div class="col-xsmall-8">
                                        <input type="text" class="input-styled validate[required]" id="checkout-ccv"
                                               name="ccv"
                                               placeholder="<?= __('Last 3 digits form signature strip') ?>"/>
                                    </div>
                                </div>

                                <div class="clearfix checkout-payment-icons">
                                    <img src="/assets/<?= $assets_folder_path ?>/images/mc-logo.png"
                                         alt="MasterCard logo"/>
                                    <img src="/assets/<?= $assets_folder_path ?>/images/visa-logo.png" alt="Visa logo"/>
                                    <img src="/assets/<?= $assets_folder_path ?>/images/realex-logo.png"
                                         alt="Realex logo"/>
                                </div>
                                <p>We use a secure certificate for all our payments and Realex, our payment partner
                                    provider all secure connections for your transaction.</p>
                            </div>
                        <?php endif; ?>

						<div>
							<label class="col-xsmall-12">
								<input type="checkbox"/> I would like to sign up to the newsletter
							</label>
							<label class="col-xsmall-12">
								<input type="checkbox" class="validate[required]" id="checkout-terms-and-conditions"
									   name="terms"/> I accept the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a>
							</label>

							<?php if ($realex_enabled): ?>
								<div class="payment-option-fields payment-option-fields-credit-card">
									<button type="submit" class="button-primary book-button"><?= __('Book Now') ?></button>
								</div>
							<?php endif; ?>

							<?php if ($paypal_enabled): ?>
								<div
									class="payment-option-fields payment-option-fields-paypal" <?= ($realex_enabled) ? ' style="display: none;"' : '' ?>>
									<button type="submit" class="button-link paypal-button" id="paypal-property-booking">
										<img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"/>
									</button>
								</div>
							<?php endif; ?>
							<p id="booking_error"></p>
						</div>

                    </div>
                </div>

                <div style="clear: both;">
                    <div class="col-xsmall-0 col-medium-7">&nbsp;</div>
                    <div class="col-xsmall-12 col-medium-5 compact-cols">

                    </div>
                </div>
            </div>

        </form>

        <?php
        if ($paypal_enabled) {
            ?>
            <form id="paypal-continue-form"
                  action="https://www.<?= (Settings::instance()->get('paypal_test_mode') == 1 ? 'sandbox.' : '') ?>paypal.com/cgi-bin/webscr"
                  method="post">
                <input type="hidden" name="cmd" value="_cart"/>
                <input type="hidden" name="upload" value="1"/>
                <input type="hidden" name="business" value="<?= Settings::instance()->get('paypal_email') ?>"/>
                <input type="hidden" name="currency_code" value="EUR"/>
                <input type="hidden" name="no_shipping" value="2"/>
                <input type="hidden" name="return" value="<?= URL::base() . 'thank-you-for-booking.html' ?>"/>
                <input type="hidden" name="cancel_return"
                       value="<?= URL::base() . 'property-details.html/' . $property_data->url ?>"/>
                <input type="hidden" name="notify_url" value="<?= URL::base() ?>frontend/propman/paypal_callback"/>
                <input type="hidden" name="item_name_1" value=""
                       data-name="<?= html::chars($property_data->name . ' ' . date('Ymd', strtotime($query['check_in'])) . ' ' . date('Ymd', strtotime($query['check_out']))) ?>"/>
                <input type="hidden" name="amount_1" value=""/>
                <input type="hidden" name="quantity_1" value="1"/>
                <input type="hidden" name="custom" value=""/>
                <input type="hidden" name="invoice" value=""/>
            </form>
            <?php
        }
        ?>
    <?php
    }
    ?>
<?php else: ?>
	<div class="notice notice-error">
		<strong><?= __('Error') ?></strong>: Invalid submission. <?php // Vague, but will do for now ?>
	</div>
<?php endif; ?>
<?php include 'template_views/footer.php' ?>