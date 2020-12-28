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
<? // Use the table id ?>
<table class="table table-striped" id="groups_table">
    <thead>
    <tr>
        <th><?=__('ID');?></th>
        <th><?=__('Title');?></th>
        <th><?=__('Created');?></th>
        <th><?=__('Last Modified');?></th>
        <th><?=__('Last Author');?></th>
        <th><?=__('Actions');?></th>
        <th><?=__('Publish');?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($groups as $key=>$group):?>
        <tr data-id="<?=$group['id']?>">
            <td><a href="/admin/surveys/add_edit_group/<?=$group['id']?>"><?= $group['id']; ?></a></td>
            <td><a href="/admin/surveys/add_edit_group/<?=$group['id']?>"><?= $group['title']; ?></a></td>
            <td><a href="/admin/surveys/add_edit_group/<?=$group['id']?>"><?= IbHelpers::relative_time_with_tooltip($group['created_on']); ?></a></td>
            <td><a href="/admin/surveys/add_edit_group/<?=$group['id']?>"><?= IbHelpers::relative_time_with_tooltip($group['updated_on']); ?></a></td>
            <td><a href="/admin/surveys/add_edit_group/<?=$group['id']?>"><?= $group['user']; ?></a></td>
            <td>
                <div class="dataTable-list-actions">
                    <a href="/admin/surveys/add_edit_group/<?=$group['id']?>"><?= __('Edit') ?></a>
                    <a href="#" class="delete" data-id="<?=$group['id']?>"><?= __('Delete') ?></a>
                </div>
            </td>
            <td>
                <?php if($group['publish'] == 1): ?>
                    <a href="#" class="publish" data-publish="1" data-id="<?=$group['id']?>"><i class="icon-ok"></i></a>
                <?php else: ?>
                    <a href="#" class="publish" data-publish="0" data-id="<?=$group['id']?>"><i class="icon-ban-circle"></i></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>

</table>

<div class="modal fade" id="confirm_delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">Ã—</button>
                <h3>Warning!</h3>
            </div>
            <div class="modal-body">
                <p>This action is
                    <strong>irreversible</strong>! Please confirm you want to delete the selected provider.</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal"><?=__('Cancel');?></a>
                <a href="#" class="btn btn-danger" id="btn_delete_yes"><?=__('Delete');?></a>
            </div>
        </div>
    </div>
</div>
