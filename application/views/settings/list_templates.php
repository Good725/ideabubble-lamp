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
<table class="table table-striped dataTable" id="list-layouts-table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Created</th>
            <th scope="col">Modified</th>
            <th scope="col">Last Author</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($templates as $template): ?>
            <tr>
                <td><?= $template->id ?></td>
                <td><?= $template->title ?></td>
                <td><?= IbHelpers::relative_time_with_tooltip($template->date_created) ?></td>
                <td><?= IbHelpers::relative_time_with_tooltip($template->date_modified) ?></td>
                <td><?= $template->last_editor->email ?></td>
                <td>
                    <ul class="list-unstyled">
                        <li><a href="/admin/settings/edit_template/<?= $template->id ?>" class="edit-link"><span class="icon-pencil"></span> edit</a></li>
                        <li><a href="/admin/settings/clone_template/<?= $template->id ?>" class="clone-link"><span class="icon-copy"></span> clone</li>
                    </ul>
                </td>
            </tr>
        <?php endforeach ;?>
    </tbody>
</table>
