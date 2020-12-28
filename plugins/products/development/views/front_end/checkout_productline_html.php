<?php
$href = URL::base().Model_Product::get_products_plugin_page().DIRECTORY_SEPARATOR.$line->product->url_title;
if (isset($line->options[0]))
{
    $option1 = $line->options[0];
    if (isset($option1->id ))
    {
        $href .= '?option1='.$option1->id;
        if (isset($option1->id2))
        {
            $href .= '&option2='.$option1->id2;
        }
    }
}

$line_text  = (isset($line->product->from_scratch_sign) AND ($line->product->from_scratch_sign)) ? __('Custom Sign') : __($line->product->title);
$line_text .= (isset($line->product->builder) AND ($line->product->builder == 1) AND isset($line->product->timestamp)) ? ' - '.$line->product->timestamp : '';
foreach($line->options as $option)
{
	$option_price = ($option->price == '') ? 0 : $option->price;
	$line_text .= ' <span class="product_line_options">['.__($option->group).':'.__($option->label).' <span class="option_extra_price">+ &euro;'.number_format($option_price, 2).'</span>]</span>';
}?>


<tr class="product_line" data-product_id="<?=$line->product->id?>" data-product_name="<?=$line->product->title?>" data-line_id="<?=$line_id?>">
    <td class="product_line_first_td">
		<?php if (isset($line->product->sign_thumbnail) AND $line->product->sign_thumbnail != ''): ?>
			<a class="checkout_sign_thumbnail" href="<?= $line->product->sign_thumbnail ?>">
				<img src="<?= $line->product->sign_thumbnail ?>" alt="" height="40" />
			</a>
			<?= $line_text ?>
		<?php else: ?>
			<a title="<?=__($line->product->title) ?>" href="<?= $href ?>"><?= $line_text ?></a>
		<?php endif; ?>
    </td>
    <td class="priceField item_price_background">
        &euro;<?=number_format($line->price_per_unit,2)?>
    </td>
    <td class="center item_price_background item_counter">
        <?php if(Settings::instance()->get('stock_enabled') != "TRUE"): ?>
        	<img src="<?=URL::site()?>assets/default/images/minus_small.png" class="cartIcon decrease_product_amount"/>
        <?php endif; ?>
        <input type="text" value="<?=$line->quantity?>" size="2" maxlength="3" readonly="readonly" class="quantity"/>
        <?php if(Settings::instance()->get('stock_enabled') != "TRUE"): ?>
        	<img src="<?=URL::site()?>assets/default/images/plus_small.png" class="cartIcon increase_product_amount"/>
        <?php endif; ?>
    </td>
    <td class="priceField price_total_line item_price_background">&euro;<?=number_format($line->price, 2)?></td>
    <td class="">
        <img title="<?= __('Cancel cart') ?>" src="/assets/default/images/cross_small.png" class="cartIcon delete_product" />
    </td>
</tr>