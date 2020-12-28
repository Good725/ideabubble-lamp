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
<table class="table table-striped" id="todo_table">
    <thead>
        <tr>
            <?php if (@$my === "1") : ?>
                <th scope="col">Title</th>
                <th scope="col">Delivery Mode</th>
                <th scope="col">Category</th>
                <th scope="col">Type</th>
                <th scope="col">Schedule</th>
                <th scope="col">Reporter</th>
                <th scope="col">Status</th>
                <th scope="col">Due Date</th>
                <th scope="col">Actions</th>
            <?php else: ?>
                <th scope="col">Title</th>
                <th scope="col">Delivery Mode</th>
                <th scope="col">Category</th>
                <th scope="col">Type</th>
                <th scope="col">Schedule</th>
                <th scope="col">Reporter</th>
                <th scope="col">Assignee</th>
                <th scope="col">Status</th>
                <th scope="col">Due Date</th>
                <th scope="col">Updated</th>
                <th scope="col">Actions</th>
            <?php endif; ?>
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
                <p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected todo.</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <a href="#" data-id="0" class="btn btn-danger" id="btn_delete_yes">Delete</a>
            </div>
        </div>
    </div>
</div>

