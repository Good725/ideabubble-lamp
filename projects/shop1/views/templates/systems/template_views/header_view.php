<!-- Header -->
<div class="main-header" id="header">
	<div class="header-group header-group-logo logo">
		<a href="<?= URL::site() ?>">
			<img src="<?= URL::get_skin_urlpath(TRUE) ?>images/logo.png" alt="<?= Settings::instance()->get('company_title') ?>" title="<?= Settings::instance()->get('company_title') ?>" />
		</a>
	</div>
	<div class="header-group header-group-search">
		<div class="header-slogan"><?= Settings::instance()->get('company_slogan') ?></div>
		<div class="header-search">
			<form action="/search.html" method="get" class="header-search-form">
				<div class="header-search-form-fields">
					<div class="header-search-keyword-wrapper" id="search-by-keyword-bl">
						<label class="accessible-hide" for="search-by-keyword">Keyword</label>
						<input name="keyword" type="text" class="search-by-keyword" id="search-by-keyword" placeholder="Keywords" value="<?= @$_GET['keyword'] ?>" />
						<div id="search-by-keyword-result"></div>
					</div>
					<div class="header-search-category-wrapper">
						<label class="accessible-hide" for="top-category-filter">Category</label>
						<select name="category[]" class="header-select header-select-category" id="top-category-filter">
							<option value="category">Category</option>
							<?php $category_id = isset($_GET['category'][0]) ? $_GET['category'][0] : ''; ?>
							<?php if (isset($top_level_categories)): ?>
								<?php foreach ($top_level_categories as $category): ?>
									<option value="<?= $category['id'] ?>"<?= ($category['id'] == $category_id) ? ' selected="selected"' : '' ?>><?= __($category['category']) ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				</div>
				<button type="submit" class="search-by-keyword-btn" id="search-by-keyword-btn" data-base="<?= URL::base().'products/' ?>"><span>Search</span></button>
			</form>
		</div>
	</div>
	<div class="header-group header-group-shopinfo">
		<div class="shopping_actions">
			<?php
				echo trim(Model_Product::render_minicart_view());
				$logged_in_user = Auth::instance()->get_user();
				if (isset($logged_in_user['id'])): ?><!--
					--><button type="button" class="shopping_actions_name" id="shopping_actions_name"><?= $logged_in_user['name'] ?></button><!--
					--><div class="shopping_actions_dropout" id="shopping_actions_dropout">
						<a href="/order-history.html">View order history</a>
					</div><!--
					--><a href="/frontend/users/logout" id="login-out">Log out</a><!--
				--><?php else: ?><!--
					--><a href="/register-account.html" id="register-btn">Register</a><!--
					--><a href="/login.html" id="login-btn">Login</a><!--
				--><?php endif; ?>
		</div>
		<div class="header-contact-info">
			<span class="header-contact-contactus">Contact Us</span>
			<span class="header-contact-telephone">
				<span class="header-contact-label">Telephone</span>
				<span class="header-contact-value"><?= trim(Settings::instance()->get('telephone')) ?></span>
			</span>
		</div>

	</div>
</div>
<div>
	<div class="header-menu-wrapper">
		<a href="#" id="header-menu-expand">Menu</a>
		<?= menuhelper::add_menu_editable_heading('main', 'header-menu'); ?>
	</div>
</div>
<!-- /Header -->
