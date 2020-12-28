<div class="col-sm-12">
	<?= (isset($alert)) ? $alert : '' ?>
	<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
</div>

<form class="col-sm-12 form-horizontal" action="/admin/products/save_review/<?= $review->id ?>" method="post">

	<div class="form-group">
		<label class="col-sm-4 control-label" for="edit-review-product"><?= __('Product') ?></label>
		<div class="col-sm-5">
			<?php $selected_product_id = isset($_GET['product_id']) ? $_GET['product_id'] : $review->product->id; ?>
			<select class="form-control ib-combobox" id="edit-review-product" name="product_id" data-placeholder="Please select">
				<option value="0"></option>
				<?php foreach ($products as $product): ?>
					<option value="<?= $product->id ?>"<?= ($product->id == $selected_product_id) ? ' selected="selected"' : ''?>><?= $product->title ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-4 control-label"><?= __('Rating') ?></div>
		<div class="col-sm-5"><?= $review->render_stars() ?></div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label" for="edit-review-title"><?= __('Title') ?></label>
		<div class="col-sm-5">
			<input type="text" class="form-control" id="edit-review-title" name="title" value="<?= $review->title ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label" for="edit-review-review"><?= __('Review') ?></label>
		<div class="col-sm-5">
			<textarea class="form-control" id="edit-review-review" name="review" rows="8"><?= $review->review ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label" for="edit-review-author"><?= __('Author') ?></label>
		<div class="col-sm-5">
			<input type="text" class="form-control" id="edit-review-author" name="author" value="<?= $review->author ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label" for="edit-review-email"><?= __('Author email') ?></label>
		<div class="col-sm-5">
			<input type="text" class="form-control" id="edit-review-email" name="email" value="<?= $review->email ?>" />
		</div>
	</div>

	<?php /*
	<div class="form-group">
		<label class="col-sm-4 control-label" for="edit-review-ip_address"><?= __('IP address') ?></label>
		<div class="col-sm-5">
			<input type="text" class="form-control" id="edit-review-ip_address" disabled="disabled" value="<?= $review->ip_address ?>" />
		</div>
	</div>
 	*/ ?>


	<div class="form-group">
		<?php
		// Unlike other forms, publish should be "No" by default here.
		$published = ($review->publish == 1);
		?>
		<label class="col-sm-3 control-label" for="publish"><?= __('Publish') ?></label>
		<div class="col-sm-9">
			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-default<?= $published ? ' active' : '' ?>">
					<input type="radio"<?= $published ? ' checked="checked"' : '' ?> value="1" name="publish"><?= __('Yes') ?>
				</label>
				<label class="btn btn-default<?= ( ! $published) ? ' active' : '' ?>">
					<input type="radio"<?= ( ! $published) ? ' checked="checked"' : '' ?> value="0" name="publish"><?= __('No') ?>
				</label>
			</div>
		</div>
	</div>

	<div class="well">
		<button type="submit" class="btn btn-primary" name="redirect" value="0"><?= __('Save') ?></button>
		<button type="submit" class="btn btn-success save_button" name="redirect" value="1"><?= __('Save &amp; Exit') ?></button>
		<button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
		<?php if ($review->id): ?>
			<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-product-review-modal"><?= __('Delete') ?></button>
		<?php endif; ?>
		<a href="/admin/products/reviews" class="btn btn-default"><?= __('Cancel') ?></a>
	</div>
</form>

<?php if ($review->id): ?>
	<div class="modal fade" tabindex="-1" role="dialog" id="delete-product-review-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="/admin/products/delete_review" method="post">
					<input type="hidden" name="id" value="<?= $review->id ?>" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><?= __('Delete review') ?></h4>
					</div>
					<div class="modal-body">
						<p><?= __('Are you sure you want to delete this review?') ?></p>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-danger" id="delete-product-review-button"><?= __('Delete') ?></button>
						<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php endif; ?>

<style>

	.review-form-rating-stars {
		float: left;
	}
	.review-form-rating-stars [type="radio"] {
		position: absolute;
		left: -99999px;
		opacity: 0;
	}
	.review-form-rating-stars label {
		float: right;
		font-size: 0;
	}
	.review-form-rating-stars label:before {
		content: '';
		background: url('/engine/plugins/products/images/icons/star-empty.png') no-repeat;
		display: inline-block;
		width: 18px;
		height: 18px;
	}

	.review-form-rating-stars:not(:hover) [type="radio"]:checked + label:before,
	.review-form-rating-stars:not(:hover) [type="radio"]:checked ~ label:before,
	.review-form-rating-stars:not(:hover) [type="radio"]:not(:checked) > label:not(:hover):before,
	.review-form-rating-stars:not(:hover) [type="radio"]:not(:checked) > label ~ label:before  {
		background-image: url('/engine/plugins/products/images/icons/star-full.png');
	}

	.review-form-rating-stars [type="radio"]:hover + label:before,
	.review-form-rating-stars [type="radio"]:hover ~ label:before,
	.review-form-rating-stars [type="radio"]:not(:hover) > label:before,
	.review-form-rating-stars [type="radio"]:not(:hover) > label ~ label:before {
		background-image: url('/engine/plugins/products/images/icons/star-full.png');
	}
</style>
