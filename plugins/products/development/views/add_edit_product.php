<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>
<style type="text/css">
    <?php
    foreach($highlight AS $key=>$css)
    {
        echo '#'.$css.'{border:2px solid red !important;}';
    }
    ?>
	#file_previews {
		display: none;
	}
</style>
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

<form class="col-sm-12 form-horizontal" id="form_add_edit_product" name="form_add_edit_product" action="/admin/products/save_product/" method="post">
<input type="hidden" id="youtube_options" name="youtube_videos" value="<?=isset($data['videos']) ? htmlspecialchars($data['videos']) : '';?>"/>

    <div class="form-group">
		<div class="col-sm-9">
			<label class="sr-only" for="title">Title</label>
			<input type="text" class="form-control required" id="title" name="title" placeholder="Enter product title here" value="<?=isset($data['title']) ? $data['title'] : ''?>"/>
		</div>
    </div>

	<?php $reviews_enabled = (isset($data['id']) AND Settings::instance()->get('enable_customer_reviews') == '1'); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#summary_tab" data-toggle="tab">Configuration</a></li>
        <li><a href="#categories_tab" data-toggle="tab">Categories</a></li>
		<li><a href="#tags_tab" data-toggle="tab">Tags</a></li>
        <li><a href="#details_tab" data-toggle="tab">Details</a></li>
        <li><a href="#seo_tab" data-toggle="tab">SEO</a></li>
        <li><a href="#images_tab" data-toggle="tab">Media</a></li>
		<li><a href="#options_tab" data-toggle="tab">Options</a></li>
        <li><a href="#related_to_tab" data-toggle="tab">Related</a></li>
		<li><a href="#youtube" data-toggle="tab">YouTube</a></li>
		<?php if ($reviews_enabled): ?>
			<li><a href="#edit-product-reviews_tab" data-toggle="tab">Reviews</a></li>
		<?php endif; ?>
        <li id="sign_builder_list"><a href="#sign_builder_tab" data-toggle="tab">Sign Builder</a></li>
    </ul>

    <div class="tab-content clearfix">
        <!-- Summary -->
        <div class="col-sm-9 tab-pane active" id="summary_tab">
            <?php /* Deprecated
            <!-- Category -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="category_id">Category</label>
                <div class="col-sm-9">
                    <select class="form-control" id="category_id" name="category_id">
                        <? if ( ! isset($data['category_id']) OR ($data['category_id'] == '') ): ?>
                        <option value="">-- Select Category --</option>
                        <? endif; ?>

                        <? foreach ($categories as $item): ?>
                        <option value="<?=$item['id']?>" <?=( isset($data['category_id']) AND ($data['category_id'] == $item['id']) ) ? 'selected="selected"' : '' ?>><?=$item['category']?></option>
                        <? endforeach; ?>
                    </select>
                </div>
            </div>
            */ ?>

            <!-- Order -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="order">Display Order</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="order" name="order" value="<?=isset($data['order']) ? $data['order'] : ''?>"/>
                </div>
            </div>

            <!-- Price -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="price">Price</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="price" name="price" value="<?=isset($data['price']) ? $data['price'] : ''?>"/>
                </div>
            </div>

            <?php if(Settings::instance()->get('stock_enabled') == "TRUE"): ?>

				<!-- Quantity Enabled -->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="quantity_enabled">Quantity Enabled</label>
					<div class="col-sm-9">
						<input type="checkbox" class="form-control" id="quantity_enabled" name="quantity_enabled" <?=(isset($data['quantity_enabled']) AND $data['quantity_enabled'] == "1") ? 'checked' : '';?>/>
					</div>
				</div>

				<!-- Quantity -->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="quantity">Quantity</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="quantity" name="quantity" value="<?=(isset($data['quantity']) ? $data['quantity']: '');?>" <?=(isset($data['quantity_enabled']) AND $data['quantity_enabled'] == "1") ? '' : 'readonly';?>/>
					</div>
				</div>

            <?php endif; ?>

            <!-- Disable Purchase -->
