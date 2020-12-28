<div class="modal fade bulk_attend_update" id="<?= $prefix ?>bulk-update-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Bulk Update</h4>
			</div>
			<div class="modal-body">

				<div class="form-group">
					<label class="col-sm-3 control-label">Days</label>

					<div class="col-sm-4">
						<select class="form-control bulk_attend_update_days multiple_select" multiple size="3">
							<option value="1">Mon</option>
							<option value="2">Tue</option>
							<option value="3">Wed</option>
							<option value="4">Thu</option>
							<option value="5">Fri</option>
							<option value="6">Sat</option>
							<option value="0">Sun</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="<?= $prefix ?>bulk-update-starts">Starts</label>

					<div class="col-sm-4">
						<label class="input-group">
							<input type="text" class="form-control bulk_attend_update_date_from"
								   id="<?= $prefix ?>bulk-update-starts" readonly="readonly" size="6"
								   data-date-format="dd/mm/yyyy"/>

							<span class="input-group-addon">
								<span class="icon-calendar"></span>
							</span>
						</label>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="<?= $prefix ?>bulk-update-ends">Ends</label>

					<div class="col-sm-4">
						<label class="input-group">
							<input type="text" class="form-control bulk_attend_update_date_to"
								   id="<?= $prefix ?>bulk-update-ends" readonly="readonly" size="6"
								   data-date-format="dd/mm/yyyy"/>

							<span class="input-group-addon">
								<span class="icon-calendar"></span>
							</span>
						</label>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="<?= $prefix ?>bulk-update-notes">Notes</label>

					<div class="col-sm-8">
						<textarea class="form-control bulk_attend_update_note"
								  id="<?= $prefix ?>bulk-update-notes"></textarea>
					</div>
				</div>

				<div class="form-group">

					<label class="col-sm-3 control-label">Attending</label>

					<div class="col-sm-9">
						<div class="btn-group btn-group-slide" data-toggle="buttons">
							<label class="btn btn-plain">
								<input type="radio" name="bulk_attend_update_attending" value="1"
									   class="bulk_attend_update_attending_yes"/> Yes
							</label>
							<label class="btn btn-plain active">
								<input type="radio" name="bulk_attend_update_attending" value="0"
									   class="bulk_attend_update_attending_no" checked="checked"/> No
							</label>
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<div class="text-center">
					<button class="btn btn-primary bulk_attend_update_set" type="button">Bulk Update</button>
				</div>
			</div>
		</div>
	</div>
</div>