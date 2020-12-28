<div class="right">
    <a class="btn btn-primary add-eprinter"><span class="plus-icon"></span> <?= __('Create Printer') ?></a>
</div>


<?=(isset($alert)) ? $alert : ''?>
    <table class="table table-striped table-condensed " id="list-eprinters-table">
        <thead>
        <tr>
            <th scope="col"><?= __('ID') ?></th>
            <th scope="col"><?= __('Location') ?></th>
            <th scope="col"><?= __('Tray') ?></th>
            <th scope="col"><?= __('Email') ?></th>
			<th scope="col"><?= __('Published') ?></th>
            <th scope="col"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($eprinters as $eprinter) {
        ?>
            <tr data-id="<?=$eprinter['id']?>">
                <td><?=$eprinter['id']?></td>
                <td><?=$eprinter['location']?></td>
                <td><?=$eprinter['tray']?></td>
                <td><?=$eprinter['email']?></td>
				<td><?=$eprinter['published'] == 1 ? __('Yes') : __('No')?></td>
                <td>
                    <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle btn-actions" type="button" data-toggle="dropdown">
                        <?= __('Actions') ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
						<?php if ($eprinter['published'] == 0) { ?>
                        <li>
                            <a class="publish">
                                <span class="icon-thumbs-up"></span> <?= __('Publish') ?>
                            </a>
                        </li>
						<?php } ?>
						<?php if ($eprinter['published'] == 1) { ?>
                        <li>
                            <a class="unpublish">
                                <span class="icon-thumbs-down"></span> <?= __('Unpublish') ?>
                            </a>
                        </li>
						<?php } ?>
                        <li>
                            <a class="delete">
                                <span class="icon-ban-circle"></span> <?= __('Delete') ?>
                            </a>
                        </li>
                    </ul>
                    </div>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<div class="modal fade" tabindex="-1" role="dialog" id="eprinter-edit-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="/admin/eprinter/save">
                <input type="hidden" name="id" value="" />
                <div class="modal-header">
                    <h4 class="modal-title"><?= __('Printer Details') ?></h4>
                </div>
                <div class="modal-body">

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="eprinter-location">Location</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="eprinter-location"  type="text" name="location" value="" placeholder="Ireland, Spanish, etc..." />
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="eprinter-tray">Tray</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="eprinter-tray" type="text" name="tray" value="" placeholder="Plain, Headed, etc..." />
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="eprinter-email">Email</label>
                        <div class="col-sm-9">
                            <input type="text" name="email" id="eprinter-email" class="form-control" value="" placeholder="email@host.com">
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="col-sm-3 control-label" for="eprinter-published">Published</label>
                        <div>
                            <input type="hidden" name="published" value="0"/><?php // If the checkbox is unticked, this value will get sent to the server  ?>
                            <input type="checkbox" name="published" value="1" <?=( ! isset($eprinter['published']) OR $eprinter['published'] == 1) ? 'checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Yes') ?>" data-off="<?= __('No') ?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="well text-center">
                        <button type="submit" id="eprinter-save" name="action" value="save" class="btn btn-primary continue-button"><?= __('Save') ?></button>
                        <a class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

$(".btn.add-eprinter").on("click", function(){
    display_editor();
});

function display_editor(id)
{
    $("#eprinter-edit-modal input[type=text]").val("");
    $("#eprinter-edit-modal input[type=checkbox]").prop("checked", true);
    $("#eprinter-edit-modal").modal();
}

$(document).on("click", "#list-eprinters-table tr a.publish", function(){
	var id = $(this).parents("tr").data("id");
	$.post(
		"/admin/eprinter/save",
		{id: id, published: 1},
		function (response) {
			window.location.reload();
		}
	)
});

$(document).on("click", "#list-eprinters-table tr a.unpublish", function(){
	var id = $(this).parents("tr").data("id");
	$.post(
		"/admin/eprinter/save",
		{id: id, published: 0},
		function (response) {
			window.location.reload();
		}
	)
});

$(document).on("click", "#list-eprinters-table tr a.delete", function(){
	var id = $(this).parents("tr").data("id");
	$.post(
		"/admin/eprinter/remove",
		{id: id},
		function (response) {
			window.location.reload();
		}
	)
});
</script>