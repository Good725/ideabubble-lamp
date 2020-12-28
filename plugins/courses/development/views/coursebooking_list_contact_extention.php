<?=(isset($alert)) ? $alert : ''?>
<table class="table table-striped" id="booking_table" data-contact_id="<?@$contact_details['id']?>">
    <thead>
    <tr>
        <th scope="col">#ID</th>
        <th scope="col">Course</th>
        <th scope="col">Category</th>
        <th scope="col">Schedule</th>
        <th scope="col">Total</th>
        <th scope="col">Outstanding</th>
        <th scope="col">Status</th>
        <td scope="col">Last Update</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data['bookings'] as $booking) { ?>
        <tr data-id="<?=$booking['id']?>">
            <td><?=$booking['id']?></td>
            <td><?=$booking['course']?></td>
            <td><?=$booking['category']?></td>
            <td><?=$booking['schedule']?></td>
            <td><?=$booking['total']?></td>
            <td><?=$booking['outstanding']?></td>
            <td><?=$booking['status']?></td>
            <td><?=$booking['updated']?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?=View::factory('coursebooking_details')?>

