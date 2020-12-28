<?php
	/**
	 * Created by JetBrains PhpStorm.
	 * User: kosta
	 * Date: 09/11/2013
	 * Time: 10:13
	 * To change this template use File | Settings | File Templates.
	 *
	 * This is the DEFAULT Minicart View, provided by the Products Plugin.
	 * If Required to OVERWRITE it -=> COPY this FILE in your project/views/template/default directory and edit it as required
	 */

	$session = Session::instance();
	$cart = $session->get(Model_Checkout::CART_SESSION_ID);

	if (is_null($cart) OR !isset($cart->number_of_items) OR !isset($cart->cart_price))
	{
?>
		<span id="mini_cart"<?= (Settings::instance()->get('cart_hidden_when_empty') == 1) ? ' class="minicart-hidden-when-empty"' : ''; ?> data-product_count="0">
			<p>
				Items: <span class="value mycart_items_amount">0</span><br />
				Total: <span class="value" id="mycart_total_price">&euro; 0.00</span>
			</p>
		</span>
<?php
	}
	else
	{
?>
		<span id="mini_cart"<?= (Settings::instance()->get('cart_hidden_when_empty') == 1) ? ' class="minicart-hidden-when-empty"' : ''; ?> data-product_count="<?= $cart->number_of_items ?>">
			<p>
				Items: <span class="value mycart_items_amount"><?=$cart->number_of_items?></span><br />
				Total: <span class="value" id="mycart_total_price">&euro; <?=number_format($cart->cart_price, 2)?></span>
			</p>
			<p><a href="<?=URL::site().'checkout.html'?>">Checkout »</a></p>
		</span>
<?php
	}
?>

