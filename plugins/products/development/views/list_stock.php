<?=(isset($alert)) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div class="alert_message alert alert-success">

</div>
<table class="table table-striped" id="stock_table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Category</th>
        <th>Option Group</th>
        <th>Option Value</th>
        <th>Qty</th>
        <th>Location</th>
        <th>Adjustment Price</th>
        <th>Final Price</th>
        <th>Publish</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($stock AS $key=>$option):?>
        <tr data-option_id="<?=$option['option_id'];?>" data-product_id="<?=$option['id'];?>">
            <td><?=$option['id'];?></td>
            <td class="product_name"><?=$option['title'];?></td>
            <td><?=$option['category'];?></td>
            <td class="option_group"><?=$option['group'];?></td>
            <td class="option_label"><?=$option['label'];?></td>
            <td class="option_quantity"><input type="text" class="quantity_input" value="<?=$option['quantity'];?>"/></td>
            <td class="option_location"><?=$option['name'];?></td>
            <td class="option_price"><input type="text" class="price_input" value="<?=$option['price'];?>"/></td>
            <td class="final_price"><span class="final_price"><?=$option['product_price'];?></span></td>
            <td class="toggle-publish-stock-option"><i class="<?=(isset($option['publish']) AND $option['publish'] == '1') ? 'icon-ok': 'icon-remove';?>"></i><input type="hidden" class="option_publish" value="<?=(isset($option['publish']) AND $option['publish'] == '1') ? 1: 0;?>"/></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
