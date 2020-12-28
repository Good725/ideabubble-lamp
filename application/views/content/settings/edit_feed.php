<?php $action = (isset($feed)) ? 'edit' : 'add'; ?>

<? /*
<div class="col-sm-12 header">
    <h1><?= ucfirst($action) ?> Feed</h1>
</div> */ ?>

<div class="col-sm-12">
    <form id="add_edit_form" class="form-horizontal" action="<?php echo URL::Site('admin/settings/'.$action.'_feed') ?>" method="post">
        <?php //This is needed to display any error that might be loaded into the messages queue ?>
        <?= (isset($alert))? $alert : '' ?>
        <?php
			if(isset($alert)){
			?>
				<script>
					remove_popbox();
				</script>
			<?php
			}
		?>
        <input type="hidden" id="feed_id" name="id" value="<?= @$feed['id'] ?>" />
        <input type="hidden" id="feed_redirect" name="redirect" value="" />

		<div class="form-group">
			<div class="col-sm-9">
				<label for="feed_name" class="hidden">Name</label>
				<input type="text" id="feed_name" class="form-control" name="name" value="<?= @$feed['name'] ?>"placeholder="Please enter a feed title" />
			</div>
		</div>

        <div class="tabbable"> <!-- Only required for left/right tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab">Details</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab1">

                    <div class="form-group">
                        <label for="feed_code_path" class="col-sm-2 control-label">Short Tag</label><!-- Probably should be called friendly name! -->

                        <div class="col-sm-7">
                            <input class="form-control" id="short_tag" name="short_tag" value="<?= @$feed['short_tag'] ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feed_code_path" class="col-sm-2 control-label">Function Call</label><!-- Probably should be called friendly name! -->

                        <div class="col-sm-7">
                            <input class="form-control" id="function_call" name="function_call" value="<?= @$feed['function_call'] ?>" placeholder="Class,Function_name e.g. Model_Pages,get_page"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feed_plugin" class="col-sm-2 control-label">Plugin</label>

                        <div class="col-sm-7">
                            <select class="form-control" id="feed_plugin" name="plugin">
                                <?php foreach($plugins as $plugin): ?>
                                    <option <?php if (@$feed['plugin_id'] == $plugin['id']) echo 'selected="selected" '; ?>value="<?= $plugin['id'] ?>"><?= $plugin['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feed_code_path" class="col-sm-2 control-label">Code path</label>

                        <div class="col-sm-7">
                            <input class="form-control" id="feed_code_path" name="code_path" value="<?= @$feed['code_path'] ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feed_order" class="col-sm-2 control-label">Order</label>

                        <div class="col-sm-7">
                            <input class="form-control" id="feed_order" name="order" value="<?= @$feed['order'] ?>" onkeyup="this.value=this.value.replace(/[^0123456789]/,'');" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feed_summary" class="col-sm-2 control-label">Summary</label>

                        <div class="col-sm-7">
                            <textarea class="form-control" id="feed_summary" name="summary"><?= @$feed['summary'] ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feed_content" class="col-sm-2 control-label">Content</label>

                        <div class="col-sm-12">
                            <textarea class="form-control ckeditor" id="feed_content" name="content"><?= isset($feed['content']) ? $feed['content'] : '' ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feed_publish" class="col-sm-2 control-label">Publish</label>

                        <div class="col-sm-7">
                            <select class="form-control" id="feed_publish" name="publish">
                                <option value="1"<?php if (!isset($feed['publish']) OR @$feed['publish'] == 1) echo ' selected="selected"' ?>>Yes</option>
                                <option value="0"<?php if (isset($feed['publish']) AND @$feed['publish'] == 0) echo ' selected="selected"' ?>>No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="well">
            <button class="btn btn-primary" id="btn_save">Save</button>
            <button class="btn btn-primary" id="btn_save_exit">Save &amp; Exit</button>
            <button class="btn" type="reset">Reset</button>
            <?php if ($action == 'edit'): ?>
                <button type="button" class="btn btn-danger" id="btn_delete">Delete</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<script type="text/javascript">
    // Save
    $('#btn_save').on('click', function(){
        var id = $('#feed_id').val();
        $("#feed_redirect").val('/admin/settings/edit_feed/'+id);
        $('#add_edit_form').submit();
    });

    // Save and exit
    $('#btn_save_exit').on('click', function(){
        $("#feed_redirect").val('/admin/settings/manage_feeds');
        $('#add_edit_form').submit();
    });

    // Delete feed
    $('#btn_delete').on('click', function(event)
    {
        var click_item = $(this);
        $('#confirm_delete').modal();

        $('#btn_delete_yes').on('click', function(event)
        {
            $('#confirm_delete').modal('hide');
            var id = $('#feed_id').val();

            //Remove alerts, prevent stack
            $('.alert').remove();

            $.get('delete_feed/' + id, function(data)
            {
                if (data == 'success')
                {
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong> Feed deleted</div>';
                }
                else
                {
                    var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Error: </strong> The feed cannot be deleted</div>';
                }
                $("#main").prepend(smg);

            }).error(function()
                {
                    var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Error: </strong> Cannot connect with the server</div>';
                    $("#main").prepend(smg);
                });
        });



    });
</script>

<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Warning!</h3>
			</div>

			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected feed.</p>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" id="btn_delete_no" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
			</div>
		</div>
	</div>
</div>
