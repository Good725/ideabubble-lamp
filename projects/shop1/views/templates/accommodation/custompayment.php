<?php include 'template_views/header.php' ?>
        <form class="checkout-wrapper" action="/frontend/propman/custom_payment" id="booking-balance-payment-form" method="post">
            <input type="hidden" name="payment_id" value="" />
            <div class="checkout-group">
                <div class="col-xsmall-12 col-small-6">
                    <div>
                        <h3>Billing Information</h3>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-booking_id"><?= __('Booking Id') ?></label>

                            <div class="col-xsmall-4">
                                <input type="text" class="input-styled validate[number]"
                                       id="billing-information-bookingid" name="booking_id"/>
                            </div>
                            <div class="col-xsmall-4">
                                <button type="button" id="booking-lookup-button">Look Up</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xsmall-12" for="billing-information-booking-id"><?= __('Enter and click look up if you know your booking id')?></label>
                        </div>

                        <div class="form-group"></div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-name"><?= __('First Name') ?> *</label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-firstname" name="firstName"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-name"><?= __('Last Name') ?> *</label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-lastname" name="lastName"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-email"><?= __('Email') ?></label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[email]"
                                       id="billing-information-email" name="email"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-property"><?= __('Property Name') ?> *</label>

                            <div class="col-xsmall-8">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-property" name="property"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-checkin"><?= __('Check In Date') ?></label>

                            <div class="col-xsmall-4">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-checkin" name="checkin"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-checkout"><?= __('Check Out Date') ?></label>

                            <div class="col-xsmall-4">
                                <input type="text" class="input-styled validate[required]"
                                       id="billing-information-checkout" name="checkout"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xsmall-4" for="billing-information-email"><?= __('Balance Due') ?></label>

                            <div class="col-xsmall-3">
                                <div class="input-with-addon">
                                    <div class="input-addon">&euro;</div>
                                    <input type="text" class="input-styled validate[required]" id="booking-balance" name="balance" />
                                </div>
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
                                    proived all secure connections for your transaction.</p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div style="clear: both;">
                    <div class="col-xsmall-0 col-medium-7">&nbsp;</div>
                    <div class="col-xsmall-12 col-medium-5 compact-cols">
                        <label class="col-xsmall-12">
                            <input type="checkbox"/> I would like to sign up to the newsletter
                        </label>
                        <label class="col-xsmall-12">
                            <input type="checkbox" class="validate[required]" id="checkout-terms-and-conditions"
                                   name="terms"/> I accept the terms and conditions
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
                        <p id="payment_error"></p>
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
                <input type="hidden" name="cancel_return" value="<?= URL::base() . 'cpayment.html'?>"/>
                <input type="hidden" name="notify_url" value="<?= URL::base() ?>frontend/propman/paypal_callback"/>
                <input type="hidden" name="item_name_1" value=""
                       data-name="balance payment"/>
                <input type="hidden" name="amount_1" value=""/>
                <input type="hidden" name="quantity_1" value="1"/>
                <input type="hidden" name="custom" value=""/>
                <input type="hidden" name="invoice" value=""/>
            </form>
            <?php
        }
        ?>
<?php include 'template_views/footer.php' ?>