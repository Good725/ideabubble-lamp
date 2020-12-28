<!-- Left Menu -->
<?php $show_search_filters = isset($show_search_filters) ? $show_search_filters : TRUE; ?>
<?php if ($show_search_filters): ?>
	<div id="products_menu">
		<ul class="body-menu">
			<li class="body-menu-li">
				<div class="body-menu-li-name body-menu-li-name-title active">Advanced Search <span class="opened">&#187;</span><a class="clear-all">clear all</a></div>
				<? include 'filter.php'; ?>
			</li>
			<li class="body-menu-li">
				<div class="body-menu-li-name active">Categories <span class="opened">&#187;</span></div>
				<ul class="body-sub-menu">
					<?=Model_SITC::render_category('body-sub-menu-li', 'category');?>
				</ul>
			</li>
			<li class="body-menu-li">
				<div class="body-menu-li-name active">Brands <span class="opened">&#187;</span></div>
				<ul class="body-sub-menu">
					<?=Model_SITC::render_brands('body-sub-menu-li', 'brand');?>
				</ul>
			</li>
		</ul>
	</div>
<?php endif; ?>
<?= Menuhelper::add_menu_editable_heading('side', 'side_menu'); ?>
<!-- /Left Menu -->
<script>
window.products_menu_filter_prefill = <?=json_encode($_GET);?>;
</script>