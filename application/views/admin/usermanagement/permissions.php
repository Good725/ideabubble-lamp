<?php
if(isset($alert)){
?>
<?= $alert ?>
<script>
    remove_popbox();
</script>
<?php
}
?>

<table id="resources_table" class="table table-striped dataTable">
    <thead>
    <tr>
        <th scope="col" style="background-color: #fff;">Resource</th>
        <?php
        foreach ($roles as $role) {
        ?>
        <th scope="col"><?=$role['role']?></th>
        <?php
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($resources as $resource): ?>
        <tr>
            <td><?=$resource['name']; ?></td>
            <?php
            foreach ($roles as $role) {
                echo '<td title="' . $role['role'] . '">';
                foreach ($resource['permissions'] as $permission) {
                    if ($role['role'] == $permission['role']) {
                        if ($permission['has_permission'] == 1) {
                            echo '<span class="icon-ok"></span>';
                        }
                    }
                }
                echo '</td>';
            }
            ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
$("#resources_table a.view").on("click", function(){
    var $table = $(this).next('table');
    if ($table.css('display') == 'none') {
        $table.css('display', '');
        $(this).html('Hide');
    } else {
        $table.css('display', 'none');
        $(this).html('View');
    }
});
var permissions = {};
$("#resources_table input").on("change", function(){
    if (!permissions["_"+$(this).data("resource_id")]) {
        permissions["_"+$(this).data("resource_id")] = {};
    }
    permissions["_"+$(this).data("resource_id")]["_"+$(this).data("role_id")] = this.checked ? 1 : 0;
});
$("#permissions-form").on("submit", function(){
    $.post(
        "/admin/usermanagement/permissions",
        {
            action: "save",
            permissions: JSON.stringify(permissions)
        },
        function (response) {
            alert("Permissions updated");
        }
    );
    return false;
});
</script>