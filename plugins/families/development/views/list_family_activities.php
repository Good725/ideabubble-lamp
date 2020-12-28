<?php
$contacts_activities = [];
if (!empty($family_activities)) {
    foreach ($family_activities as $contact_id => $family_activity) {
        foreach($family_activity as $contact_activity) {
            $contacts_activities[] = $contact_activity;
        }
    }
}
?>

<?php if (!empty($contacts_activities)): ?>
    <h2>Family activities</h2>

    <table class="table dataTable family_activities_table">
        <thead>
            <tr>
                <th scope="col">Activity ID</th>
                <th scope="col">Time</th>
                <th scope="col">User</th>
                <th scope="col">Action</th>
                <th scope="col">Item</th>
                <th scope="col">Item ID</th>
                <th scope="col">Scope ID</th>
                <th scope="col">User affected</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($family_activities as $contact_id => $family_activity): ?>
               <?php $family_contact_changed = new Model_Contacts3($contact_id); ?>
               <?php foreach($family_activity as $contact_activity): ?>
                   <tr>
                       <td><?= $contact_activity['id'] ?></td>
                       <td><?= $contact_activity['timestamp'] ?></td>
                       <td><?= htmlentities($contact_activity['firstname'] . ' ' . $contact_activity['surname']) ?></td>
                       <td><?= $contact_activity['action_name'] ?></td>
                       <td><?= $contact_activity['item_type_name'] ?></td>
                       <td><?= $contact_activity['item_id'] ?></td>
                       <td><?= $contact_activity['scope_id'] ?></td>
                       <td><?= htmlentities($family_contact_changed->get_contact_name()) ?></td>
                   </tr>
              <?php endforeach; ?>
          <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>There are no activities yet.</p>
<?php endif; ?>
