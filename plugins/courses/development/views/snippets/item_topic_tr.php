<tr>
    <td>
        <?= $topic['name'] ?>
        <input type="hidden" name="topic_ids[]" value="<?= $topic['id'] ?>" />
    </td>

    <td><?= $topic['description'] ?></td>

    <td>
        <button type="button" class="btn-link delete_course_topic" data-id="<?= $topic['id'] ?>">Remove</button>
    </td>
</tr>