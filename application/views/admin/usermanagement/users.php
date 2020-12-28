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
<table id="users_table" class='table table-striped dataTable'>
    <thead>
    <tr>
        <th>User ID</th>
        <th>User</th>
        <th>Role</th>
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
            <label for="search_role" class="hidden">Search by role</label>
            <input type="text" id="search_role" class="form-control search_init" name="" placeholder="Search"/>
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
    <tbody>
    </tbody>
</table>
