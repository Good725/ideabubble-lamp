<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>

<form id="homework-edit" name="homework-edit" class="form-horizontal custom-form-horizontal" method="post" action="/admin/homework/edit/<?= $homework['id'] ?>">
    <input type="hidden" name="id" value="<?= $homework['id'] ?>" />

    <div class="col-md-4  align-left">
        <h2 class="text-primary">Details</h2>

        <div class="form-group">
            <label class="col-xs-12" for="edit-homework-title"><?= __('Title') ?></label>
            <div class="col-xs-12">
                <input type="text" class="form-control ib-title-input required" id="edit-homework-title" name="title"
                       placeholder="<?= __('Title') ?>" value="<?= htmlspecialchars($homework['title']) ?>"/>
            </div>
        </div>

<!--
     <div class="form-group">
            <label class="col-xs-12" for="published">Publish</label>

            <div class="col-xs-12">
                <div class="selectbox">
                    <select class="form-control" name="published" id="published">
                        <option value="1"<?php /*if ($homework['published'] == 1) echo ' selected="selected"'; */?>>Yes</option>
                        <option value="0"<?php /*if ($homework['published'] == 0) echo ' selected="selected"'; */?>>No</option>
                    </select>
                </div>
            </div>
        </div>
-->

        <div class="form-group">
            <label class="col-xs-12" for="edit-homework-title"><?= __('Schedule') ?></label>

            <div class="col-xs-12">
                <input data-schedule-id="<?= $homework['course_schedule_event_id'] ?>" type="text"
                       class="form-control ib-title-input required" id="edit-homework-schedule"
                       placeholder="<?= __('Schedule') ?>" value="<?= $homework['schedule'] ?>"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12" for="edit-homework-schedule-event-id"><?= __('Date/Time') ?></label>
            <div class="col-xs-12">
                <div class="selectbox">
                    <select class="form-control required" id="edit-homework-schedule-event-id" name="schedule_event_id"
                            placeholder="<?= __('Date') ?>">
                        <option value="">Select</option>
                        <?=html::optionsFromRows('value', 'label', $events, $homework['course_schedule_event_id']);?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12" for="edit-homework-description"><?= __('Description') ?></label>

            <div class="col-xs-12">
                <textarea class="form-control ib-title-input required" id="edit-homework-description" name="description"
                          placeholder="<?= __('Description') ?>"><?=html::entities($homework['description'])?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12">Publish</label>
            <div class="col-sm-12">
                <div class="btn-group btn-group-slide" data-toggle="buttons">
                    <label class="btn btn-plain <?php if ($homework['published'] == 1) echo 'active';?>">
                        <input <?php if ($homework['published'] == 1) echo 'checked="checked"';?> value="1" name="published" type="radio">Yes
                    </label>
                    <label class="btn btn-plain <?php if ($homework['published'] == 0) echo 'active';?> ">
                        <input value="0" <?php if ($homework['published'] == 0) echo 'checked="checked"';?>  name="published" type="radio">No
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 add-work-from">
        <h2 class="text-primary">Add Homework</h2>

        <div>
            <div id="multiple_upload_wrapper">
                <script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('media'); ?>/js/h5utils.min.js"></script>

                <div id="drag_and_drop_area" class="graybg">
                    <input type="hidden" name="test" value="test3"/>

                    <div id="upload_text">
                        <p id="dnd_notice"><span class="fa fa-cloud-upload" aria-hidden="true"></span> Drag and drop files here</p>
                        <p class="sub-title-txt">or select an option below</p>

                        <p id="file_upload_button">
                            <button type="button" class="btn greenbtn"><span class="fa fa-folder" aria-hidden="true"></span> Upload</button>
                            <input type="file" multiple="multiple" name="images[]"/>
                        </p>
                    </div>
                </div>

                <div id="file_previews"></div>

                <script>
                    $(document).ready(function() {
                        if (document.getElementById('multiple_upload_wrapper')) {
                            uploader_ready();
                        }
                    });

                    var files_to_upload = null;

                    function uploader_ready()
                    {
                        var file_upload_button = $('#file_upload_button'),
                            drop_area = document.getElementById('drag_and_drop_area'),
                            dnd_supported = 'draggable' in document.createElement('span');

                        if (dnd_supported)
                        {
                            drop_area.ondragover = function()
                            {
                                $(this).addClass('hover');
                                return false;
                            };
                            drop_area.ondragend = function()
                            {
                                $(this).removeClass('hover');
                                return false;
                            };
                            drop_area.ondrop = function(e)
                            {
                                e.preventDefault();
                                e.stopPropagation();
                                $(this).removeClass('hover');

                                files_to_upload = e.dataTransfer.files;

                                var image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];

                                file_upload(files_to_upload);
                            }
                        }
                        else
                        {
                            $('#dnd_notice').html('Your browser does not support drag and drop. Please use the button below.');
                        }

                        file_upload_button.find('button').click(function()
                        {
                            $(this).next().click();
                        });

                        file_upload_button.find('input[type="file"]').on('change', function(event)
                        {
                            event.stopPropagation();
                            event.preventDefault();
                            files_to_upload = this.files;

                            file_upload(files_to_upload);
                        });

                        function file_upload(files)
                        {
                            $.each(files, function(key, value)
                            {
                                var error_message = '';
                                var data = new FormData();

                                data.append(key, value);

                                $.ajax(
                                    {
                                        'url': '/admin/homework/upload',
                                        'type': 'POST',
                                        'data': data,
                                        'cache': false,
                                        'dataType': 'json',
                                        'async': true,
                                        'processData': false,
                                        'contentType': false,
                                        'success': function(data, textStatus, jqXHR)
                                        {
                                            if (typeof data.error == 'undefined' && data.errors.length == 0)
                                            {
                                                if (data.errors.length != 0)
                                                {
                                                    for (var j = 0; j < data.errors.length; j++)
                                                    {
                                                        error_message += data.errors[j] + '<br />';
                                                    }
                                                }
                                                else if (data.files.length < 1)
                                                {
                                                    upload_error(value, 'file not found');
                                                }
                                                else
                                                {
                                                    var original_filename, upload_item;
                                                    // Add thumbnail replace progress bar with details button
                                                    var files_list = $("#edit-homework-files-table tbody");
                                                    for (var i = 0; i < data.files.length; i++)
                                                    {

                                                        files_list.append(
                                                            '<tr class="edit-homework-files-new">' +
                                                                '<td><input type="hidden" name="has_file_id[]" value="' + data.files[i].file_id + '" />' + data.files[i].name + '</td>' +
                                                                '<td></td>' +
                                                                '<td></td>' +
                                                                '<td></td>' +
                                                                '<td></td>' +
                                                                '</tr>'
                                                        );
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                var message = (typeof data.error != 'undefined') ? data.error : data.errors[0];
                                                upload_error(value, message);
                                            }
                                        },
                                        error: function(jqXHR, textStatus, errorThrown)
                                        {
                                            upload_error(value, errorThrown);
                                        }
                                    });

                            });
                            files_to_upload = null;
                            file_upload_button.find('input[type="file"]').val('');
                        }

                        function upload_error(image, error_message)
                        {
                            var filename = image['name'].substr(image['name'].lastIndexOf('/') + 1);
                            var upload_item = $('.upload_item[data-name="' + filename + '"]').last();
                            upload_item.addClass('error');
                            upload_item.find('.uploading_notice, .uploaded_notice')
                                .html('Error: ' + error_message)
                                .attr('class', 'upload_error_message');
                            alert(error_message);
                        }
                    }

                    // When the user tries to save, display a modal asking the user to confirm the files are okay.
                    $('#homework-edit').on('submit', function()
                    {
                        var button_clicked  = document.activeElement;
                        var new_files_added = ($('.edit-homework-files-new').length > 0);
                        var confirmed_okay  = (button_clicked && button_clicked.id == 'edit-homework-modal-family_friendly-confirm');

                        // Only show the modal if the user has added new files.
                        // Don't try showing the modal a second time if the user is clicking "Confirm".
                        if (new_files_added && ! confirmed_okay)
                        {
                            // Ensure the "Confirm" button has the same action as the clicked save button ("Save" or "Save & Exit")
                            var action = 'save';
                            if ($(button_clicked).hasClass('edit-homework-save')) {
                                action = button_clicked.value;
                            }
                            $('#edit-homework-modal-family_friendly-confirm').val(action);

                            // Show the modal
                            $('#edit-homework-modal-family_friendly').modal();

                            // Stop the form from submitting, since the modal is being shown instead.
                            return false;
                        }
                    });

                </script>

                <style type="text/css">
                .fa {
                  display: inline-block;
                  font: normal normal normal 14px/1 FontAwesome;
                  font-size: inherit;
                  text-rendering: auto;
                  -webkit-font-smoothing: antialiased;
                  -moz-osx-font-smoothing: grayscale;
                }
                .fa-cloud-upload:before {
                  content: "\f0ee";
                }
                .fa-folder:before {
                  content: "\f07b";
                }
                .upload_error_message {
                    float: right;
                }

                .upload_details {
                    clear: both;
                    border-top: 1px solid #AAA;
                }

                .upload_item {
                    background: #F6F6F6;
                    border: solid #AAA;
                    border-width: 0 1px 1px;
                    padding: 15px 15px 10px;
                }

                .upload_item:first-child {
                    border-top: 1px solid #AAA;
                }

                .upload_item:after {
                    clear: both;
                    content: '';
                    display: table;
                }

                .upload_item.error {
                    background: #FDD;
                }

                .upload_item .details_button {
                    color: #00C;
                    cursor: pointer;
                    float: right;
                    font-size: 14px;
                }

                .upload_name {
                    float: left;
                }

                .uploaded_image {
                    float: left;
                    margin-bottom: 5px;
                    margin-right: 5px;
                }

                .uploaded_notice {
                    color: #5679da;
                    float: right;
                }
                .greenbtn{background:#96c511;border: none; border-radius: 2px; margin-top: 15px; box-shadow: 0 2px 0 #ddd; text-align: center; color: #fff;text-shadow:none;}
                .greenbtn i{display: block; color: #fff; font-size: 15px;}
                .greenbtn:hover{background: #0e2a6b; color: #fff;}

                .uploaded_image img {
                    width: 40px;
                }

                #drag_and_drop_area {
                    border: 5px dashed #CCC;
                    height: 200px;
                    margin: 5px 5px 20px;
                    position: relative;
                }

                #drag_and_drop_area.hover {
                    border: 5px dashed #FCC;
                }

                #upload_text {
                    color: #767a7b;
                    margin-left: -150px;
                    position: absolute;
                    top: 40px;
                    left: 50%;
                    text-align: center;
                    width: 300px;
                    font-size: 24px;
                    margin-bottom: 0px;
                }
                #upload_text p{margin-bottom: 0px;}

                #upload_text i{font-size:26px;}
                .sub-title-txt{font-size: #767a7b; font-size: 18px;margin-bottom: 0px;}

                #file_upload_button input[type="file"] {
                    width: 0;
                    height: 0;
                }

                .uploading_notice {
                    background-image: -webkit-linear-gradient(135deg, #6187f2 0%, #6187f2 25%, #5679da 25%, #5679da 50%, #6187f2 50%, #6187f2 75%, #5679da 75%, #5679da 100%);
                    background-image: -moz-linear-gradient(135deg, #6187f2 0%, #6187f2 25%, #5679da 25%, #5679da 50%, #6187f2 50%, #6187f2 75%, #5679da 75%, #5679da 100%);
                    background-image: -o-linear-gradient(135deg, #6187f2 0%, #6187f2 25%, #5679da 25%, #5679da 50%, #6187f2 50%, #6187f2 75%, #5679da 75%, #5679da 100%);
                    background-image: linear-gradient(135deg, #6187f2 0%, #6187f2 25%, #5679da 25%, #5679da 50%, #6187f2 50%, #6187f2 75%, #5679da 75%, #5679da 100%);
                    background-clip: content-box;
                    background-position: 0 0;
                    background-repeat: repeat;
                    background-size: 16px 16px;
                    border: 1px solid #a4a4a4;
                    float: right;
                    height: 9px;
                    line-height: 32px;
                    position: relative;
                    padding: 1px;
                    width: 160px;
                    -webkit-animation: loading 1s linear infinite;
                    -moz-animation: loading 1s linear infinite;
                    -o-animation: loading 1s linear infinite;
                    animation: loading 1s linear infinite;
                }

                .uploading_notice:after {
                    background: #FFF;
                    content: '';
                    height: 8px;
                    position: absolute;
                    right: 0;
                    top: 0;
                    z-index: 20;
                    -webkit-animation: progress 4s ease-in-out infinite;
                    -moz-animation: progress 4s ease-in-out infinite;
                    -o-animation: progress 4s ease-in-out infinite;
                    animation: progress 4s ease-in-out infinite;
                    padding: 1px 0;
                }

                @-webkit-keyframes loading {
                    from {
                        background-position: 0 0;
                    }
                    to {
                        background-position: -16px 0;
                    }
                }

                @-webkit-keyframes progress {
                    0% {
                        min-width: 100%;
                    }
                    30% {
                        min-width: 80%;
                    }
                    50% {
                        min-width: 65%;
                    }
                    69% {
                        min-width: 20%;
                    }
                    85% {
                        min-width: 0;
                    }
                    100% {
                        min-width: 0;
                    }
                }

                @keyframes loading {
                    from {
                        background-position: 0 0;
                    }
                    to {
                        background-position: -16px 0;
                    }
                }

                @keyframes progress {
                    0% {
                        min-width: 100%;
                    }
                    30% {
                        min-width: 80%;
                    }
                    50% {
                        min-width: 65%;
                    }
                    69% {
                        min-width: 20%;
                    }
                    85% {
                        min-width: 0;
                    }
                    100% {
                        min-width: 0;
                    }
                }
                </style>
            </div>
        </div>

        <table class="table table-striped dataTable border-hover" id="edit-homework-files-table">
            <thead>
                <tr>
                    <th scope="col"><?= __('Filename') ?></th>
                    <th scope="col"><?= __('Type') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Author') ?></th>
                    <th scope="col"><?= __('Actions') ?></th>
                </tr>
            </thead>

            <tbody>
                <?php if ($homework['files']): ?>
                    <?php foreach ($homework['files'] as $file): ?>
                        <tr data-file_id="<?=$file['file_id']?>">
                            <td><?=$file['name']?></td>
                            <td><?=substr($file['name'], strrpos($file['name'], '.') + 1)?></td>
                            <td><?=$file['date_created']?></td>
                            <td><?=$file['author']?></td>
                            <td>
                                <input type="hidden" name="has_file_id[]" value="<?= $file['file_id'] ?>"/>
                                <div class="action-btn">
                                    <a><span class="icon-ellipsis-h"></span></a>
                                    <ul>
                                        <li><a class="file" href="/admin/files/download_file?file_id=<?= $file['file_id'] ?>">download</a></li>
                                        <li><a class="delete">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="well form-action-group small-btn">
            <button type="submit" class="edit-homework-save btn btn-primary  btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="edit-homework-save btn  btn-success" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php if (is_numeric($homework['id'])): ?>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-homework-modal"><?= __('Delete') ?></button>
            <?php endif; ?>
            <a href="/admin/homework/list" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="edit-homework-modal-family_friendly">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <p><?= __('All students in the selected schedule will be able to see the files you have uploaded.') ?></p>
                        <p><?= __('Uploaded content must be family friendly.') ?></p>
                        <p><?= __('Click "Confirm" if you are okay with this.') ?></p>
                    </div>

                    <div class="modal-footer form-actions">
                        <button type="submit" name="action" class="btn btn-primary" id="edit-homework-modal-family_friendly-confirm"><?= __('Confirm') ?></button>
                        <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
                    </div>
                </div>
            </div>
        </div>

    </div><!--  right end -->

</form>

<div class="modal fade" id="delete-homework-modal" tabindex="-1" role="dialog"
	 aria-labelledby="delete-homework-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="delete-homework-modal-label"><?= __('Confirm delete') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= __('Are you sure you want to delete this homework?') ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal" id="delete-homework-modal-btn"
						data-id="<?= $homework['id'] ?>"><?= __('Delete') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click', '.action-btn > a', function() {
        $(this).toggleClass('open');
        $(this).siblings('.action-btn ul').slideToggle();
        return false;
    });

    $('#edit-homework-files-table').find('.delete').on('click', function(){
        $(this).parents('tr').remove();
    });
});
</script>
<style>
    .main-content-wrapper .form-horizontal .well {
        margin-top: 70px;
    }
    .main-content-wrapper .form-horizontal .well button, .main-content-wrapper .form-horizontal .well a{
        margin: 5px;
    }
</style>