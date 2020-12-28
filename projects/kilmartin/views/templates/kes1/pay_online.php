<?php include Kohana::find_file('template_views', 'header'); ?>

    <div class="row page-content">
        <?php if (!empty($page_data['content'])): ?>
            <div><?= $page_data['content'] ?></div>
        <?php endif; ?>

        <?php
        $paypal = trim(Settings::instance()->get('paypal_email'));
        $realex = trim(Settings::instance()->get('realex_username'));
        ?>

        <div id="checkout_messages"><?= IbHelpers::get_messages() ?></div>

        <div id="paynow-form">
            <form method="post" id="payment" name="payment_form" action="#" style="max-width: 1000px;">
                <input type="hidden" value="payment-thanks.html" name="thanks_page" id="thanks_page" />

                <div>
                    <h2><?= __('Your Transaction Details') ?></h2>

                    <div class="row gutters">
                        <div class="col-sm-6">
                            <div class="form-row">
                                <?php
                                $attributes = array('id' => 'payment_ref', 'placeholder' => 'Invoice No.');
                                echo Form::ib_input(null, 'payment_ref', null, $attributes, array('icon' => '<span class="icon_documents"></span>'));
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-row">
                                <?php
                                $attributes = array('class' => 'validate[required,custom[number],min[20]]', 'id' => 'payment_total', 'placeholder' => 'Payment total');
                                echo Form::ib_input(null, 'payment_total', null, $attributes, array('icon' => '&euro;'));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="pay_online_form-comments">Comments</label>
                        <?php
                        $attributes = array('placeholder' => 'Enter any comments to help us process your payment', 'id' => 'comments', 'rows' => 3);
                        echo Form::ib_textarea(null, 'comments', null, $attributes);
                        ?>
                    </div>
                </div>

                <div class="row gutters">
                    <div class="col-sm-6">
                        <h2><?= __('Your Payment Details') ?></h2>

                        <div class="form-row">
                            <?php
                            $attributes = array('class' => 'validate[required]', 'id' => 'name', 'placeholder' => 'Full name');
                            echo Form::ib_input(null, 'name', null, $attributes, array('icon' => '<span class="icon_profile"></span>'));
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $attributes = array('placeholder' => 'Phone number', 'id' => 'phone');
                            echo Form::ib_input(null, 'phone', null, $attributes, array('icon' => '<span class="icon_phone"></span>'));
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $attributes = array('class' => 'validate[required,custom[email]]', 'id' => 'email', 'placeholder' => 'Email address');
                            echo Form::ib_input(null, 'email', null, $attributes, array('icon' => '<span class="icon_mail"></span>'));
                            ?>
                        </div>

                        <div class="form-row">
                            <img src="<?= URL::overload_asset('img/secure-payment.png') ?>" alt="<?= __('Secure Online Payments') ?>" />
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <h2><?= __('Credit Card Payment') ?></h2>

                        <div class="form-row">
                            <?php
                            $options = array('' => 'Card type', 'visa' => 'Visa', 'mc' => 'MasterCard', 'laser' => 'Laser');
                            $attributes = array('class' => 'validate[required]', 'id' =>'ccType');
                            echo Form::ib_select(null, 'ccType', $options, null, $attributes);
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $attributes = array('id' => 'ccName', 'placeholder' => 'Name on Card', 'maxlength' => '50', 'class' => 'validate[required,custom[onlyLetterSp]]');
                            echo Form::ib_input(null, 'ccName', null, $attributes, array('icon' => '<span class="icon_id"></span>'));
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $attributes = array('id' => 'ccNum', 'placeholder' => 'Card Number', 'class' => 'validate[required,funcCall[luhnTest]]', 'id' => 'checkout-ccNum');
                            echo Form::ib_input(null, 'ccNum', null, $attributes, array('icon' => '<span class="icon_shield"></span>'));
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $attributes = array('id' => 'ccv', 'placeholder' => 'CVV Number', 'class' => 'validate[required,custom[onlyNumberSp]]', 'maxlength' => 4);
                            echo Form::ib_input(null, 'ccv', null, $attributes, array('icon' => '<span class="icon_lock"></span>'));
                            ?>
                        </div>

                        <div class="form-row">
                            <div class="input_columns" id="expiry">
                                <div class="input_column"><label for="ccExpMM">Expiry</label></div>
                                <div class="input_column">
                                    <?php
                                    $options = array('' => 'mm*');
                                    for ($i = 1; $i <= 12; $i++) {
                                        $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                                        $options[$num] = $num;
                                    }
                                    $attributes = array('class' => 'validate[required]', 'id' => 'ccExpMM');
                                    echo Form::ib_select(null, 'ccExpMM', $options, null, $attributes);
                                    ?>
                                </div>
                                <div class="input_column">
                                    <?php
                                    $options = array('' => 'yyyy*');
                                    for ($i = 0; $i <= 10; $i++) {
                                        $value = str_pad(date('y') + $i, 2, '0', STR_PAD_LEFT);
                                        $options[$value] = date('Y') + $i;
                                    }
                                    $attributes = array('class' => 'validate[required]', 'id' => 'ccExpYY');
                                    echo Form::ib_select(null, 'ccExpYY', $options, null, $attributes);
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-row">
                    <script>var RecaptchaOptions = {theme: 'clean'};</script>
                    <?php
                    $captcha_enabled = Settings::instance()->get('captcha_enabled');
                    if ($captcha_enabled) {
                        require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                        $captcha_public_key = Settings::instance()->get('captcha_public_key');
                        echo recaptcha_get_html($captcha_public_key);
                    }
                    ?>
                </div>

                <div class="form-row">
                    <?php
                    $label = 'I have read and agree to the <a href="/terms-and-conditions.html" target="_blank">terms and conditions</a> *';
                    echo Form::ib_checkbox($label, 'terms', '1', null, array('class' => 'validate[required]', 'id' => 'terms'));
                    ?>
                </div>
                <div class="form-row">
                    <?php
                    $label = 'I would like to sign up to the newsletter';
                    echo Form::ib_checkbox($label, 'signupCheckbox', '1', null, array('id' => 'signupCheckbox'));
                    ?>
                </div>

                <?php if ($realex): ?>
                    <div class="form-row submit_checkout_button">
                        <button type="button" class="button button--submit" id="pay_now_button" name="submit-payment">Submit</button>
                    </div>
                <?php endif; ?>

                <?php if ($paypal): ?>
                    <div id="paypalButton" class="form-row payment_method_view">
                        <a href="" id="paypal-button"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" alt="Checkout with PayPal"></a>
                    </div>
                <?php endif; ?>

                <?php if (!$realex && !$paypal): ?>
                    <h3>No payments methods are enabled.</h3>

                    <p>Please contact site administrator.</p>
                <?php endif;?>
            </form>
        </div>
    </div>
<?php include Kohana::find_file('views', 'footer'); ?>