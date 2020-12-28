<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=payments" method="post">
        <?= $section_switcher ?>

        <input type="hidden" name="contact_id" value="<?= $contact_id ?>" />

        <section>
            <p><?=__('Receive payments via')?></p>

            <div>
                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $options  = array('1' => __('Stripe (as tickets are sold)'), '0' => __('Bank Transfer (1st working day after event)'));
                        $selected = ($account['use_stripe_connect'] == 1) ? 1 : 0;
                        echo Form::ib_select(__('Receive payments via'), 'use_stripe_connect', $options, $selected, array('id' => 'use_stripe_connect'));
                        ?>
                    </div>
                </div>

                <div class="use-stripe">
                    <?php if ( ! $account['stripe_auth']): ?>
                        <p>
                            <i><?=__('You currently do not have a connected stripe account.')?></i><br />
                            <a class="btn" href="/admin/profile/stripe_connect_start">Connect Stripe Account</a>
                        </p>
                    <?php else: ?>
                        <p>
                            <?=__('You have a connected stripe account.')?> <?=__('Your connected stripe user id:') . $account['stripe_auth']['stripe_user_id']?><br />
                            <a class="btn" href="/admin/profile/stripe_disconnect">Disconnect</a>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="bank-details">
                    <div class="form-group">
                        <div class="col-sm-6 bank">
                            <?= Form::ib_input('IBAN', 'iban', $account['iban'], array('id' => 'event-account-details-iban')) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6 bank">
                            <?= Form::ib_input('BIC', 'bic', $account['bic'], array('id' => 'event-account-details-bic'))?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="form-action-group" id="ActionMenu">
                <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save" value="save"><?=__('Save')?></button>
                <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save_and_exit" value="save_and_exit"><?=__('Save & Exit')?></button>
                <button type="reset" class="btn btn-default"><?=__('Reset')?></button>
                <a href="/admin" class="btn btn-cancel">Cancel</a>
            </div>
        </section>
    </form>
</div>

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