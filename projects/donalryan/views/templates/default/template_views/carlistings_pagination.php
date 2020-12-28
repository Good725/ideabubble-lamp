<?php if (count($cars) > 0): ?>
	<div class="car_pagination">
		<?php
		$href      = 'car-listings.html'.$query_string;
		$prev_href = $href.'&offset='.(($offset - 10 < 0) ? 0 : $offset - 10);
		$next_href = $href.'&offset='.(($is_last_page) ? $offset : $offset + 10 );
		?>
		<ul>
			<li class="car_pagination_prev">
				<a href="<?= $prev_href ?>"<?= ($is_first_page) ? ' class="disabled_link"' : '' ?>>Previous</a>
			</li>

			<?php for ($i = 0; $i < $count_cars; $i += 10): ?>
				<li class="car_pagination_number">
					<a href="<?= $href ?>&offset=<?= $i ?>"<?= ($current_page == $i / 10 + 1) ? ' class="disabled_link"' : '' ?>><?= $i / 10 + 1 ?></a>
				</li>
			<?php endfor; ?>

			<li class="car_pagination_next">
				<a href="<?= $next_href ?>"<?= ($is_last_page) ? ' class="disabled_link"' : '' ?>>Next</a>
			</li>
		</ul>
	</div>
<?php endif; ?>