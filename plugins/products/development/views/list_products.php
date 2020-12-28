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
<table class="table table-striped dataTable table-condensed " id="products_table">
    <thead>
        <tr>
			<th scope="col">Image</th>
            <th scope="col">ID</th>
			<th scope="col">Product</th>
            <th scope="col">Code</th>
            <th scope="col">Category</th>
            <th scope="col">Quantity</th>
            <th scope="col">Order</th>
			<th scope="col">Publish</th>
			<th scope="col">Featured</th>
			<th scope="col">Date Added</th>
			<th scope="col">Last Modified</th>
			<th scope="col">Actions</th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th scope="col">
                <input type="text" id="search_id" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <input type="text" id="search_id" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <input type="text" id="search_title" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <input type="text" id="search_code" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <input type="text" id="search_category" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <input type="text" id="search_quantity" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th scope="col">
                <input type="text" id="search_order" class="form-control search_init" name="" placeholder="Search" />
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
	<tbody></tbody>
</table>
<?php if (isset($products_table_options)): ?>
    <input type="hidden" id="products_table_options" value="<?= htmlspecialchars($products_table_options) ?>" />
<?php endif; ?>
<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Warning!</h3>
			</div>
			<div class="modal-body" id="warning_message"><!-- DO NOT ENTER TEXT HERE --></div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete">Delete</a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade manage-product-categories-modal" id="manage-product-categories-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h2 class="modal-title">Manage categories</h2>
			</div>
			<div class="modal-body">
				<input type="hidden" id="manage-product-categories-product-id" />
				<h3 class="col-sm-12" id="manage-product-categories-product-name"></h3>
				<div class="manage-product-categories-lists clearfix">
					<h4 class="col-sm-6">All categories</h4>
					<h4 class="col-sm-6">Product categories</h4>
					<div class="col-sm-6">
						<ul class="manage-product-categories-list" id="manage-product-categories-excluded">
							<?php foreach ($categories as $category): ?>
								<li class="btn btn-default btn-block"
									data-id="<?= $category->id ?>"
									role="button"
									tabindex='0"'><?= $category->category ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="col-sm-6">
						<ul class="manage-product-categories-list" id="manage-product-categories-included">
						</ul>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="manage-product-categories-save">Save changes</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
