<?php if (!empty($family_member_booking_activities)): ?>
    <h2>Booking Activities</h2>

    <table class="table dataTable family_activities_table" id="family_member_activities_table">
        <thead>
            <tr>
                <th scope="col">Activity ID</th>
                <th scope="col">Time</th>
                <th scope="col">User</th>
                <th scope="col">Action</th>
                <th scope="col">Item</th>
                <th scope="col">Item ID</th>
                <th scope="col">Scope ID</th>
                <th scope="col">Message Detail</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($family_member_booking_activities as $family_member_booking_activity): ?>
                <tr>
                    <td><?= $family_member_booking_activity['id'] ?></td>
                    <td><?= $family_member_booking_activity['timestamp'] ?></td>
                    <td><?= htmlentities($family_member_booking_activity['firstname'] . ' ' . $family_member_booking_activity['surname']) ?></td>
                    <td><?= $family_member_booking_activity['action_name'] ?></td>
                    <td><?= $family_member_booking_activity['item_type_name'] ?></td>
                    <td><?= $family_member_booking_activity['item_id'] ?></td>
                    <td><?= $family_member_booking_activity['scope_id'] ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>There are no activities yet.</p>
<?php endif; ?>