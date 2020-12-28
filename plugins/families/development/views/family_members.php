<table id="list_family_members_table" class="table table-striped dataTable">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Name</th>
        <th scope="col">Role</th>
        <th scope="col">Type</th>
        <th scope="col">Mobile</th>
        <th scope="col">Address</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (isset($family['members'])) {
        foreach ($family['members'] AS $member) {
            ?>
        <tr data-id="<?= $member['id'] ?>"<?= ($member['id'] == @$selected_member_id) ? ' class="selected"' : '' ?>>
            <td><?= $member['id']; ?></td>
            <td><?= $member['fullname']; ?></td>
            <td><?= @$member['role']; ?></td>
            <td><?= @$member['type']; ?></td>
            <td><?= $member['mobile']; ?></td>
            <td><?= trim(trim(trim($member['address1'] . ', ' . $member['address2']), ',')); ?></td>
        </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>