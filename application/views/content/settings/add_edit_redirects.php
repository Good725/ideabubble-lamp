<div>
    <form id="add_edit_redirects" action="/admin/settings/save_redirects" class="col-sm-12 form-horizontal" method="post">
        <input type="hidden" name="values" id="values" value=""/>
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab">Redirects</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active tab-redirect" id="tab1">
                    <div id="edit_redirects_list">
						<?php foreach(Model_PageRedirect::get_active_redirect() as $redirect): ?>
							<?php $redirectId = $redirect['id']; ?>
							<div class="form-group redirect_group">
								<label class="col-sm-1">From:</label>
								<div class="col-sm-3">
									<input type="text" class="form-control from" value="<?= $redirect['from'] ?>" name="oldRedirect[from][<?= $redirectId; ?>]" />
								</div>

								<label class="col-sm-1">To:</label>
								<div class="col-sm-3">
									<input type="text" class="form-control to" value="<?= $redirect['to'] ?>" name="oldRedirect[to][<?= $redirectId; ?>]" />
								</div>

								<label class="col-sm-1">Type:</label>
								<div class="col-sm-2">
									<select class="form-control type" name="oldRedirect[type][<?= $redirectId; ?>]">
										<option value="301" <?= ($redirect['type'] == 301) ? 'selected' : ''; ?>>301</option>
										<option value="302" <?= ($redirect['type'] == 302) ? 'selected' : ''; ?>>302</option>
									</select>
								</div>
								<a href="#" class="delete" data-id="<?= $redirectId ?>">&times;</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="well clearfix">
			<button type="button" class="btn btn-primary left" id="save">Save</button>
			<button type="button" class="btn btn-success right" id="add">Add New Redirect</button>
		</div>
	</form>
</div>