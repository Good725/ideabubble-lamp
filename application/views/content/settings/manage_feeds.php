<?php
if (isset($alert))
    echo $alert;
?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table id="feeds_datatable" class='table table-striped dataTable'>
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Feed Name</th>
            <th scope="col">Plugin</th>
            <th scope="col">Summary</th>
            <th scope="col">Order</th>
            <th scope="col">Edit</th>
            <th scope="col">Publish</th>
            <th scope="col">Delete</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($feeds as $feed): ?>
        <?php
            if ($feed['publish'] == '1')
                $publish_icon = 'icon-ok';
            else
                $publish_icon = 'icon-remove';

            if (strlen($feed['summary']) > 29)
                $feed['cut_summary'] = trim(substr($feed['summary'], 0, 25)).'...';
            else
                $feed['cut_summary'] = $feed['summary'];
        ?>
        <tr>
            <td><a href='<?php echo URL::Site('admin/settings/edit_feed/' . $feed['id']); ?>'><?= $feed['id'] ?></a></td>
            <td><a href='<?php echo URL::Site('admin/settings/edit_feed/' . $feed['id']); ?>'><?= $feed['name'] ?></a></td>
            <td><a href='<?php echo URL::Site('admin/settings/edit_feed/' . $feed['id']); ?>'><?= $feed['plugin'] ?></a></td>
            <td><a href='<?php echo URL::Site('admin/settings/edit_feed/' . $feed['id']); ?>'><?= $feed['cut_summary'] ?></a></td>
            <td><a href='<?php echo URL::Site('admin/settings/edit_feed/' . $feed['id']); ?>'><?= $feed['order'] ?></a></td>
            <td><a href="<?php echo URL::Site('admin/settings/edit_feed/' . $feed['id']); ?>"><i class="icon-pencil"></i></a></td>
            <td id="publish_<?= $feed['id'] ?>" class="publish"><i class="<?= $publish_icon ?>"></i></td>
            <td id="delete_<?= $feed['id'] ?>" class="delete"><i class="icon-remove-circle"></i></td>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function(){

        //Change publish status, AJAX request
        $('.publish').on('click', function(event)
        {
            var click_item = $(this);
            //Get the id from the id attribute
            var str = $(this).attr('id');
            var n=str.split('publish_');

            //Remove alerts, prevent stack
            $('.alert').remove();

            $.get('publish_feed/' + n[1], function(data)
            {
                if(data == 'success')
                {
                    if($(click_item).html() == '<i class="icon-remove"></i>')
                        $(click_item).html('<i class="icon-ok"></i>');
                    else
                        $(click_item).html('<i class="icon-remove"></i>');

                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong> Feed updated</div>';
                    $("#main").prepend(smg);
                }
                else
                {
                    var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Error: </strong> The feed cannot be saved</div>';
                    $("#main").prepend(smg);
                }
            }).error(function()
                {
                    var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Error: </strong> Cannot connect with the server</div>';
                    $("#main").prepend(smg);
                });
        });

        // Delete feed, AJAX request
        $('.delete').on('click', function(event)
        {
            var click_item = $(this);

            $('#confirm_delete').modal();


            $('#btn_delete_yes').on('click', function(event)
            {
                $('#confirm_delete').modal('hide');

                //Get the id from the id attribute
                var str = click_item.attr('id');
                var n=str.split('delete_');

                //Remove alerts, prevent stack
                $('.alert').remove();

                var smg;
                $.get('delete_feed/' + n[1], function(data)
                {
                    if (data == 'success')
                    {
                        click_item.parent().remove();
                        smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong> Feed deleted</div>';
                        $("#main").prepend(smg);
                    }
                    else
                    {
                        smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Error: </strong> The feed cannot be deleted</div>';
                        $("#main").prepend(smg);
                    }
                }).error(function()
                    {
                        smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Error: </strong> Cannot connect with the server</div>';
                        $("#main").prepend(smg);
                    });
            });

        });

    });

</script>

<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Warning!</h3>
			</div>

			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected feed.</p>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" id="btn_delete_no" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
			</div>

		</div>
	</div>
</div>
