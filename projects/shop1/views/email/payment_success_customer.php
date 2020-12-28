<p><?=isset($post['name']) ? 'Dear '.$post['name'].',' : (isset($checkout->ccName) ? 'Dear '.$checkout->ccName.',' : '') ?></p>
<?= isset($header) ? $header : '' ;?>
<p>Your order has been successfully processed.</p>
<p>Reference Code: <?= isset($products->id) ? $products->id : (isset($checkout->payment_ref) ? $checkout->payment_ref : '') ?></p>
<h2>Your Details</h2>
<p>
	<?php if (isset($checkout->ccName) AND $checkout->ccName != ''): ?>
		Name:  <?= $checkout->ccName ?><br />
	<?php endif; ?>
	<?php if (isset($checkout->phone)): ?>
		Phone: <?= $checkout->phone ?><br />
	<?php endif; ?>
	<?php if (isset($checkout->email)): ?>
		Email: <?= $checkout->email  ?>
	<?php endif; ?>
</p>

<?php if ( ! empty($checkout->is_gift)): ?>
	<p><b>Gift:</b> <?= $checkout->is_gift ? 'Yes' : 'No' ?></p>
	<p><b>Gift card text:</b> <?= ! empty($checkout->gift_card_text) ? nl2br($checkout->gift_card_text) : '' ?></p>
<?php endif; ?>

<?php if (isset($checkout->delivery_time) AND $checkout->delivery_time != ''): ?>
	<p>Requested delivery time: <?= $checkout->delivery_time ?></p>
<?php endif; ?>

<p>
	<?php if (isset($checkout->shipping_same_address) AND $checkout->shipping_same_address === "true"): ?>

		<?php if (isset($checkout->shipping_address_1) ): ?>
			<b>Billing and shipping address 1:</b> <?= nl2br($checkout->shipping_address_1) ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_address_2)): ?>
			<b>Billing and shipping address 2:</b> <?= $checkout->shipping_address_2 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_address_3)): ?>
			<b>Billing and shipping address 3:</b> <?= $checkout->shipping_address_3 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_address_4)): ?>
			<b>Billing and shipping address 4:</b> <?= $checkout->shipping_address_4 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_postcode)): ?>
			<b>Billing and shipping postcode:</b> <?= $checkout->shipping_postcode ?><br />
		<?php endif; ?>
		<?php if ( ! empty($checkout->shipping_eircode)): ?>
			<b>Billing and shipping eircode:</b> <?= $checkout->shipping_eircode ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_phone)): ?>
			<b>Billing and shipping phone:</b> <?= $checkout->shipping_phone ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_email)): ?>
			<b>Billing and shipping email:</b> <?= $checkout->shipping_email ?><br />
		<?php endif; ?>

	<?php else:?>

		<?php if (isset($checkout->address_1)): ?>
			<b>Billing address 1:</b> <?= nl2br($checkout->address_1) ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->address_2)): ?>
			<b>Billing address 2:</b> <?= $checkout->address_2 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->address_3)): ?>
			<b>Billing address 3:</b> <?= $checkout->address_3 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->address_4)): ?>
			<b>Billing address 4:</b> <?= $checkout->address_4 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->postcode)): ?>
			<b>Postcode:</b> <?= $checkout->postcode ?><br />
		<?php endif; ?>
		<?php if ( ! empty($checkout->eircode)): ?>
			<b>Eircode:</b> <?= $checkout->eircode ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->ccName)): ?>
			<b>Billing name:</b> <?= $checkout->ccName ?><br />
		<?php endif; ?>

		<?php if (isset($checkout->shipping_address_1)): ?>
			<b>Shipping address 1:</b> <?= nl2br($checkout->shipping_address_1) ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_address_2)): ?>
			<b>Shipping address 2:</b> <?= $checkout->shipping_address_2 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_address_3)): ?>
			<b>Shipping address 3:</b> <?= $checkout->shipping_address_3 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_address_4)): ?>
			<b>Shipping address 4:</b> <?= $checkout->shipping_address_4 ?><br />
		<?php endif; ?>
		<?php if (isset($checkout->shipping_postcode)): ?>
			<b>Postcode:</b> <?= $checkout->shipping_postcode ?><br />
		<?php endif; ?>
		<?php if ( ! empty($checkout->shipping_eircode)): ?>
			<b>Eircode:</b> <?= $checkout->shipping_eircode ?><br />
		<?php endif; ?>
		<?php if ( ! empty($checkout->shipping_name) OR ! empty($checkout->shipping_surname)): ?>
			<b>Shipping name:</b>
			<?= isset($checkout->shipping_name)    ? $checkout->shipping_name    : '' ?>
			<?= isset($checkout->shipping_surname) ? $checkout->shipping_surname : '' ?>
			<br />
		<?php endif; ?>

	<?php endif; ?>
