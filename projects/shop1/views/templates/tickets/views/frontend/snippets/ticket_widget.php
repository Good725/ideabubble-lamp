<div class="widget widget--tickets widget--<?= $ticket_widget_display ?>">
	<form class="checkout_form" action="/checkout.html" method="post">
		<div class="widget-heading">
			<h3 class="widget-title"><?= __('Tickets') ?></h3>
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

        <?php if($event['status'] == Model_Event::EVENT_STATUS_SALE_ENDED): ?>
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
					   data-commission-type="<?=$commission['type']?>" data-commission-amount="<?=$commission['amount']?>" data-commission-currency="<?=$commission['currency']?>" data-fixed-charge-amount="<?=$commission['fixed_charge_amount']?>"
					   data-commission="<?=$commission['type'] == 'Fixed' ? ($commission['currency'] . $commission['amount']) : $commission['amount'] . '%'?> "
					   data-vat-rate="<?=Settings::instance()->get('vat_rate')?>" />
				<?php $itemIndex = 0; ?>

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
                            <?php
                            $date_onsale = $onsale && ($date['is_onsale'] == 1);

                            ?>
                            <span class="ticket_error" style="display:none;">No More Tickets Available For This Type </span>
                            <div class="row collapse ticket-container">
                                <div class="columns small-6 medium-6">
                                    <div class="ticket-date"><?= ($dtcount > 1 && $event['one_ticket_for_all_dates'] == 0 ? date('F j, g:ia', strtotime($date['starts'])) . ' ' : '') ?></div>
                                    <div class="ticket-title"><?= $ticket_type['name'] ?></div>
                                    <?php if ( ! empty($ticket_type['show_description']) AND ! empty($ticket_type['description'])): ?>
                                        <div class="ticket-description"><?= $ticket_type['description'] ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="columns small-4 medium-3 ">
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

                                    if ($tickets_available > 0) {
                                    ?>
                                    <input type="hidden" class="final_price"
                                           data-include-commission="<?= $ticket_type['include_commission'] ?>"
                                           data-price="<?= $ticket_type['price'] ?>"
                                           data-available="<?= $tickets_available ?>"
                                           data-free="<?= $ticket_type['type'] == 'Free' ? 1 : 0 ?>"
                                           data-donation="<?= $ticket_type['type'] == 'Donation' ? 1 : 0 ?>"
                                    />
                                    <span class="final_price_value" data-currency="<?= $ticket_type['currency'] ?>"><?php
                                        if ($ticket_type['type'] == 'Donation') {
                                            echo '&nbsp;';
                                        } else if ($ticket_type['type'] == 'Free') {
                                            echo 'Free';
                                        } else {
                                            if ($price_break_down['total'] == floor($price_break_down['total'])) {
                                                $price_decimals = 0;
                                            } else {
                                                $price_decimals = 2;
                                            }
                                            echo $currencies[$ticket_type['currency']]['symbol'] . number_format($price_break_down['total'], $price_decimals);
                                        }
                                        ?></span>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="columns small-2 medium-3 ticket-val">
                                    <input type="hidden" name="item[<?=$itemIndex?>][ticket_type_id]" value="<?=$ticket_type['id']?>" />

                                    <?php if ($ticket_type['is_sold_out'] || $tickets_available == 0): ?>

                                        <input type="hidden" name="item[<?=$itemIndex?>][quantity]" value="0" />
                                        <p class="text-uppercase"><?= __('Sold out') ?></p>

                                    <?php elseif ($sales_ended || ($date['ends'] && strtotime($date['ends']) < time()) || ($date_onsale == false)): ?>

                                        <input type="hidden" name="item[<?=$itemIndex?>][quantity]" value="0" />
                                        <p class="text-uppercase ticket-sales_ended"><?= __('Sales ended') ?></p>

                                    <?php else: ?>

                                        <input
                                            type="number"
                                            class="form_field validate[groupRequired[payments]]"
                                            name="item[<?=$itemIndex?>][quantity]"
                                            value="<?= $ticket_type['min_per_order']?>"
                                            max="<?= ($ticket_type['max_per_order']!='')?($ticket_type['max_per_order'] > $tickets_available)?$tickets_available:$ticket_type['max_per_order']:$tickets_available?>"
                                            min="<?= $ticket_type['min_per_order']?>" data-remaining="<?= $tickets_available ?>"
                                            size="2" <?=$onsale ? '' : 'disabled="disabled"'?>
                                            <?=$onsale ? '' : 'disabled="disabled"'?> />

                                    <?php endif; ?>

                                    <?php if ($event['one_ticket_for_all_dates'] == 0) { ?>
                                        <input type="hidden" name="item[<?=$itemIndex?>][dates][]" value="<?=$date['id']?>" />
                                    <?php } ?>
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
				<?php if($event['show_remaining_tickets'] == 1) : ?>
					<div class="row collapse">
						<div class="columns small-10 medium-9">Remaining Tickets </div>
						<div class="columns small-2 medium-3"><?= $total_tickets_available; ?></div>
					</div>
				<?php endif; ?>
			</div>

            <?php if(!$isAllSoldOut): ?>
                <div class="widget-footer text-center">
                    <?php if ($event['age_restriction'] > 0) { ?>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="age_confirmed" id="age_confirmed" value="<?=$event['age_restriction']?>" class="form-checkbox validate[required]" required />
                                <label for="age_confirmed"></label>
                                I am over <?= $event['age_restriction'] ?>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="form-group">
                            <label for="age_confirmed"><?=__('All ages event')?></label>
                        </div>
                    <?php } ?>
                    <?php if ($event['publish']) { ?>
                        <button type="submit" class="button secondary event-book" name="action" value="buy" <?=$book_enable ? '' : 'disabled="disabled"'?>><?= __('Book Now') ?></button>
                    <?php } else { ?>
                        <p><?=('Event is offline.')?></p>
                    <?php } ?>
                </div>
            <?php endif; ?>
		<?php endif; ?>
	</form>
</div>
