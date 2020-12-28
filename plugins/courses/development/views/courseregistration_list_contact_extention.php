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
    <?php foreach ($data['registrations'] as $registration) { ?>
        <tr data-id="<?=$registration['id']?>">
            <td><?=$registration['id']?></td>
            <td><?=$registration['first_name']?></td>
            <td><?=$registration['last_name']?></td>
            <td><?=$registration['schedule']?></td>
            <td><?=$registration['status']?></td>
            <td><?=$registration['updated'] ? $registration['updated'] : $registration['created']?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
