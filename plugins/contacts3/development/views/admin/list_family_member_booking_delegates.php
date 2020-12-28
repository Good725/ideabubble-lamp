<?php if (!empty($delegates)): ?>
    <h2>Delegates</h2>

    <table class="table dataTable family_delegates_table" id="family_member_delegates_table">
        <thead>
            <tr>
                <th scope="col">Contact ID</th>
                <th scope="col">First name</th>
                <th scope="col">Last name</th>
                <th scope="col">Email</th>
                <th scope="col">Mobile</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($delegates as $delegate): ?>
                <tr>
                    <td><?= $delegate['id'] ?></td>
                    <td><?= $delegate['first_name'] ?></td>
                    <td><?= $delegate['last_name'] ?></td>
                    <td><?= $delegate['email'] ?></td>
                    <td><?= $delegate['mobile'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>There are no activities yet.</p>
<?php endif; ?>