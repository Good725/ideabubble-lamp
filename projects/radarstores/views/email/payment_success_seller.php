<p>An order has been posted through the www.militaryandcamping.ie website</p>
<p>Reference Code: <?= $products->id ?></p>
<h2>Customer details:</h2>
<p>
    <b>Name:</b> <?= $checkout->ccName ?><br />
    <b>Phone:</b> <?= $checkout->phone ?><br />
    <b>Email:</b> <?= $checkout->email ?><br />
    <br />
    <?php if($checkout->shipping_same_address === "true"): ?>

        <b>Billing and shipping address 1:</b> <?=$checkout->shipping_address_1?><br />
        <b>Billing and shipping address 2:</b> <?=$checkout->shipping_address_2?><br />
        <b>Billing and shipping address 3:</b> <?=$checkout->shipping_address_3?><br />
        <b>Billing and shipping address 4:</b> <?=$checkout->shipping_address_4?><br />
        <b>Billing and shipping name:</b> <?=$checkout->shipping_name?><br />

        <?php else:?>

        <b>Billing address 1:</b> <?=$checkout->address_1?><br />
        <b>Billing address 2:</b> <?=$checkout->address_2?><br />
        <b>Billing address 3:</b> <?=$checkout->address_3?><br />
        <b>Billing address 4:</b> <?=$checkout->address_4?><br />
        <b>Billing name:</b> <?=$checkout->ccName?><br /><br />

        <b>Shipping address 1:</b> <?=$checkout->shipping_address_1?><br />
        <b>Shipping address 2:</b> <?=$checkout->shipping_address_2?><br />
        <b>Shipping address 3:</b> <?=$checkout->shipping_address_3?><br />
        <b>Shipping address 4:</b> <?=$checkout->shipping_address_4?><br />
        <b>Shipping name:</b> <?=$checkout->shipping_name?><br />

    <?php endif; ?>
</p>
<h2>Order details:</h2>
<p>
<?php foreach($products->lines as $line):?>
    ID:<?=$line->product->id?> (<?=$line->product->title?>
    <?php foreach($line->options as $option):?>
        [<?=$option->group?>:<?=$option->label?> <span class="option_extra_price">+ &euro;<?=$option->price?></span>]
    <?php endforeach;?>
    ) x<?=$line->quantity?> = &euro;<?=$line->price?><br />
<?php endforeach;?>
Shipping: &euro;<?=$products->shipping_price?><br />
TOTAL: <b>&euro;<?=$products->final_price?></b><br /><br />

Comments: <?=$checkout->comments?><br /><br />

</p>
<h5>This email was sent <?= date('F j,  Y,  g:i a') ?> from: www.militaryandcamping.ie</h5>
