<form id="filter-block-js" action="<?= URL::base().'search.html'; ?>" method="get">
    <ul class="filter" id="filter-category" style="display: none;">
        <li class="filter-title">Categories</li>
    </ul>
    <ul class="filter" id="filter-brand" style="display: none;">
        <li class="filter-title">Brands</li>
    </ul>
	<div><label class="filter-title" for="price">Price Range</label></div>
	<input type="hidden" name="minprice" id="minprice" value="<?=@$_GET['minprice'] ? $_GET['minprice'] : 0?>" />
	<input type="hidden" name="maxprice" id="maxprice" value="<?=@$_GET['maxprice'] ? $_GET['maxprice'] : 0?>" />
	<div id="price_slider"></div>
	<div class="filter-range">
		<div class="filter-range-min" id="price-min"><span>€</span><span class="num"><?=@$_GET['minprice'] ? $_GET['minprice'] : 0?></span></div>
		<div class="filter-range-max" id="price-max"><span>€</span><span class="num"><?=@$_GET['maxprice'] ? $_GET['maxprice'] : 0?></span></div>
	</div>
	<input type="hidden" name="keyword" value="<?=@htmlspecialchars($_GET['keyword'])?>" />
    <button type="submit" class="button button-primary" id="search-filter-js">Search</button>
</form>
<script>

</script>

