<div class="header">
	<div class="logo">
		<a href="/"><img src="<?= $page_data['logo'] ?>" alt="<?= Settings::instance()->get('company_title') ?>" /></a>
	</div>

	<div class="header_content">
		<div class="header_content_section top1">
			<p class="header_slogan"><?= Settings::instance()->get('company_slogan'); ?></p>
			<a href="/course-list.html" class="booking_button header_booking_button">book now</a>
            <a href="/course-list.html"><div id="shop_button_image"></div></a>
		</div>
		<form class="header_content_section course_searchbar_wrapper" action="/course-list.html" action="get">
			<div class="course_search_input_wrapper">
				<label class="course_searchbar_label" for="course_searchbar">Search Courses</label>
				<input class="course_searchbar" id="course_searchbar" type="text" name="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" />
			</div>
			<div class="course_searchbar_button_wrapper">
				<button class="course_search_button" type="submit">Find Course</button>
			</div>
		</form>
	</div>
</div>

<div class="menu_section" id="main_menu">
	<?php menuhelper::add_menu_editable_heading('main') ?>
</div>