<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>
<?php
if (isset($alert)) {
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>
<table class="table table-striped" id="course_table">
    <thead>
    <tr>
        <th scope="col">Title</th>
        <th scope="col">Code</th>
        <th scope="col">Year</th>
        <th scope="col">Level</th>
        <th scope="col">Category</th>
        <th scope="col">Subject</th>
        <th scope="col">Type</th>
        <th scope="col">Provider</th>
        <th scope="col">Topics</th>
        <th scope="col">Edit</th>
        <th scope="col">Publish</th>
        <th scope="col">Actions</th>
    </tr>
    </thead>
    <thead>
    <tr>
        <th scope="col">
            <label for="search_title" class="hide2">Search by ID</label>
            <input type="text" id="search_title" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_code" class="hide2">Search by Code</label>
            <input type="text" id="search_code" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_year" class="hide2">Search by Year</label>
            <input type="text" id="search_syear" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_level" class="hide2">Search by Level</label>
            <input type="text" id="search_level" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_category" class="hide2">Search by Category</label>
            <input type="text" id="search_category" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_subject" class="hide2">Search by Subject</label>
            <input type="text" id="search_start_date" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_type" class="hide2">Search by Type</label>
            <input type="text" id="search_available" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_provider" class="hide2">Search by Prover</label>
            <input type="text" id="search_provider" class="form-control search_init" name="" placeholder="Search"/>
        </th>
        <th scope="col">
            <label for="search_topics" class="hide2">Search by Topics</label>
            <input type="text" id="search_topics" class="form-control search_init" name="" placeholder="Search"/>
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
                <p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected course.
                </p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <a href="#" data-id="0" class="btn btn-danger" id="btn_delete_yes">Delete</a>
            </div>
        </div>
    </div>
</div>

