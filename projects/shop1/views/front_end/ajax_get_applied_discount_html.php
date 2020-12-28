<table class="checkout-sud-table">
<?php
	/* cart amount based discount */
	foreach ($applied_discounts as $applied_discount) {
?>
	<tr>
		<td></td>
		<td><span class="ajax_checkout_title"><?php
            if (isset($applied_discount['data'])) {
                echo $applied_discount['data']['title'];
            } else if (isset($applied_discount['payback'])) {
                echo 'Payback Loyalty';
            }
        ?></span></td>
		<td><span class="ajax_checkout_price"><?php
            if (isset($applied_discount['data'])) {
                if ($applied_discount['data']['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_AMOUNT_SHIPPING
                    ||
                    $applied_discount['data']['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_FIRST_PURCHASE_SHIPPING){
                    if ($applied_discount['data']['discount_rate'] == 100) {
                        echo 'Free shipping';
                    } else {
                        echo $applied_discount['data']['discount_rate'] . '% shipping, &minus;&euro;' . $applied_discount['amount'];
                    }
                } else {
                    echo $applied_discount['data']['discount_rate'] . '%, &minus;&euro;' . $applied_discount['amount'];
                }
            }
        ?></span><br /></td>
	</tr>
<?php
	}
?>
</table>
