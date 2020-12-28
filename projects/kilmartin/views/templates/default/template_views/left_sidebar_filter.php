<?php
$filter_locations = Model_Locations::get_locations_without_parent();
$filter_years = Model_Years::get_all_years();
$filter_categories = Model_Categories::get_all_categories();
$filter_levels = Model_Levels::get_all_levels();
$selected_location = $selected_category = '';

if (Session::instance()->get('filter_reminder') != 'FALSE')
{
	Session::instance()->set('filter_reminder', 'TRUE');
}

if (@$_GET['location'] != '')
{
	foreach ($filter_locations as $filter_location)
	{
		if ($_GET['location'] == $filter_location['id'])
		{
			$selected_location = '<li><span class="remove" data-category="location" data-id="'.$filter_location['id'].
				'" onclick="remove_criteria(this)">x</span> <span class="category">location</span>: '.$filter_location['name'].'</li>';
		}
	}
}

if (@$_GET['category'] != '')
{
	foreach ($filter_categories as $filter_category)
	{
		if ($_GET['category'] == $filter_category['id'])
		{
			$selected_category = '<li><span class="remove" data-category="category" data-id="'.$filter_category['id'].
				'" onclick="remove_criteria(this)">x</span> <span class="category">category</span>: '.$filter_category['category'].'</li>';
		}
	}
}

?>
<div id="courses_filter">
	<div
		id="course_filter_criteria"<?= ($selected_location.$selected_category == '') ? ' style="display:none;"' : '' ?>>
		<h4>Criteria</h4>
		<ul>
			<?= @$selected_location ?>
			<?= @$selected_category ?>
		</ul>

		<div id="course_filter_reset"><span class="remove" title="remove">x</span> Reset criteria</div>
	</div>

	<div>
		<label for="filter_keywords">Keywords</label>
		<input id="filter_keywords" type="text" name="keywords" value="<?= @$_GET['title'] ? @$_GET['title'] : @$_GET['keywords'] ?>"/>
	</div>

	<dl id="course_filter_accordion">
		<dt><a href="">Locations</a></dt>
		<dd>
			<ul>
				<?php foreach ($filter_locations as $filter_location): ?>
					<li>
						<input type="checkbox" id="filter_location_<?= $filter_location['id'] ?>"
							   value="location_<?= $filter_location['id'] ?>"<?= ($filter_location['id'] == @$_GET['location']) ? ' checked="checked"' : ''; ?> />
						<label
							for="filter_location_<?= $filter_location['id'] ?>"><?= $filter_location['name'] ?></label>
					</li>
				<?php endforeach; ?>
			</ul>
		</dd>

		<dt><a href="">Years</a></dt>
		<dd>
			<ul>
				<?php foreach ($filter_years as $filter_year): ?>
					<li>
						<input type="checkbox" id="filter_year_<?= $filter_year['id'] ?>"
							   value="year_<?= $filter_year['id'] ?>"/>
						<label for="filter_year_<?= $filter_year['id'] ?>"><?= $filter_year['year'] ?></label>
					</li>
				<?php endforeach; ?>
			</ul>
		</dd>

		<dt><a href="">Class Types</a></dt>
		<dd>
			<ul>
				<?php foreach ($filter_categories as $filter_category): ?>
					<li>
						<input type="checkbox" id="filter_category_<?= $filter_category['id'] ?>"
							   value="year_<?= $filter_category['id'] ?>"<?= ($filter_category['id'] == @$_GET['category']) ? ' checked="checked"' : ''; ?> />
						<label
							for="filter_category_<?= $filter_category['id'] ?>"><?= $filter_category['category'] ?></label>
					</li>
				<?php endforeach; ?>
			</ul>
		</dd>

		<dt><a href="">Subject Level</a></dt>
		<dd>
			<ul>
				<?php foreach ($filter_levels as $filter_level): ?>
					<li>
						<input type="checkbox" id="filter_level_<?= $filter_level['id'] ?>"
							   value="year_<?= $filter_level['id'] ?>"/>
						<label for="filter_level_<?= $filter_level['id'] ?>"><?= $filter_level['level'] ?></label>
					</li>
				<?php endforeach; ?>
			</ul>
		</dd>
	</dl>

	<div id="course_filter_overlay" style="display:none;">
		<div id="course_filter_dialogue">
			<h5>Update Search Results</h5>

			<p>Clicking this button will update your search results.</p>

			<p>Do you wish to proceed?</p>

			<div class="button_group">
				<button type="button" class="yes">Yes</button>
				<button type="button" class="cancel">Cancel</button>
			</div>
			<input
				type="checkbox"<?= (Session::instance()->get('filter_reminder') == 'FALSE') ? ' checked="checked"' : ''; ?>
				id="cancel_filter_reminder"/>
			<label for="cancel_filter_reminder">Don't remind me again.</label>
		</div>
	</div>

</div>

<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/smart_filter.js"></script>