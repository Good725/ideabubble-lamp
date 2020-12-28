<p>Dear <?= $checkout->ccName ?>,</p>
<?=$header;?>
<p>Your order has been successfully processed.</p>
<p>Reference Code: <?= $products->id ?></p>
<h2>Your Details</h2>
<p>
    Name: <?= $checkout->ccName ?><br />
    Phone: <?= $checkout->phone ?><br />
    Email: <?= $checkout->email ?><br />
</p>
<p>
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
        <?=$line->product->title?>
        <?php foreach($line->options as $option):?>
            [<?=$option->group?>:<?=$option->label?> <span class="option_extra_price">+ &euro;<?=$option->price?></span>]
        <?php endforeach;?>
        <?php if ( ! empty($line->product->product_code) AND ! empty($line->product->url_title)): ?>
            (<a href="<?= URL::base() ?>products.html/<?= $line->product->url_title ?>"><?= $line->product->product_code ?></a>)
        <?php endif; ?> &times;<?=$line->quantity?> = &euro;<?=$line->price?><br />
    <?php endforeach;?>
</p>
Shipping: &euro;<?=$products->shipping_price?><br />
TOTAL: <b>&euro;<?=$products->final_price?></b><br /><br />

Comments: <?=$checkout->comments?><br /><br />

<?=$footer;?>

<h5>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> webshop</h5>
