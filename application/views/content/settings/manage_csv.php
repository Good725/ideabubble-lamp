<div class="tab-content col-sm-12">
    <div class="tab-pane active" id="summary_tab">
        <form class="form-horizontal" id="manage_csv_form" name="manage_csv_form" action="/admin/settings/save_csv/" method="post">
            <input type="hidden" id="id" name="id" value="<?=$csv->get_id();?>"/>
            <input type="hidden" id="columns" name="columns" value="<?= (isset($csv) AND $csv->get_columns() != '' AND is_string($csv)) ? htmlentities($csv->get_columns()) : '' ?>"/>

            <div class="form-group">
				<div class="col-sm-7">
					<input type="text" class="form-control" id="title" name="title" placeholder="CSV Import Title" value="<?=$csv->get_title();?>"/>
				</div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="publish">Publish</label>
                <div class="col-sm-5">
                    <div class="btn-group" data-toggle="buttons">
						<?php $publish = ($csv->get_id() == '' OR $csv->get_publish() == 1) ?>
						<label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
							<input type="radio" name="publish" value="1"<?= $publish ? ' checked="checked"' : '' ?> />Yes
						</label>
						<label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
							<input type="radio" name="publish" value="0"<?= ( ! $publish) ? ' checked="checked"' : '' ?> />No
						</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="table">Code</label>
                <div class="col-sm-5">
                    <select class="form-control" name="table" id="table">
                        <option value="">Select Table</option>
                        <?php foreach($tables as $key=>$table): ?>
                            <option value="<?=$table;?>"><?=$table;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
				<div class="col-sm-7">
					<div class="well">
						<table id="columns_table" class="table table-striped">
							<thead>
								<tr>
									<th scope="col">Table Column Title</th>
									<th scope="col">CSV Column Title</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
            </div>

            <div class="well">
                <button type="button" class="btn btn-primary save" data-redirect="save">Save</button>
                <button type="button" class="btn btn-primary" data-redirect="save_and_exit">Save &amp; Exit</button>
                <button type="button" class="btn btn-primary" data-redirect="save_and_exit">Cancel</button>
            </div>

        </form>
    </div>
</div>