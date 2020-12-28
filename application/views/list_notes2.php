<div class="row-fluid header list_notes_alert">
    <?= (isset($alert)) ? $alert : '' ?>
</div>

<table class="table table-striped dataTable notes">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Reference</th>
            <th scope="col">Note</th>
            <th scope="col">Author</th>
            <th scope="col">Date</th>
        </tr>
    </thead>
    <tbody>
    <?php if (is_array(@$notes)) { ?>
    <?php foreach ($notes as $id => $note) { ?>
        <tr data-id="<?= $note['id']        ?>">
            <td><?= $note['id']; ?></td>
            <td><?= trim((isset($note['type']) ? $note['type'] : '') . ' ' . (isset($note['reference_id']) ? $note['reference_id'] : '')); ?></td>
            <td><?= Text::limit_chars(html::chars($note['note']), 200); ?></td>
            <td><?= isset($note['creator']) ? $note['creator'] : ''; ?></td>
            <td><?= isset($note['created']) ? $note['created'] : ''; ?></td>
        </tr>
    <?php } ?>
    <?php } ?>
    </tbody>
</table>
