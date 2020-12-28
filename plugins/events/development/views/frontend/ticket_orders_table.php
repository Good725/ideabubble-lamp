<?php if (count($items) > 0): ?>
	<table style="border: 1px solid #eee;border-collapse: collapse;width: 100%;">
		<thead>
			<tr>
				<th style="border: 1px solid #eee; padding: .5em;" scope="col">Tickets</th>
				<th style="border: 1px solid #eee; padding: .5em;" scope="col">Qty</th>
				<th style="border: 1px solid #eee; padding: .5em;" scope="col">Price</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $ticket_count = 0;
            $total        = 0;
            ?>
			<?php foreach ($items as $item): ?>
                <?php
                $ticket_count += $item['quantity'];
                $item_price    = $show_net ? $item['price'] + $item['donation'] - $item['discount']: $item['total'];
                $total        += $item_price * $item['quantity'];
                ?>
				<tr>
					<td style="border: 1px solid #eee; padding: .5em;"><?= $item['name'] . ' - ' . $event->name ?></td>
					<td style="border: 1px solid #eee; padding: .5em;"><?= $item['quantity'] ?></td>
					<td style="border: 1px solid #eee; padding: .5em;"><?= $order['currency'] ?><?= number_format($item_price, 2) ?></td>
				</tr>
				<tr>
					<td colspan="3" style="border: 1px solid #eee; padding: .5em;">
						<?= $item['ticket_description'] ?>
					</td>
				</tr>
			<?php endforeach; ?>

			<tr>
				<td colspan="2" style="border: 1px solid #eee; padding: .5em; text-align: right;">
					Total paid:
				</td>
				<td style="border: 1px solid #eee; padding: .5em;">
                    <?= $order['currency'] ?><?= number_format($total, 2) ?>
				</td>
			</tr>
		</tbody>
	</table>
<?php endif; ?>