<?php if ( ! empty($family_member_activities)): ?>
    <h2>Contact activities</h2>

    <table class="table dataTable family_member_activities_table">
        <thead>
            <tr>
                <th scope="col">Activity ID</th>
                <th scope="col">Time</th>
                <th scope="col">User</th>
                <th scope="col">Action</th>
                <th scope="col">Item</th>
                <th scope="col">Item ID</th>
                <th scope="col">Scope ID</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($family_member_activities as $contact_activity): ?>
                <tr>
                    <td><?= $contact_activity['id'] ?></td>
                    <td><?= $contact_activity['timestamp'] ?></td>
                    <td><?= htmlentities($contact_activity['firstname'] . ' ' . $contact_activity['surname']) ?></td>
                    <td><?= $contact_activity['action_name'] ?></td>
                    <td><?= $contact_activity['item_type_name'] ?></td>
                    <td><?= $contact_activity['item_id'] ?></td>
                    <td><?= $contact_activity['scope_id'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>There are no activities yet.</p>
<?php endif; ?>
