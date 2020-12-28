<div class="alert-area"><?= isset($alert) ? $alert : '' ?>
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
<div>
	<form id="dashboard_edit_form" class="form-horizontal" action="/admin/dashboards/save_dashboard" method="post">
		<input type="hidden" name="id" value="<?= $dashboard->id ?>" />

		<div class="form-group">
			<div class="col-sm-7">
				<label class="sr-only" for="edit_dashboard_title">Title</label>
				<input type="text" class="form-control validate[required]" id="edit_dashboard_title" name="title" value="<?= $dashboard->title ?>" placeholder="Title" />
			</div>
		</div>

		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active">
				<a href="#edit_dashboard_tab_details" aria-controls="edit_dashboard_tab_details" role="tab" data-toggle="tab">Details</a>
			</li>
			<li role="presentation">
				<a href="#edit_dashboard_tab_sharing" aria-controls="edit_dashboard_tab_sharing" role="tab" data-toggle="tab">Share</a>
			</li>
			<?php if ($dashboard->id): ?>
				<li role="presentation">
					<a href="#edit_dashboard_tab_preview" id="tab_preview_button" aria-controls="edit_dashboard_tab_preview" role="tab" data-toggle="tab"><?= __('Preview') ?></a>
				</li>
			<?php endif; ?>
		</ul>

		<!-- Tab content - start -->
		<div class="tab-content">
			<!-- details tab -->
			<div role="tabpanel" class="tab-pane active" id="edit_dashboard_tab_details">

				<div class="form-group">
					<label class="col-sm-2 control-label" for="edit_dashboard_description">Description</label>
					<div class="col-sm-5">
						<textarea class="form-control" id="edit_dashboard_description" name="description"><?= $dashboard->description ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label" for="edit_dashboard_columns">Columns</label>
					<div class="col-sm-2">
						<input type="number" inputmode="numeric" pattern="[0-9]*" min="1" max="6"
							   class="form-control" id="edit_dashboard_columns"
							   name="columns" value="<?= $dashboard->columns ? $dashboard->columns : 3 ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label" for="edit_dashboard_date_filter">Date Filter</label>
					<div class="col-sm-5">
						<?php $date_filter = ($dashboard->date_filter == 1); ?>
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-default<?= ($date_filter) ? ' active' : '' ?>">
								<input type="radio"<?= ($date_filter) ? ' checked="checked"' : '' ?> value="1" name="date_filter">Yes
							</label>
							<label class="btn btn-default<?= ( ! $date_filter) ? ' active' : '' ?>">
								<input type="radio"<?= ( ! $date_filter) ? ' checked="checked"' : '' ?> value="0" name="date_filter">No
							</label>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-2 control-label">Publish</div>
					<div class="col-sm-5">
						<?php $publish = ($dashboard->id == '' OR $dashboard->publish == 1); ?>
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-default<?= ($publish) ? ' active' : '' ?>">
								<input type="radio"<?= ($publish) ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
							</label>
							<label class="btn btn-default<?= ( ! $publish) ? ' active' : '' ?>">
								<input type="radio"<?= ( ! $publish) ? ' checked="checked"' : '' ?> value="0" name="publish">No
							</label>
						</div>
					</div>
				</div>

			</div>

			<!-- sharing tab -->
			<div role="tabpanel" class="tab-pane" id="edit_dashboard_tab_sharing">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="edit_dashboard_favorite">Favourite</label>
					<div class="col-sm-5">
						<label>
							<input id="edit_dashboard_favorite" class="star_checkbox" name="is_favorite" type="checkbox" value="1"<?= $is_favorite ? ' checked="checked"' : '' ?> />
							<span class="star_checkbox_icon"></span>
						</label>
					</div>
				</div>

				<div class="form-group">
					<input type="hidden" name="save_sharing_data" value="1" />
					<label class="col-sm-2 control-label" for="edit_dashboard_share_with">Share With</label>
					<div class="col-sm-5">
						<select class="form-control"  id="edit_dashboard_share_with" name="shared_with">
							<option value="0" data-value="everyone">Everyone</option>
							<option value="1" data-value="group"<?= (count($shared_with_groups) > 0) ? ' selected="selected"' : '' ?>>Group</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-5 col-sm-offset-2">
						<div id="edit_dashboard_share_with_groups_wrapper"<?= (count($shared_with_groups) == 0) ? ' style="display:none;"' : '' ?>>
							<label class="sr-only" for="edit_dashboard_share_with_groups">Groups</label>
							<select multiple="multiple" class="form-control multipleselect" id="edit_dashboard_share_with_groups" name="shared_with_groups[]">
								<?php foreach ($roles as $role): ?>
									<option value="<?= $role['id'] ?>"<?= in_array($role['id'], $shared_with_groups) ? ' selected="selected"' : '' ?>>
										<?= $role['role'] ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
			</div>

			<?php if ($dashboard->id): ?>
				<!-- dashboard tab -->
				<div role="tabpanel" class="tab-pane" id="edit_dashboard_tab_preview">
					<?= $dashboard->render('edit_mode'); ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- Tab content - end -->

		<div class="well">

			<div>
				<button type="submit" id="dashboard_save" class="btn btn-primary" name="save" value="save">Save</button>
				<button type="submit" id="dashboard_save_and_view" class="btn" name="save" value="save_and_view">Save &amp; View</button>
				<button type="submit" id="dashboard_save_and_exit" class="btn" name="save" value="save_and_exit">Save &amp; Exit</button>
				<a href="/admin/dashboards" class="btn">Cancel</a>
				<button type="reset" class="btn">Reset</button>
				<?php if ($dashboard->id != ''): ?>
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_dashboard_modal">Delete</button>
				<?php endif; ?>
			</div>
		</div>
	</form>

	<?php if ($dashboard->id): ?>
		<div class="modal fade" id="delete_dashboard_modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Delete Dashboard</h4>
					</div>
					<div class="modal-body">
						<p>Are you sure you want to delete this dashboard?</p>
					</div>
					<div class="modal-footer">
						<a href="/admin/dashboards/delete_dashboard/<?= $dashboard->id?>" class="btn btn-danger" id="delete_dashboard_button">Delete</a>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

