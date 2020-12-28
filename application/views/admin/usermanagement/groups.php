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
<?php require_once 'group_edit.php'?>

<table id="userroles_table" class="table table-striped">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Role</th>
        <th scope="col">Permissions</th>
        <th scope="col">Description</th>
        <th scope="col">Users</th>
        <th scope="col">Delete</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($roles as $role): ?>
        <tr id="user_<?= $role['id'] ?>">
            <td><?= $role['id'] ?></td>
            <td><a href="<?= URL::Site('admin/usermanagement/group/'.$role['id']); ?>"><?= $role['role']; ?></a></td>
            <td><a href="<?= URL::Site('admin/usermanagement/group/'.$role['id'] . '#group-edit-modal'); ?>"><?= $role['permission_count']; ?></a></td>
            <td><a href="<?= URL::Site('admin/usermanagement/group/'.$role['id']); ?>"><?= $role['description']; ?></a></td>
            <td><?= $role['users'] ?></td>
            <td><i class="icon-remove-circle" onclick="roleDelete(<?= $role['id'] ?>, '<?=$role['role']?>');"></i></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
function roleDelete(id, name)
{
    if (confirm('Are you sure you want to delete ' + name + '?')) {
        $.post(
            '/admin/usermanagement/delete_group',
            {id: id},
            function (result) {
                if (result.message == "This role has been deleted") {
                    $('#user_' + id).remove();
                    alert(name + " has been deleted");
                } else {
                    alert(result.message);
                }
            }
        );
    }
}
</script>
