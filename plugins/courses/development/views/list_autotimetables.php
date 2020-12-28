<?= (isset($alert)) ? $alert : '' ?>
<?php
if (isset($alert)) {
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>
<table class="table table-striped" id="autotimetables_table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Name</th>
        <th scope="col">Category</th>
        <th scope="col">Location</th>
        <th scope="col">Start Period</th>
        <th scope="col">End Period</th>
        <th scope="col">Edit</th>
        <th scope="col">Publish</th>
        <th scope="col">Actions</th>
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
            <input type="text" id="search_course" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_category" class="hide2">Search by category</label>
            <input type="text" id="search_name" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_location" class="hide2">Search by location</label>
            <input type="text" id="search_location" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_date_start" class="hide2">Search by start period</label>
            <input type="text" id="search_date_start" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_date_end" class="hide2">Search by end period</label>
            <input type="text" id="search_date_end" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="modal fade" id="confirm_delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Warning!</h3>
            </div>

            <div class="modal-body">
                <p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected
                    category.
                    <br>All items like subcategories, courses will be also deleted!</p>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <a href="#" data-id="0" class="btn btn-danger" id="btn_delete_yes">Delete</a>
            </div>
        </div>
    </div>
</div>

