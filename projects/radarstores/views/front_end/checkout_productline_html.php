<tr class="product_line" data-product_id="<?=$line->product->id?>" data-product_name="<?=$line->product->title?>" data-line_id="<?=$line_id?>">
    <td class="product_line_first_td">
        <a title="<?=$line->product->title?>" href="#">
            <?=$line->product->title?>
            <?php foreach($line->options as $option):?>
                [<?=$option->group?>:<?=$option->label?> <span class="option_extra_price">+ €<?=$option->price?></span>]
            <?php endforeach;?>
        </a>
    </td>
    <td class="priceField">
        €<?=$line->price_per_unit?>
    </td>
    <td class="center">
        <img src="<?=URL::site()?>assets/default/images/minus_small.png" class="cartIcon decrease_product_amount">
        <input type="text" value="<?=$line->quantity?>" size="2" maxlength="3" readonly="readonly" class="quantity">
        <img src="<?=URL::site()?>assets/default/images/plus_small.png" class="cartIcon increase_product_amount">
    </td>
    <td class="priceField price_total_line">€<?=$line->price?></td>
    <td>
        <img title="Cancel cart" src="<?=URL::site()?>assets/default/images/cross_small.png" class="cartIcon delete_product">
    </td>
</tr>