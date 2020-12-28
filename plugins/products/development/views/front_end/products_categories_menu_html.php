<?php
	$products_plugin_page = '/'.Model_Product::get_products_plugin_page();
	function recursive_menu($category,$level, $request_uri, $first_cat, $last_cat){
		if(!Model_Category::has_parent($category['id'])){
			echo '<li class="li_level_',$level,$first_cat,$last_cat,'"><a href="',$request_uri.'/'.str_replace(' ', '-',$category['category']),'">',rtrim(__($category['category']),'#'),'</a>';
		}
		if(Model_Category::has_subcategories($category['id'])){
			$level++;
			$sub_cats = Model_Category::get_sub_categories($category['id']);
			echo '<a href="#" class="expand"></a><ul class="ul_level_',$level,'">';
			foreach($sub_cats as $index => $sub_cat){
				$first = '';
				$last = '';
				if($index == 0){
					$first = ' first';//Add class to manipulate first li element
				}
				if($index == (count($sub_cats)-1)){
					$last = ' last';//Add class to manipulate last li element
				}
				if(isset($sub_cat['category'])){
					echo '<li class="li_level_',$level,$first,$last,'"><a href="',$request_uri.'/'.str_replace(' ', '-',$sub_cat['category']),'">',rtrim(__($sub_cat['category']),'#'),'</a>';
					if(Model_Category::has_parent($sub_cat['id']) && isset($sub_cat['category'])){
						recursive_menu($sub_cat, $level, $request_uri, false, false);
					}
					echo '</li>';
				}
			}
			echo '</ul>';
		}
		if(!Model_Category::has_parent($category['id'])){
			echo '</li>';
		}
	}

	$main_categories = array();
	$categories = Model_Category::get_by_name();

// Render the Products Menu
	echo '<ul class="ul_level_1">';
		foreach($categories as $index => $category){
			if(!Model_Category::has_parent($category['id']) && isset($category['category'])){
				$main_categories[] = $category;//Get main categories, i.e. categories at level 1.
			}
		}
		foreach($main_categories as $index_main => $main_c){
			$first = '';
			$last = '';
			if($index_main == 0){
				$first = ' first';//Add class to manipulate first li element
			}
			if($index_main == (count($main_categories)-1)){
				$last = ' last';//Add class to manipulate last li element
			}
			recursive_menu($main_c,1, $products_plugin_page, $first, $last);
		}
	echo '</ul>';

	// Render CSS Files for THIS View
	if (isset($view_css_files))
	{
		foreach ($view_css_files as $css_item_html) echo $css_item_html;
	}
	// Render JS Files for This View
	if (isset($view_js_files))
	{
		foreach ($view_js_files as $js_item_html) echo $js_item_html;
	}
?>
