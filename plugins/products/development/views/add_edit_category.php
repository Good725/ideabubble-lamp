<div class="row">
    <div class="col-sm-12">
        <?=(isset($alert)) ? $alert : ''?>
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
</div>

<form class="form-horizontal col-sm-12" id="form_add_edit_category" name="form_add_edit_category" action="/admin/products/save_category/" method="post">

	<div class="form-group">
		<div class="col-sm-8">
			<input type="text" class="form-control required" id="category" name="category" placeholder="Enter category name here" value="<?= $category->category ?>"/>
		</div>
	</div>

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#edit-category-tab-details"  aria-controls="details"  role="tab" data-toggle="tab"><?= __('Details')  ?></a></li>
		<li role="presentation"><a href="#edit-category-tab-products" aria-controls="products" role="tab" data-toggle="tab"><?= __('Products') ?></a></li>
		<li role="presentation"><a href="#edit-category-tab-seo"      aria-controls="products" role="tab" data-toggle="tab"><?= __('SEO')      ?></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content" style="margin-bottom: 1em;">
		<div role="tabpanel" class="tab-pane active" id="edit-category-tab-details">

			<!-- Description -->
			<div class="form-group">
				<label class="col-sm-2 control-label" for="description">Description</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="description" name="description" value="<?= $category->description ?>"/>
				</div>
			</div>

			<!-- Information -->
			<div class="form-group">
				<label class="col-sm-2 control-label" for="information">Information</label>
				<div class="col-sm-10">
					<textarea class="form-control" id="information" name="information" rows="10"><?= $category->information ?></textarea>
				</div>
			</div>

			<!-- Order -->
			<div class="form-group">
				<label class="col-sm-2 control-label" for="order">Display Order</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="order" name="order" value="<?= $category->order ?>"/>
				</div>
			</div>

			<!-- Image -->
			<div class="form-group">
				<label class="col-sm-2 control-label" for="image">Thumbnail Image</label>
				<div class="col-sm-6">
					<select class="form-control ib-combobox" id="image" name="image">
						<option value=""<?= ($category->image == '') ? ' selected="selected"' : '' ?>></option>
						<?php foreach ($images as $item): ?>
							<option value="<?=$item['filename']?>"<?=($category->image == $item['filename']) ? ' selected="selected"' : '' ?>><?=$item['filename']?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-sm-4">
					<div id="image_preview_container">
						<img id="image_preview" src="" alt=""/>
					</div>
				</div>
			</div>

			<!-- Parent -->
			<div class="form-group">
				<label class="col-sm-2 control-label" for="parent_id">Parent</label>
				<div class="col-sm-6">
					<select class="form-control" id="parent_id" name="parent_id">
						<option value=""<?=($category->parent_id == '') ? ' selected="selected"' : '' ?>>No Parent</option>

						<?php foreach ($categories as $item): ?>
							<?php if ($category->id != $item->id): ?>
								<option value="<?=$item->id ?>"<?=($category->parent_id == $item->id) ? ' selected="selected"' : '' ?>><?= $item->category ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<!-- Theme -->
			<?php if (Settings::instance()->get('use_config_file') === '0'): ?>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="edit_category_theme">Theme</label>
					<div class="col-sm-6">
						<select class="form-control" id="edit_category_theme" name="theme">
							<?= $theme_options ?>
						</select>
					</div>
				</div>
			<?php endif; ?>

			<!-- Publish -->
			<div class="form-group">
				<label class="col-sm-2 control-label" for="project_publish_toggle">Publish</label>
				<div class="btn-group col-sm-5" data-toggle="buttons">
					<?php $published = ($category->id == '' OR $category->publish == 1); ?>
					<label class="btn btn-default<?= $published ? ' active' : '' ?>">
						<input type="radio"<?= $published ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
					</label>
					<label class="btn btn-default<?= ( ! $published) ? ' active' : '' ?>">
						<input type="radio"<?= ( ! $published) ? ' checked="checked"' : '' ?> value="0" name="publish">No
					</label>
				</div>
			</div>


		</div>
		<div role="tabpanel" class="tab-pane" id="edit-category-tab-products">

			<label class="col-sm-12" for="edit-category-select-product">Select a product to add to this category</label>

			<div class="form-group">
				<div class="col-sm-5">
					<select class="form-control ib-combobox" id="edit-category-select-product">
						<option value=""></option>
						<?php foreach ($products as $product): ?>
							<option value="<?= $product->id ?>"><?= $product->title ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-sms2">
					<button type="button" class="btn btn-default" id="edit-category-add-product-btn">Add</button>
				</div>
			</div>

			<table class="table table-striped edit-category-products-table" id="edit-category-products-table">
				<thead>
					<tr>
						<th scope="col">ID</th>
						<th scope="col">Product</th>
						<th scope="col">Remove</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($category->products->find_all_undeleted() as $product): ?>
						<tr data-id="<?= $product->id ?>">
							<td><input type="hidden" name="product_ids[]" value="<?= $product->id ?>" /><?= $product->id ?></td>
							<td><?= $product->title ?></td>
							<td><button type="button" class="close">&times;</button></td>
						</tr>
					<?php endforeach ;?>
				</tbody>
			</table>

		</div>

		<div role="tabpanel" class="tab-pane clearfix" id="edit-category-tab-seo">

			<div class="col-sm-9 tab-pane" id="seo_tab">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="seo_title">Page Title</label>
					<div class="col-sm-9">
						<textarea class="form-control" id="seo_title" name="seo_title" rows="1"><?= $category->seo_title ?></textarea>
					</div>
				</div>

				<?php /* To be re-enabled in ENGINE-473
				<div class="form-group">
					<label class="col-sm-3 control-label" for="edit-category-url-title">URL Name</label>

					<div class="col-sm-9">
						<input type="text" class="form-control" id="edit-category-url-title" name="url_title" value="<?= $category->url_title ?>" placeholder="Default">
					</div>
				</div>
 				*/ ?>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="edit-category-keywords">Keywords</label>
					<div class="col-sm-9">
						<textarea class="form-control" id="edit-category-keywords" name="seo_keywords" rows="2"><?= $category->seo_keywords ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="edit-category-seo_description">Meta Description</label>
					<div class="col-sm-9">
						<textarea class="form-control" id="edit-category-seo_description" name="seo_description" rows="2"><?= $category->seo_description ?></textarea>
					</div>
				</div>
			</div>

		</div>


	</div>

    <!-- Category Identifier -->
    <input type="hidden" id="id" name="id" value="<?= $category->id ?>"/>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="reset" class="btn">Reset</button>
    </div>
</form>