</div>
<script>
	$(document).ready(function()
	{
		$('.multipleselect').multiselect(
		{
			numberDisplayed:2,
			includeSelectAllOption:true,
			selectAllName:'multiselect_selectAll',
			inheritClass: true,
			buttonWidth: '100%'
		});
		$('.multiselect').addClass('form-control').removeClass('btn btn-default').parent().removeClass('btn-group');


        // ----- START: This block responsible for hiding save buttons if Preview tab is active ----- //
        $('#edit_dashboard_tab_preview').bind("DOMSubtreeModified", function(){
            if($(this).hasClass('active')){
                hideSaveButtons();
            }
        });

		$('.nav-tabs > li > a').on('click', function(){
            if(this.id == 'tab_preview_button') {
                hideSaveButtons();
            }else{
                showSaveButtons();
            }
		});

        function hideSaveButtons(){
            $('#dashboard_save').hide();
            $('#dashboard_save_and_view').hide();
            $('#dashboard_save_and_exit').hide();
        }

        function showSaveButtons(){
            $('#dashboard_save').show();
            $('#dashboard_save_and_view').show();
            $('#dashboard_save_and_exit').show();
        }

        // ----- END: This block responsible for hiding save buttons if Preview tab is active ----- //
	});

	// Toggle the display of the list of user groups, depending on the choice in the "Share With" dropdown
	$('#edit_dashboard_share_with').on('change', function()
	{
		var selected            = this[this.selectedIndex].getAttribute('data-value');
		var group_input_wrapper = document.getElementById('edit_dashboard_share_with_groups_wrapper');
		if (selected == 'group')
		{
			group_input_wrapper.style.display = 'block';
		}
		else
		{
			group_input_wrapper.style.display = 'none';
			$('#edit_dashboard_share_with_groups').val('').multiselect('refresh'); // empty the list
		}
	});

	$("#dashboard_edit_form").on('submit', function() {
		if (!$(this).validationEngine('validate')) {
			return false;
		}
	});
</script>

<style>
	#edit_dashboard_favorite + span {
		margin-top: 8px;
	}
	/* Style radio buttons as full/empty stars */
	.star_checkbox {
		position: absolute !important;
		z-index: -9999 !important;
		opacity: 0 !important;
	}
	.star_checkbox + .star_checkbox_icon {
		display: inline-block;
	}
	.star_checkbox + .star_checkbox_icon:before {
		content: '\f006'; /* star-o */
		font-family: FontAwesome;
		display: inline-block;
	}
	.star_checkbox:checked + .star_checkbox_icon:before {
		content: '\f005' /* star */
	}
	.star_checkbox:focus + .star_checkbox_icon {
		outline: 1px dotted #aaa;
	}

	/* Bootstrap multiselect misc styling */
	.multiselect {
		text-align: left;
	}
	.multiselect .caret {
		float: right;
		margin-top: 8px;
	}
	.multiselect + .dropdown-menu {
		right: 15px;
		left: auto;
		width: 100%;
		width: calc(100% - 30px);
	}
</style>
