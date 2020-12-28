<?php if(isset($alert)): ?>
    <?= $alert ?>
    <script>remove_popbox();</script>
<?php endif; ?>

<div>
    <div class="form-group clearfix">
        <label class="col-sm-2" for="edit-homework-title"><?= __('Title') ?></label>
        <div class="col-sm-10"><?=$homework['title']?></div>
    </div>

    <div class="form-group clearfix">
        <label class="col-sm-2" for="edit-homework-title"><?= __('Course') ?></label>
        <div class="col-sm-10"><?=$homework['course']?></div>
    </div>

    <div class="form-group clearfix">
        <label class="col-sm-2" for="edit-homework-title"><?= __('Schedule') ?></label>
        <div class="col-sm-10"><?=$homework['schedule'] . ' ' . $homework['datetime_start']?></div>
    </div>

    <div class="form-group clearfix">
        <label class="col-sm-2" for="edit-homework-description"><?= __('Description') ?></label>
        <div class="col-sm-10"><?=$homework['description']?></div>
    </div>

    <div class="form-group clearfix">
        <div class="col-sm-12">
            <h3><?= __('Files') ?></h3>

            <table class="table table-striped table-hover" id="edit-homework-files-table">
                <thead>
                    <tr>
                        <th scope="col"><?= __('Filename') ?></th>
                        <th scope="col"><?= __('Type') ?></th>
                        <th scope="col"><?= __('Created') ?></th>
                        <th scope="col"><?= __('Author') ?></th>
                        <th scope="col"><?= __('Actions') ?></th>
                    </tr>
                </thead>

                <tbody class="sortable-tbody">
                    <?php if (count($homework['files']) > 0): ?>
                        <?php foreach ($homework['files'] as $file): ?>
                            <tr>
                                <td><?= $file['name'] ?></td>
                                <td><?= substr($file['name'], strrpos($file['name'], '.') + 1) ?></td>
                                <td><?= $file['date_created'] ?></td>
                                <td><?= $file['author'] ?></td>
                                <td><a href="/admin/files/download_file?file_id=<?=$file['file_id']?>">download</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5"><?= __('No files have been uploaded for this homework') ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
