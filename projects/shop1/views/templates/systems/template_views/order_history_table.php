<?php $user = Auth::instance()->get_user(); ?>
<?php if ($user['id'] != ''): ?>
	<table class="purchase-history-table">
		<caption>
			Your Order History &ndash; Account #<?= $user['id'].' '.$user['name'].' '.$user['surname'] ?>
		</caption>
		<thead>
			<tr>
				<th scope="col">Order ID</th>
				<th scope="col">Account Name</th>
				<th scope="col">Date Purchased</th>
				<th scope="col">Items</th>
				<!-- <th scope="col">Reorder</th> -->
			</tr>
		</thead>
		<tbody>
			<?php foreach ($order_history as $order): ?>
				<tr>
					<?php $cart = json_decode($order['cart_data'])->data; ?>
					<td><?= $order['id'] ?></td>
					<td><?= $user['email'] ?></td>
					<td><?= ($order['date_created'] != '0000-00-00 00:00:00') ? IbHelpers::relative_time_with_tooltip($order['date_created']) : '' ?></td>
					<td>
						<ul>
							<?php foreach ($cart->lines as $line): ?>
								<li><a href="/products.html/<?= $line->product->url_title ?>"><?= $line->product->title ?></a><?= ($line->quantity != 1) ? ' &times;'.$line->quantity : '' ?></li>
							<?php endforeach; ?>
						</ul>
					</td>
					<!-- <td><a href="#">Reorder</a></td> -->
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>