<table class="table dataTable dataTable-collapse" id="applications-list-table">
    <thead>
        <tr>
            <th scope="col"><?= __('Created')    ?></th>
            <th scope="col"><?= __('Applicant')  ?></th>
            <th scope="col"><?= __('Course')     ?></th>
            <th scope="col"><?= __('Status')     ?></th>
            <th scope="col"><?= __('Interview')  ?></th>
            <th scope="col"><?= __('Last offer') ?></th>
            <th scope="col"><?= __('Updated')    ?></th>
            <?php if (!empty($access_actions)): ?>
                <th scope="col"><?= __('Actions') ?></th>
            <?php endif; ?>
        </tr>
    </thead>

    <tbody></tbody>
</table>