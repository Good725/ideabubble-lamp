<form id="todo" class="form-horizontal" method="post"
      action="<?=URL::adminaction($action,$todo['todo_id'] ?? "")?>">
	<div class="modal-body">
        <input type="hidden" id="type" name="type" value="Task"/>
		<div class="text-left">

			<div class="form-group">
				<label for="title" class="col-sm-3 control-label">Title</label>
				<div class="col-sm-9">
					<input type="text" id="title" name="title" class="form-control" value="<?= @$todo['title']; ?>">
				</div>
			</div>
            
            <div class="form-group">
                <label for="summary" class="col-sm-3 control-label">Summary</label>

                <div class="col-sm-9">
                    <textarea id="details" name="summary" rows=3
                              class="form-control"><?= @$todo['summary']; ?></textarea>
                </div>
            </div>
            
			<div class="form-group">
				<label for="to_user_id" class="col-sm-3 control-label">Assignee</label>
                    <div class="col-sm-9">
                        <input type="hidden" name="todo-group-student-search-autocomplete-id1"
                               value="<?= @$contact_assignee->get_id() ?>"/>
                        <input type="text" class="form-control datepicker"
                               disabled="disabled" value="<?= @$contact_assignee->get_contact_name() ?>"/>
                    </div>
			</div>

            <div class="form-group" data-todo_type="Task">
                <label class="col-sm-3" for="related_to_id">Regarding</label>
                
                <div class="col-xs-12 col-sm-4">
                    <?php
                    foreach ($related_to_types as $related_to_type) {
                        $options[$related_to_type['id']] = $related_to_type['title'];
                    }
                    echo Form::ib_select(__('Regarding'), 'related_to_id', $options, @$todo['related_to_id'],
                        array('id' => 'related_to_id'));
                    ?>
                </div>
                <div class="col-sm-5">
                    <input type="hidden" id="related_to_value" name="related_to_value"
                           value="<?= @$todo['related_to_value'] ?>">
                    <?= Form::ib_input(null, 'related_to_label', @$todo['related_to_label'],
                        array('id' => 'related_to', 'placeholder' => 'Type to select', 'class' => 'autocomplete')) ?>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="col-sm-3 control-label">Status</label>

                <div class="col-sm-9">
                    <div class="selectbox">
                        <select class="form-control" id="status" name="status">
                            <option <?= (isset($todo['status']) AND $todo['status'] == 'Open') ? 'selected' : ''; ?>
                                    value="Open">Open
                            </option>
                            <option <?= (isset($todo['status']) AND $todo['status'] == 'In Progress') ? 'selected' : ''; ?>
                                    value="In Progress">In Progress
                            </option>
                            <option <?= (isset($todo['status']) AND $todo['status'] == 'Closed') ? 'selected' : ''; ?>
                                    value="Closed">Closed
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            
            <div class="form-group">
				<label for="priority" class="col-sm-3 control-label">Priority</label>

				<div class="col-sm-9">
					<div class="selectbox">
						<select class="form-control" id="priority" name="priority" >
							<option <?=(isset($todo['priority_id']) AND $todo['priority_id'] == 'Normal') ? 'selected' : '';?> value="Normal">Normal</option>
							<option <?=(isset($todo['priority_id']) AND $todo['priority_id'] == 'Low') ? 'selected' : '';?> value="Low">Low</option>
							<option <?=(isset($todo['priority_id']) AND $todo['priority_id'] == 'High') ? 'selected' : '';?> value="High">High</option>
						</select>
					</div>	
				</div>
			</div>
   
			<div class="form-group">
				<label for="date" class="col-sm-3 control-label">Date</label>
				<div class="col-sm-9">
					<input type="text" id="date" name="date" class="form-control datepicker"
						   value="<?= (isset($todo['date']) AND $todo['date'] != 0) ? $todo['date'] : date('Y-m-d'); ?>" />
				</div>
			</div>

		</div>
	</div>

    <div class="modal-footer form-actions form-action-group">
        <button class="btn btn-cancel" id="cancel_button" data-dismiss="modal" aria-hidden="true">Close</button>
        <button id='todo_save' class="btn btn-primary" type="button">Save Changes</button>
        <?php if ( $user['role_id'] == '1' ): ?>
            <a href="#delete_modal" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
        <? endif;?>
    </div>

</form>
