<?=(isset($alert)) ? $alert : ''?>
<table class="table table-striped" id="booking_table">
    <thead>
        <tr>
			<th scope="col">#ID</th>
            <th scope="col">Course</th>
            <th scope="col">Category</th>
            <th scope="col">Schedule</th>
            <th scope="col">Provider</th>
            <th scope="col">Student</th>
			<th scope="col">Time</th>
            <th scope="col">Last booking</th>
            <th scope="col">Total</th>
            <th scope="col">Outstanding</th>
            <th scope="col">Status</th>
			<th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>

</table>

<?=View::factory('coursebooking_details')?>

<?= View::factory("make_payment_modal") ?>

<script>
$(document).on("ready", function(){
    transactions_list_setup($(document));
})
</script>
