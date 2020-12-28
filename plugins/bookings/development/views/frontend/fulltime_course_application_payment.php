<?php

include Kohana::find_file('template_views', 'header');
?>

<script>

    window.application_payment = true;
	jQuery(document).ready(function () {
		// Needed for input info popups
		jQuery('.popinit').popover({ html : true ,trigger:'hover'});

		// init bootstrap-tooltip
		jQuery('a[rel="tooltip"]').tooltip();

		/* for tabs */
		jQuery( "#payment-tabs" ).tabs({active: 1});

        var checkout_posted = false;
		$('#application-complete_booking').on('click', function()
        {
            if (checkout_posted) {
                return false;
            }
            var $form = $('#booking-checkout-form');

            if ($form.validationEngine('validate'))
            {
                checkout_posted = true;
                $.ajax({
                    url    : '/frontend/bookings/application_payment',
                    method : 'post',
                    data   : $form.serialize()
                }).done(function(data)
                    {
                        if (data.payment.status == "success")
                        {
                            window.location = data.redirect;
                        }
                        else
                        {
                            checkout_posted = false;

                            if (typeof grecaptcha != 'undefined')
                            {
                                // Same CAPTCHA cannot be submitted twice
                                grecaptcha.reset();
                            }

                            if (data.payment.error)
                            {
                                var $clone = $('#checkout-error_message-template').clone();
                                $clone.removeClass('hidden').find('.checkout-error_message-text').html(data.payment.message);
                                $('#checkout-error_messages').append($clone)[0].scrollIntoView();
                            }
                        }
                    });
            } else {

            }
        });

        // Space out the credit card number as the user enters it
        $('#checkout-ccNum').on('keyup keypress change', function ()
        {
            var start   = this.selectionStart;
            var end     = this.selectionEnd;
            var oldleft = this.value.substr(0, start).replace(/[^ ]/g, '').length;

            $(this).val(function (index, value) {
                return value.replace(/\W/gi, '').replace(/(.{4})/g, '$1 ').substr(0, 19);
            });

            var newleft = this.value.substr(0, start).replace(/[^ ]/g, '').length;
            start += newleft - oldleft;
            end   += newleft - oldleft;

            this.setSelectionRange(start, end);
        });


	});

</script>

<div class="row theme-form" id="checkout-error_messages">
    <div class="alert alert-danger checkout-error_message hidden" id="checkout-error_message-template">
        <button type="button" class="close-btn button--plain">&times;</button>
        <div class="checkout-error_message-text"></div>
    </div>
</div>

<div class="row">
	<div class="left-section">
		<div class="checkout-progress">
			<ul>
				<li class="prev"><a href="/home.html"><p>Home</p>
					<span></span></a>
				</li>
				<li class="curr"><a><p>Payment</p>
					<span></span></a>
				</li>
				<li><a><p>Thank you</p>
					<span></span></a>
				</li>
			</ul>
            <span class="pro-box-checkout"></span>
            <span class="unvisited-box-checkout"></span>
		</div>
	</div>
