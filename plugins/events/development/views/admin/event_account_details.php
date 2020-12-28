		<section>
			<h2><?= __('Notifications') ?></h2>

			<div>
				<label>
					<input type="checkbox" name="notify_email_on_buy_ticket" id="notify_email_on_buy_ticket" value="1" <?=$account['notify_email_on_buy_ticket'] == 1 ? 'checked="checked"' : ''?> />
					<?=__('When someone buys a ticket, email me')?>
				</label>
			</div>
		</section>

		<section>
			<h2><?=__('Receive payments via')?></h2>
			<div>
				<div class="form-group">
					<div class="col-sm-12">
						<label class="sr-only" for="use_stripe_connect"><?=__('Receive payments via')?></label>
						<select class="form-control" name="use_stripe_connect" id="use_stripe_connect">
							<option value="1" <?=$account['use_stripe_connect'] == 1 ? 'selected="selected"' : ''?>><?=__('Stripe (as tickets are sold)')?></option>
							<option value="0" <?=$account['use_stripe_connect'] != 1 ? 'selected="selected"' : ''?>><?=__('Bank Transfer (1st working day after event)')?></option>
						</select>
					</div>
				</div>

				<div class="use-stripe">
					<?php if ( ! $account['stripe_auth']): ?>
						<p>
							<i><?=__('You currently do not have a connected stripe account.')?></i><br />
							<a class="btn" href="/admin/events/stripe_connect_start">Connect Stripe Account</a>
						</p>
					<?php else: ?>
						<p>
							<?=__('You have a connected stripe account.')?> <?=__('Your connected stripe user id:') . $account['stripe_auth']['stripe_user_id']?><br />
							<a class="btn" href="/admin/events/stripe_disconnect">Disconnect</a>
						</p>
					<?php endif; ?>
				</div>

				<div class="bank-details">
					<div class="form-group">
						<label class="sr-only bank" for="event-account-details-iban">IBAN</label>
						<div class="col-sm-12 bank">
							<input class="form-control" id="event-account-details-iban" type="text" name="iban" placeholder="IBAN" value="<?= $account['iban'] ?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="sr-only bank" for="event-account-details-bic">BIC</label>
						<div class="col-sm-12 bank">
							<input class="form-control" id="event-account-details-bic" type="text" name="bic" placeholder="BIC" value="<?= $account['bic'] ?>" />
						</div>
					</div>
				</div>

			</div>
		</section>

		<section>
			<h2><?=__('Checkout information')?></h2>
			<div>
                <div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-firstname">First Name</label>
                    <div class="col-sm-12">
                        <input class="form-control" class="event-account-checkout-details-firstname" type="text" name="checkout[firstname]" placeholder="First Name" value="<?= $checkoutDetails['firstname'] ?>" />
                    </div>
                </div>
				<div class="form-group">
					<label class="sr-only" for="event-account-checkout-details-lastname">Last Name</label>
					<div class="col-sm-12">
						<input class="form-control" class="event-account-checkout-details-lastname" type="text" name="checkout[lastname]" placeholder="Last Name" value="<?= $checkoutDetails['lastname'] ?>" />
					</div>
				</div>

				<div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-address">Address</label>
                    <div class="col-sm-12">
                        <textarea class="form-control" class="event-account-checkout-details-address" name="checkout[address]" rows="3" placeholder="Address"><?= $checkoutDetails['address'] ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-city">Town/​City</label>
                    <div class="col-sm-12">
                        <input class="form-control" class="event-account-checkout-details-city" type="text" name="checkout[city]" placeholder="Town/​City" value="<?= $checkoutDetails['city'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-county">State/County</label>
                    <div class="col-sm-12">
                        <input class="form-control" class="event-account-checkout-details-county" type="text" name="checkout[county]" placeholder="State/County" value="<?= $checkoutDetails['county'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-full_name">Country</label>
                    <div class="col-sm-12">
						<select class="form-control" name="checkout[country_id]">
							<?=html::optionsFromRows('id', 'name', Model_Event::getCountryMatrix(), $checkoutDetails['country_id'], array('value' => '', 'label' => 'Choose Country'))?>
						</select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-postcode">Postcode</label>
                    <div class="col-sm-12">
                        <input class="form-control" class="event-account-checkout-details-postcode" type="text" name="checkout[postcode]" placeholder="Postcode" value="<?= $checkoutDetails['postcode'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-telephone">Tel/Mobile</label>
                    <div class="col-sm-12">
                        <input class="form-control" class="event-account-checkout-details-telephone" type="text" name="checkout[telephone]" placeholder="Tel/Mobile" value="<?= $checkoutDetails['telephone'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-email">Email</label>
                    <div class="col-sm-12">
                        <input class="form-control" class="event-account-checkout-details-email" type="email" name="checkout[email]" placeholder="Email" value="<?= $checkoutDetails['email'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="event-account-checkout-details-full_name">Comments</label>
                    <div class="col-sm-12">
                        <textarea class="form-control" class="event-account-checkout-details-comments" name="checkout[comments]" rows="3" placeholder="Comments"><?= $checkoutDetails['comments'] ?></textarea>
                    </div>
                </div>
			</div>
		</section>

        <script>
        $("#use_stripe_connect").on("change", function(){
            if (this.value == 1) {
                $(".use-stripe").show();
                $(".bank-details").hide();
            } else {
                $(".use-stripe").hide();
                $(".bank-details").show();
            }
        });
        $("#use_stripe_connect").change();
        </script>

		<?php
		if (Request::$current->query('warn_banking')) {
		?>
		<div class="modal" id="banking-warn-modal" tabindex="-1" role="dialog" aria-labelledby=banking-warn-modal-label">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="banking-warn-modal-label"><?= __('Set your payment') ?></h4>
					</div>
					<div class="modal-body">
						<p><?= __('You need to setup how you want to be paid before creating an event.') ?></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" id="banking-warn-modal-btn" data-row_class="" data-dismiss="modal" ><?= __('Ok') ?></button>
					</div>
				</div>
			</div>
		</div>
			<script>
				$(document).ready(function(){
					$("#banking-warn-modal").modal();
					$("#banking-warn-modal button").on("click", function(){
						setTimeout(
							function() {
								$("#use_stripe_connect").focus();
							},
							100
						);
					});
				});
			</script>
		<?php
		}
		?>

