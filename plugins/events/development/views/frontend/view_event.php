<?php
if ($event == null) {
?>
    <p>No event found!</p>
<?php
} else {
    //echo '<pre>' . print_r($event, true) . '</pre>';
?>
<div>
    <div>
        <h1><?=$event['name']?></h1>
        <p><b>Description:</b> <?=$event['description']?></p>
        <p><b>Tags:</b><?php
            foreach ($event['tags'] as $i => $tag) {
                if ($i > 0 )echo ', ';
                echo $tag['tag'];
            }
        ?></p>
        <p>On Sale:<?=$event['is_onsale'] ? __('Yes') : __('No')?></p>
        <p>Dates:</p>
		<ul><?php
        foreach ($event['dates'] as $date) {
            ?>
            <li>Starts: <?=$date['starts']?><br />
                Ends: <?=$date['ends']?><br />
                <?php
				if (isset($data['others'])) {
					foreach ($date['others'] as $dtother) {
						echo $dtother['title'] . ': ' . $dtother['dt'] . '<br />';
					}
				}
                ?>
            </li>
            <?php
        }
        ?></ul>
    </div>

    <div>
        <h2>Venue</h2>
        <p>
            <b><?=$event['venue']['name']?></b>
            <address><?=
            $event['venue']['address_1'] . ' ' . $event['venue']['address_2'] . $event['venue']['address_3'] .
            $event['venue']['city'] . ' ' . $event['venue']['eircode'] . ' '
            ?></address>
        </p>
    </div>

    <div>
        <h2>Organizers</h2>
        <ul>
            <?php
            foreach ($event['organizers'] as $organizer) {
                ?>
                <li><b><?=$organizer['first_name'] . ' ' . $organizer['last_name']?></b>
                    <p><?=$organizer['description']?></p>
                    <p><?=$organizer['phone'] ? 'Phone:' . $organizer['phone'] : ''?></p>
                    <p><?=$organizer['mobile'] ? 'Mobile:' . $organizer['mobile'] : ''?></p>
                    <p><?=$organizer['email'] ? 'Email:' . $organizer['email'] : ''?></p>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>

    <div>
        <form action="/checkout.html" method="post">
            <input type="hidden" name="event_id" value="<?=$event['id']?>" />
            <h2>Ticket Types</h2>
            <ul id="tickets">
            <?php
            $now = time();
            $canBuy = false;
            foreach ($event['ticket_types'] as $ticketType) {
                $ttprice = $ticketType['price'];
                if ($commission['type'] == 'Fixed') {
                    $ttprice += $commission['amount'];
                } else {
                    $ttprice += round($ttprice * ($commission['amount'] / 100), 2);
                }
                if ($ticketType['visible'] == 1
                    &&
                    !(($ticketType['hide_after'] != null && time() > strtotime($ticketType['hide_after'])) ||
                        ($ticketType['hide_until'] != null && time() < strtotime($ticketType['hide_until'])))) {
            ?>
                <li><b><?=$ticketType['name']?></b>
                <?php if($ticketType['show_description'] == 1) { ?>
                    <p><?=$ticketType['description']?></p>
                <?php } ?>
                    Type:<?=$ticketType['type']?><br />
                    Price:<?=$ticketType['type'] == 'Paid' ? $account['commission_currency'] . number_format($ttprice, 2) : ($ticketType['type']) ?><br />
                    Sale Starts:<?=$ticketType['sale_starts']?><br />
                    Sale Ends:<?=$ticketType['sale_ends']?><br />
                    Min. Per Order:<?=$ticketType['min_per_order']?><br />
                    Max. Per Order:<?=$ticketType['max_per_order']?><br />
                    <?php if ($event['show_remaining_tickets']) { ?>
                        Available: <?=$ticketType['quantity'] - $ticketType['sold']?><br />
                    <?php } ?>
                    <?php
                    if (($ticketType['sale_starts'] == null || strtotime($ticketType['sale_starts']) < $now) &&
                        ($ticketType['sale_ends'] == null || strtotime($ticketType['sale_ends']) > $now)) {
                        $canBuy = true;
                    ?>
                        <input type="number" name="ticket_type[<?=$ticketType['id']?>]" value="<?=$ticketType['min_per_order']?>" max="<?=$ticketType['max_per_order']?>" min="<?=$ticketType['min_per_order']?>" size="2" />
                        <select name="ticket_date[<?=$ticketType['id']?>][]" <?=count($event['dates']) > 1? 'multiple="multiple"' : ''?>>
                            <?php foreach ($event['dates'] as $date) { ?>
                                <option value="<?=$date['id']?>"><?=$date['starts']?></option>
                            <?php } ?>
                        </select>
                    <?php
                    }
                    ?>

                </li>
            <?php
                }
            }
            if (!$event['is_onsale']) {
                $canBuy = false;
            }
            ?>
            </ul>
        <?php if ($canBuy) { ?>
            <button type="submit" name="action" value="buy">Buy Tickets</button>
        <?php } ?>
        </form>
    </div>

</div>
<?php

}
?>
