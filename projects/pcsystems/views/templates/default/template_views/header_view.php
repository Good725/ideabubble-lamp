<!-- Header -->
<div id="header" class="left">
	<a href="/" class="logo"></a>
	<!--<div class="logo left">
		<a href="<?/*=URL::site();*/?>">
			<img src="<?/*=URL::get_skin_urlpath(TRUE)*/?>images/logo.png" alt="<?/*= Settings::instance()->get('company_title') */?>" title="<?/*= Settings::instance()->get('company_title') */?>" />
		</a>
	</div>-->
	<div class="user-bl">
		<div class="irish-owned-bl">
			<div class="irish-owned-bl-slogan">
				100% IRISH OWNED COMPANY
				<span class="irish-flag"></span>
			</div>
			<div class="shopping_actions">
				<?php
				echo trim(Model_Product::render_minicart_view());
				$logged_in_user = Auth::instance()->get_user();
				if (isset($logged_in_user['id'])): ?><!--
					--><span class="shopping_actions_name"><?= $logged_in_user['name'] ?></span><!--
					--><a href="/frontend/users/logout" id="login-out">Log out</a><!--
				--><?php else: ?><!--
					--><a href="/register-account.html" id="register-btn">Register</a><!--
					--><a href="/login.html" id="login-btn">Login</a><!--
				--><?php endif; ?>
			</div>
		</div>

		<div class="shop-info">
			<span>Call us +353 (0) 21 4933 166</span>
			<span class="divider"></span>
			<span>Mon - Fri 9.00 - 17.00</span>
		</div>
	</div>
	<div class="search-bl">
		<form action="<?= URL::base().'search.html'; ?>" method="get">
		<select name="category[]" class="header-select header-select-category" id="top-category-filter">
			<option value="category">Select Category</option>
			<?= Model_SITC::render_top_category(@$_GET['category'][0]) ?>
		</select>
		<select name="sub_category[<?=isset($_GET['category'][0]) ? $_GET['category'][0] : '' ?>][]" class="header-select" id="top-subcategory-filter">
			<option value="">Select Subcategory</option>
			<?php if(is_numeric(@$_GET['category'][0])){ ?>
			<?= Model_SITC::render_sub_category2(@$_GET['category'][0], @$_GET['sub_category'][$_GET['category'][0]][0]) ?>
			<?php } ?>
		</select>
		<div class="search-by-keyword-bl" id="search-by-keyword-bl">
			<input name="keyword" type="text" class="search-by-keyword" id="search-by-keyword" placeholder="Search for a brand, product or specific item ..." value="<?=@$_GET['keyword']?>">
			<div id="search-by-keyword-result"></div>
		</div>
		<button type="submit" class="search-by-keyword-btn" id="search-by-keyword-btn" data-base="<?= URL::base().'products/' ?>">Search</button>
		</form>
	</div>
	<div class="header-menu-wrapper">
		<a href="#" id="header-menu-expand">Menu</a>
		<ul class="header-menu" id="header-menu">
			<li><a class="main-nav-i" href="/">Home</a></li>
			<li>
				<a class="main-nav-i">Components</a>
				<a href="#" class="submenu-expand"></a>
				<?= Model_SITC::get_subcategory(112747) ?>
			</li>
			<li>
				<a class="main-nav-i">Computers</a>
				<a href="#" class="submenu-expand"></a>
				<?= Model_SITC::get_subcategory(208133) ?>
			</li>
			<li>
				<a class="main-nav-i">Entertainment</a>
				<a href="#" class="submenu-expand"></a>
				<?= Model_SITC::get_subcategory(208134) ?>
			</li>
			<li>
				<a class="main-nav-i">Peripherals</a>
				<a href="#" class="submenu-expand"></a>
				<?= Model_SITC::get_subcategory(112753) ?>
			</li>
			<li>
				<a class="main-nav-i">Networking</a>
				<a href="#" class="submenu-expand"></a>
				<?= Model_SITC::get_subcategory(207652) ?>
			</li>
			<li>
				<a class="main-nav-i">Supplies</a>
				<a href="#" class="submenu-expand"></a>
				<?= Model_SITC::get_subcategory(208135) ?>
			</li>
			<?= menuhelper::add_menu_editable_heading('main', 'main_menu'); ?>
		</ul>
	</div>
</div>
<!-- /Header -->
