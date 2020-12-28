<?php foreach($options_table AS $key=>$option):?>
    <tr data-option_id="<?=$option['option_id'];?>" data-group_id="<?=$option['group_id'];?>" class="stock_row">
        <td class="product_name"><?=(isset($option['title'])) ? $option['title']:'';?></td>
        <td class="product_category"><?=(isset($option['category'])) ? $option['category']:'';?></td>
        <td class="option_group"><?=(isset($option['group'])) ? $option['group']:'';?></td>
        <td class="option_label"><?=(isset($option['label'])) ? $option['label']:'';?></td>
        <td><input class="option_quantity" value="<?=(isset($option['quantity'])) ? $option['quantity']:'';?>"/></td>
        <td><select class="option_location"><?=Model_Product::get_store_locations();?></select></td>
        <td><input type="text" class="option_price" value="<?=(isset($option['price'])) ? $option['price']:'';?>"/></td>
        <td class="final_price"></td>
        <td class="toggle-publish-stock-option"><i class="<?=(isset($option['publish']) AND $option['publish'] == '1') ? 'icon-ok': 'icon-remove';?>"></i><input type="hidden" class="option_publish" value="<?=(isset($option['publish']) AND $option['publish'] == '1') ? 1: 0;?>"/></td>
   </tr>
<?php endforeach;?>