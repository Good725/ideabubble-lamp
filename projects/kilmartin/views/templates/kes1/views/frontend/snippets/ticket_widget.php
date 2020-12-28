<form action="/checkout.html" method="post" class="widget widget--mobile_menu widget--tickets widget--<?= $ticket_widget_display ?> checkout_form hidden--mobile">
    <div class="widget-heading ticket-widget-heading">
        <?php if (empty($event['from_price'])): ?>
            <h3 class="widget-title"><?= __('Tickets') ?></h3>
        <?php else: ?>
            <div class="row gutters hidden--mobile">
                <div class="col-sm-7 text-left">
                    from <strong><?= $event['from_price_currency'] ?><?= number_format($event['from_price'], 2) ?></strong>
                </div>

                <div class="col-sm-5 text-right">
                    <div><?= ($event['enable_multiple_payers']) ? __('per person') : __('per ticket') ?></div>
                </div>
            </div>

            <div class="row gutters hidden--tablet hidden--desktop">
                <div style="width: 100%">
                    <div class="col-xs-offset-2 col-xs-10 text-center">
                        <h3 class="widget-title"><?= __('Price breakdown') ?></h3>
                    </div>

                    <div class="col-xs-2 text-right">
                        <button type="button" class="button--plain" data-hide_toggle=".widget--tickets" data-hide_toggle-class="hidden--mobile">
                            <span class="flaticon-remove"></span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php
    $currencies = Model_Currency::getCurrencies(true);
    $onsale = false; $book_enable = false; $isAllSoldOut = false; $total_tickets_available = 0;
    foreach ($event['ticket_types'] as $ticket_type)
    {
        foreach ($ticket_type['dates_quantity_remaining'] as $dates_quantity_remaining) {
            $total_tickets_available += $dates_quantity_remaining['quantity'];
        }
    }
    foreach ($event['ticket_types_pending_checkout'] as $ticket_pending) {
        $total_tickets_available -= $ticket_pending['quantity'];
    }
    ?>

    <?php if ($event['status'] == Model_Event::EVENT_STATUS_SALE_ENDED): ?>
        <div class="widget-body text-center">
            <h3><?= __('Sales Ended') ?></h3>
        </div>
    <?php elseif ($total_tickets_available <= 0): ?>
        <div class="widget-body text-center">
            <h3><?= __('Sold Out') ?></h3>
        </div>
    <?php else: ?>
        <div class="widget-body">
            <input type="hidden" name="event_id" value="<?=$event['id']?>" />
            <span class="ticket_error qty_error" style="display:none;">Please select at least one ticket.</span>
            <input type="hidden" class="ticket-commission" readonly="readonly"
                   data-commission-type="<?=$commission['type']?>"
                   data-commission-amount="<?=$commission['amount']?>"
                   data-commission-currency="<?=$commission['currency']?>"
                   data-fixed-charge-amount="<?=$commission['fixed_charge_amount']?>"
                   data-commission="<?=$commission['type'] == 'Fixed' ? ($commission['currency'] . $commission['amount']) : $commission['amount'] . '%'?>"
                   data-vat-rate="<?=Settings::instance()->get('vat_rate')?>" />
            <?php $itemIndex = 0; ?>



            <div class="ticket-widget-list">
                <?php foreach ($event['ticket_types'] as $ticket_type): ?>
                    <?php
                    if ($ticket_type['archived'] == 1) {
                        continue;
                    }
                    ?>

                    <?php if($ticket_type['quantity'] == 0): ?>
                        <div class="text-center">
                            <h3><?= __('Sold Out') ?></h3>
                        </div>
                        <?php
                        $isAllSoldOut = true;
                        continue;
                        ?>
                    <?php endif; ?>

                    <?php
                    $sales_started = true;
                    $sales_ended = false;
                    //do not display if hidden
                    if ($ticket_type['hide_until'] != null && strtotime($ticket_type['hide_until']) < time()) {
                        continue;
                    }
                    if ($ticket_type['hide_after'] != null && strtotime($ticket_type['hide_after']) > time()) {
                        continue;
                    }

                    $onsale = true;
                    if ($ticket_type['sale_starts'] != null && strtotime($ticket_type['sale_starts']) > time()) {
                        $sales_started = false;
                        $onsale = false;
                    }
                    if ($ticket_type['sale_ends'] != null && strtotime($ticket_type['sale_ends']) < time()) {
                        $sales_ended = true;
                        $onsale = false;
                    }
                    if ($event['is_onsale'] != 1 OR $event['publish'] != 1) {
                        $onsale = false;
                    }
                    if ($onsale) {
                        $book_enable = true;
                    }
                    $dtcount = count($event['dates']);
                    ?>

                    <?php if ($sales_started && $event['is_onsale'] && $event['publish'] == 1): ?>
                        <?php foreach ($event['dates'] as $date) { ?>
                            <span class="ticket_error" style="display:none;"><?= __('No more tickets available for this type.') ?></span>
                            <div class="row no-gutters ticket-container">
                                <div class="col-xs-8">
                                    <div class="ticket-date"><?= ($dtcount > 1 && $event['one_ticket_for_all_dates'] == 0 ? date('F j, g:ia', strtotime($date['starts'])) . ' ' : '') ?></div>

                                    <div class="ticket-title" style="display: block;"><?= $ticket_type['name'] ?></div>

                                    <?php if ($ticket_type['sleep_capacity'] || !empty($ticket_type['show_description'])): ?>
                                        <div class="ticket-description" style="display: block;">
                                            <?php
                                            if ($ticket_type['show_description'] && trim($ticket_type['description'])) {
                                                echo '<div>'.$ticket_type['description'].'</div>';
                                            }

                                            if ($ticket_type['sleep_capacity'] == 1) {
                                                echo __('Max 1 person');
                                            } elseif ($ticket_type['sleep_capacity']) {
                                                echo __('Max $1 people', array('$1' => $ticket_type['sleep_capacity']));
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-xs-4">
                                    <?php
                                    $tickets_available = $ticket_type['dates_quantity_remaining'][$date['id']]['quantity'];

                                    foreach ($event['ticket_types_pending_checkout'] as $ticket_pending) {
                                        if($ticket_pending['ticket_type_id'] == $ticket_type['id'] && $ticket_pending['date_id'] == $date['id']) {
                                            $tickets_available -= $ticket_pending['quantity'];
                                        }
                                    }

                                    $price_break_down = Model_Event::calculate_price_breakdown(
                                        $ticket_type['price'],
                                        $commission['fixed_charge_amount'] + ($commission['type'] == 'Fixed' ? $commission['amount'] : 0),
                                        $commission['type'] == 'Fixed' ? 0 : $commission['amount'],
                                        Settings::instance()->get('vat_rate'),
                                        $ticket_type['include_commission']
                                    );
                                    ?>

                                    <?php if ($tickets_available > 0): ?>
                                        <input type="hidden" class="final_price"
                                               data-include-commission="<?= $ticket_type['include_commission'] ?>"
                                               data-price="<?= $ticket_type['price'] ?>"
                                               data-available="<?= $tickets_available ?>"
                                               data-free="<?= $ticket_type['type'] == 'Free' ? 1 : 0 ?>"
                                               data-donation="<?= $ticket_type['type'] == 'Donation' ? 1 : 0 ?>"
                                               data-haspaymentplan="<?=@$ticket_type['paymentplan'][0] ? "yes" : "no"?>"
                                            />
                                        <?php if ($ticket_type['type'] == 'Donation'): ?>
                                            <span class="final_price_value" data-currency="<?= $ticket_type['currency'] ?>">&nbsp;</span>
                                        <?php elseif ($ticket_type['type'] == 'Free'): ?>
                                            <span class="final_price_value" data-currency="<?= $ticket_type['currency'] ?>"><?= __('Free') ?></span>
                                        <?php else: ?>
                                            <?php if ($event['enable_multiple_payers'] && $ticket_type['sleep_capacity']): ?>
                                                <?php
                                                $pp_price           = $price_break_down['total'] / $ticket_type['sleep_capacity'];
                                                $pp_price_decimals  = ($pp_price == floor($pp_price)) ? 0 : 2;
                                                $pp_price_formatted = $currencies[$ticket_type['currency']]['symbol'] . number_format($pp_price, $pp_price_decimals);
                                                $price_decimals     = ($price_break_down['total'] == floor($price_break_down['total'])) ? 0 : 2;
                                                $price_formatted    = $currencies[$ticket_type['currency']]['symbol'] . number_format($price_break_down['total'], $price_decimals);
                                                ?>

                                                <strong class="final_price_value" data-currency="<?= $ticket_type['currency'] ?>"><?= __('$1 pp', array('$1' => $pp_price_formatted)) ?></strong><br />

                                                <small><?= __('$1 total', array('$1' => $price_formatted)) ?></small>
                                            <?php else: ?>
                                                <?php $price_decimals = ($price_break_down['total'] == floor($price_break_down['total'])) ? 0 : 2; ?>

                                                <span class="final_price_value" data-currency="<?= $ticket_type['currency'] ?>">
                                                    <?= $currencies[$ticket_type['currency']]['symbol'] . number_format($price_break_down['total'], $price_decimals); ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <div class="ticket-val">
                                        <input type="hidden" name="item[<?=$itemIndex?>][ticket_type_id]" value="<?=$ticket_type['id']?>" />

                                        <?php if ($tickets_available == 0): ?>

                                            <input type="hidden" name="item[<?=$itemIndex?>][quantity]" value="0" />
                                            <p><?= __('Sold out') ?></p>

                                        <?php elseif ($sales_ended || $date['ends'] && strtotime($date['ends']) < time()): ?>

                                            <input type="hidden" name="item[<?=$itemIndex?>][quantity]" value="0" />
                                            <p class="ticket-sales_ended"><?= __('Sales ended') ?></p>

                                        <?php elseif ($event['enable_multiple_payers'] && $ticket_type['sleep_capacity']): ?>

                                            <label class="checkbox-icon" data-haspaymentplan="<?=@$ticket_type['paymentplan'][0] ? "yes" : "no"?>">
                                                <input
                                                    type="checkbox"
                                                    name="item[<?= $itemIndex ?>][quantity]"
                                                    value="1"
                                                    class="form_field validate[groupRequired[payments]] event-multiple_payers-quantity"
                                                    <?= $onsale ? '' : ' disabled="disabled"' ?>
                                                    />

                                                <span class="checkbox-icon-unchecked button button--continue" style="font-size: 14px; padding: .25em;"><?= __('Add to cart') ?></span>
                                                <span class="checkbox-icon-checked button button--continue inverse" style="font-size: 14px; padding: .25em;"><?= __('Remove') ?></span>
                                            </label>

                                        <?php else: ?>

                                            <?php
                                            $max = ($ticket_type['max_per_order'] != '')
                                                ? ($ticket_type['max_per_order'] > $tickets_available)
                                                    ? $tickets_available
                                                    : $ticket_type['max_per_order']
                                                : $tickets_available;
                                            $min = $ticket_type['min_per_order'];
                                            ?>

                                            <select
                                                class="form-input form_field validate[groupRequired[payments]]"
                                                name="item[<?=$itemIndex?>][quantity]"
                                                <?= $onsale ? '' : 'disabled="disabled"' ?>
                                                >
                                                <?php for ($i = $min; $i <= $max; $i++): ?>
                                                    <option value="<?= $i?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>

                                        <?php endif; ?>

                                        <?php if ($event['one_ticket_for_all_dates'] == 0) { ?>
                                            <input type="hidden" name="item[<?=$itemIndex?>][dates][]" value="<?=$date['id']?>" />
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            ++$itemIndex;
                            if ($event['one_ticket_for_all_dates'] == 1) {
                                break;
                            }
                            ?>
                        <?php } ?>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($event['show_remaining_tickets'] == 1): ?>
                    <div class="row gutters">
                        <div class="col-xs-10 col-md-9"><?= __('Remaining Tickets') ?></div>
                        <div class="col-xs-2 col-md-3"><?= $total_tickets_available ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$isAllSoldOut): ?>
            <div class="widget-footer">
                <?php if ($event['age_restriction'] > 0): ?>
                    <div class="form-row">
                        <div class="col-xs-12">
                            <?php
                            echo Form::ib_checkbox(
                                __('I am over $1', array('$1' => $event['age_restriction'])),
                                'age_confirmed',
                                $event['age_restriction'],
                                false,
                                array('class' => 'validate[required]', 'id' => 'age_confirmed')
                            );
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="ticket-widget-bottom">
                    <div class="hidden--tablet hidden--desktop">
                        <p class="text-center" style="font-size: 1rem;">
                            <?php
                            $from_price_formatted = '<strong>'.$event['from_price_currency'].number_format($event['from_price'], 2).'</strong>';
                            if ($event['enable_multiple_payers']) {
                                echo  __('From $1 per person', array('$1' => $from_price_formatted));
                            } else {
                                echo  __('From $1 per ticket', array('$1' => $from_price_formatted));
                            }
                            ?>
                        </p>
                    </div>

                    <?php if ($event['publish']) { ?>
                        <button type="submit" class="button button--full event-book" name="action" value="buy"<?= $book_enable ? '' : ' disabled="disabled"'?>><?= __('Continue') ?></button>
                    <?php } else { ?>
                        <p><?=('Event is offline.')?></p>
                    <?php } ?>
                </div>


            </div>
        <?php endif; ?>
    <?php endif; ?>
</form>
<script>
$(document).on("ready", function(){
    function uncheck_other_tickets()
    {
        var ticket = this;
        $(".event-multiple_payers-quantity").off("change", uncheck_other_tickets);
        $(".event-multiple_payers-quantity").each(function(){
            if (this != ticket) {
                this.checked = false;
                $(this).parent().find(".checkbox-icon-unchecked");
                $(this).parent().find(".checkbox-icon-checked");
            } else {
            }
            $(".event-multiple_payers-quantity").on("change", uncheck_other_tickets);
        });
    }
    $(".event-multiple_payers-quantity").on("change", uncheck_other_tickets);
});
</script>