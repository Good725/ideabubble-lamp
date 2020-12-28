<fieldset class="col-sm-12" id="notes_editor">
    <legend>Notes</legend>

    <div id="list_notes_alert"><?= (isset($alert))? $alert : '' ?></div>

    <button type="button" class="btn" onclick="add_service_note_popup($('#service_id').val())">Add note</button>
    <?php if (isset($notes) AND ! empty($notes)): ?>
        <table id="extra_notes_table" class="table table-stripped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Note</th>
                    <th scope="col">Added&nbsp;by</th>
                    <th scope="col">Edited&nbsp;by</th>
                    <th scope="col">Created</th>
                    <th scope="col">Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $note): ?>
                    <tr data-note_id="<?=$note['id']?>" onclick="edit_service_note_popup(<?= $note['id'] ?>);">
                        <td><?= $note['id']; ?></td>
                        <td><?= Text::limit_chars($note['note'], 80); ?></td>
                        <td><?= $note['added_by']; ?></td>
                        <td><?= $note['edited_by']; ?></td>
                        <td><?= $note['date_added']; ?></td>
                        <td><?= $note['date_edited']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>There are no notes</p>
    <?php endif; ?>
</fieldset>

<script type="text/javascript">
    $(document).ready(function ()
    {
        $('#extra_notes_table').dataTable({
            "bPaginate": false,
            "bFilter": false,
            "aaSorting": [[ 5, "desc" ]] // Updated column
        });
    });

    function add_service_note_popup(id)
    {
        popup({'action': 'open', 'position': 'fixed'});

        $.ajax({url:'/admin/extra/ajax_add_service_note/'+id,
            type: 'GET',
            data: {ajax: '1'}
        })
            .done(function(data){
                $('#new_js').html(data);
            });
    }

    function edit_service_note_popup(id)
    {
        popup({'action': 'open', 'position': 'fixed'});

        $.ajax({url:'/admin/extra/ajax_edit_listed_note/'+id,
            type: 'GET',
            data: {ajax: '1'}
        })
            .done(function(data){
                $('#new_js').html(data);

            });
    }
</script>