<?= (isset($alert)) ? $alert : '' ?>

<table class="table table-striped dataTable" id="list-themes-table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Template</th>
            <th scope="col">Theme</th>
            <th scope="col">Created</th>
            <th scope="col">Modified</th>
            <th scope="col">Last Author</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($themes as $theme): ?>
            <tr>
                <td><?= $theme->id ?></td>
                <td><?= $theme->template->title ?></td>
                <td><?= $theme->title ?></td>
                <td><?= IbHelpers::relative_time_with_tooltip($theme->date_created) ?></td>
                <td><?= IbHelpers::relative_time_with_tooltip($theme->date_modified) ?></td>
                <td><?= $theme->last_editor->email ?></td>
                <td>
                    <ul class="list-unstyled">
                        <li><a href="/admin/settings/edit_theme/<?= $theme->id ?>" class="edit-link"><span class="icon-pencil"></span> edit</a></li>
                        <li><a href="/admin/settings/clone_theme/<?= $theme->id ?>" class="clone-link"><span class="icon-copy"></span> clone</li>
                    </ul>
                </td>

            </tr>
        <?php endforeach ;?>
    </tbody>
</table>