<div id="browse_images_wrapper">
    <div id="browse_files_modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <?php if ($location == 'docs'): ?>
                        <h3>Available files</h3>
                    <?php elseif (in_array($location, ['audio', 'audios', 'videos'])): ?>
                        <h3>Available media</h3>
                    <?php else: ?>
                        <h3>Available images</h3>
                    <?php endif; ?>
                </div>

                <div class="modal-body">
                    <div class="control-group browse-images-search-wrapper">
                        <label class="col-sm-offset-6 col-sm-2 control-label" for="browse-images-search">Search</label>
                        <div class="col-sm-4 controls">
                            <input type="text" class="form-control" id="browse-images-search" />
                        </div>
                    </div>

                    <div class="browse-images-list<?= ($location != ''&& $location != 'content') ? ' browse-images-noeditor' : '' ?>">
                        <ul>
                            <?php foreach ($images as $image): ?>
                                <?php if (in_array($location, ['audios', 'videos'])): ?>
                                    <li class="py-1" data-id="<?= $image['id'] ?>">
                                        <button type="button" class="button--plain" data-id="<?= $image['id'] ?>">
                                            <?php
                                            switch ($location) {
                                                case 'audios' : $icon = 'volume-up';   break;
                                                case 'videos' : $icon = 'play-circle'; break;
                                                default       : $icon = 'file-o';      break;
                                            }
                                            ?>
                                            <span class="icon-<?= $icon ?>"></span>
                                            <span class="filename"><?= $image['filename'] ?></span>
                                        </button>
                                    </li>
                                <?php else: ?>
                                    <li class="image_thumb<?= ($image['mime_type'] == 'image/svg+xml') ? ' svg_thumb' : '' ?>" tabindex="0" data-id="<?= $image['id'] ?>">
                                        <img src="<?= Model_Media::get_image_path($image['filename'],(($location == '') ? 'content' : $location).'/_thumbs_cms'); ?>" alt="" />
                                        <p><span class="filename"><?= $image['filename'] ?></span></p>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn">Select</a>
                    <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
        <style type="text/css">
            .browse-images-list {
                clear: both;
                max-height: 450px;
                overflow-y: scroll;
            }
            .image_thumb{display:inline-block;list-style-type:none;padding:10px;text-align:center;vertical-align:top;}
            .image_thumb .filename{display:block;word-wrap:break-word;width:80px;}
        </style>
    </div>
</div>