</div>
<div class="row">
    <form class="clearfix" id="booking-checkout-form">
        <input type="hidden" name="booking_id" value="<?=$booking['booking_id']?>" />
        <input type="hidden" name="payment_method" value="<?=!empty($realex_enabled) || !empty($stripe_enabled) ? 'cc' : ($cash_payment_enabled ? 'cash'  : '')?>" />
        <div class="right-section" id="right-section">
			<div class="checkout-right-sect gray-box">
                <?php
                $course_amend_fee_percent = Settings::instance()->get('course_amend_fee_percent');
                ?>
				<?php include Kohana::find_file('template_views', 'sidebar_cart'); ?>

                <button type="button" class="button button--book<?= empty($count_seat_options) ? ' hidden' : '' ?>" data-toggle="slidein" data-target="#checkout-zone-selector">Select zones for your booking</button>

                <div class="total-pay">
                    <div class="check-wrap" style="<?=!$has_amendable ? 'display:none;' : ''?>">
                        <input id="checkout-amendable_tier" type="checkbox" name="amendable" value="1" <?=!$has_amendable ? 'disabled="disabled"' : '' ?>>
                        <label for="checkout-amendable_tier">Amendable tier <?=$course_amend_fee_percent?>% extra.<br/> Amend your booking up to 48 hours before the beginning of the course</label>
                    </div>
                </div>

                <script>
                $(document).on("ready", function(){
                    $("input[name=amendable]").on("change", function(){
                        if (this.checked) {
                            $("#checkout-breakdown-total").html($("#checkout-breakdown-total").data('amend-total'));
                            $("li.amend-fee").css("display", "block");
                        } else {
                            $("#checkout-breakdown-total").html($("#checkout-breakdown-total").data('total'));
                            $("li.amend-fee").css("display", "none");
                        }
                    })
                })
                </script>

                <div class="total-pay">
                    <?php $total = $sub_total - $discount + $zone_fee + $booking_fee; ?>

                    <ul id="checkout-breakdown">
                        <li<?= $discount ? '' : ' class="hidden"' ?>>
                            <?= __('Discount') ?>

                            <span class="right">
                                &minus;&euro;<span id="checkout-breakdown-discount" data-amount="-<?=$discount?>"><?= number_format($discount, 2) ?></span>
                            </span>
                        </li>

                        <li<?= $zone_fee ? '' : ' class="hidden"' ?>>
                            <?= __('Zone fee') ?>

                            <span class="right">
                                &euro;<span id="checkout-breakdown-zone_fee" data-amount="<?=$zone_fee?>"><?= number_format($zone_fee, 2) ?></span>
                            </span>
                        </li>

                        <?php
                        if ( ! empty($payg_bookings)) {
                            $payg_fee = (float)Settings::instance()->get('course_payg_booking_fee');
                        ?>
                        <li class="checkout-payg_fee-wrapper<?= $payg_fee == 0 ? ' hidden' : '' ?>">
                            <?= __('PAYG fee') ?>

                            <span class="right">
                            &euro;<span class="checkout-breakdown-booking_fee" data-amount="<?=$payg_fee?>"><?= number_format($payg_fee, 2) ?></span>
                        </span>
                        </li>
                        <?php
                        }
                        ?>

                        <?php
                        $cc_fee = (float)Settings::instance()->get('course_cc_booking_fee');
                        ?>
                        <li class="booking_fee cc <?=$payment_method != 'cc' ? 'hidden' : ''?>">
                            <?= __('Card fee') ?>

                            <span class="right">
                            &euro;<span class="checkout-breakdown-booking_fee" data-amount="<?=$cc_fee?>"><?= number_format($cc_fee, 2) ?></span>
                            </span>
                        </li>

                        <?php
                        $sms_fee = (float)Settings::instance()->get('course_sms_booking_fee');
                        ?>
                        <li  class="booking_fee sms <?=$payment_method != 'sms' ? 'hidden' : ''?>">
                            <?= __('SMS fee') ?>

                            <span class="right">
                        &euro;<span class="checkout-breakdown-booking_fee" data-amount="<?=$sms_fee?>"><?= number_format($sms_fee, 2) ?></span>
                        </span>
                        </li>
                        <li class="amend-fee" style="display: none" data-amend-fee="<?=$amend_fee?>" data-amount="<?=$amend_fee?>">
                            <?= __('Amend Fee') ?>

                            <span class="right">
                                &euro;<span id="checkout-breakdown-amend_fee"><?= number_format($amend_fee, 2) ?></span>
                            </span>
                        </li>


                        <li>
                            <?= __('Sub total') ?>

                            <span class="right">
                                &euro;<span id="checkout-breakdown-subtotal" data-amount="<?=$sub_total?>"><?= number_format($sub_total, 2) ?></span>
                            </span>
                        </li>


                        <li class="sub-total">
                            <?= __('Total') ?>

                            <?php
                            if (!isset($currency_symbol)) {
                                $currency_symbol = '&euro;';
                            }
                            ?>
                            <span class="right">
                                <span id="checkout-breakdown-total" data-total="<?= number_format($total, 2) ?>" data-amend-total="<?= number_format($total + $amend_fee, 2) ?>"><?= $currency_symbol ?><?= number_format($total, 2) ?></span>
                                <input type="hidden" name="amount" value="<?= $total ?>" id="checkout-breakdown-total-field" />
                            </span>
                        </li>

                        <li class="booking_fee deposit_due_now hidden">
                            <?= __('Total due now') ?>
                            <span class="right">
                            &euro;<span class="checkout-breakdown-booking_fee" data-amount=""></span>
                            </span>
                        </li>

                    </ul>
                </div>
                <div class="purchase-packages hidden"></div>

                <?php if (!empty($brochure) || !empty($payments_enabled)): ?>
                    <div class="terms-txt">
                        <p>By clicking ‘<?=$total > 0 ? 'Complete booking' : 'Apply'?>’ you agree to the Terms &amp; Conditions</p>

                        <div class="check-wrap">
                            <input class="validate[required]" id="checkout-terms_and_conditions" name="terms_and_conditions" type="checkbox" />
                            <label for="checkout-terms_and_conditions">I understand that fees are non-refundable and non-transferable</label>
                        </div>
                    </div>

                    <div class="button-action">
                        <button type="button" class="button button--continue btn-primary" id="application-complete_booking"><?=$total > 0 ? 'Complete booking' : 'Apply'?> </button>
                    </div>
                <?php endif; ?>
			</div>
		</div>

		<div class="left-section contact--left">
            <?php if (!empty($brochure) || (!empty($payments_enabled))): ?>
                <?php
                include Kohana::find_file('template_views', 'checkout_contact_details');
                if (!empty($payments_enabled)) include Kohana::find_file('template_views', 'checkout_pay_with');
                ?>
            <?php else: ?>
                <p><?= __('No payment providers have currently been set up.') ?></p>
                <p><?= __('Please $1 for more information', array('$1' => '<a href="/contact-us.html" target="_blank">'.__('contact the administration').'</a>')) ?></p>
            <?php endif; ?>
		</div><?php // contact left side end ?>

        <div class="slidein" id="checkout-zone-selector">
            <div class="slidein-content">
                <div class="slidein-header">
                    <h2><?= __('Please select your zone') ?></h2>
                </div>

                <?php
                $prepay_bookings = isset($prepay_bookings) ? $prepay_bookings : array();
                $payg_bookings   = isset($payg_bookings)   ? $payg_bookings   : array();
                $all_bookings    = $prepay_bookings + $payg_bookings;
                ?>

                <div class="slidein-body">
                    <label class="select">
                        <select class="form-input" id="seating-selector-select_schedule">
                            <?php $i = 0; ?>
                            <?php foreach ($all_bookings as $schedule_id_and_time => $booking_events): ?>
                                <?php foreach ($booking_events as $booking): ?>
                                    <?php if (count($booking['zones']) > 0): ?>
                                        <option value="<?= $i ?>">
                                            <?= $booking['schedule']['name'] ?> -
                                            <?= $booking['schedule']['location'] ?> -
                                            <?= $booking['date_formatted'] ?> -
                                            <?= date('H:i', strtotime($booking['event']['datetime_start'])) ?>
                                        </option>

                                        <?php $i++; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <?php $i = 0; ?>

                    <?php foreach ($all_bookings as $schedule_id_and_time => $booking_events): ?>
                        <?php foreach ($booking_events as $event_id => $booking): ?>
                            <?php if (count($booking['zones']) > 0): ?>
                                <div class="seating-selector<?= ($i == 0) ? '' : ' hidden' ?>" data-booking="<?= $i ?>">
                                    <div class="row gutters">
                                        <div class="seating-selector-map">
                                            <div class="text-center">
                                                <p><span class="fa fa-user" style="font-size: 3em;"></span></p>

                                                <p><?= __('Teacher') ?></p>
                                            </div>

                                            <div class="seating-selector-map-body">
                                                <?php foreach ($booking['zones'] as $zone): ?>
                                                    <div class="seating-selector-row" data-row_id="<?= $zone['row_id'] ?>" data-zone_id="<?= $zone['zone_id'] ?>">
                                                        <label class="seating-selector-option">
                                                            <input
                                                                type="radio"
                                                                class="sr-only seating-selector-option-radio"
                                                                name="booking_items[<?= $booking['schedule_id'] ?>][<?= $event_id ?>][seat_row_id]"
                                                                value="<?=          $zone['row_id']             ?>"
                                                                data-row_id="<?=    $zone['row_id']             ?>"

                                                                <?php if (Auth::instance()->has_access('courses_bookings_see_seating_numbers')): ?>
                                                                    data-total="<?=     $zone['seats']['total']     ?>"
                                                                    data-booked="<?=    $zone['seats']['booked']    ?>"
                                                                    data-available="<?= $zone['seats']['available'] ?>"
                                                                <?php endif; ?>

                                                                data-currency="&euro;"
                                                                data-price="<?=     number_format($zone['price'], 2) ?>"
                                                                <?= $zone['seats']['available'] > 0 ? '' : 'disabled="disabled"' ?>
                                                                />

                                                            <span class="button button--book seating-selector-zone-button inverse">
                                                                <?= $zone['zone_name'] ?>
                                                                <?= $zone['seats']['available'] > 0 ? '' : '('.__('Full').')' ?>
                                                            </span>

                                                            <?php if ($zone['seats']['available'] > 0): ?>
                                                                <span class="seating-selector-option-hover">
                                                                    <?= __('$1 additional', array('$1' => '&euro;'.number_format($zone['price'], 2))) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="seating-selector-row">
                                                <label class="seating-selector-option">
                                                    <input
                                                        type="radio"
                                                        class="seating-selector-option-radio sr-only"
                                                        name="booking_items[<?= $booking['schedule_id'] ?>][<?= $event_id ?>][seat_row_id]"
                                                        value=""
                                                        />
                                                    <span class="seating-selector-checkbox-helper"></span>

                                                    <span class="seating-selector-option-name"><?= __('I don\'t mind where I sit') ?></span>
                                                </label>
                                            </div>

                                            <?php if ($count_seat_options > 1): ?>
                                                <div class="seating-selector-footer">
                                                    <button
                                                        type="button"
                                                        class="button button--send inverse seating-selector-prev"
                                                        <?= $i == 0 ? ' disabled="disabled"' : '' ?>>
                                                        <?= __('Previous') ?></button>
                                                    <button
                                                        type="button"
                                                        class="button button--send inverse seating-selector-next"
                                                        <?= $i == $count_seat_options - 1 ? ' disabled="disabled"' : '' ?>
                                                        ><?= __('Next') ?></button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php $i++; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>

                <div class="slidein-footer">
                    <button type="button" class="button button--book" data-dismiss="slidein"><?= __('Done') ?></button>
                </div>
            </div>
        </div>
	</form>

</div><?php // row end- ?>


<div class="ajax_loader hidden" id="checkout-ajax_loader"></div>

<!-- popup hover jquery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<!-- tab jquery -->
<script src="<?= URL::get_engine_assets_base() ?>js/jquery-ui.js"></script>


<?php include Kohana::find_file('views', 'footer'); ?>


