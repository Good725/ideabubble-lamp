<?= (isset($alert))? $alert : ''; ?>
<?php
if(isset($alert)){
?>
	<script>
		remove_popbox();
	</script>
<?php
}
?>
<?php $plugin = isset($todo['related_to_plugin']) ? $todo['related_to_plugin'] : ''; ?>
<form class="form-horizontal" method="post" name="add_todo" action="ajax_update_todo" id="ajax_update_todo">
    <fieldset class="add_policy_todo">
        <legend>Update To Do</legend>
        <input type="hidden" value="<?= $todo_id ?>" name="todo_id" />

        <div class="form-group">
            <div class="col-sm-4 control-label">Title</div>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="title" value="<?=@$todo['title']?>">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 control-label">Assignee</div>
            <div class="col-sm-8">
                <select class="form-control" id="to_user_id" name="to_user_id" >
                    <?= $to_user_options ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 control-label">Priority</div>
            <div class="col-sm-8">
                <select class="form-control" id="priority_id" name="priority_id" >
                    <option value="Normal"<?php if(isset($todo['priority_id']) AND $todo['priority_id'] == 'Normal') echo ' selected="selected"'; ?> >Normal</option>
                    <option value="Low"   <?php if(isset($todo['priority_id']) AND $todo['priority_id'] == 'Low')    echo ' selected="selected"'; ?> >Low</option>
                    <option value="High"  <?php if(isset($todo['priority_id']) AND $todo['priority_id'] == 'High')   echo ' selected="selected"'; ?> >High</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="edit_todo_type" class="col-sm-4 control-label">Type</label>
            <div class="col-sm-8">
                <select class="form-control" id="edit_todo_type" name="type_id">
                    <option value="Task"       <?= (isset($todo['type']) AND $todo['type'] == 'Task')        ? ' selected="selected"' : ''; ?> >Task</option>
                    <option value="Bug"        <?= (isset($todo['type']) AND $todo['type'] == 'Bug')         ? ' selected="selected"' : ''; ?> >Bug</option>
                    <option value="Improvement"<?= (isset($todo['type']) AND $todo['type'] == 'Improvement') ? ' selected="selected"' : ''; ?> >Improvement</option>
                    <option value="Internal"<?= (isset($todo['type']) AND $todo['type'] == 'Internal') ? ' selected="selected"' : ''; ?> >Internal</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 control-label">Status</div>
            <div class="col-sm-8">
                <select class="form-control" id="status_id" name="status_id">
                    <option value="Open"        <?php if(isset($todo['status_id']) AND $todo['status_id'] == 'Open')        echo 'selected="selected"'; ?> >Open</option>
                    <option value="In Progress" <?php if(isset($todo['status_id']) AND $todo['status_id'] == 'In Progress') echo 'selected="selected"'; ?> >In Progress</option>
                    <option value="Closed"      <?php if(isset($todo['status_id']) AND $todo['status_id'] == 'Closed')      echo 'selected="selected"'; ?> >Closed</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 control-label">Due Date</div>
            <div class="col-sm-8">
                <input type="text" name="due_date" class="form-control datepicker" value="<?= (isset($todo['due_date']) AND $todo['due_date'] != 0) ? $todo['due_date'] : date('d-m-Y'); ?>" />
            </div>
        </div>

        <div class="form-group">
            <label for="todo_related_to_plugin" class="col-sm-4 control-label">Regarding</label>
            <div class="col-sm-8">
                <select class="form-control" id="todo_related_to_plugin">
                    <option value="accounts"<?= ($plugin == 'account' OR $plugin == 'accounts') ? ' selected="selected"' : '' ?>>Account</option>
                    <option value="claim"   <?= ($plugin == 'claim')                            ? ' selected="selected"' : '' ?>>Claim</option>
                    <option value="contacts"<?= ($plugin == 'contact' OR $plugin =='contacts')  ? ' selected="selected"' : '' ?>>Contact</option>
                    <option value="policy"  <?= ($plugin == 'policy')                           ? ' selected="selected"' : '' ?>>Policy</option>
                </select>
                <input type="hidden" name="related_to_plugin" value="<?= $plugin ?>" />
                <label class="sr-only" for="todo_related_to_autocomplete">Item ID</label>
                <input type="text" class="form-control" id="todo_related_to_autocomplete" value="<?= $todo['related_to_id'] ?>" placeholder="Type to select" style="display:block;" />
                <input type="hidden" id="todo_related_to_id" name="related_to_id" value="<?= isset($todo['related_to_id']) ? $todo['related_to_id'] : ''; ?>" />
                <input type="hidden" name="contact_id" value="<?= $todo['contact_id'] ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="sr-only" for="todo_details">Details</label>
            <div class="col-sm-12">
                <textarea class="form-control" id="todo_details" name="details" placeholder="Description" style="height:80px;"><?= isset($todo['details']) ? $todo['details'] : '' ?></textarea>
            </div>
        </div>
    </fieldset>
    <div class="add_todo_buttons">
        <input type="button" value="Cancel" class="btn" onclick="popup('close')">
        <input type="button" value="Update To Do" class="btn btn-primary" onclick="update_ajax_todo()">
        <button type="button" id="edit_todo_add_note_btn" class="btn">Add Note</button>
    </div>
</form>

<script type="text/javascript">

    $(document).ready(function()
    {
        prepare_autocomplete();
    });

    $('#todo_related_to_plugin').on('change', function()
    {
        prepare_autocomplete();
    });

    function prepare_autocomplete()
    {
        var plugin   = $('#todo_related_to_plugin').val();
        var source   = '';

        switch (plugin)
        {
            case 'contacts':
            case 'accounts':
                source = '/admin/insuranceoptions/ajax_get_all_customers';
                break;

            case 'claim':
                source = '';
                break;

            case 'policy':
                source = '';
                break;
        }

        $('#todo_related_to_autocomplete').autocomplete(
            {
                source: source,
                select:function (event, ui)
                {
                    var customer_id = ui.item.id;
                    $('#todo_related_to_id').val(customer_id);
                }
            });

    }

    function update_ajax_todo(){
        var form = $('#ajax_update_todo').serialize();
        $.ajax({
            url:'/admin/todos/ajax_update_todo/?ajax=1',
            type:'POST',
            data: form,
            dataType: 'json'
        }).done(function(data){
            if(data['status'] == 'ok'){
                popup('close');
                load_todos('To Do Updated!');
            }
            else{
                $('.header').first().prepend('<div id="save_msg"><div class="alert alert-error"><a data-dismiss="alert" class="close">×</a>Error!</div></div>');
            }
        });
    }

    $('#edit_todo_add_note_btn').on('click', function()
    {
        popup({'action': 'open', 'position': 'fixed'});

        $.ajax(
        {
            url:'/admin/todos/ajax_add_note/' + <?= $todo_id ?>,
            type: 'GET',
            data: {ajax: '1'}
        }).done(function(data)
        {
            var notes_popup = $('#new_js');
            notes_popup.html(data);
            notes_popup.on('click', '#add_todo_note_btn', function()
            {
                $.ajax({url:'/admin/notes/ajax_save_note?ajax=1',
                    type: 'POST',
                    data: {
                        link_id:     <?= $todo_id ?>,
                        type:        6, // plugin_todos
                        customer_id: $('#todo_contact_id').val(),
                        notes:       $('#edit_note_note').val()
                    }
                }).done(function()
                {
                    popup('close');
                });
            });
        });
    });
</script>
