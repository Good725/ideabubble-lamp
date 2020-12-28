<?=(isset($alert)) ? $alert : ''?>

<table class="table table-striped dataTable table-condensed " id="list-donations-products-table">
    <thead>
        <tr>
            <th scope="col"><?= __('ID') ?></th>
            <th scope="col"><?= __('Name') ?></th>
            <th scope="col"><?= __('Value') ?></th>
            <th scope="col"><?= __('Status') ?></th>
            <th scope="col"><?= __('Requests') ?></th>
            <th scope="col"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($products as $product) {
    ?>
        <tr data-id="<?=$product['id']?>">
            <td><?= $product['id'] ?></td>
            <td><?= $product['name'] ?></td>
			<td><?= $product['value'] ?></td>
            <td><?= $product['status'] ?></td>
            <td><?= $product['requested'] ? $product['requests'] : 0?></td>
            <td>
                <a class="product-edit" data-id="<?=$product['id']?>"><?= __('Edit')?></a>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>


<div class="modal fade modal-primary" tabindex="-1" role="dialog" id="edit-product-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/donations/product" method="post" id="edit-product-form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Edit Product') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group paid">
                        <label class="form-label" for="code">Code</label>
                        <input class="form-control col-sm-2" type="text" name="id" value=""/>
                    </div>
                    <div class="form-group name">
                        <label class="form-label" for="name">Name</label>
                        <input class="form-control col-sm-2" type="text" name="name" value=""/>
                    </div>
                    <div class="form-group value">
                        <label class="form-label" for="value">Value</label>
                        <input class="form-control col-sm-2" type="text" name="value" value=""/>
                    </div>
                    <div class="form-group status">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-control col-sm-2" name="status">
                            <?=html::optionsFromArray(array('Active' => 'Active', 'Deactive' => 'Deactive'), '')?>
                        </select>
                    </div>
                    <br />
                </div>
                <div class="modal-footer form-actions">
                    <input type="hidden" name="deleted" value="0" />
                    <input type="hidden" name="action" value="save" />
                    <button type="submit" class="btn btn-success" id="save-product-button"><?= __('Save') ?></button>
                    <button type="submit" class="btn btn-danger hidden" id="delete-product-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
