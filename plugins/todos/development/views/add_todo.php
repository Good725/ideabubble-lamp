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
<form method="post" name="add_todo" action="ajax_new_<?= (($plugin == 'account') ? 'contact' : $plugin) ?>_todo" id="ajax_new_<?= $plugin ?>_todo" style="padding: 1em;">
    <fieldset class="add_<?= $plugin ?>_todo">
        <legend>New To Do</legend>

        <div class="form-row">
            <?= Form::ib_input(__('Title'), 'title', @$post['title'], array('class' => 'validate[required]', 'id' => 'add_todo_title')) ?>
        </div>

        <div class="form-row">
            <?= Form::ib_select(__('Assignee'), 'to_user_id', $to_user_options, null, array('id' => 'to_user_id')) ?>
        </div>

        <div class="form-row">
            <?php
            $options = array('Normal' => __('Normal'), 'Low' => __('Low'), 'High' => __('High'));
            $selected = isset($post['priority_id']) ? $post['priority_id'] : '';
            echo Form::ib_select(__('Priority'), 'priority_id', $options, $selected, array('id' => 'priority_id'));
            ?>
        </div>

        <div class="form-row">
            <?php
            $options = array('Task' => __('Task'), 'Bug' => __('Bug'), 'Improvement' => __('Improvement'), 'Internal' => __('Internal'));
            $selected = isset($post['type']) ? $post['type'] : '';
            echo Form::ib_select(__('Type'), 'type_id', $options, $selected, array('id' => 'add_todo_type'));
            ?>
        </div>

        <div class="form-row">
            <?php
            $options = array('Open' => __('Open'), 'In Progress' => __('In Progress'), 'Closed' => __('Closed'));
            $selected = isset($post['status_id']) ? $post['status_id'] : '';
            echo Form::ib_select(__('Status'), 'status_id', $options, $selected, array('id' => 'status_id'));
            ?>
        </div>

        <div class="form-row">
            <?php
            $value = (isset($post['due_date']) AND $post['due_date'] != 0) ? $post['due_date'] : date('d-m-Y'); ;
            echo Form::ib_input(__('Due Date'), 'due_date', $value, array('class' => 'datepicker'));
            ?>
        </div>

        <div class="form-row gutters">
            <div class="col-sm-6">
                <?php
                $options = array('accounts' => __('Account'), 'claim' => __('Claim'), 'contacts' => __('Contacts'), 'policy' => __('Policy'));
                $attributes = array('id' => 'todo_related_to_plugin', 'disabled' => 'disabled');
                echo Form::ib_select(__('Regarding'), 'status_id', $options, $plugin, $attributes);
                ?>
                <input type="hidden" name="related_to_plugin" value="<?= $plugin ?>" />
            </div>

            <div class="col-sm-6">
                <label class="sr-only" for="todo_<?= $plugin ?>_id"><?= __('Item ID') ?></label>

                <?php
                $name = (($plugin == 'account') ? 'transaction' : $plugin).'_id';
                $attributes = array('id' => 'todo_'.$plugin.'_id', 'readonly' => 'readonly');
                echo Form::ib_input(null, $name, $id, $attributes);
                ?>

            </div>
        </div>

        <div class="form-row">
            <?php
            if (isset($contact_name)) {
                echo Form::ib_input(null, null, $contact_name, array('readonly' => 'readonly'));
            }
            ?>

            <input type="hidden" value="<?= $contact_id ?>" name="contact_id" />
        </div>

        <div class="form-row">
            <?php
            $value = isset($post['details']) ? $post['details'] : '';
            $attributes = array('class' => 'validate[required]', 'id' => 'todo_details', 'placeholder' => __('Description'));
            echo Form::ib_textarea(__('Details'), 'details', $value, $attributes);
            ?>
        </div>
    </fieldset>

    <div class="add_todo_buttons form-actions">
        <input type="button" value="Create" class="btn btn-primary" onclick="create_todo()">
        <input type="button" value="Cancel" class="btn-cancel" onclick="popup('close')">
    </div>
</form>

<script type="text/javascript">

    function create_todo(){
        var form = $('#ajax_new_<?= $plugin ?>_todo').serialize();
        $.ajax({
            url:'/admin/todos/ajax_new_<?= $plugin ?>_todo/?ajax=1',
            type:'POST',
            data: form
        }).done(function(data)
            {
                if(data == 'ok'){
                    popup('close');
                    $('.header').first().prepend('<div id="save_msg"><div class="alert alert-success"><a data-dismiss="alert" class="close">×</a>To Do added!</div></div>');
                }
                else{
                    $('#new_js').html(data);
                }
            });
    }
</script>
