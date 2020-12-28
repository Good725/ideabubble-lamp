<?php
$settings = Settings::instance();
$realex = true;
?>

<div id="payment_form">
    <form id="payment">
        <ul>
            <li>
				<label for="payment_ref">Booking Reference Number:</label>
				<input type="text" name="payment_ref" id="payment_ref" class="validate[required]" placeholder="TO BE OBTAINED FROM OFFICE"/>
			</li>
            <li>
				<label for="payment_total">Total Payment:</label>
				<div class="kes-input-group">
					<label class="kes-input-group-addon" for="payment_total">&euro;</label>
					<input type="text" name="payment_total" id="payment_total" class="validate[required,custom[onlyNumber],min[25]]" value="<?=(isset($_GET['amount'])) ? $_GET['amount'] : '';?>" placeholder="Numbers only" />
				</div>
			</li>
        </ul>
        <fieldset>
            <legend>Course Details</legend>
            <ul>
                <li><label for="course_name">Course Title</label><input type="text" name="course_name" id="course_name" class="validate[required]" value="<?=(isset($_GET['title'])) ? $_GET['title'] : '';?>"/></li>
                <li>
                    <select name="location" id="location" class="validate[required]">
                        <option value="">Select Course Location</option>
                        <option value="Limerick">Limerick</option>
                        <option value="Ennis">Ennis</option>
                    </select>
                </li>
                <li><label for="student_name">Student Name</label><input type="text" name="student_name" id="student_name" placeholder="Student Name" class="validate[required]"/></li>
                <li><label for="comments">Comments/Additional Info</label><textarea name="comments" id="comments"></textarea></li>
            </ul>
        </fieldset>
        <div id="payment_details">
            <h3>Your Payment Details</h3>
            <ul>
                <li><label for="name">Full Name</label><input type="text" name="name" placeholder="Full Name" id="name" class="validate[required]"/></li>
                <li><label for="phone">Phone Number</label><input type="text" name="phone" placeholder="Phone Number" id="phone"/></li>
                <li><label for="email">Email Address</label><input type="text" name="email" placeholder="Email Address" id="email" class="validate[required,custom[email]]"/></li>
                <li><input type="checkbox" id="t_and_c_agreement" name="t_and_c_agreement" class="validate[required]" /><label for="t_and_c_agreement">I accept the <a href="/terms-and-conditions.html">term and conditions</a></label></li>
                <li><input type="checkbox" id="fee_agreement" name="fee_agreement" class="validate[required]" /><label for="fee_agreement">I accept that <span id='red_text'>fees are non transferable and non refundable</span></label></li>
            </ul>
        </div>
        <div id="credit_card_payment">
            <h3>Credit Card Payment</h3>
            <ul>
                <li>
                    <select name="ccType" id="ccType" class="validate[required]">
                        <option value="">Select a card type</option>
						<option value="VISA">VISA</option>
						<option value="VISA">VISA Debit</option>
                        <option value="MC">MasterCard</option>
                    </select>
                </li>
                <li>
					<label for="ccName">Name on Card</label>
					<input type="text" name="ccName" id="ccName" placeholder="Name on Card" class="validate[required,custom[ccholder]] cc-data" autocomplete="off" />
				</li>
                <li>
					<label for="ccNum">Card Number</label>
					<input type="text" name="ccNum" id="ccNum" placeholder="Card Number" class="validate[required,funcCall[luhnTest]] cc-data" autocomplete="off" />
				</li>
                <li>
					<label for="ccv">CCV Number</label>
					<input type="text" name="ccv" id="ccv" placeholder="Last 3 digits back of card" class="validate[required] cc-data" autocomplete="off" />
				</li>
                <li>
                    <label for="ccExpMM" class="expiry">Expiry</label>
                    <select name="ccExpMM" id="ccExpMM" class="validate[required,funcCall[checkCCDates]] cc-data" data-errormessage="Expiration date must not have passed">
                        <?php
                        $months = array('January','February','March','April','May','June','July','August','September','October','November','December');
                        for($i = 0;$i < 12;++$i)
                            {
                                echo '<option value="'.sprintf('%02d', ($i+1)).'">'.$months[$i].'</option>';
                            }
                        ?>
                    </select>
                    <select name="ccExpYY" id="ccExpYY" class="validate[required,funcCall[checkCCDates]] cc-data">
                        <?php
                            $year = date("Y",time());
                            $final_year = $year + 11;
                            for($year;$year < $final_year;$year++)
                            {
                                echo '<option value="'.($year - 2000).'">'.$year.'</option>';
                            }
                        ?>
                    </select>
                </li>
            </ul>
        </div>
        <script>
            var RecaptchaOptions = {
                theme: 'clean'
            };
        </script>
        <div style="float:left;width:100%;"></div>
        <?php
        $captcha_enabled = Settings::instance()->get('captcha_enabled');
        if ($captcha_enabled) {
			require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
            $captcha_public_key = Settings::instance()->get('captcha_public_key');
            echo recaptcha_get_html($captcha_public_key);
            echo recaptcha_get_html($captcha_public_key,NULL,TRUE);
        }
        ?>
        <div id="checkout_messages" class="hidden">
            <?php echo IbHelpers::get_messages(); //The message. example: "Error processing the payment ?>
        </div>
        <span id="error_message_area"></span>
        <ul>
            <li>
                <button class="btn-primary button blue course-enquire" id="pay_now_button" type="button">
                    <span><span>PAY NOW »</span></span>
                </button>
            </li>
        </ul>
    </form>
</div>



