<?php
$settings = Settings::instance();
$paypal = false;
$realex = false;
if (strlen($settings->get('paypal_email')) > 0) {
    $paypal = true;
}
if (strlen($settings->get('realex_username')) > 0) {
    $realex = true;
}

?>

<script type="text/javascript">
    $(document).ready(function () {
        $('input[type=text][title],input[type=password][title],textarea[title]').each(function (i) {
            $(this).addClass('input-prompt-' + i);
            var promptSpan = $('<span class="input-prompt"/>');
            $(promptSpan).attr('id', 'input-prompt-' + i);
            $(promptSpan).append($(this).attr('title'));
            $(promptSpan).click(function () {
                $(this).hide();
                $('.' + $(this).attr('id')).focus();
            });
            if ($(this).val() != '') {
                $(promptSpan).hide();
            }
            $(this).before(promptSpan);
            $(this).focus(function () {
                $('#input-prompt-' + i).hide();
            });
            $(this).blur(function () {
                if ($(this).val() == '') {
                    $('#input-prompt-' + i).show();
                }
            });
        });
    });

</script>

<style>
    .input-prompt {
        position: absolute;
        font-style: italic;
        color: #aaa;
        margin: 0.5em 0 0 0.8em;
    }

</style>
<div id="checkout_messages">
    <?php echo IbHelpers::get_messages(); //The message. example: "Error processing the payment ?>
</div>
<div id="paynow-form">
    <form method="post" id="payment_form" name="payment_form" action="#">

        <div style="float:left;height:40px;">
            <div class="checkout_page_form_header"><h1>Your Transaction Details</h1></div>
            <div class="checkout_page_form_header"></div>
        </div>
        <div class="checkout_form_holder_first">
            <section class="form-block">
                <br class="spacer"/>
                <label>Payment reference<span class="red"></span></label>

                <div class="txtbox">
                    <span></span><span></span>
                    <input id="payment_ref" name="payment_ref" type="text" title="Invoice No."/>
                </div>
                <label>Comments</label>

                <div class="txtarea">
                    <span></span><span></span>
                    <textarea id="comments" name="comments" title="Enter any comments to help us process your payment" class="width-225"></textarea>
                </div>
                <br class="spacer"/>
            </section>
        </div>
        <div id="accordion">
            <div class="checkout_form_holder_second" id="realex">
                <section class="form-block" id="right-block">
                    <br class="spacer"/>
                    <label>Payment total (in Euros)<span class="red"> *</span></label>

                    <div class="txtbox">
                        <span></span><span></span>
                        <input id="payment_total" name="payment_total" type="text" title="Payment total" class="width-225" data-validation-engine="validate[required,custom[number],min[20]]" data-errormessage-custom-error="Please enter a numeric value"/>
                    </div>
                </section>
            </div>
        </div>

        <div style="float:left;height:40px;">
            <div class="checkout_page_form_header"><h1>Your Payment Details</h1></div>
            <div class="checkout_page_form_header"><h1>Credit Card Payment</h1></div>
            <input type="hidden" value="payment-thanks.html" name="thanks_page" id="thanks_page">
        </div>

        <div class="checkout_form_holder_first">
            <section class="form-block">
                <div class="txtbox">
                    <span></span><span></span>
                    <input id="name" name="name" type="text" title="Full name" placeholder="Full name" class="validate[required] width-225" value=""/>
                </div>
                <div class="txtbox">
                    <span></span><span></span>
                    <input id="phone" name="phone" type="text" title="Phone number" placeholder="Phone number" class="width-225"/>
                </div>
                <div class="txtbox">
                    <span></span><span></span>
                    <input id="email" name="email" type="text" title="Email address" placeholder="Email address" data-validation-engine="validate[required,custom[email]]" data-errormessage-value-missing="Email is required!" data-errormessage-custom-error="Let me give you a hint: someone@somewhere.com" data-errormessage="This is the fall-back error message."/>
                </div>
                <img src="<?= URL::site() ?>assets/default/images/secure-payment-2.png" alt="visa"/>
                <script>
                    var RecaptchaOptions = {
                        theme: 'clean'
                    };
                </script>
                <?php
                $captcha_enabled = Settings::instance()->get('captcha_enabled');
                if ($captcha_enabled) {
					require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                    $captcha_public_key = Settings::instance()->get('captcha_public_key');
                    echo recaptcha_get_html($captcha_public_key);
                }
                ?>
                <div id="checkboxes">
                    <p>I have read and agree to the
                        <a href="terms-and-conditions.html">terms and conditions</a><input type="checkbox" id="terms" name="terms" value="1" data-validation-engine="validate[required]">
                    </p>
                    <p>I would like to sign up to the newsletter<input type="checkbox" id="signupCheckbox" name="signupCheckbox" value="1">
                    </p>
                </div>
                <br class="spacer"/>

                <div class="submit_checkout_button">
                    <input type="button" id="submit-payment" name="submit-payment" class="left-padding-100" onclick="submitCheckout();return false;">
                </div>
        </div>

        <?php if ($realex === true): ?>
            <div id="accordion">
                <?php if ($realex === true): ?>
                <div class="checkout_form_holder_second" id="realex">

                    <section class="form-block" id="right-block">
                        <div>
                            <span></span><span></span>
                            <select name="ccType" id="ccType" data-validation-engine="validate[required]">
                                <option value="">Card Type*</option>
                                <option value="visa">Visa</option>
                                <option value="mc">Mastercard</option>
                                <option value="laser">Laser</option>
                            </select>
                        </div>

                        <div class="txtbox">
                            <span></span><span></span>
                            <input type="text" id="ccName" name="ccName" title="Name on Card" placeholder="Name on Card" data-validation-engine="validate[required,custom[onlyLetterSp]]" maxlength="50" class="width-225"/>
                        </div>

                        <div class="txtbox">
                            <span></span><span></span>
                            <input type="text" id="ccNum" name="ccNum" title="Card Number" placeholder="Card Number" data-validation-engine="validate[required,funcCall[luhnTest]]" maxlength="19" class="width-225"/>
                        </div>

                        <div class="txtbox">
                            <span></span><span></span>
                            <input type="text" id="ccv" name="ccv" title="CCV No" placeholder="CCV No" data-validation-engine="validate[required,custom[onlyNumberSp]]" maxlength="4" class="width-225"/>
                        </div>

                        <div id='expiry'>
                            <label>Expiry<span class="red"></span></label>
                            <select name="ccExpMM" id="ccExpMM" title="Card Expiry No" data-validation-engine="validate[required]">
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
                            <select name="ccExpYY" id="ccExpYY" data-validation-engine="validate[required]">
                                <option value="">yyyy</option>
                                <?php
                                for ($i = date('y'); $i <= (date('y') + 10); $i++) {
                                    $j = str_pad($i, 2, "0", STR_PAD_LEFT);
                                    echo "<option value='$j'>20$j</option>\n";
                                }
                                ?>
                            </select>
                        </div>

                        <?php endif; ?>

                    </section>

                </div>


                <?php if ($paypal === true): ?>
                    <h3>Paypal payment</h3>

                    <div>
                        <div id="paypalButton" class="payment_method_view">
                            <a href="" id="paypal-button"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;"></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div>
                <h3>No payments methods are enabled.</h3>

                <p>
                    Please contact site administrator.
                </p>
            </div>
        <?php
        endif;?>


    </form>

</div>



