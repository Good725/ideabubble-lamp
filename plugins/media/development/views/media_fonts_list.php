<table class="table table-striped dataTable">
    <thead>
        <tr>
            <th scope="col">Type</th>
            <th scope="col">Filename</th>
            <th scope="col">Size (Kbs)</th>
            <th scope="col">Last Modified</th>
            <th scope="col">Modified By</th>
            <th scope="col">Download</th>
            <th scope="col">Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($media_fonts as $media_document):
                $media_item_path = Model_Media::get_path_to_media_item_admin(
                    Kohana::$config->load('config')->project_media_folder,
                    $media_document['filename'],
                    $media_document['location']
                );
        ?>
            <tr data-font-id="<?=$media_document['id']?>">
                <td>
                    <span class="icon-file">&nbsp;</span>
                </td>
                <td><?=$media_document['filename']?></td>
                <td><?=$media_document['size']?></td>
                <td><?=$media_document['date_modified']?></td>
                <td>modified by</td>
                <td>
                    <?php
                    if (@$selectionDialog) {
                        ?>
                        <a tabindex="0"
                           data-url="<?=$media_item_path?>"
                           data-id="<?=$media_document['id']?>"
                           data-filename="<?=$media_document['filename']?>"><span class="icon-download"></span>&nbsp;select</a>
                        <?php
                    }
                    ?>
                    <a href="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$media_document['filename'], 'fonts'); ?>" target="_blank">
                        <span class="icon-download">&nbsp;</span>
                    </a>
                </td>
                <td id="delete_<?=$media_document['id']?>" class="delete_docs" onclick="delete_media_item(this);"><i class="icon-remove-circle"></i></td>
            </tr>
        <?	endforeach?>
    </tbody>
</table>