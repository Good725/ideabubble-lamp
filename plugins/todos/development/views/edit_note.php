<style type="text/css">
    .edit_notes{background:#F5F5F5;border:1px solid #000;border-radius:5px;margin:10px 10px 10px 20px;padding:10px;width:896px;}
    .edit_notes textarea{height:100px;width:798px;}
    .edit_notes_btn{margin-left:20px;}
</style>

<div class="row-fluid">
    <form class="form-horizontal" method="post">
        <?= (isset($alert)) ? $alert : ''; ?>
        <?php
		if(isset($alert)){
		?>
			<script>
				remove_popbox();
			</script>
		<?php
		}
		?>
        <fieldset class="edit_notes">
            <legend>Note</legend>
            <h3>Add Note to To Do<?= ( ! is_null($id)) ? ' #'.$id : '' ?></h3>
            <input type="hidden" name="todo_id" value="<?= $id ?>">

            <label for="edit_note_note" style="position:absolute;top:-99999px;left:-99999px;">Enter your note</label>
            <textarea id="edit_note_note" class="field span12" rows="10" name="notes" placeholder="Add your note"></textarea>
        </fieldset>

        <div class="edit_notes_btn">
            <a class="btn" href="">Cancel</a>
            <button type="button" id="add_todo_note_btn" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
