<div class="row-fluid header">
	<h1 class="left"><?= $todo['title']; ?></h1>
</div>

<?php
echo (isset($alert))? $alert : '';
$plugin = isset($todo['related_to_plugin']) ? $todo['related_to_plugin'] : '';
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

<div>
	<ul class="nav nav-tabs">
		<li><a href="#info" data-toggle="tab" id="mapTab">To do info</a></li>
	</ul>
</div>

<div class="tab-content">
    <div class="tab-pane active" id="info">
        <div class="row-fluid">
            <div class="col-sm-12 col-sm-9 col-lg-6">

                <form id="todo" class="form-horizontal" method="post"
                      action="<?=URL::adminaction($action,$todo['todo_id'])?>?<?= "return_url=$return_url&related_plugin_name=$related_plugin_name&related_to_id=$related_to_id" ?>">
                    <input type="hidden" name="todo_id" value="<?= $todo['todo_id']?>">
                    <input type="hidden" name="related_to_text" value="<?= @$todo['related_to_text'] ?>">

                    <fieldset>

                        <div class="form-row">
                            <?= Form::ib_input(__('Title'), 'title', @$todo['title'], array('class' => 'validate[required]')); ?>
                        </div>

                        <div class="form-row">
                            <?= Form::ib_select(__('Assignee'), 'to_user_id[]', $to_user_options, NULL, array('id' => 'to_user_id',  'multiple' => 'multiple')) ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $attributes = array();
                            // If there is edit mode and the user have not right, then disable the form
                            if ( ! is_numeric($todo['todo_id']) || ! Auth::instance()->has_access('todos_edit')) {
                                $attributes['disabled'] = 'disabled';
                                $name = NULL;
                            }
                            else {
                                $name = 'from_user_id';
                            }

                            echo Form::ib_select(__('From user'), $name, $from_user_options, NULL, $attributes);
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $options = array(
                                'Normal' => __('Normal'),
                                'Low'    => __('Low'),
                                'High'   => __('High')
                            );
                            echo Form::ib_select(__('Priority'), 'priority_id', $options, NULL, array('id' => 'priority_id'));
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $options = array(
                                '' => __('Please Select'),
                                'Task'        => __('Task'),
                                'Bug'         => __('Bug'),
                                'Improvement' => __('Improvement'),
                                'Internal' => __('Internal')
                            );
                            $selected = isset($post['type']) ? $post['type'] : '';
                            echo Form::ib_select(__('Type'), 'type_id', $options, $selected, array('id' => 'policy_todo_type'));
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $options = array(
                                'Open'        => __('Open'),
                                'In Progress' => __('In Progress'),
                                'Closed'      => __('Closed')
                            );
                            echo Form::ib_select(__('Status'), 'status_id', $options, NULL, array('id' => 'status_id'));
                            ?>
                        </div>

                        <div class="form-row">
                            <?php
                            $value = (isset($todo['due_date']) AND $todo['due_date'] != 0) ? $todo['due_date'] : date('d-m-Y');
                            $attributes = array('class' => 'datepicker', 'id' => 'due_date');
                            echo Form::ib_input(__('Due Date'), 'due_date', $value, $attributes)
                            ?>
                        </div>

                        <div class="form-row gutters vertically_center">
                            <div class="col-sm-6">
                                <?php
                                $options  = '<option value="">'.__('Please select').'</option>';
                                $options .= html::optionsFromRows('id', 'title', $related_to_list, @$related_to ? $related_to['id'] : @$todo['related_to']);
                                $attributes = array('id' => 'todo_related_to');
                                echo Form::ib_select(__('Regarding'), 'related_to', $options, NULL, $attributes);
                                ?>
                            </div>
							<div class="col-sm-5">
								<?php
                                $related_to_text = ( ! empty($todo['related_to_text'])) ? $todo['related_to_text'] : (isset($todo['related_to_id']) ? $todo['related_to_id'] : '');
                                $related_to_details = Model_Todos::get_related_to_details_by_id($related_plugin_name, $related_to_id);
                                $related_to_text = $related_to_details['value'];
                                $attributes = array('id' => 'todo_related_to_autocomplete', 'placeholder' => __('Type to select'));
                                echo Form::ib_input(NULL, 'related_to_text', $related_to_text, $attributes);
                                ?>
                                <input type="hidden" id="todo_related_to_id" name="related_to_id" value="<?= @$related_to_id ? $related_to_id : (@$todo['related_to_id'] ? $todo['related_to_id'] : ''); ?>" />
                            </div>
                            <div class="col-sm-1">
                                <a href="<?=@$todo['url']?>" id="open_url" target="_blank"<?= @$todo['url'] ? '' : ' style="display:none"' ?>><?= __('GO') ?></a>
                            </div>
                        </div>

                        <div class="form-row">
                            <?php
                            $attributes = array('class' => 'validate[required]', 'rows' => '3', 'id' => 'details');
                            echo Form::ib_textarea(__('Details'), 'details', $todo['details'], $attributes) ?>
                        </div>

                    </fieldset>

                    <div class="form-actions">
                        <button id="todo_save" class="btn btn-primary" type="submit"><?= __('Save') ?></button>
                        <?php if ( $user['role_id'] == '1' ): ?>
                            <a href="#delete_modal" role="button" class="btn btn-outline-danger" data-toggle="modal"><?= __('Delete') ?></a>
                        <?php endif;?>
                        <a class="btn-cancel" href="<?=$return_url?>"><?= __('Cancel') ?></a>
                    </div>

                </form>

            </div>

        </div>
    </div>

    <script type="text/javascript">
        var relates_list = <?=json_encode($related_to_list)?>;
        document.getElementById("status_id").value='<?=$todo['status_id']?>';
        document.getElementById("priority_id").value='<?=$todo['priority_id']?>';

        $(document).ready(function()
        {
            $("#to_user_id").multiselect({numberDisplayed: 4, maxHeight: 300, enableFiltering: true, enableCaseInsensitiveFiltering: true});
            prepare_autocomplete();
        });

        $('#todo_related_to').on('change', function()
        {
           prepare_autocomplete();
        }).trigger('change');

        function prepare_autocomplete()
        {
            var source   = '/admin/todos/autocomplete?related_to=' + $('#todo_related_to').val();

            try {
                $('#todo_related_to_autocomplete').autocomplete('destroy');
            } catch (exc) {

            }

            $("#open_url").css("display", "none");

            $('#todo_related_to_autocomplete').autocomplete(
            {
                source: source,
                select:function (event, ui)
                {
                    var url = "";
                    for (var i = 0 ; i < relates_list.length ; ++i) {
                        if (relates_list[i].id == $('#todo_related_to').val()) {
                            if (relates_list[i].related_open_link_url) {
                                url = relates_list[i].related_open_link_url.replace("#ID#", ui.item.id)
                            }
                        }
                    }
                    $("#open_url").css("display", "none");
                    if (url) {
                        $("#open_url").css("display", "");
                        $("#open_url").attr("href", url);
                    }
                    $('#todo_related_to_id').val(ui.item.id);
                }
            });

        }


        $(document).ready(function(){
            $("form#todo").validationEngine();
        });
    </script>

    <div class="modal fade" id="delete_modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<a class="close" data-dismiss="modal">×</a>

					<h3>Delete task?</h3>
				</div>

				<div class="modal-body">
					<p>Warning: This cannot be undone.</p>
				</div>

				<div class="modal-footer form-actions">
					<form class="form-horizontal" method="GET" action="<?=URL::adminaction('delete_todo',$todo['todo_id'])?>">
                        <button class="btn btn-danger" type="submit" id="delete_submit" name="delete" value="delete" >Delete</button>
                        <a href="#" class="btn-cancel" data-dismiss="modal">Cancel</a>
                    </form>
				</div>
			</div>
		</div>
    </div>
</div>


