<table class="table table-striped dataTable border-top">
    <thead>
        <tr>
            <th scope="col">Delegate name</th>
            <th scope="col">Mobile</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($attendees as $attendee): ?>
            <tr data-id="<?= $attendee->id ?>">
                <td><?= htmlspecialchars($attendee->get_full_name()) ?></td>
                <td><?= $attendee->get_mobile_number(' ') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>