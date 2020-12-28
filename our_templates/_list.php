<?=(isset($alert)) ? $alert : ''?>
<? // Use the table id ?>
<table class="table table-striped" id="academic_year_table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name / Title</th>
        <th>Publish</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>
    <tbody>
    </tbody>

</table>

<div class="modal fade" id="confirm_delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Warning!</h3>
            </div>
            <div class="modal-body">
                <p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected provider.</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
            </div>
        </div>
    </div>
</div>