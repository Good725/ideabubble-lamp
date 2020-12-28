<?= (isset($alert)) ? $alert : '' ?>
<table class="table table-striped" id="registrations_table">
    <thead>
        <tr>
            <th scope="col">#ID</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Schedule</th>
            <th scope="col">Status</th>
            <th scope="col">Updated</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
$(document).on("ready", function(){
    var ajax_source = '/admin/courses/student_schedule_registrations_list_data';
    var settings = {
        "sPaginationType" : 'bootstrap'
    };
    $('#registrations_table').ib_serverSideTable(ajax_source, settings);
});
</script>