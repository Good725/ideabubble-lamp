<?= (isset($alert)) ? $alert : '' ?>
<table class="table table-striped" id="registrations_table">
    <thead>
    <tr>
        <th scope="col">#ID</th>
        <th scope="col">Schedule</th>
        <th scope="col">Status</th>
        <th scope="col">Updated</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data['registrations'] as $registration) { ?>
        <tr>
            <td><?=$registration['id']?></td>
            <td><?=$registration['schedule']?></td>
            <td><?=$registration['status']?></td>
            <td><?=$registration['updated']?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
