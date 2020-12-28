<div class="related-properties-wrapper">
	<div class="related-properties">
		<?php foreach ($properties as $wishlist_property): ?>
			<div class="col-xsmall-6 col-small-3 related-property">
				<?= View::factory('property_thumbnail')->set('property_item', $wishlist_property); ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
