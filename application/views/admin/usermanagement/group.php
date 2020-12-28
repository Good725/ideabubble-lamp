<?php
if (isset($alert))
{
    echo $alert;
}
?>
<?php
if(isset($alert)){
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>
<script>
    if (window.location.hash == '#group-edit-modal') {
        $(document).on("ready", function(){
            $("#group-edit-modal").modal();
            $("[href='#permissions-tab']").click();
        });
    }
</script>
<table id="users_table" class='table table-striped dataTable' data-role_id="<?=@$role['id']?>">
    <thead>
    <tr>
        <th>User ID</th>
        <th>User</th>
        <th>Register Date</th>
        <th>Last Login</th>
        <th>Register Source</th>
    </tr>
    </thead>
    <thead>
    <tr>
        <td>
            <label for="search_id" class="hidden">Search by ID</label>
            <input type="text" id="search_id" class="form-control search_init" name="" placeholder="Search"/>
        </td>
        <td>
            <label for="search_user" class="hidden">Search by user</label>
            <input type="text" id="search_email" class="form-control search_init" name="" placeholder="Search"/>
        </td>
        <td>
            <label for="search_register_date" class="hidden">Search by register date</label>
            <input type="text" id="search_register_date" class="form-control search_init" name="" placeholder="Search"/>
        </td>
        <td>
            <label for="search_last_login" class="hidden">Search by last login</label>
            <input type="text" id="search_last_login" class="form-control search_init" name=""
                   placeholder="Search"/>
        </td>
        <td>
            <label for="search_register_source" class="hidden">Search by register source</label>
            <input type="text" id="search_register_source" class="form-control search_init" name=""
                   placeholder="Search"/>
        </td>
    </tr>
    </thead>
</table>
<script>
$(document).on("click", ".btn.delete", function(){
    var btn = this;
    if (confirm("Are you sure you want to deactivate this user?")) {
        $.post(
            '/admin/usermanagement/user_delete',
            {id : $(btn).data('id')},
            function (response) {
                if (response) {
                    //alert("User has been deactivated");
                    window.location.reload();
                } else {
                    alert("Unknown error");
                }
            }
        )
    }
});
$(document).on("click", ".btn.undelete", function(){
    var btn = this;
    if (confirm("Are you sure you want to activate this user?")) {
        $.post(
            '/admin/usermanagement/user_undelete',
            {id : $(btn).data('id')},
            function (response) {
                if (response) {
                    //alert("User has been activated");
                    window.location.reload();
                } else {
                    alert("Unknown error");
                }
            }
        )
    }
});
</script>

<?php
if (Auth::instance()->has_access('role_edit')) {
    require_once 'group_edit.php';
}
?>