<!--            <div class="form-group">-->
<!--                <label class="col-sm-3 control-label" for="disable_purchase">Disable Purchase</label>-->
<!--                <div class="col-sm-9">-->
<!--					<div class="btn-group" data-toggle="buttons">-->
<!--						--><?php //$disable_purchase = (isset($data['disable_purchase']) AND $data['disable_purchase'] == '1'); ?>
<!--						<label class="btn btn-plain--><?//= $disable_purchase ? ' active' : '' ?><!--">-->
<!--							<input type="radio" name="disable_purchase" value="1"--><?//= $disable_purchase ? ' checked' : '' ?><!-- />Yes-->
<!--						</label>-->
<!--						<label class="btn btn-plain--><?//= ( ! $disable_purchase) ? ' active' : '' ?><!--">-->
<!--							<input type="radio" name="disable_purchase" value="0"--><?//= ( ! $disable_purchase) ? ' checked' : '' ?><!-- />No-->
<!--						</label>				-->
<!--					</div>-->
<!--
<!--                    <p class="help-inline">Setting this to "Yes", will display the product without the price and without the option to purchase it.</p>-->
<!--                    <input type="hidden" id="disable_purchase" name="disable_purchase" value="--><?//= isset($data['disable_purchase']) ? $data['disable_purchase'] : '0' ?><!--"/>-->
<!--                </div>-->
<!--            </div>-->

            <div class="form-group">
                <label class="col-sm-3 control-label" for="disable_purchase">Purchase Option</label>
               <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= (! isset($data['disable_purchase']) OR $data['disable_purchase'] == '0') ? ' active' : '' ?>">
                        <input type="radio"<?= (! isset($data['disable_purchase']) OR $data['disable_purchase'] == '0') ? ' checked="checked"' : '' ?> value="0" name="disable_purchase">Yes
                    </label>
                    <label class="btn btn-default<?= (isset($data['disable_purchase']) AND $data['disable_purchase'] == '1') ? ' active' : '' ?>">
                        <input type="radio"<?= (isset($data['disable_purchase']) AND $data['disable_purchase'] == '1') ? ' checked="checked"' : '' ?> value="1" name="disable_purchase">No
                    </label>

                   <p class="help-inline">Toggle the add to cart button and price display on a product</p>
                </div>
            </div>

            <!-- Display Price -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="display_price">Display Price</label>
                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= (! isset($data['display_price']) OR $data['display_price'] == '1') ? ' active' : '' ?>">
                        <input type="radio"<?= (! isset($data['display_price']) OR $data['display_price'] == '1') ? ' checked="checked"' : '' ?> value="1" name="display_price">Yes
                    </label>
                    <label class="btn btn-default<?= (isset($data['display_price']) AND $data['display_price'] == '0') ? ' active' : '' ?>">
                        <input type="radio"<?= (isset($data['display_price']) AND $data['display_price'] == '0') ? ' checked="checked"' : '' ?> value="0" name="display_price">No
                    </label>
                </div>
            </div>

            <!-- Offer Price -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="offer_price">Offer Price</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="offer_price" name="offer_price" value="<?=isset($data['offer_price']) ? $data['offer_price'] : ''?>"/>
                </div>
            </div>

            <!-- Display Offer -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="display_offer">Display Offer</label>
                <div class="btn-group col-sm-9" data-toggle="buttons">
					<?php $display_offer = (isset($data['display_offer']) AND $data['display_offer'] == '1') ?>
                    <label class="btn btn-default<?= $display_offer ? ' active' : '' ?>">
                        <input type="radio"<?= $display_offer ? ' checked="checked"' : '' ?> value="1" name="display_offer">Yes
                    </label>
                    <label class="btn btn-default<?= ( ! $display_offer) ? ' active' : '' ?>">
                        <input type="radio"<?= ( ! $display_offer) ? ' checked="checked"' : '' ?> value="0" name="display_offer">No
                    </label>
                </div>
            </div>

            <!-- Featured -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="featured">Featured</label>
                <div class="btn-group col-sm-9" data-toggle="buttons">
					<?php $featured = (isset($data['featured']) AND $data['featured'] == '1') ?>
                    <label class="btn btn-default<?= $featured ? ' active' : '' ?>">
                        <input type="radio"<?= $featured ? ' checked="checked"' : '' ?> value="1" name="featured">Yes
                    </label>
                    <label class="btn btn-default<?= ( ! $featured) ? ' active' : '' ?>">
                        <input type="radio"<?= ( ! $featured) ? ' checked="checked"' : '' ?> value="0" name="featured">No
                </div>
            </div>

            <!-- Builder Product -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="builder">Builder Product</label>
                <div class="col-sm-9">
                    <select class="form-control" name="builder" id="builder">
                        <option value="0" <?=(isset($data['builder']) AND $data['builder'] == '0') ? 'selected' : '' ;?>>None</option>
                        <option value="1" <?=(isset($data['builder']) AND $data['builder'] == '1') ? 'selected' : '' ;?>>Sign Builder</option>
                        <option value="2" <?=(isset($data['builder']) AND $data['builder'] == '2') ? 'selected' : '' ;?>>T-Shirt Builder</option>
                    </select>
                </div>
            </div>

            <!-- Matrix Selection -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="matrix">Matrix Selection</label>
                <div class="col-sm-9">
                    <select class="form-control" name="matrix" id="matrix">
                        <option value="null">Select a Matrix</option>
                        <?php
                        foreach($matrices AS $key=>$matrix):
                            ?>
                            <option value="<?=$matrix['id'];?>" <?=(isset($data['matrix']) AND $matrix['id'] == $data['matrix']) ? 'selected' : '';?>><?=$matrix['name'];?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>

            <!-- Over 18 -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="featured">Over 18 Only</label>
                <div class="col-sm-9">
                    <div class="btn-group " data-toggle="buttons">
						<?php $over_18_only = (isset($data['over_18']) AND $data['over_18'] == '1') ?>
                        <label class="btn btn-default<?= $over_18_only ? ' active' : '' ?>">
                            <input type="radio"<?= $over_18_only ? ' checked="checked"' : '' ?> value="1" name="over_18">Yes
                        </label>
                        <label class="btn btn-default<?= ( ! $over_18_only) ? ' active' : '' ?>">
                            <input type="radio"<?= ( ! $over_18_only) ? ' checked="checked"' : '' ?> value="0" name="over_18">No
                        </label>
                    </div>
                </div>
            </div>

        </div>

        <!-- Categories -->
        <div class="col-sm-9 tab-pane" id="categories_tab">
			<label class="col-sm-5" for="product_categories">Select a category to add this product to</label>
			<div class="col-sm-5">
				<select class="form-control ib-combobox" id="product_categories" data-placeholder="Please select">
					<option value="0"></option>
					<?php foreach ($categories as $category): ?>
						<option value="<?= $category->id ?>" data-image="<?= $category->image ?>" data-name="<?= $category->category ?>"><?= $category->category ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-sm-2">
				<button type="button" class="btn" id="add_category_btn">Add</button>
			</div>

            <table id="categories_table" class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Thumb</th>
                        <th scope="col">Category</th>
                        <th scope="col">Remove</th>
                    </tr>
                </thead>
            </table>

            <!-- Hidden Input -->
            <input type="hidden" name="category_ids" id="product_category_ids" value="<?=isset($data['category_ids']) ? htmlspecialchars($data['category_ids']) : ''?>"/>
        </div>

		<!-- Tags -->
		<div class="col-sm-9 tab-pane" id="tags_tab">

			<div id="tags_list" class="tags_list">
				<?php if (isset($tags) AND count($tags) > 0): ?>
					<?php foreach ($tags as $tag): ?>
						<span class="label label-primary">
							<?= $tag['title'] ?>
            				<a href="#" class="remove_tag">&times;</a>
            				<input type="hidden" name="tag_ids[]" value="<?= $tag['id'] ?>" />
            			</span>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<label class="tag_selector_autocomplete_label" id="tag_selector_autocomplete_label">
				<i class="icon-plus"></i>
				<input type="text" class="tag_selector_autocomplete" id="tag_selector_autocomplete" placeholder="Type the name of a tag" autocomplete="off" />
			</label>
		</div>

		<!-- Details -->
        <div class="col-sm-9 tab-pane" id="details_tab">
            <!-- Brief Description -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="brief_description">Summary</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="brief_description" name="brief_description" rows="4"><?=isset($data['brief_description']) ? $data['brief_description'] : ''?></textarea>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="description">Description</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="description" name="description" rows="4"><?=isset($data['display_offer']) ? $data['description'] : ''?></textarea>
                </div>
            </div>

            <!-- Product Code -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="product_code">Product Code</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="product_code" name="product_code" value="<?=isset($data['product_code']) ? $data['product_code'] : ''?>"/>
                </div>
            </div>

            <!-- Ref. Code -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="ref_code">Ref. Code</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="ref_code" name="ref_code" value="<?=isset($data['ref_code']) ? $data['ref_code'] : ''?>"/>
                </div>
            </div>

            <!-- Weight -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="weight">Weight</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="weight" name="weight" value="<?=isset($data['weight']) ? $data['weight'] : ''?>"/>
                </div>
            </div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="featured">Use Postage Format</label>
				<div class="col-sm-9">
					<div class="btn-group " data-toggle="buttons">
						<?php $use_postage = (empty($data['id']) OR ! empty($data['use_postage'])) ?>
						<label class="btn btn-default<?= $use_postage ? ' active' : '' ?>">
							<input type="radio"<?= $use_postage ? ' checked="checked"' : '' ?> value="1" name="use_postage">Yes
						</label>
						<label class="btn btn-default<?= ( ! $use_postage) ? ' active' : '' ?>">
							<input type="radio"<?= ( ! $use_postage) ? ' checked="checked"' : '' ?> value="0" name="use_postage">No
						</label>
					</div>
				</div>
			</div>

            <!-- Postal Format -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="postal_format_id">Postal Format</label>
                <div class="col-sm-9">
                    <select class="form-control" id="postal_format_id" name="postal_format_id">
                        <?php if ( ! isset($data['postal_format_id']) OR ($data['postal_format_id'] == '') ): ?>
                        	<option value="">-- Select Postal Format --</option>
                        <?php endif; ?>

						<?php $selected_id = (isset($data['postal_format_id']) AND is_numeric($data['postal_format_id']) AND $data['postal_format_id'] > 0) ? $data['postal_format_id'] : 1 ?>

						<?php foreach ($postal_formats as $item): ?>
                        	<option value="<?=$item['id']?>"<?= ($item['id'] == $selected_id) ? ' selected="selected"' : '' ?>><?=$item['title']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Publish -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="publish">Publish</label>
                    <div class="col-sm-9">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?= (! isset($data['publish']) OR $data['publish'] == '1') ? ' active' : '' ?>">
                                <input type="radio"<?= (! isset($data['publish']) OR $data['publish'] == '1') ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
                            </label>
                            <label class="btn btn-default<?= (isset($data['publish']) AND $data['publish'] == '0') ? ' active' : '' ?>">
                                <input type="radio"<?= (isset($data['publish']) AND $data['publish'] == '0') ? ' checked="checked"' : '' ?> value="0" name="publish">No
                                </label>
                    </div>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="out_of_stock">Out of Stock</label>
                <div class="col-sm-9">
                    <div class="btn-group" data-toggle="buttons">
						<?php $out_of_stock = (isset($data['out_of_stock']) AND $data['out_of_stock'] == '1') ?>
                        <label class="btn btn-default<?= $out_of_stock ? ' active' : '' ?>">
                            <input type="radio"<?= $out_of_stock ? ' checked="checked"' : '' ?> value="1" name="out_of_stock">Yes
                        </label>
                        <label class="btn btn-default<?= ( ! $out_of_stock) ? ' active' : '' ?>">
                            <input type="radio"<?= ( ! $out_of_stock) ? ' checked="checked"' : '' ?> value="0" name="out_of_stock">No
                        </label>
                    </div>
                </div>
            </div>

            <!-- Out of stock message -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="out_of_stock_msg">Out of Stock Message</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="out_of_stock_msg" name="out_of_stock_msg" value="<?=isset($data['out_of_stock_msg']) ? $data['out_of_stock_msg'] : ''?>"/>
                </div>
            </div>

            <!-- Size Guide -->
            <div class="form-group">
                <label class="col-sm-3 control-label" for="size_guide">Size Guide</label>
                <div class="col-sm-9">
                    <select class="form-control" id="size_guide" name="size_guide">
                        <option value="">-- Select Size Guide --</option>
                        <?php foreach ($size_guides as $item): ?>
							<option value="<?=$item['name_tag']?>"<?=( isset($data['size_guide']) AND ($data['size_guide'] == $item['name_tag']) ) ? ' selected="selected"' : '' ?>><?=$item['name_tag']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Document
            <div class="form-group">
                <label class="col-sm-3 control-label" for="document">Document</label>
                <div class="col-sm-9">
                    <select class="form-control" id="document" name="document">
                        <? if ( ! isset($data['document']) OR ($data['document'] == '') ): ?>
                        <option value="">-- Select Document --</option>
                        <? endif; ?>

                        <? foreach ($documents as $item): ?>
                        <option value="<?=$item['filename']?>" <?=( isset($data['document']) AND ($data['document'] == $item['filename']) ) ? 'selected="selected"' : '' ?>><?=$item['filename']?></option>
                        <? endforeach; ?>
                    </select>
                </div>
            </div>
			-->
			<input type="hidden" name="document" />

            <!-- Width and height -->
            <div class="form-group">
                <div
                    id="upload_dimensions"
                    class="col-sm-3 control-label"
                    data-title="Minimum upload dimensions"
                    data-content="If the end user can upload images they would like to use on the product. They must obey these dimensions"
                    data-trigger="hover">Minimum upload dimensions</div>
                <div class="col-sm-9">
                    <input type="text" id="upload_min_width" placeholder="W" name="min_width" value="<?= isset($data['min_width']) ? $data['min_width'] : '';  ?>" style="display:inline;width: 3.5em;" /> &times;
                    <input type="text" id="upload_min_height" placeholder="H" name="min_height" value="<?= isset($data['min_height']) ? $data['min_height'] : ''; ?>" style="display:inline;width: 3.5em;" /> px

                </div>
            </div>

        </div>

        <!-- SEO -->
        <div class="col-sm-9 tab-pane" id="seo_tab">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="seo_title">Page Title</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="seo_title" name="seo_title" rows="1"><?=isset($data['seo_title']) ? $data['seo_title'] : ''?></textarea>
                </div>
            </div>

			<!-- URL name -->
			<div class="form-group">
				<label class="col-sm-3 control-label" for="edit_product_url_title">URL Name</label>

				<div class="col-sm-9">
					<div class="input-group">
						<input type="text" class="form-control" id="edit_product_url_title" name="url_title" value="<?= isset($data['url_title']) ? $data['url_title'] : '' ?>" placeholder="Default">
						<span class="input-group-addon" data-content="The name to be used in URLs. If left blank, a value, based on the product name, will be generated" rel="popover" data-original-title="URL Name"><strong>?</strong></span>
					</div>
				</div>
			</div>

			<div class="form-group">
                <label class="col-sm-3 control-label" for="seo_keywords">Keywords</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="seo_keywords" name="seo_keywords" rows="2"><?=isset($data['seo_keywords']) ? $data['seo_keywords'] : ''?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="seo_description">Meta Description</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="seo_description" name="seo_description" rows="2"><?=isset($data['seo_description']) ? $data['seo_description'] : ''?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="footer_editor">Footer Text</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="footer_editor" name="seo_footer" rows="2"><?=isset($data['seo_footer']) ? $data['seo_footer'] : ''?></textarea>
                </div>
            </div>
        </div>

        <!-- Images -->
        <div class="col-sm-12 tab-pane" id="images_tab">
			<div class="edit-product-images-wrapper">
				<h2>Images</h2>
				<!-- Selector & Button -->
				<!--
				<label for="image">Select an image to add below:</label><br/>
				<select class="form-control" id="image">
					<option value="">-- Select Image --</option>

					<? foreach ($images as $item): ?>
					<option value="<?=$item['filename']?>"><?=$item['filename']?></option>
					<? endforeach; ?>
				</select>

				<button type="button" class="btn form-control" id="add_image">Add</button>
				-->

				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Choose existing <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="#" id="add_existing_image_button">Add existing</a></li>
						<li><a href="#" id="add_edit_existing_image_button">Edit existing and add</a></li>
						<?php // <li><a href="#" id="multi_upload_button">Upload new</a></li> ?>
					</ul>
				</div>

				<?= View::factory('multiple_upload') ?>

				<!-- Table -->
				<table class="table table-striped" id="images_table">
					<thead>
						<tr>
							<th>Thumb</th>
							<th>File Name</th>
							<th>Remove</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>

				<!-- Hidden Input -->
				<input type="hidden" name="images" id="images" value="<?=isset($data['images']) ? htmlspecialchars($data['images']) : ''?>"/>
			</div>

			<div class="edit-product-documents-wrapper">
				<h2>Documents</h2>
				<label class="col-sm-5" for="edit_product_add_documents_dropdown">Select a document to add</label>
				<div class="col-sm-5">

					<select class="form-control" id="edit_product_add_documents_dropdown">
						<option value="">-- Please select --</option>
						<?php foreach ($documents as $item): ?>
							<option value="<?= $item['filename'] ?>"><?= $item['filename'] ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-sm-2">
					<button type="button" class="btn" id="add_document_btn">Add</button>
				</div>

				<table id="documents_table" class="table table-striped">
					<thead>
					<tr>
						<th scope="col">Filename</th>
						<th scope="col">Remove</th>
					</tr>
					</thead>
				</table>

				<!-- Hidden input -->
				<input type="hidden" name="documents" id="product_documents" value="<?= isset($data['documents']) ? htmlspecialchars($data['documents']) : ''?>" />

			</div>

        </div>

        <!-- Options -->
        <div class="col-sm-12 tab-pane" id="options_tab">
            <!-- Selector & Button -->
            <h5>Product Options</h5>
            <label for="option">Select an option to add to this product below:</label><br/>
			<div class="col-sm-5">
				<select class="form-control" id="edit_product_add_option_dropdown_id">
					<option value="">-- Select Option --</option>

					<? foreach ($options as $itemId => $item): ?>
						<option value="<?=$itemId?>"><?=$item?></option>
					<? endforeach; ?>
				</select>
			</div>
			<div class="col-sm-2">
				<button type="button" class="btn form-control" id="add_option" >Add</button>
			</div>

            <!-- Table -->
            <table class="table table-striped" id="options_table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Option Values</th>
                        <th>Stock Enabled</th>
                        <th>Required</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <h5>Product Stock Levels</h5>
            <label>Add the quantity of items you hold in stock for this product</label>
            <div class="stock_count">
                <ul>
                    <li><label>Total Online Stock: </label> <input disabled="disabled" class="online_stock" value="<?=(isset($data['quantity'])) ? $data['quantity'] : '';?>"/></li>
                    <li><label>Total Offline Stock: </label> <input disabled="disabled" class="offline_stock" value="0"/></li>
                </ul>
            </div>
            <table id="product_stock_levels" class="table optiontable table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Option Group</th>
                        <th>Option</th>
                        <th>Qty</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Final Price</th>
                        <th>Publish/<br/>Unpublish</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

            <!-- Hidden Input -->
            <input type="hidden" name="options" id="options" value="<?=isset($data['options']) ? htmlspecialchars($data['options']) : ''?>"/>
            <input type="hidden" name="stock_options" id="stock_options" value="<?=isset($data['stock_options']) ? htmlspecialchars($data['stock_options']) : '';?>"/>
        </div>

        <!-- Related -->
        <div class="col-sm-12 tab-pane" id="related_to_tab">
            <!-- Selector & Button -->
            <label for="edit_product_add_related_dropdown">Select another product to relate to this product below:</label>
			<div class="form-group">
				<div class="col-sm-5">
                    <input type="hidden" id="edit_product_add_related_dropdown_id" />
					<input type="text" class="form-control" id="edit_product_add_related_dropdown" placeholder="Select related product" />
				</div>
				<div class="col-sm-7">
					<button type="button" class="btn" id="add_related_to">Add</button><br/>
				</div>

			</div>

            <!-- Table -->
            <table class="table table-striped" id="related_to_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <!-- Hidden Input -->
            <input type="hidden" name="related_to" id="related_to" value="<?=isset($data['related_to']) ? htmlspecialchars($data['related_to']) : ''?>"/>
        </div>

        <!-- YouTube -->
        <div class="tab-pane" id="youtube">
            <button type="button" class="btn btn-success  add_video_button" data-toggle="modal" data-target="#youtube_video_add">Add Video</button>
            <table class="table table-striped" id="youtube_table">
                <thead>
					<tr>
						<th>Video ID</th>
						<th>Delete</th>
					</tr>
                </thead>
                <tbody>
					<?php foreach($youtube_videos AS $key=>$video): ?>
						<tr><td><?=$video['video_id'];?></td><td><i class="icon-remove"></i></td></tr>
					<?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="tab-pane" id="stock_tab">

            <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h3 id="myModalLabel">Edit Stock Options</h3>
						</div>
						<div class="modal-body">
							<ul>
								<li><label>Product:</label> <input disabled="disabled" class="product_name"/></li>
								<li><label>Option Group:</label> <input disabled="disabled" class="option_group"/></li>
								<li><label>Option Value:</label> <input disabled="disabled" class="option_label"/></li>
								<li><label>Stock Amount:</label> <input class="option_quantity"/></li>
								<li><label>Price:</label> <input class="option_price"/></li>
								<li><label>Location:</label> <select class="option_location"><?=Model_Product::get_store_locations();?></select></li>
								<input type="hidden" class="option_product_id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>
								<input type="hidden" class="option_option_id" value=""/>
							</ul>
						</div>
						<div class="modal-footer">
							<button class="btn dismiss" data-dismiss="modal" aria-hidden="true">Close</button>
							<button type="button" class="btn btn-primary save_option">Save changes</button>
						</div>
					</div>
				</div>

			</div>

            <table class="table table-striped" id="stock_options_table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Option Group</th>
                    <th>Option</th>
                    <th>Adjustment Price</th>
                    <th>Location</th>
                    <th>Qty</th>
                    <th>Edit</th>
                </tr>
                </thead>
                <tbody>
                    <?=$options_table;?>
                </tbody>
            </table>
        </div>

		<?php if ($reviews_enabled): ?>

			<div class="tab-pane" id="edit-product-reviews_tab">
				<div class="clearfix">
					<a href="/admin/products/edit_review?product_id=<?= isset($data['id']) ? $data['id'] : '' ?>" class="right btn btn-default" style="margin-bottom: 1em;"><?= __('Add review') ?></a>
				</div>
				<table class="table table-striped dataTable table-condensed" id="edit-product-reviews-table">
					<thead>
						<tr>
							<th scope="col"><?= __('ID') ?></th>
							<th scope="col"><?= __('Title') ?></th>
							<th scope="col"><?= __('Rating') ?></th>
							<th scope="col"><?= __('Author') ?></th>
							<th scope="col"><?= __('Email') ?></th>
							<th scope="col"><?= __('Created') ?></th>
							<th scope="col"><?= __('Modified') ?></th>
							<th scope="col"><?= __('Created by') ?></th>
							<th scope="col"><?= __('Actions') ?></th>
							<th scope="col"><?= __('Publish') ?></th>
						</tr>
					</thead>
				</table>

			</div>
		<?php endif; ?>

		<!-- Sign Builder -->
        <div class="tab-pane" id="sign_builder_tab">
            <div id="sign_builder_area">
                <?php
                $id         = isset($data['id']) ? $data['id'] : '';
                $cms_editor = TRUE;
                include 'front_end/sign_builder.php'
                ?>
            </div>
            <input type="hidden" id="sign_builder_layers_input" name="sign_builder_layers" value="<?= isset($data['sign_builder_layers']) ? trim(htmlspecialchars($data['sign_builder_layers'])) : '' ?>" />
            <input type="hidden" id="sign_builder_data_url" name="sign_builder_data_url" />
        </div>

    </div>

    <!-- Product Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>
    <input type="hidden" id="redirect" name="redirect" value="products"/>
    <div class="well">

		<div class="btn-group">
			<button type="submit" id="save_button" data-redirect="self" class="btn btn-primary save_button">Save</button>
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu">
				<li><button type="submit" data-redirect="products" class="save_button">Save &amp; Exit</button></li>
				<?php if ( ! empty($data['id']) AND Settings::instance()->get('twitter_api_access') == 1): ?>
					<li><a
							href="http://twitter.com/home/?status=<?= urlencode("New product, ".$data['title']."\n".URL::site().'products.html/'.$data['url_title']) ?>"
							type="button"
							class="tweet-item-btn"
							>Tweet</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>

		<?php if ( ! empty($data['id'])): ?>
			<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-product-modal">Delete</button>
		<?php endif; ?>
        <button type="reset" class="btn">Reset</button>
		<a href="/admin/products" class="btn btn-default"><?= __('Cancel') ?></a>
    </div>
