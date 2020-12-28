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
<table id="list_departments_table" class="table table-striped dataTable">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Name</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div id="family_menu_wrapper" class="family_menu_wrapper"></div>
