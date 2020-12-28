<?php
	/* cart amount based discount */
	echo '<div class="offer-con">';

    if (isset($recommend_discounts)){
        foreach ($recommend_discounts as $discount) {
            if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_AMOUNT_PRICE) {
                $spend_more = $discount['range_from'] - $cart_price;
                echo "You can get " . $discount['discount_rate_percentage'] . "% discount if you buy &euro; " .
                    number_format($spend_more, 2) . " more.<br />";
            } else if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_AMOUNT_SHIPPING) {
                $spend_more = $discount['range_from'] - $cart_price;
                if ($discount['discount_rate_percentage'] < 100) {
                    echo "You can get " . $discount['discount_rate_percentage'] . "% discount of shipping price if you buy &euro; " .
                        number_format($spend_more, 2) . " more.<br />";
                } else {
                    echo "You can get free shipping if you buy &euro; " . number_format($spend_more, 2) . " more.<br />";
                }
            } else if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_FIRST_PURCHASE_PRICE) {
                echo "You can get " . $discount['discount_rate_percentage'] . "% discount on your first purchase<br />";
            } else if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_FIRST_PURCHASE_SHIPPING) {
                if ($discount['discount_rate_percentage'] < 100) {
                    echo "You can get " . $discount['discount_rate_percentage'] . "% discount of shipping price on your first purchase.<br />";
                } else {
                    echo "You can get free shipping on your first purchase.<br />";
                }
            } else if ($discount['type_id'] == Model_DiscountFormat::CART_BASED_QTY_DISCOUNT) {
                $items_more = $discount['range_from'] - $number_of_items;
                echo "You can get " . $discount['discount_rate_percentage'] . "% discount if you buy " . $items_more . " more.<br />";
            }
        }
    }

  echo '</div>';
 
?>
