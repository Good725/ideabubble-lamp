<div class="row-fluid header list_notes_alert">
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
<?php if ( ! empty($notes)): ?>
    <table class="table dataTable educate_notes_table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Created by</th>
                <th scope="col">Modified by</th>
                <th scope="col">Note</th>
                <th scope="col">Updated</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';
            foreach ($notes as $id => $note):
            ?>
                <?php
                $note['author'] = trim($note['author_name'].' '.$note['author_surname']);
                $note['editor'] = trim($note['editor_name'].' '.$note['editor_surname']);
                $note['author'] = ($note['author'] == '') ? $note['author_email'] : $note['author'];
                $note['editor'] = ($note['editor'] == '') ? $note['editor_email'] : $note['editor'];
                ?>
                <tr data-id="<?= $note['id'] ?>">
                    <td><?= $note['id'] ?></td>
                    <td><?= htmlentities($note['author']) ?></td>
                    <td><?= htmlentities($note['editor']) ?></td>
                    <td><?= Text::limit_chars($note['note'], 80); ?></td>
                    <td><?= date($date_format, strtotime($note['date_modified'])); ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php else: ?>
    <p>There are no notes yet.</p>
<?php endif; ?>
