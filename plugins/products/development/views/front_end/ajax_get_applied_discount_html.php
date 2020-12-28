<?php
	/* cart amount based discount */
	$hundered = 100;
	$cart_price = $cart_data->cart_price;
	echo '<table class="checkout-sud-table">';
	if(isset($cart_data->cart_based_price_discounts) && !empty($cart_data->cart_based_price_discounts) && sizeof($cart_data->cart_based_price_discounts) > 0)
	{
		foreach($cart_data->cart_based_price_discounts as $chk_cart_discount)
		{
			echo '<tr><td></td><td><span class="ajax_checkout_title">'.$chk_cart_discount['title'].'</span></td><td><span class="ajax_checkout_price">'.$chk_cart_discount['discount_rate_percentage'].'&euro;'.$cart_price*$chk_cart_discount['discount_rate_percentage']/$hundered.'</span><br /></td></tr>';
		}
	}
	/* cart amount based shipping discount */
	if(isset($cart_data->cart_based_free_shipping_discounts) && is_array($cart_data->cart_based_free_shipping_discounts) && sizeof($cart_data->cart_based_free_shipping_discounts) > 0)
	{
		foreach($cart_data->cart_based_free_shipping_discounts as $free_shipping){
			echo '<tr><td></td><td><span class="ajax_checkout_title">'.$free_shipping['title'].'</span></td><td><span class="ajax_checkout_price">&euro;0</span><br /></td></tr>';
		}
	}

	//cart qty based discount
	if(isset($cart_data->cart_based_qty_discounts) && !empty($cart_data->cart_based_qty_discounts) && sizeof($cart_data->cart_based_qty_discounts) > 0)
	{
		foreach($cart_data->cart_based_qty_discounts as $qty_discount)
		{
			echo '<tr><td></td><td><span class="ajax_checkout_title">'.$qty_discount['title'].'</span></td><td><span class="ajax_checkout_price">'.$qty_discount['discount_rate_percentage'].' &euro;'.$cart_price*$qty_discount['discount_rate_percentage']/$hundered.'</span><br /></td></tr>';
		}
	}
	echo '</table>';
?>
