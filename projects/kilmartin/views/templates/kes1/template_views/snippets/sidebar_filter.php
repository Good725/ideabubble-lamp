<div class="sidebar-section">
	<h2 class="sidebar-section-title">
		<?= $filter_title ?>
		<button type="button" class="sidebar-section-collapse">
			<span class="fa fa-chevron-down"></span>
		</button>
	</h2>

    <?php $filter_items = is_array($filter_items) ? $filter_items : $filter_items->as_array(); ?>

    <div class="sidebar-section-content">
		<ul class="list-unstyled sidebar-filter-options" data-category="<?= $filter_title ?>">
			<?php foreach ($filter_items as $filter_item): ?>
				<?php
                $filter_item = is_array($filter_item) ? $filter_item : $filter_item->as_array();

                if (is_array($default)) {
					$checked = in_array($filter_item['id'], $default);
				} else {
					$checked = ($filter_item['id'] == $default);
				}
				?>
				<li class="sidebar-filter-li">
					<label>
                        <span class="mr-sm-2">
                            <?php
                            $attributes = ['data-category' => $filter_title, 'data-value' => $filter_item[$filter_item_key], 'data-id' => $filter_name.'-'.$filter_item['id']];
                            echo Form::ib_checkbox(null, $filter_name.'[]', $filter_item['id'], $checked, $attributes);
                            ?>
                        </span>

                        <span>
                            <?= $filter_item[$filter_item_key] ?>
                        </span>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>