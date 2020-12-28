<?php

// This should be moved to appropriate controller/model files
$form_data = Kohana::sanitize($_GET);
$countries = Model_Event::getCountryMatrix();
$deposit_percent = Settings::instance()->get('courses_deposit_percent');
$user = Auth::instance()->get_user();
$linked_users = empty($user['id']) ? array() : Model_Contacts::search(array('user_id' => $user['id']));

$child = NULL;
$guardian = NULL;
foreach ($linked_users as $linked_user)
{
	if ($linked_user['mail_list'] != 'Parent/Guardian')
	{
		//  do not auto fill child for now
		//$child = $linked_user;
		//$child_data = new Model_Contacts($linked_user['id']);
	}
	if ($linked_user['email'] == $user['email'])
	{
		$guardian = $linked_user;
		$guardian_data = new Model_Contacts($linked_user['id']);
	}
}
if (isset($guardian_data) AND $guardian_data->get_communications())
{
	foreach ($guardian_data->get_communications() as $communication)
	{
		switch ($communication['type'])
		{
			case 'Mobile':          $guardian_mobile          = $communication['value']; break;
			case 'Emergency Phone': $guardian_emergency_phone = $communication['value']; break;
		}
	}
}

if (isset($child_data) AND $child_data->get_preferences())
{
	foreach ($child_data->get_preferences() as $preference)
	{
		switch ($preference['type'])
		{
			case 'Photo/Video Permission': $photo_consent       = $preference['value']; break;
			case 'Medical information':    $medical_information = $preference['value']; break;
		}
	}
}

