<div class="mail-title">
    <h2>View your Attachments</h2>
    <a href="javascript:void(0)" class="basic_close"><span class="fa icon-times" aria-hidden="true"></span></a>
</div>
<?php $see_underdeveloped = Auth::instance()->has_access('messaging_see_under_developed_features'); ?>

<div class="file-detail">
    <h4>Files</h4>
    <div class="table-scroll">
        <table class="table-border">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Download</th>
                    <?php if ($see_underdeveloped): ?>
                        <th>View</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($message['attachments'] as $attachment): ?>
                    <?php unset($src); ?>
                    <tr>
                        <td>
                            <?php if ($attachment['content_encoding'] == 'base64'): ?>
                                <?php $src = 'data: '.$attachment['type'].';base64,'.$attachment['content']; ?>
                                <img src="<?= $src ?>" />
                            <?php endif; ?>
                            <?= $attachment['name'] ?>
                        </td>
                        <td>
                            <?php if ( ! empty($src)): ?>
                            <a href="<?= $src ?>" download="<?= $attachment['name'] ?>">
                                <span class="fa icon-cloud-download" aria-hidden="true"></span>
                            </a>
                            <?php endif; ?>
                        </td>
                        <?php if ($see_underdeveloped): ?>
                            <td><a href="#"><span class="fa icon-eye" aria-hidden="true"></span></a></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($see_underdeveloped): ?>
        <a href="#" class="download-btn">Download All</a>
    <?php endif; ?>
</div>

