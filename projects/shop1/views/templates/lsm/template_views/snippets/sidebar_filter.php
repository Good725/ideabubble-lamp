<div class="sidebar-section">
	<h2 class="sidebar-section-title">
		<?= $filter_title ?>
		<button type="button" class="sidebar-section-collapse">
			<span class="fa fa-chevron-down"></span>
		</button>
	</h2>

	<div class="sidebar-section-content">
		<ul class="list-unstyled sidebar-filter-options" data-category="<?= $filter_title ?>">
			<?php foreach ($filter_items as $filter_item): ?>
				<?php
				if (is_array($default)) {
					$checked = in_array($filter_item['id'], $default) ? ' checked="checked"' : '';
				} else {
					$checked = ($filter_item['id'] == $default) ? ' checked="checked"' : '';
				}
				?>
				<li>
					<label>
						<input
							type="checkbox"
							name="<?= $filter_name ?>[]"<?= $checked ?>
							value="<?= $filter_item['id'] ?>"
							data-category="<?= $filter_singular ?>"
							data-value="<?=  $filter_item[$filter_item_key]?>"
							data-id="<?= $filter_name.'-'.$filter_item['id'] ?>"
							/>
						<?= $filter_item[$filter_item_key] ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>