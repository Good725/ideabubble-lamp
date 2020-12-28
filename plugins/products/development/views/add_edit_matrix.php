<div class="row">
    <div class="span12">
        <?=(isset($alert)) ? $alert : ''?>
        <?php
			if(isset($alert)){
			?>
				<script>
					remove_popbox();
				</script>
			<?php
			}
		?>
    </div>
</div>

<form class="col-sm-9 form-horizontal" id="add_edit_matrix_form" name="add_edit_matrix_form" action="/admin/products/save_matrix/" method="POST">
    <input type="hidden" id="matrix_data" name="matrix_data"/>
    <input type="hidden" name="id" id="id" value="<?=$matrix->get_id();?>"/>
    <div class="form-group">

        <label class="col-sm-3 control-label" for="name">Name</label>
        <div class="col-sm-9">
            <input type="text" class="form-control required" id="name" name="name" placeholder="Enter name here" value="<?=$matrix->get_name();?>"/>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="option_1">Option A</label>
        <div class="col-sm-9">
            <select class="form-control" name="option_1_id" id="option_1">
                <option value="">Select Option A</option>
                <?php foreach($option_groups AS $key=>$group): ?>
                    <option value="<?=$key;?>" <?=($matrix->get_option_1_id() == $key ? 'selected' : '');?>><?=$group;?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="option_2">Option B</label>
        <div class="col-sm-9">
            <select class="form-control" name="option_2_id" id="option_2">
                <option value="">Select Option B</option>
                <?php foreach($option_groups AS $key=>$group): ?>
                    <option value="<?=$key;?>" <?=($matrix->get_option_2_id() == $key ? 'selected' : '');?>><?=$group;?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="table_holder">

    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="publish">Publish</label>
        <div class="col-sm-9">
            <div class="btn-group" data-toggle="buttons">
				<?php $publish = ($matrix->get_id() == '' OR $matrix->get_publish() == 1); ?>

				<label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
					<input type="radio" name="enabled" value="1"<?= $publish ? ' checked' : '' ?> />Yes
				</label>

				<label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
					<input type="radio" name="enabled" value="0"<?= ( ! $publish) ? ' checked' : '' ?> />No
				</label>
            </div>
        </div>
    </div>

    <!-- Option Identifier -->
    <div class="form-actions">
        <button type="button" class="btn btn-primary save_btn">Save</button>
        <button type="button" class="btn">Reset</button>
    </div>
</form>

<div id="edit_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Add/Edit Sub Matrix</h3>
			</div>
			<div class="modal-body">
				<span id="extra_data" data-option_1_id="" data-option_2_id=""></span>
				<span id="option2" data-option2=""></span>
				<select class="form-control" id="modal_option_select">
					<option value="">Select Option C</option>
					<?php
					foreach($option_groups AS $key=>$option):
						?>
						<option value="<?=$option;?>"><?=$option;?></option>
					<?php
					endforeach;
					?>
				</select>
				<span class="" id="third_option_table_holder">

				</span>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="add_edit_sub_matrix">Save &amp; Exit</button>
			</div>
		</div>
	</div>
</div>

<div id="association_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel">Add Association: <span id="association_modal_subtitle"></span></h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="featured">Additional Price</label>
					<div class="col-sm-3">
						<div class="btn-group" data-toggle="buttons">
							<?php $additional_price_toggle = TRUE; ?>
							<label class="btn btn-plain<?= $additional_price_toggle ? ' active' : '' ?>">
								<input type="radio" name="additional_price_toggle" value="1"<?= $additional_price_toggle ? ' checked' : '' ?> />Yes
							</label>
							<label class="btn btn-plain<?= ( ! $additional_price_toggle) ? ' active' : '' ?>">
								<input type="radio" name="additional_price_toggle" value="0"<?= ( ! $additional_price_toggle) ? ' checked' : '' ?> />No
							</label>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">&euro;</span>
							<input type="text" class="form-control" id="additional_price" name="additional_price">
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="image_selector" style="float:left;">Image&nbsp;</label>
					<div class="col-sm-9">
						<select class="form-control" id="image_selector">
							<?php foreach($available_images AS $key=>$image): ?>
								<option value="<?=$image['id'];?>"><?=$image['filename'];?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn third_modal_close" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary third_modal_close" data-dismiss="modal" id="save_association" data-is_sub_option="0" data-option_1_id="0" data-option_2_id="0">Save &amp; Exit</button>
			</div>
		</div>
	</div>

</div>
