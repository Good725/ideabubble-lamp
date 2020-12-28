<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">Question</th>
            <th scope="col">Right answer</th>
            <th scope="col">Your answer</th>
            <th scope="col">Correct</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($results as $result): ?>
            <tr>
                <td><?= htmlspecialchars($result['question']) ?></td>
                <td><?= htmlspecialchars($result['right_answer']) ?></td>
                <td><?= htmlspecialchars($result['submitted_answer']) ?></td>
                <td>
                    <?php if (!$result['right_answer']): ?>
                        n/a
                    <?php elseif ($result['correct']): ?>
                        <strong style="color: #3c763d">Yes</strong>
                    <?php else: ?>
                        <span class="text-danger">No</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>