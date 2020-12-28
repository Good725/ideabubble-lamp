<?= (isset($alert)) ? $alert : '' ?>
<?php
if(isset($alert)){
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
$contact_subtype = Settings::instance()->get('display_sub_contact_types');
?>

<table id="list_organisations_table" class="table table-striped dataTable">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Name</th>
        <?php if($contact_subtype == '1') : ?>
        <th scope="col">Subtype</th>
        <?php endif; ?>
        <th scope="col">Mobile</th>
        <th scope="col">Address</th>
    </tr>
    </thead>
    <thead>
        <tr>
            <th scope="col">
                <label for="search_id" class="hide2">Search by ID</label>
                <input type="text" id="search_id" class="form-control search_init" name="" placeholder="Search"/>
            </th>
            <th scope="col">
                <label for="search_name" class="hide2">Search by name</label>
                <input type="text" id="search_name" class="form-control search_init" name="" placeholder="Search"/>
            </th>
            <?php if ($contact_subtype == '1') : ?>
            <th scope="col">
                <label for="search_role" class="hide2">Search by subtype</label>
                <input type="text" id="search_subtype" class="form-control search_init" name="" placeholder="Search"/>
            </th>
            <?php endif; ?>
            <th scope="col">
                <label for="search_role" class="hide2">Search by subtype</label>
                <input type="text" id="search_mobile" class="form-control search_init" name="" placeholder="Search"/>
            </th>
            <th scope="col">
                <label for="search_role" class="hide2">Search by subtype</label>
                <input type="text" id="search_address" class="form-control search_init" name="" placeholder="Search"/>
            </th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div id="family_menu_wrapper" class="family_menu_wrapper"></div>
