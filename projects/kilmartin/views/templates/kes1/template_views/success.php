<?php
$success_page      = Model_Payments::get_thank_you_page(['full_url' => false]);
$is_success_page   = trim(trim(Request::detect_uri(), '.html'), '/') == trim(trim($success_page, '.html'), '/');
$last_order        = null;

if ($is_success_page) {
    $last_order_result = Session::instance()->get('last_order_result');
    $last_order_id     = isset($last_order_result['order_id']) ? $last_order_result['order_id'] : null;
    $last_order        = Model_Event::orderLoad($last_order_id);
    $user              = Auth::instance()->get_user();
}
?>

<?php if ($is_success_page && isset($last_order['id'])): ?>

    <?php
    $currencies      = Model_Currency::getCurrencies(true);
    $currency_symbol = isset($currencies[$last_order['currency']]) ? $currencies[$last_order['currency']]['symbol'] : $last_order['currency'];
    // If "intl" is enabled in the PHP settings, this code can be used instead to format currency amounts
    // $number_formatter = new NumberFormatter('en_us', NumberFormatter::CURRENCY);
    // $number_formatter->formatCurrency($item['total'], $last_order['currency'])
    ?>

    <div class="row">


        <hr />

        <?php if (@$_REQUEST['invite_friends'] == 1 && @$last_order['payments'][0]['paymentgw'] == 'Payment Plan') { ?>
        <form id="invite_payers_form">
            <input type="hidden" name="order_id" value="<?=$last_order['id']?>" />
        <div class="widget widget--checkout">
            <div class="widget-heading">
                <h3 class="widget-title"><?= __('Invite your friends to join you') ?></h3>
            </div>

            <div class="widget-body" style="font-size: 1rem;">
                <p style="font-size: 1.125em;"><?= __('You booked accommodation for $1 people. Would you like to send your friends an invite to join your adventure and pay for their share of the accommodation now? This can also be done later if you are not sure who is coming yet.', array(
                    '$1' => (@$last_order['items'][0]['sleep_capacity'] ?: 0)
                )) ?></p>

                <div id="invite_friend_list">
                    <div class="form-row gutters invite_friend">
                        <div class="col-sm-4">
                            <label for="invite-form-firstname"><?= __('First name') ?></label>
                            <?= Form::ib_input(null, 'payer[index][firstname]', null, array('placeholder' => __('First name'))) ?>
                        </div>

                        <div class="col-sm-4">
                            <label for="invite-form-lastname"><?= __('Last name') ?></label>
                            <?= Form::ib_input(null, 'payer[index][lastname]', null, array('placeholder' => __('Last name'))) ?>
                        </div>

                        <div class="col-sm-4">
                            <label for="invite-form-email"><?= __('Email address') ?></label>
                            <?= Form::ib_input(null, 'payer[index][email]', null, array('placeholder' => __('Email'))) ?>
                        </div>
                    </div>
                </div>

                <div class="form-row gutters">
                    <div class="col-sm-12">
                        <label for="invite-form-comment"><?= __('Add a comment') ?></label>
                        <?= Form::ib_textarea(null, 'comment', null, array('placeholder' => __('I just booked this accommodation for us!'), 'id' => 'invite-form-comment')) ?>
                    </div>
                </div>

                <div class="form-row gutters text-center">
                    <button type="button" class="button button--add_person" id="add_more">
                        <span class="flaticon-plus-circle"></span>&nbsp;
                        <?= __('Add another person') ?>
                    </button>

                    <button type="button" class="button text-uppercase" data-toggle="modal" data-target="#invite_payers-confirm" id="invite_payers-trigger">
                        <?= __('Invite friends') ?>
                    </button>
                </div>
            </div>
        </div>
        </form>

        <?php ob_start(); ?>
            <p><?= __('After you send this invitation, you will not be able to add more friends later. Are you happy with the list of friends you have provided? But you can still invite friends to pay their share by sending them the link you received in your confirmation email.') ?></p>
        <?php $modal_body = ob_get_clean(); ?>


        <?php ob_start(); ?>
            <button type="button" class="button button--continue cancel" id="invite_payers" data-dismiss="modal">
                <?= __('Invite') ?>
            </button>
            <button type="button" class="button button--continue inverse cancel" data-dismiss="modal"><?= __('Go back') ?></button>
        <?php $modal_footer = ob_get_clean(); ?>

        <?php
        echo View::factory('front_end/snippets/modal')
            ->set('id',         'invite_payers-confirm')
            ->set('width',      '680px')
            ->set('title',       __('Invite friends'))
            ->set('body_class', 'course-txt')
            ->set('body',       $modal_body)
            ->set('footer',     $modal_footer)
        ?>

        <script>
            var max_invite = <?=@$last_order['items'][0]['sleep_capacity'] ?: 0?>;
            var $multiple_buyer_template = $("#invite_friend_list .invite_friend");
            $multiple_buyer_template.remove();
            function add_multiple_buyer()
            {
                var $tr = $multiple_buyer_template.clone();

                $tr.removeClass("hidden");

                var index = $("#invite_friend_list > div").length;
                if (index >= max_invite) {
                    return;
                }
                $tr.find("input").each (function(){
                    this.name = this.name.replace("payer[index]", "payer[" + index + "]");
                });

                $("#invite_friend_list").append($tr);

                // After the maximum number of people have been invited, hide the button
                if (index + 1 == max_invite) {
                    $('#add_more').prop('disabled', true).addClass('hidden');
                }
            }
            add_multiple_buyer();
            $("#add_more").on("click", add_multiple_buyer);

            function invite_payers()
            {
                var data = $("#invite_payers_form").serialize();

                $.post (
                    '/frontend/events/invite_payers',
                    data,
                    function (response) {
                        $("#invite_payers, #invite_payers-trigger, #add_more").prop("disabled", true).addClass("hidden");
                    }
                );
            }

            $("#invite_payers").on("click", invite_payers);
        </script>
        <hr />
        <?php } ?>

        <h2 class="text-primary"><?= __('Your Booking Details') ?></h2>

        <table class="table table--checkout text-center">
            <thead>
                <tr>
                    <th scope="col" class="item text-left"><?= __('Item') ?></th>
                    <th scope="col" class="price"><?= __('Price') ?></th>
                    <th scope="col" class="total"><?= __('Total') ?></th>
                    <th scope="col" class="quantity"><?= __('Quantity') ?></th>
                </tr>
            </thead>

            <tbody id="event-checkout-items">
                <?php foreach ($last_order['items'] as $item): ?>

                    <tr>
                        <td class="text-left"><?= htmlentities($item['event']) ?> – <?= $item['name'] ?> - <?= date(' F j g:i a', strtotime($item['starts'])) ?></td>
                        <td><?= $currency_symbol.number_format(($last_order['items_total'] != $last_order['total'] ? $last_order['total'] : $item['total']), 2) ?></td>
                        <td><?= $currency_symbol.number_format(($last_order['items_total'] != $last_order['total'] ? $last_order['total'] : $item['total'] * $item['quantity']), 2) ?></td>
                        <td><strong><?= $item['quantity'] ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="checkout-prices row gutters text-right">
            <div class="col-sm-10">
                <strong>Total Received</strong>
            </div>

            <div class="col-sm-2">
                <strong><?= $currency_symbol.number_format($last_order['total_paid'], 2); ?></strong>
            </div>
        </div>

        <div class="row gutters">
            <div class="col-sm-6">
                <div class="widget widget--checkout">
                    <div class="widget-heading">
                        <h3 class="widget-title"><?= __('Billing information') ?></h3>
                    </div>

                    <div class="widget-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('Full Name') ?></dt>
                            <dd><?= htmlentities(trim($last_order['firstname'].' '.$last_order['lastname'])) ?></dd>

                            <dt><?= __('Address') ?></dt>
                            <dd><?= str_replace("\n", ', ', htmlentities($last_order['address_1'])) ?></dd>

                            <dt><?= __('Town/City') ?></dt>
                            <dd><?= htmlentities($last_order['city']) ?></dd>

                            <dt><?= __('State/County') ?></dt>
                            <dd><?= htmlentities($last_order['county']) ?></dd>

                            <dt><?= __('Country') ?></dt>
                            <dd><?= htmlentities($last_order['country']) ?></dd>

                            <dt><?= __('Postcode') ?></dt>
                            <dd><?= htmlentities($last_order['eircode']) ?></dd>

                            <dt><?= __('Tel/Mobile') ?></dt>
                            <dd><?= htmlentities($last_order['telephone']) ?></dd>

                            <dt><?= __('Email') ?></dt>
                            <dd><?= htmlentities($last_order['email']) ?></dd>

                            <dt><?= __('Comments') ?></dt>
                            <dd><?= htmlentities($last_order['comments']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="widget widget--checkout">
                    <div class="widget-heading">
                        <h3 class="widget-title"><?= __('Order details') ?></h3>
                    </div>

                    <div class="widget-body">
                        <?php foreach ($last_order['payments'] as $payment); ?>
                        <dl class="dl-horizontal">
                            <dt><?= __('Order Number') ?></dt>
                            <dd><?= $last_order_id ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="event-success-actions text-center">
            <a href="/" class="button text-uppercase"><?= __('Find more events') ?></a>
        </div>
    </div>
<?php endif; ?>