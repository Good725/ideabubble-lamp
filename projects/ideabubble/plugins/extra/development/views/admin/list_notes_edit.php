<form class="col-sm-12 form-horizontal" name="edit_notes" method="post">
    <fieldset class="edit_notes">
        <legend>Notes</legend>
		<h3 class='edit_notes_h3'>
			<?= (isset($service)) ? $service['company_title'] : @$note['table_link_id'].' - #1'.@$note['link_id'] ?>
		</h3>
        <div class="form-group">
            <label class="col-sm-1 control-label" for="note_notes">Notes</label>
            <div class="col-sm-11">
                <textarea class="form-control" id="note_notes" rows="6" name="notes" autofocus="autofocus"><?= @$note['note']; ?></textarea>
            </div>
        </div>
        <?php if ( ! isset($service)): ?>
            <div id='edit_note_dates'>(<strong>created</strong> <?= date('H:i:s d/m/Y', strtotime($note['date_added'])) ?> <strong>last updated</strong> <?= date('H:i:s d/m/Y', strtotime($note['date_edited']))  ?>)</div>
            <input type="hidden" name="id" value="<?= $note['id'] ?>">
        <?php else:?>
            <input type="hidden" name="id" value="<?= $service['id'] ?>">
        <?php endif;?>
    </fieldset>
    <div class="edit_notes_btn">
        <input type="button" onclick="popup('close');" class="btn" value="Cancel" />
        <?php if ( ! isset($service)): ?>
            <input type="button" onclick="save_edit_notes(this.form)" class="btn btn-primary" value="Save">
            <a class="btn" data-toggle="modal" href="#delete_modal_notes">Delete</a>
        <?php else:?>
            <input type="button" onclick="add_service_note(this.form)" class="btn btn-primary" value="Save">
        <?php endif; ?>
    </div>

    <!-- Modal popup -->
    <div class="modal fade" id="delete_modal_notes">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<a class="close" data-dismiss="modal">&times;</a>

					<h3>Delete Note?</h3>
				</div>
				<div class="modal-body">
					<p>Warning: This cannot be undone.</p>
				</div>
				<div class="modal-footer">
					<button class="btn" type="button" name="action" value="delete" onclick="delete_edit_notes(this.form)">Delete</button>
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				</div>
			</div>
		</div>
    </div>
</form>
<style type="text/css">
    .edit_notes legend {
        margin-bottom: 0;
		border-bottom: none;
		width: auto;
		padding: 0 .4em;
    }
    .edit_notes {
        background: #F5F5F5;
        border: 1px solid #000;
        border-radius: 5px;
		margin-bottom: 10px;
        padding: 10px;
    }
    #edit_note_dates{
        margin-left: 82px;
        font-size: 11px;
    }
        /*Modal fix*/
    .modal-backdrop{
        visibility: hidden;
    }
</style>
<script type="text/javascript">
    <?php if ( ! isset($service)): ?>
        function save_edit_notes(element){
            var notes = $(element).find('[name="notes"]').val();
            var id = $(element).find('[name="id"]').val();

            $.ajax({url:'/admin/extra/ajax_edit_listed_note_save?ajax=1',
                type: 'POST',
                data: { notes: notes, id: id }

            })
                .done(function(data){
                    $.ajax({url:'/admin/extra/ajax_load_notes_list?ajax=1',
                        type: 'POST',
                        data: { id: <?=$note['link_id']?> }
                    }).done(function(data_html){
                            $('#notes_editor').html(data_html);
                            if(data == 'success') {
                                show_msg('The note has been updated', 'list_notes_alert', 'success');
                            }
                            else {
                                show_error_msg('Error on update', 'list_notes_alert');
                            }
                        });
                    popup('close');
                });
        }

        function delete_edit_notes(element){
            var id = $(element).find('[name="id"]').val();

            $.ajax({url:'/admin/extra/ajax_edit_listed_note_delete?ajax=1',
                type: 'GET',
                data: { id: id }

            })
                .done(function(data){
                    $.ajax({url:'/admin/extra/ajax_load_notes_list?ajax=1',
                        type: 'POST',
                        data: { id: <?=$note['link_id']?> }
                    }).done(function(data_html){
                            $('#notes_editor').html(data_html);
                            if(data == 'success') {
                                show_msg('The note has been deleted', 'list_notes_alert', 'success');
                            }
                            else {
                                show_error_msg('Error on delete', 'list_notes_alert');
                            }
                            $('.modal-backdrop').hide();
                            popup('close');
                        });

                });
        }
    <?php else:?>
        function add_service_note(element){
            var notes = $(element).find('[name="notes"]').val();
            var id = $(element).find('[name="id"]').val();

            $.ajax({
                url:'/admin/extra/ajax_add_service_note_add?ajax=1',
                type: 'POST',
                data: { notes: notes, customer_id: id, type: '1', link_id: '<?= $service['id']; ?>' } // type 1 = Services

            })
                .done(function(data){
                    $.ajax({url:'/admin/extra/ajax_load_notes_list?ajax=1',
                        type: 'POST',
                        data: { id: <?=@$service['id']?> }
                    }).done(function(data_html){
                            $('#notes_editor').html(data_html);
                            if(data == 'success') {
                                show_msg('The note has been added', 'list_notes_alert', 'success');
                            }
                            else {
                                show_error_msg('Error adding new note', 'list_notes_alert');
                            }
                        });
                    popup('close');
                });
        }
    <?php endif; ?>
</script>