</p>

<?php if (isset($products)): ?>
	<h2>Order details:</h2>
	<?php if (isset($checkout->purchase_order_reference) AND $checkout->purchase_order_reference != ''): ?>
		<p>Purchase Order Reference: <?= $checkout->purchase_order_reference ?></p>
	<?php endif; ?>

	<p>
		<?php foreach($products->lines as $line):?>
			(<?= html::entities($line->product->title) ?>
			<?php foreach($line->options as $option):?>
				<span class="product_line_options">[<?=$option->group?>:<?=$option->label?> <span class="option_extra_price">+ &euro;<?=$option->price?></span>]</span>
			<?php endforeach;?>
			) x<?=$line->quantity?> = &euro;<?=$line->price?><br />
		<?php endforeach;?>
	</p>

    Shipping: &euro;<?= isset($products->shipping_price) ? number_format($products->shipping_price, 2) : '' ?><br />
    <?php if (isset($products->discounts) && $products->discounts != 0): ?>
        Discount: &minus;&euro;<?= number_format($products->discounts, 2) ?><br />
    <?php endif; ?>
    TOTAL: <b>&euro;<?= isset($products->final_price) ? number_format($products->final_price, 2)  : '' ?></b><br /><br />

<?php elseif (isset($checkout->payment_total)): ?>

	<p>TOTAL: <?= number_format($checkout->payment_total, 2) ?></p>
	<br />
<?php endif; ?>

Comments: <?=isset($checkout->comments) ? $checkout->comments : '' ?><br /><br />
<?php if (isset($checkout->message_for_the_card) ){ ?>
    Message for the card: <?= isset($checkout->message_for_the_card) ? html::entities($checkout->message_for_the_card) : '' ?><br />
<?php } ?>

<?php if (isset($checkout->delivery_method) AND $checkout->delivery_method != ''): ?>
	<p><strong>Payment method</strong>: <?= str_replace('_', ' ', $checkout->delivery_method) ?></p>

	<?php if ($checkout->delivery_method == 'reserve_and_collect'): ?>
		<p>A payment for this order is to made in store.</p>
	<?php endif; ?>
<?php endif; ?>


<?php if (isset($store) AND ! is_null($store)): ?>
	<h3>Store</h3>
	<p><strong><?= $store['title'] ?></strong></p>
	<p>Address:<br />
		<?= ($store['address_1'] != '') ? '<br />'.$store['address_1']    : '' ?>
		<?= ($store['address_2'] != '') ? '<br />'.$store['address_2']    : '' ?>
		<?= ($store['address_3'] != '') ? '<br />'.$store['address_3']    : '' ?>
		<?= ($store['phone']     != '') ? '<br />Phone: '.$store['phone'] : '' ?>
		<?= ($store['email']     != '') ? '<br />Email: '.$store['email'] : '' ?>
	</p>
<?php endif; ?>


<h3>Form data</h3>
<table style="text-align: left;font-size: 12px;">
	<thead>
		<tr>
			<th scope="col">Input</th>
			<th scope="col">Value</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($post as $key => $value): ?>
			<?php if (is_string($value)): ?>
				<tr>
					<th scope="row"><?= str_replace('_', ' ', ucfirst($key)) ?></th>
					<td><?= $value ?></td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	</tbody>
</table>

<?= isset($footer) ? $footer : '' ?>

<h5>This email was sent <?= date('F j,  Y,  g:i a') ?> from: <?= $_SERVER['HTTP_HOST'] ?> webshop</h5>