</form>

<div id="youtube_video_add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="youtube_video_add" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="youtube_video_modal_title">Add Youtube Video</h3>
			</div>

			<div class="modal-body form-horizontal">
				<div class="form-group">
					<label class="col-sm-4 control-label" for="youtube_video_url">Youtube URL/Video ID</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="youtube_video_url" placeholder="Enter a Youtube Video ID or URL" />
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" id="close_button" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
				<button type="button" id="add_youtube_video_button" class="btn btn-success">Save changes</button>
			</div>
		</div>
	</div>
</div>

<?php if ( ! empty($data['id'])): ?>
	<div class="modal fade" id="delete-product-modal" tabindex="-1" role="dialog" aria-labelledby="delete-product-modal-title">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="delete-product-modal-title">Confirm delete</h4>
				</div>
				<div class="modal-body">
					<p>Are you sure you want to delete this product?</p>
				</div>
				<div class="modal-footer">
					<a href="/admin/products/delete_product/<?= $data['id'] ?>" type="button" class="btn btn-danger">Delete</a>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if ($reviews_enabled): ?>
	<div class="modal fade" tabindex="-1" role="dialog" id="delete-product-review-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="/admin/products/delete_review" method="post">
					<input type="hidden" name="id" />
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

<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('media'); ?>/js/multiple_upload.js"></script>
<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('media'); ?>/js/image_edit.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
		$('[rel="popover"]').popover();
    });
</script>
