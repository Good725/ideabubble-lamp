<?php $realex = true; ?>

<div id="payment_form">
    <form id="payment">
		<div class="form-group">
			<label class="form-label" for="payment_ref">Booking Reference Number</label>

			<div class="form-control">
				<input type="text" name="payment_ref" id="payment_ref" class="form-input validate[required]" placeholder="TO BE OBTAINED FROM OFFICE"/>
			</div>
		</div>

		<div class="form-group">
			<label class="form-label" for="payment_total">Total Payment</label>

			<div class="form-control">
				<div class="input_group">
					<div class="input_group-icon">&euro;</div>
					<input type="text" name="payment_total" id="payment_total" class="form-input validate[required,custom[onlyNumber],min[25]]" value="<?=(isset($_GET['amount'])) ? $_GET['amount'] : '';?>" placeholder="Numbers only" />
				</div>
			</div>
		</div>

        <div>
            <h2>Course Details</h2>

			<div class="form-group">
				<label class="form-label" for="course_name">Course Title</label>

				<div class="form-controls">
					<input type="text" name="course_name" id="course_name" class="form-input validate[required]" value="<?=(isset($_GET['title'])) ? $_GET['title'] : '';?>"/>
				</div>
			</div>

			<div class="form-group">
				<div class="form-controls">
					<label class="form-label" for="location">Location</label>

					<div class="select">
						<select name="location" id="location" class="form-input validate[required]">
							<option value="">Select Course Location</option>
							<option value="Limerick">Limerick</option>
							<option value="Ennis">Ennis</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="form-label" for="student_name">Student Name</label>
				<div class="form-controls">
					<input type="text" name="student_name" id="student_name" placeholder="Student Name" class="form-input validate[required]"/>
				</div>
			</div>

			<div class="form-group">
				<label class="form-label" for="comments">Comments/Additional Info</label>
				<div class="form-controls">
					<textarea name="comments" class="form-input" id="comments" rows="4"></textarea>
				</div>
			</div>
        </div>

        <div id="payment_details">
            <h2>Your Payment Details</h2>

			<div class="form-group">
				<label class="form-label" for="name">Full Name</label>
				<input type="text" name="name" placeholder="Full Name" id="name" class="form-input validate[required]"/>
			</div>

			<div class="form-group">
				<label class="form-label" class="form-label"for="phone">Phone Number</label>
				<input type="text" name="phone" placeholder="Phone Number" class="form-input" id="phone"/>
			</div>

			<div class="form-group">
				<label class="form-label" for="email">Email Address</label>
				<input type="text" name="email" placeholder="Email Address" id="email" class="form-input validate[required]"/>
			</div>

			<div class="form-group">
				<input type="checkbox" id="t_and_c_agreement" name="t_and_c_agreement" class="validate[required]" />
				<label class="form-label" for="t_and_c_agreement">I accept the <a href="/terms-and-conditions.html">term and conditions</a></label>
			</div>

			<div class="form-group">
				<input type="checkbox" id="fee_agreement" name="fee_agreement" class="validate[required]" />
				<label class="form-label" for="fee_agreement">I accept that <span id='red_text'>fees are non transferable and non refundable</span></label>
			</div>
        </div>

        <div id="credit_card_payment">
            <h2>Credit Card Payment</h2>

			<div class="form-group">
				<label class="form-label" for="ccType">Card Type</label>

				<div class="form-controls">
					<div class="select">
						<select name="ccType" id="ccType" class="form-input validate[required]">
							<option value="">Select a card type</option>
							<option value="VISA">VISA</option>
							<option value="VISA">VISA Debit</option>
							<option value="MC">MasterCard</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="form-label" for="ccName">Name on Card</label>
				<div class="form-controls">
					<div class="form-controls">
						<input type="text" name="ccName" id="ccName" placeholder="Name on Card" class="form-input validate[required,custom[ccholder]] cc-data" autocomplete="off" />
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="form-label" for="ccNum">Card Number</label>
					<input type="text" name="ccNum" id="ccNum" placeholder="Card Number" class="form-input validate[required,funcCall[luhnTest]] cc-data" autocomplete="off" />
				</div>
			</div>

			<div class="form-group">
				<label class="form-label" for="ccv">CCV Number</label>
				<div class="form-controls">
					<input type="text" name="ccv" id="ccv" placeholder="Last 3 digits back of card" class="form-input validate[required] cc-data" autocomplete="off" />
				</div>
			</div>

			<div class="form-group">
				<label class="form-label" for="ccExpMM" class="expiry">Expiry</label>
				<div class="form-controls">
					<div class="select" style="float: left;width: 25%;">
						<select name="ccExpMM" id="ccExpMM" class="form-input validate[required,funcCall[checkCCDates]] cc-data" data-errormessage="Expiration date must not have passed">
							<?php
							$months = array('January','February','March','April','May','June','July','August','September','October','November','December');
							for($i = 0;$i < 12;++$i)
							{
								$month = date_parse($months[$i]);
								echo '<option value="'.date("m",strtotime($months[$i])).'">'.$months[$i].'</option>';
							}
							?>
						</select>
					</div>
					<div class="select" style="float: left;width: 25%;">
						<select name="ccExpYY" id="ccExpYY" class="form-input validate[required,funcCall[checkCCDates]] cc-data">
							<?php
							$year = date("Y",time());
							$final_year = $year + 11;
							for($year;$year < $final_year;$year++)
							{
								echo '<option value="'.($year - 2000).'">'.$year.'</option>';
							}
							?>
						</select>
					</div>
				</div>
			</div>
        </div>
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
            echo recaptcha_get_html($captcha_public_key,NULL,TRUE);
        }
        ?>
        <div id="checkout_messages" class="hidden">
            <?php echo IbHelpers::get_messages(); //The message. example: "Error processing the payment ?>
        </div>
        <span id="error_message_area"></span>
		<div class="form-group" style="clear: both; padding-top: 1rem;">
			<button class="button button--pay course-enquire" id="pay_now_button" type="button">Pay Now</button>
		</div>
    </form>
</div>



