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
<table class="table table-striped dataTable" id="list-layouts-table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Template</th>
            <th scope="col">Layout</th>
            <th scope="col">Created</th>
            <th scope="col">Modified</th>
            <th scope="col">Last Author</th>
            <th scope="col">Publish</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div class="modal" tabindex="-1" role="dialog" id="layouts-delete-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>Are you sure you want to delete this layout?</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="layouts-delete-modal-confirm">Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function()
    {
        var $table = $('#list-layouts-table');
        $table.on('click', '.delete-button', function()
        {
            $('#layouts-delete-modal').modal();
            $('#layouts-delete-modal-confirm').data('id', $(this).data('id'));
        });

        $('#layouts-delete-modal-confirm').on('click', function()
        {
            $.ajax({
                url     : '/admin/settings/ajax_delete_layout/'+$(this).data('id'),
                success : function() {
                    $('#layouts-delete-modal').modal('hide');
                    $table.dataTable().fnDraw();
                }
            });
        });

        $table.on('click', '.publish-toggle', function()
        {
            var $toggle = $(this);

            $.ajax({
                url: '/admin/settings/ajax_toggle_layout_publish/'+$(this).data('id'),
                success: function(result) {
                    if (result != -1) {
                        var icon = (result == 1) ? 'ok' : 'ban-circle';
                        $toggle.html('<span class="icon-'+icon+'">');
                    }
                }
            });
        });
    });

</script>