?>
<?php if ( ! (empty($_GET['event_id']))): ?>
	<?php
	$schedule = Model_Schedules::get_one_for_details($form_data['schedule_id']);
	$existing_bookings_params = array(
		'schedule_id' => $schedule['id'],
		'status' => array('Processing', 'Confirmed', 'Pending')
	);
	$capacity = is_numeric($schedule['max_capacity']) ? $schedule['max_capacity'] : null;
	if ($form_data['event_id'] != 'all') {
        $event = Model_Schedules::get_event_details($form_data['event_id']);
		$existing_bookings_params['timeslot_id'] = $form_data['event_id'];
    } else {
        $event = null;
    }
	if ($event) {
		if ($event['fee_amount'] == null) {
			$event['fee_amount'] = $schedule['fee_amount'];
		}
	}
    $book_item = $form_data['event_id'] == 'all' ? $schedule : $event;
	$available = true;
	if ($capacity) {
		$existing_bookings = Model_Coursebookings::search($existing_bookings_params);
		if (count($existing_bookings) >= $capacity) {
			$available = false;
		}
	}


    if (!$schedule) {
        ob_end_clean();
        Request::$current->redirect('/course-list.html');
    }

	$available_discounts = Model_Coursebookings::get_available_discounts(
		@$guardian_data ? $guardian_data->get_id() : 0,
		array(
			array(
				'name' => $book_item['course'],
				'fee' => (float)$book_item['fee_amount'],
				'fee_per' => $book_item['fee_per'],
				'id' => $schedule['id'],
				'prepay' => true,
				'next_payment' => null
			)
		)
	);
	$discounts_amount = 0;
	if (isset($available_discounts[0]['discounts']))
	foreach ($available_discounts[0]['discounts'] as $available_discount) {
		$discounts_amount += $available_discount['amount'];
	}

    ?>

	<?php if ( ! empty($book_item)): ?>
		<section>
			<h2>Book now</h2>

			<table class="checkout-table">
				<thead>
					<tr>
						<th scope="col"><?= __('Title') ?></th>
						<th scope="col"><?= __('Unit&nbsp;Price') ?></th>
						<th scope="col"><?= __('Qty') ?></th>
						<th scope="col"><?= __('Total') ?></th>
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<a class="checkout-course_name" href="/course-detail?id=<?= $book_item['course_id'] ?>">
								<?= $book_item['course'] ?>
							</a>
							<div>
								<?= $book_item['location'] ?> -
								<?= (date('H:i', strtotime($book_item['datetime_end'])) == '00:00' ? '' : date('H:i ', strtotime($book_item['datetime_start']))) . date('D j F Y', strtotime($book_item['datetime_start'])) ?>
							</div>
							<?php
							if(strtotime($book_item['datetime_start']) < time()) {
							?>
							<p>This course already commenced at <?= date('D j F Y', strtotime($book_item['datetime_start'])) ?><br />
								<?= $book_item['missed_classes'] ?> classes <?= ($book_item['missed_classes'] == 1) ? 'has' : 'have' ?> already taken place<br />
								<?= $book_item['remaining_classes'] ?> classes remaining</p>
							<?php
							}
							if (!$available) {
							?>
							<p style="color:#ff0000;"><b>Sold out!</b></p>
							<?php
							}
							?>
						</td>
						<td>&euro;<?= number_format($book_item['fee_amount'], 2) ?></td>
						<td>
							<div class="qty">
								<label>
									<input type="number" class="form-input qty-input" name="qty" value="<?=$available ? 1 : 0?>" id="checkout-form-qty" readonly="readonly" min="1" />
								</label>
                                <!-- <div class="qty-controls">
                                    <button type="button" class="qty-plus">+</button>
                                    <button type="button" class="qty-minus">&minus;</button>
                                </div> -->
							</div>
						</td>
						<td>&euro;<span id="checkout-item-total" data-base_price="<?= $book_item['fee_amount'] ?>" ><?= number_format($book_item['fee_amount'], 2) ?></span></td>
						<td>
							<button class="button-plain" title="<?= __('Remove') ?>">
								<span class="sr-only"><?= __('Remove') ?></span>
								<span class="cross"></span>
							</button>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2"></td>
						<th scope="row">Subtotal</th>
						<td colspan="2"><span class="checkout-subtotal" id="checkout-subtotal">&euro;<span><?= number_format($book_item['fee_amount'],2) ?></span></span></td>
					</tr>
					<?php if (isset($available_discounts[0]['discounts']) && count($available_discounts[0]['discounts']) > 0) { ?>
					<?php foreach ($available_discounts[0]['discounts'] as $available_discount) { ?>
					<tr class="discount-row">
						<td colspan="2"></td>
						<th scope="row"><?=$available_discount['title']?></th>
						<td colspan="2">-<span class="checkout-subtotal" id="checkout-subtotal">&euro;<span><?= number_format($available_discount['amount'], 2) ?></span></span></td>
					</tr>
					<?php } ?>
					<?php } ?>
					<tr class="total-row">
						<td colspan="2"></td>
						<th scope="row">Total</th>
						<td colspan="2"><span class="checkout-total" id="checkout-total">&euro;<span class="price"><?= number_format($book_item['fee_amount'] - $discounts_amount,2) ?></span></span></td>
					</tr>
				</tfoot>
			</table>
		</section>

		<section class="checkout-details <?=$available == false ? 'hidden' : ''?>">
			<h2>Check out</h2>

			<?php if (empty($logged_in_user['id'])): ?>
			<div class="checkout-user-options">
				<label class="checkout-user-option">
					<input type="radio" name="login_option" value="new" checked="checked" />
					<span><?= __('New user') ?></span>
				</label>
				<label class="checkout-user-option">
					<input type="radio" name="login_option" value="registered" />
					<span><?= __('Registered user') ?></span>
				</label>
			</div>
			<?php endif; ?>


			<form action="/frontend/users/login" class="checkout-form checkout-form--registered validate-on-submit hidden" id="checkout-form-registered" method="post">
				<input type="hidden" name="redirect" value="<?= URL::site(Request::detect_uri(), TRUE).URL::query(); ?>" />
				<div class="checkout-column">
					<div class="checkout-form-group">
						<label for="checkout-form-registered-email"><?= __('Email Address') ?></label>
						<input type="text" class="form-input validate[required,custom[email]]" name="email" id="checkout-form-registered-email"/>
					</div>

					<div class="checkout-form-group">
						<label for="checkout-form-registered-password"><?= __('Password') ?></label>
						<input type="password" class="form-input validate[required]" name="password" id="checkout-form-registered-password" />
					</div>

					<div class="checkout-form-group">
						<button type="submit" type="submit" class="checkout-login-button"><?= __('Log in to proceed') ?></button>
					</div>
				</div>
			</form>

            <form class="checkout-form checkout-form--new validate-on-submit" id="checkout-form-new" method="post" action="/frontend/courses/process_nbs_checkout">
                <input type="hidden" name="has_schedule[0][schedule_id]" value="<?=$schedule['id']?>" />
				<?php
				foreach ($available_discounts as $available_discount) {
					foreach ($available_discount['discounts'] as $di => $discount) {
				?>
					<input type="hidden" class="has-discount-input" name="has_discount[<?=$di?>][schedule_id]" value="<?=$available_discount['id']?>" />
					<input type="hidden" class="has-discount-input" name="has_discount[<?=$di?>][discount_id]" value="<?=$discount['id']?>" />
					<input type="hidden" class="has-discount-input" name="has_discount[<?=$di?>][amount]" value="<?=$discount['amount']?>" />
				<?php
					}
				}
				?>
                <?php if ($form_data['event_id'] != 'all') { ?>
                <input type="hidden" name="has_schedule[0][has_timeslots][0][timeslot_id]" value="<?=@$event['id']?>" />
                <?php } else { ?>
                <input type="hidden" name="has_schedule[0][has_timeslots]" value="all" />
                <?php } ?>

				<div class="checkout-column">
                    <fieldset>
                        <h3>Parent / Guardian Details</h3>
                        <div class="checkout-form-group">
                            <label for="checkout-form-title"><?= __('Title') ?></label>
                            <input type="text" class="form-input" name="title" id="checkout-form-title" value="<?= ( ! empty($guardian['title'])) ? $guardian['title'] : '' ?>" />
                        </div>

                        <div class="checkout-form-group">
                            <label for="checkout-form-first_name"><?= __('First Name') ?> *</label>
                            <input type="text" class="form-input validate[required]" name="first_name" id="checkout-form-first_name" value="<?= ( ! empty($guardian['first_name'])) ? $guardian['first_name'] : (( ! empty($logged_in_user['name']) ) ? $logged_in_user['name'] : '') ?>""/>
                        </div>

                        <div class="checkout-form-group">
                            <label for="checkout-form-last_name"><?= __('Last Name') ?> *</label>
                            <input type="text" class="form-input validate[required]" name="last_name" id="checkout-form-last_name" value="<?= ( ! empty($guardian['last_name'])) ? $guardian['last_name'] : (( ! empty($logged_in_user['surname']) ) ? $logged_in_user['surname'] : '') ?>"" />
                        </div>

                        <div class="checkout-form-group">
                            <label for="checkout-form-mobile"><?= __('Mobile') ?> *</label>
                            <input type="text" class="form-input validate[required]" name="mobile" id="checkout-form-mobile" value="<?= ( ! empty($guardian_mobile)) ? $guardian_mobile : (( ! empty($guardian['mobile'])) ? $guardian['mobile'] : '') ?>" />
                        </div>

						<div class="checkout-form-group">
							<label for="checkout-form-emergency_phone"><?= __('In case of emergency phone') ?></label>
							<input type="text" class="form-input" name="emergency_phone" id="checkout-form-emergency_phone" value="<?= ( ! empty($guardian_emergency_phone)) ? $guardian_emergency_phone : '' ?>" />
						</div>

                        <div class="checkout-form-group">
                            <label for="checkout-form-email"><?= __('Email Address') ?> *</label>
                            <input type="text" class="form-input validate[required,custom[email]]" name="email" id="checkout-form-email" value="<?= $logged_in_user['email'] ?>"  />
                        </div>

						<?php if (empty($logged_in_user)): ?>
							<div class="checkout-form-group">
								<label for="checkout-form-password"><?= __('Password') ?> *</label>
								<input type="password" class="form-input validate[required]" name="password" id="checkout-form-password" />
							</div>

							<div class="checkout-form-group">
								<label for="checkout-form-password2"><?= __('Confirm password') ?> *</label>
								<input type="password" class="form-input validate[required,equals[checkout-form-password]]" name="password2" id="checkout-form-password2" />
							</div>
						<?php endif; ?>

                    </fieldset>

                    <fieldset>
                        <h3>Student Details</h3>

                        <div class="checkout-form-group">
                            <label for="checkout-form-student_first_name"><?= __('Student First Name') ?> *</label>
                            <input type="text" class="form-input validate[required]" name="student_first_name" id="checkout-form-student_first_name" value="<?= ( ! empty($child['first_name'])) ? $child['first_name'] : '' ?>" />
                        </div>

                        <div class="checkout-form-group">
                            <label for="checkout-form-student_last_name"><?= __('Student Last Name') ?> *</label>
                            <input type="text" class="form-input validate[required]" name="student_last_name" id="checkout-form-student_last_name" value="<?= ( ! empty($child['last_name'])) ? $child['last_name'] : '' ?>" />
                        </div>

						<div class="checkout-form-group">
							<label for="checkout-form-student_dob"><?= __('Date of birth') ?> *</label>
							<!-- <input type="text" class="form-input datepicker-input" data-past_only="1" name="student_dob" id="checkout-form-student_dob" value="<?= ( ! empty($child['dob'])) ? $child['dob'] : '' ?>" /> -->
                            <div>
								<?php $dob = empty($child['dob']) ? array('', '', '') : explode('-', $child['dob']); ?>
								<select class="form-input validate[required]" id="student_dob_dd" name="student_dob_dd" style="width: 90px;  display: inline-block;">
									<option value="">DD</option>
									<?php for($i = 1; $i <= 31 ; ++$i) { ?>
										<?php $selected = ($i == $dob[2]) ? ' selected="selected"' : '' ?>
										<option value="<?= $i ?>"<?= $selected ?>><?= $i ?></option>
									<?php } ?>
								</select>
								<select class="form-input validate[required]" id="student_dob_mm" name="student_dob_mm" style="width: 90px;  display: inline-block;">
									<option value="">MM</option>
									<?php for($i = 1; $i <= 12 ; ++$i) { ?>
										<?php $selected = ($i == $dob[1]) ? ' selected="selected"' : '' ?>
										<option value="<?=$i?>"<?= $selected ?>><?=$i?></option>
									<?php } ?>
								</select>
								<select class="form-input validate[required]" id="student_dob_yy" name="student_dob_yy" style="width: 90px; display: inline-block;">
									<option value="">YYYY</option>
									<?php for($i = 1; $i <= 30 ; ++$i) { ?>
										<?php $selected = (date('Y') - $i == $dob[0]) ? ' selected="selected"' : '' ?>
										<option value="<?=date('Y') - $i?>"<?= $selected ?>><?=date('Y') - $i?></option>
									<?php } ?>
								</select>
                            </div>
						</div>


						<div class="checkout-form-group">
							<label for="checkout-form-medical_information"><?= __('Medical information') ?></label>
							<textarea class="form-input" name="preference[Medical Information]" id="checkout-form-medical_information" rows="4"><?= ( ! empty($medical_information)) ? $medical_information : '' ?></textarea>
						</div>

                    </fieldset>


                    <fieldset>
                        <h3>Address Information</h3>
                        <div class="checkout-form-group">
                            <label for="checkout-form-address"><?= __('Address') ?> *</label>
                            <input type="text" class="form-input validate[required]" name="address" id="checkout-form-address" value="<?= ( ! empty($guardian['address1'])) ? $guardian['address1'] : '' ?>" />
                        </div>

                        <div class="checkout-form-group">
                            <label for="checkout-form-city"><?= __('Town / City') ?></label>
                            <input type="text" class="form-input validate[required]" name="city" id="checkout-form-city" value="<?= ( ! empty($guardian['address3'])) ? $guardian['address3'] : '' ?>" />
                        </div>

                        <div class="checkout-form-group">
                            <label for="checkout-form-country"><?= __('Country') ?></label>
                            <div class="select">
                                <select class="form-input validate[required]" name="country" id="checkout-form-country">
                                    <option value="">Please select</option>
									<?php foreach ($countries as $country): ?>
										<?php $selected = (isset($guardian['address4']) AND ($country['id'] == $guardian['address4'])) ? ' selected="selected"' : '' ?>
										<option value="<?= $country['id'] ?>"<?= $selected ?>><?= $country['name'] ?></option>
									<?php endforeach; ?>
                                </select>
                            </div>
                        </div>

						<div class="checkout-form-group">
							<input type="hidden" name="state" value="">
							<label for="checkout-form-state"><?= __('State / County') ?></label>
							<select class="form-input validate[required]" id="checkout-form-state" data-default="<?= ! empty($guardian['address3']) ? $guardian['address3'] : '' ?>">
							</select>
						</div>

                    </fieldset>
				</div>

				<div class="checkout-column">

                    <fieldset>
                        <h3>Payment Options</h3>


                        <div class="checkout-form-group" style="height: 40px;padding-top: 5px;">
                            <label>
                                <input type="radio" name="amount_type" value="later" />
                                <?= __('Pay Later') ?>
                            </label>
                            <label>
                                <input type="radio" name="amount_type" value="deposit" />
                                <?= __('Pay Deposit Now') ?>
                            </label>
                            <label>
                                <input type="radio" name="amount_type" value="full" checked="checked" />
                                <?= __('Pay Full Amount') ?>
                            </label>
                        </div>

						<div id="checkout-amount-fields">
							<div class="checkout-form-group">
								<label for="checkout-form-amount--deposit"><?= __('Deposit Amount') ?> (&euro;)</label>
                                <input type="text" class="checkout-form-amount form-input" name="deposit" id="checkout-form-amount--deposit" value="<?= number_format(round($book_item['deposit'] ? $book_item['deposit'] : $book_item['fee_amount'] * ($deposit_percent / 100.0), 2), 2) ?>" readonly="readonly" disabled="disabled" />
							</div>

							<div class="checkout-form-group">
								<label for="checkout-form-amount--full"><?= __('Full Amount') ?> (&euro;)</label>
								<input type="text" class=" checkout-form-amount form-input" name="balance" id="checkout-form-amount--full" value="<?= number_format($book_item['fee_amount'] - $discounts_amount, 2) ?>" readonly="readonly"/>
							</div>
						</div>
                    </fieldset>

                    <fieldset>
                        <h3>Booking Information</h3>

                        <div class="checkout-form-group">
                            <label for="checkout-form-comments"><?= __('Comments') ?></label>
                            <textarea class="form-input" name="comments" id="checkout-form-comments" rows="8" style="height: 149px;"></textarea>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="payment_method-form payment_method-form--cc">
                            <h3 style="margin: 30px 0 5px;">Credit Card Payment Details</h3>

                            <div class="checkout-form-group">
                                <label for="checkout-form-ccType"><?= __('Card Type') ?></label>
                                <div class="select">
                                    <select class="form-input validate[required]" name="ccType" id="checkout-form-ccType">
                                        <option value="">Please select</option>
                                        <option value="visa">Visa</option>
                                        <option value="mc">MasterCard</option>
                                    </select>
                                </div>
                            </div>

                            <div class="checkout-form-group">
                                <label for="checkout-form-ccNum"><?= __('Card No.') ?></label>
                                <input type="text" class="form-input validate[required]" name="ccNum" id="checkout-form-ccNum" />
                            </div>

                            <div class="checkout-form-group">
                                <label for="checkout-form-ccv"><?= __('CCV No.') ?></label>
                                <input type="text" class="form-input validate[required]" name="ccv" id="checkout-form-ccv" />
                            </div>


                            <div class="checkout-form-group">
                                <div><?= __('Expiry') ?></div>
                                <div class="checkout-cc-expiration">
                                    <div class="select">
                                        <label class="sr-only" for="checkout-form-ccExpMM"><?= __('Expiration Month') ?></label>
                                        <select class="form-input validate[required]" name="ccExpMM" id="checkout-form-ccExpMM">
                                            <option value="">MM</option>
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                    <div class="select">
                                        <label class="sr-only" for="checkout-form-ccExpYY"><?= __('Expiration Year') ?></label>
                                        <select class="form-input validate[required]" name="ccExpYY" id="checkout-form-ccExpYY">
                                            <option value="">YYYY</option>
                                            <?php for ($i = 0; $i < 15; $i++): ?>
                                                <option value="<?= date('y') + $i ?>"><?= date('Y') + $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

							<div class="cc-assure">
								<div class="cc-icons">
									<img alt="cc-icon cc-icon--mc" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/mastercard.png" />
									<img alt="cc-icon cc-icon--visa" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/visa.png" />
									<img alt="cc-icon cc-icon--realex" src="<?= URL::get_engine_plugin_assets_base('products') ?>images/realex_icon.png" />
								</div>

								<p>We use a secure certificate for all our payments and Realex, our payment partner provide all secure connections for your transactions.</p>
							</div>
                        </div>

                        <div>
							<div class="checkout-form-group">
								<label>
									<input type="checkbox" name="preference[Photo/Video Permission]" <?= ! (empty($photo_consent)) ? ' checked="checked"' : '' ?> value="1" />
									I grant <a href="/photo-permission.html" target="_blank">photo usage permission</a>.
								</label>
							</div>

                            <div class="checkout-form-group">
                                <label>
                                    <input type="checkbox" name="newsletter" value="1" />
                                    I would like to sign up to the newsletter
                                </label>
                            </div>

                            <div class="checkout-form-group">
                                <label>
                                    <input type="checkbox" name="terms" class="validate[required]" id="checkout-form-terms" />
                                    I accept the <a href="/terms-and-conditions.html">Terms and Conditions</a>
                                </label>
                            </div>

                            <div class="checkout-form-group">
                                <button type="submit" class="checkout-pay_now-button" <?=$available == false ? 'disabled="disabled"' : ''?>><?= __('Book Now') ?></button>
                            </div>
                        </div>
                    </fieldset>

				</div>
			</form>

            <form id="paypal-continue" action="https://www.<?= Settings::instance()->get('paypal_test_mode') == 1 ? 'sandbox.' : '' ?>paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="business" value="<?=Settings::instance()->get('paypal_email')?>" />
                <input type="hidden" name="cmd" value="_xclick" />
                <input type="hidden" name="upload" value="1" />
                <input type="hidden" name="currency_code" value="EUR" />
                <input type="hidden" name="no_shipping" value="1" />
                <input type="hidden" name="return" value="<?=URL::site('/booking-confirmed.html')?>" />
                <input type="hidden" name="cancel_return" value="<?=URL::site('/')?>" />
                <input type="hidden" name="notify_url" value="<?=URL::site('/frontend/courses/paypal_callback_nbs')?>" />

                <input type="hidden" name="item_name" value="" />
                <input type="hidden" name="amount" value="" />
                <input type="hidden" name="quantity" value="" />
                <input type="hidden" name="custom" value="" />

            </form>
		</section>

		<div class="modal-wrapper" id="checkout-error-modal" style="display: none;">
			<div class="modal fade">
				<div class="modal-header">
					<button type="button" class="modal-close">&times;</button>
					<h3 class="modal-title">Checkout error</h3>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button type="button" class="booking_button modal-close">Ok</button>
				</div>
			</div>
		</div>

		<script>
            window.countries = <?= json_encode($countries) ?>;
            window.counties = <?=json_encode(Model_Propman::counties('all'))?>;

			$(document).ready(function()
			{
                $("#checkout-form-country").on("change", function(){
                    var countryId = this.value;
                    $("#checkout-form-state option").remove();
                    if (window.countries["id_" + countryId])
					{
						defaultCounty  = $("#checkout-form-state").data('default');
						$("#checkout-form-state").append('<option value="">Please select</option>');
						for (var i in window.countries["id_" + countryId].counties) {
							defaultSelected = window.countries["id_" + countryId].counties[i].name == defaultCounty;
							option = new Option(window.countries["id_" + countryId].counties[i].name, window.countries["id_" + countryId].counties[i].id, defaultSelected, defaultSelected);
							$("#checkout-form-state").append(option);
						}
					}

                    if (window.countries["id_" + countryId] && window.countries["id_" + countryId].counties.length || $('#checkout-form-country').val() == '')
                    {
                        $("#checkout-form-county").show();
                    }
                    else
                    {
                        $("#checkout-form-county").hide();
                    }
                });
                $("#checkout-form-country").change();

				$('.qty-plus').on('click', function()
				{
					var $input = $(this).parents('.qty').find('.qty-input');
					$input.val(parseInt($input.val()) + 1).trigger('change');
				});

				$('.qty-minus').on('click', function()
				{
					var $input = $(this).parents('.qty').find('.qty-input');
					$input.val(parseInt($input.val()) - 1).trigger('change');
				});

				$('[name="login_option"]').on('change', function()
				{
					var choice = $('[name="login_option"]:checked').val();
					$('.checkout-form').addClass('hidden');
					$('.checkout-form--'+choice).removeClass('hidden');
				});


				$('[name="payment_method"]').on('change', function()
				{
					var choice = $('[name="payment_method"]:checked').val();
					$('.payment_method-form').addClass('hidden');
					$('.payment_method-form--'+choice).removeClass('hidden');

                    $('[name="payment_method"]').parent().removeClass("active");
                    $('[name="payment_method"]:checked').parent().addClass("active");
				});

				$('[name="amount_type"]').on('change', function()
				{
					var choice = $('[name="amount_type"]:checked').val();
                    if (choice == 'later') {
                        $(".payment_method-form ").hide();
						$('#checkout-amount-fields').hide();
                    } else {
                        $(".payment_method-form--cc").show();
						$('#checkout-amount-fields').show();
                    }

					$('.checkout-form-amount').prop('disabled', true);
					$('#checkout-form-amount--'+choice).prop('disabled', false);
				}).trigger('change');

				$('#checkout-form-qty').on('change', function()
				{
					var $total_field = $('#checkout-item-total');
					var base_price = $total_field.attr('data-base_price');
					// multiply base price by quantity and format number as a price
					var total_price = (base_price * this.value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

					$total_field.html(total_price);
					$('#checkout-subtotal').find('span').html(total_price);
					$('#checkout-total').find('span').html(total_price);
				});

                $(".checkout-form--new").on(
                    "submit",
                    function (e) {
                        if (e.isDefaultPrevented()) {
                            return false;
                        }
                        $(".checkout-pay_now-button").html("<?=__('Please wait...')?>");
                        $(".checkout-pay_now-button").prop("disabled", true);

                        var country = window.countries['id_' + $("#checkout-form-country").val()];
                        $('input[name=country]').val(country.name);
                        $.each(country.counties, function(i, value) {
                            if ($("#checkout-form-state").val() == value.id) {
                                $('input[name=state]').val(value.name);
                            }
                        });
                        var data = $(this).serialize();

                        $.post(
                            "/frontend/courses/process_nbs_checkout",
                            data,
                            function (response) {
                                if (!response.success) {
									var $modal = $('#checkout-error-modal');
                                    $(".alert.process").removeClass("hidden");
                                    if (response.message) {
										$modal.find('.modal-body').html('<p>'+response.message+'</p>');
                                    } else {
										$modal.find('.modal-body').html('<p>Unable to continue checkout</p>');
                                    }
									$modal.show();
                                    $(".checkout-pay_now-button").prop("disabled", false);
                                    $(".checkout-pay_now-button").html("<?=__('Book Now')?>");
                                } else {
                                    if (response.continue == 'paypal') {
                                        var $paypal = $("#paypal-continue");
                                        $paypal.find("[name=item_name]").val(response.item_name);
                                        $paypal.find("[name=custom]").val(response.booking_id);
                                        $paypal.find("[name=quantity]").val(response.quantity);
                                        $paypal.find("[name=amount]").val(response.amount);
                                        $paypal.submit();
                                    } else {
                                        window.location = '/booking-confirmed.html';
                                    }
                                }
                            }
                        );

                        return false;
                    }
                );

				var booking_item = <?=json_encode(array(
				'name' => $book_item['course'],
				'fee' => (float)$book_item['fee_amount'],
				'fee_per' => $book_item['fee_per'],
				'id' => $schedule['id'],
				'prepay' => true,
				'next_payment' => null
			))?>;

				$("#checkout-form-student_first_name, #checkout-form-student_last_name").on('change', function(){
					$.post(
						'/frontend/courses/test_discounts_for_student',
						{
							first_name: $("#checkout-form-student_first_name").val(),
							last_name: $("#checkout-form-student_last_name").val(),
							item: booking_item
						},
						function (items) {
							$(".checkout-table tr.discount-row").remove();
							$(".has-discount-input").remove();
							$(".checkout-table tr.total-row span.price").html(booking_item.fee.toFixed(2));
							$("#checkout-form-amount--full").val(booking_item.fee);
							var di = 0;
							for (var i in items) {
								for (var j in items[i].discounts) {
									$(
										'<tr class="discount-row">' +
											'<td colspan="2"></td>' +
											'<th scope="row">' + items[i].discounts[j].title + '</th>' +
											'<td colspan="2">-<span class="checkout-subtotal" id="checkout-subtotal">&euro;<span>' + items[i].discounts[j].amount + '</span></span></td>' +
										'</tr>'
									).insertBefore(".checkout-table tr.total-row");

									$('.checkout-form--new').append(
										'<input type="hidden" class="has-discount-input" name="has_discount[' + di + '][schedule_id]" value="' + items[i].id + '" />' +
									 	'<input type="hidden" class="has-discount-input" name="has_discount[' + di + '][discount_id]" value="' + items[i].discounts[j].id + '" />' +
									 	'<input type="hidden" class="has-discount-input" name="has_discount[' + di + '][amount]" value="' + items[i].discounts[j].amount + '" />'
									);
									++di;
								}

								$(".checkout-table tr.total-row span.price").html(items[i].total);
								$("#checkout-form-amount--full").val(items[i].total);
							}
						}
					)
				});
			});
		</script>

	<?php endif; ?>
<?php endif; ?>
