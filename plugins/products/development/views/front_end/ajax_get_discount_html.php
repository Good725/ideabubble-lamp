<?php
    echo '<div class="offer-con">';
	/* cart amount based discount */
	if(isset($chk_cart_discounts) && !empty($chk_cart_discounts) && sizeof($chk_cart_discounts) > 0)
	{
		foreach($chk_cart_discounts as $chk_cart_discount)
		{
			$range_from = $chk_cart_discount['range_from'];
			$range_to = $chk_cart_discount['range_to'];

			if($cart_price >= $range_from && $cart_price < $range_to)
			{
				$apply_discount_price = $range_to - $cart_price;
				echo "You can get " .$chk_cart_discount['discount_rate_percentage']." discount if you buy &euro; ".$apply_discount_price." more.<br />";
			}
		}
	}
	/* cart amount based shipping discount */
	if(is_array($free_shipping_arr) && sizeof($free_shipping_arr) > 0)
	{
		foreach($free_shipping_arr as $free_shipping){
			$cart_based_free_range_from = $free_shipping['range_from'];
			$cart_based_free_range_to = $free_shipping['range_to'];

			if($cart_price >= $cart_based_free_range_from && $cart_price < $cart_based_free_range_to){
				$apply_free_shipping_price = $cart_based_free_range_to - $cart_price;
				echo "You can get free shipping if you buy &euro; ".$apply_free_shipping_price." more.<br />";
			}
		}
	}

	/* cart qty based discount */
	if(is_array($qty_array) && sizeof($qty_array) > 0)
	{
		foreach($qty_array as $qty_arr)
		{
			$cart_based_qty_range_from = $qty_arr['range_from'];
			$cart_based_qty_range_to = $qty_arr['range_to'];
			if($number_of_items >= $cart_based_qty_range_from && $number_of_items < $cart_based_qty_range_to){
				$apply_qty_discount_qty = $cart_based_qty_range_to - $number_of_items;
				echo "You can get ". $qty_arr['discount_rate_percentage']." discount when you buy ". $apply_qty_discount_qty ." more products.<br />";
			}
		}
	}
  echo '</div>';
?>

