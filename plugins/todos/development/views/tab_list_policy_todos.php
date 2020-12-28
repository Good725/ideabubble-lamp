<div class="row-fluid header" id="list_todo_alert">
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
</div>
<input type="hidden" id="todo_claim_id" value="<?= isset($claim_id) ? $claim_id : '' ?>" />
<?php if ( ! empty($todos)): ?>
    <table class='table table-striped dataTable'>
        <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col">From</th>
                <th scope="col">To</th>
                <th scope="col">Title</th>
                <th scope="col">Status</th>
                <th scope="col">Priority</th>
                <th scope="col">Type</th>
                <th scope="col">Due Date</th>
                <th scope="col">Created</th>
                <th scope="col">Updated</th>
                <th scope="col">Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($todos as $id => $todo): ?>
                <tr>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><span style="background-color:<?=$todo['status_color']?>; color:<?=$todo['status_color']?>"><?=$todo['status_order']?>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= $todo['from_user_name']; ?></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= $todo['to_user_name'];   ?></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= $todo['title'];          ?></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= $todo['status_id'];      ?></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= $todo['priority_id'];    ?></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= $todo['type_id'];        ?></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= DATE::ymdh_to_dmy($todo['due_date']);    ?></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= DATE::ymd_to_dmy($todo['date_created']); ?></td>
                    <td onclick="list_todo_edit(<?=$todo['todo_id']?>)"><?= DATE::ymd_to_dmy($todo['date_updated']); ?></td>
                    <td>
                        <a
                            data-content="<?= Model_Note::get_notes_as_based_on(array('link_id', 'table_link_id'), array($todo['todo_id'], 6), 'comments', '=', NULL, 5); ?>"
                            rel="popover" data-placement="left" data-original-title="Notes">
                            <i class="icon-book"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif;?>
<script type="text/javascript">
    $(document).find('[rel="popover"]').popover({ html : true });
    function list_todo_edit(id){

        popup({'action': 'open', 'position': 'fixed', 'width': '425px'});

        $.ajax({url:'/admin/todos/ajax_edit_listed_todo/' + id,
            type: 'GET',
            data: {ajax: '1'}
        })
            .done(function(data){
                $('#new_js').html(data);
                $('.datepicker').datepicker({
                    format:'dd-mm-yyyy'
                });

            });
    }
</script>
