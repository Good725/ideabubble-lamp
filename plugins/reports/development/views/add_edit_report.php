<?= (isset($alert)) ? $alert : ''; ?>
<div class="alert-area" id="edit_report_alert_area"></div>
<form class="form-horizontal" id="report_edit_form" method="post" action="<?=URL::site();?>admin/reports/save">
    <input type="hidden" id="id" name="id" value="<?=$report->get_id();?>"/>
	<input type="hidden" id="report_id" value="<?=$report->get_id();?>">
    <input type="hidden" id="temporary_keywords" name="temporary_keywords" value=""/>
    <input type="hidden" id="autoload_report" value="<?=$autoload;?>"/>
	<input type="hidden" id="modified" name="modified" value="0" />
    <div class="form-group">
		<div class="col-sm-12">
			<input type="text" placeholder="Report Name" class="form-control gray-input ib_text_title_input validate[required]" id="name" name="name" value="<?=$report->get_name();?>"/>
		</div>
	</div>

    <ul class="nav nav-tabs nav-tabs-blocks">
        <li><a href="#view_tab" data-toggle="tab" id="view_tab_button">View</a></li>
        <li><a href="#data_tab" data-toggle="tab" id="data_tab_button">Data</a></li>
        <li><a href="#parameter_tab" data-toggle="tab" id="parameters_tab_button" >Parameters</a></li>
        <li><a href="#details_tab" data-toggle="tab" id="details_tab_button">Details</a></li>
        <li><a href="#advanced_tab" data-toggle="tab" id="advanced_tab_button">Advanced</a></li>
        <li><a href="#cart_tab" data-toggle="tab" id="cart_tab_button">Chart</a></li>
        <li><a href="#widget_tab" data-toggle="tab" id="widget_tab_button">Widget</a></li>
        <li><a href="#activity_tab" data-toggle="tab" id="activity_tab_button">Activity</a></li>
        <li><a href="#sparkline_tab" data-toggle="tab" id="sparkline_tab_button">Sparkline</a></li>
        <li><a href="#share_tab" data-toggle="tab" id="share_tab_button">Share</a></li>
        <?php if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){ ?>
            <li><a href="#bulk_messaging" data-toggle="tab" id="bulk_messaging_tab_button">Bulk Messaging</a></li>
        <?php } ?>
        <?php if (method_exists('Model_Files','getDirectoryTree')) { ?>
            <li><a href="#print_tab" data-toggle="tab" id="document_tab_button">Document</a> </li>
        <?php } ?>
    </ul>

    <div style="height: 3em;">
        <span class="label label-danger loading-warning">Report Loading. Please Wait...</span>
    </div>

	<div class="tab-content clearfix">
		<div class="tab-pane active" id="view_tab">
			<div>
				<div class="row gutters" id="temporary_parameters"></div>

				<?php if($report->get_autosum() || $report->get_action_button()): ?>
					<div class="clearfix"></div>
				
						<div class="form-group padd-top-bottom20">
							<?php if($report->get_autosum()): ?>
							<label class="col-sm-2 control-label" for="prependedInput">Total:</label>
							<div class="col-sm-5">
								<div class="input-group">
									<span class="input-group-addon">&euro;</span>
									<input class="form-control total_settlement" id="prependedInput" type="text" readonly />
								</div>
							</div>
							<?php endif; ?>

							<div class="col-sm-2">
								<?php if($report->get_action_button()): ?>
									<input type="button"
										   data-event="<?=htmlspecialchars($report->get_action_event()); ?>"
										   value="<?=$report->get_action_button_label(); ?>"
										   class="btn btn-primary" onclick="this.disabled = true; invoke_custom_script(this); this.disabled = false;" />
								<?php endif; ?>
							</div>
						</div>
				<?php endif; ?>
					
				<div class="alert alert-success <?=(isset($_GET['success']) AND $_GET['success'] == "true") ? '' : 'hide';?>" id="report_alert">Complete: You have now settled payments.</div>
				<?php if ($report->get_generate_documents() == 1 ){ ?>
                <?php if (method_exists('Model_Files','getDirectoryTree')) { ?>
				<button type="button" class="btn"
						id="generate_document_no_print_zip"
						<?=$report->get_generate_documents() == 1 ? '' : 'disabled="disabled"'?>
						title="Generates report, all Parameters must be set, will generate the displayed records">Generate Documents Zip</button>

				<button type="button" class="btn"
						id="generate_document_no_print"
						<?=$report->get_generate_documents() == 1 ? '' : 'disabled="disabled"'?>
						title="Generates report, all Parameters must be set, will generate the displayed records">Generate Documents</button>

				<button type="button" class="btn"
					id="generate_document"
					<?=$report->get_generate_documents() == 1 ? '' : 'disabled="disabled"'?>
					title="Prints report, all Parameters must be set, will print the displayed records">Print Documents</button>
                <?php } ?>
				<?php } ?>
				<p id="generate_document_result"></p>
				<?php if(($chart->has_x_axis() OR $chart->get_type() == 4) AND ($chart->has_y_axis() OR $chart->get_type() == 4)):?>
					<div class="chart_render" id="chart_<?=$chart->get_id();?>"></div>
				<?php endif;?>
				<span id="url"></span>
                <div class="report_table_wrapper">
                    <?php if ($report->get_show_results_counter()): ?>
                        <p class="hidden" id="report_table-records_found">
                            <?= $report->get_results_counter_text() ? $report->get_results_counter_text() : 'Records found' ?>:
                            <span id="report_table-records_found-amount"></span>
                        </p>
                    <?php endif; ?>

                    <table id="report_table" class="table report_datatable" data-fixed_filter="true"></table>
                </div>
			</div>

		</div>

		<div class="tab-pane" id="data_tab">
			<div class="form-group" id="versions">
				<label class="col-sm-2 control-label" for="rollback_to_version">Previous Queries</label>
				<div class="col-sm-4">
					<select class="form-control" id="rollback_to_version" name="rollback_to_version">
						<option value=""></option>
						<?php foreach($report->list_versions() as $i => $report_version){ ?>
							<option value="<?=$report_version['id']?>" <?=$report_version['id'] == $report->get_rolledback_to_version() ? 'selected="selected"' : ''?> <?=$report_version['id'] == $loaded_version_id ? 'selected="selected"' : ''?>><?=($i + 1) . '; ' . $report_version['created_date'] . '; ' . $report_version['name'] . ' ' . $report_version['surname']?>  <?=$report_version['id'] == $loaded_version_id ? ' (loaded)' : ''?> <?=$report_version['id'] == $report->get_rolledback_to_version() ? ' (rolled back)' : ''?></option>
						<?php } ?>
					</select>
				</div>
				<?php if(is_numeric($report->get_id())): ?>
					<div class="col-sm-2">
						<button type="button" data-action="load_version" class="save_btn btn">Load</button>
					</div>
				<?php endif; ?>
			</div>

			<div id="sql_data_tab" class="form-group"<?=(in_array($report->get_report_type(), array('', 0, 'sql')) ? '' : ' style="display: none;"');?>>

				<label class="col-sm-2 control-label" for="sql">SQL</label>
				<div class="col-sm-6">
					<textarea class="form-control" name="sql" id="sql" <?=(isset($_SESSION['admin_user']) ? 'active' : 'readonly');?>><?=$report->get_sql();?></textarea>
				</div>

				<div class="col-sm-4">
					<div style="height: 300px; overflow: auto; border: 1px solid #ccc; padding: 5px;">
						<div id="table_column_list" style="word-wrap: break-word;display:none;">

						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<a class="show_sql" data-source="#sql" data-target="#beautify_sql">Beautify SQL</a>
					<div id="beautify_sql">
						<?=Model_SqlFormatter::format($report->get_sql());?>
					</div>

				</div>
			</div>

			<div id="serp_data_tab" class="<?=($report->get_report_type() == 'serp' ? '' : 'hide');?>">
				<div class="form-group">
				   <button class="btn btn-success" type="button" id="add_keyword" data-toggle="modal" data-target="#new_keywords_dialog">Add Keyword</button>
				</div>
				<table id="keywords_table" class="keywords_datatable" data-fixed_filter="true">
					<?php
					$search_engine = $report->get_parameters('search_engine','google.ie');
					echo View::factory('keywords')->bind('keywords',$keywords)->bind('search_engine',$search_engine[0]['value']);
					?>
				</table>

				<div id="new_keywords_dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">?</button>
								<h3 id="myModalLabel">Add New Keyword</h3>
							</div>
							<div class="modal-body form-horizontal">
								<div class="form-group">
									<label class="col-sm-3 control-label">Domain</label>
									<div class="col-sm-8">
										<input type="text" class="form-control url" value="<?=URL::site();?>"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Keyword</label>
									<div class="col-sm-8">
										<input type="text" class="form-control keywords"/>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
								<button type="button" class="btn btn-success" data-dismiss="modal" id="save_keywords_button">Save Keyword</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="tab-pane" id="parameter_tab">
			<div class="form-group">
				<label class="col-sm-3 control-label" for="add_parameter"></label>
			</div>

			<div class="well">
				<button type="button" class="btn btn-primary" id="add_new_parameter">Add Parameter</button>
				<!--<button type="button" class="btn btn-danger" id="delete_parameters">Remove Parameters</button>-->
			</div>
			<input type="hidden" name="parameter_fields" id="parameter_fields" value=""/>
			<input type="hidden" name="action" id="action" value=""/>
			<div id="parameter_area">
				<?php
					$parameters = Model_Parameter::get_all_parameters($report->get_id());
					foreach($parameters AS $parameter)
					{
						$parameter = new Model_Parameter($parameter['id']);
						echo View::factory('add_edit_parameter')->bind('parameter',$parameter);
					}
				?>
			</div>
		</div>

		<div class="col-sm-9 tab-pane" id="details_tab">

			<div class="form-group">
				<label class="col-sm-3 control-label" for="summary">Summary</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="summary" name="summary" value="<?=$report->get_summary();?>"/>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="type">Report Type:</label>
				<div class="col-sm-9">
					<select class="form-control" name="report_type" id="report_type">
						<option value="sql" <?=($report->get_report_type() == 'sql' ? 'selected' : '');?>>SQL Report</option>
						<option value="serp" <?=($report->get_report_type() == 'serp' ? 'selected' : '');?>>SERP Report</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="category">Category</label>
				<div class="col-sm-4">
					<select class="form-control"  id="category" name="category">
						<option value="">---Select a category---</option>
						<?=Model_Reports_Categories::categories_as_option($report->get_category(),false);?>
					</select>
				</div>
				<div class="col-sm-4">
					<select class="form-control"  id="sub_category" name="sub_category">
						<option value="">---Select a sub-category---</option>
						<?=Model_Reports_Categories::categories_as_option($report->get_sub_category(),true);?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="autoload">Show on Dashboard</label>
                <div class="col-sm-9">
                    <div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default<?= ($report->get_dashboard() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ($report->get_dashboard() == 1) ? ' checked="checked"' : '' ?> value="1" name="dashboard">Yes
					</label>
					<label class="btn btn-default<?= ( ! $report->get_dashboard() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ( ! $report->get_dashboard() == 1) ? ' checked="checked"' : '' ?> value="0" name="dashboard">No
					</label>
				</div>
			</div>
            </div>


			<div class="form-group">
				<label class="col-sm-3 control-label" for="publish">Publish</label>
				<div class="btn-group col-sm-9" data-toggle="buttons">
					<label class="btn btn-default<?= ($report->get_publish() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ($report->get_publish() == 1) ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
					</label>
					<label class="btn btn-default<?= ( ! $report->get_publish() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ( ! $report->get_publish() == 1) ? ' checked="checked"' : '' ?> value="0" name="publish">No
					</label>
				</div>
			</div>

		</div>

		<div class="col-sm-9 tab-pane" id="advanced_tab">

            <div class="form-group vertically_center">
                <label class="col-sm-3 control-label" for="autoload">Autoload Report</label>
				<div class="col-sm-9">
                    <?php $autoload = ($report->get_autoload() == 1); ?>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default<?= $autoload ? ' active' : '' ?>">
                            <input type="radio"<?= $autoload ? ' checked="checked"' : '' ?> value="1" name="autoload">Yes
                        </label>
                        <label class="btn btn-default<?= !$autoload ? ' active' : '' ?>">
                            <input type="radio"<?= !$autoload ? ' checked="checked"' : '' ?> value="0" name="autoload">No
                        </label>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="type">URL Parameter</label>
				<div class="col-sm-9">
					<select class="form-control"  name="link_column" id="link_column">
						<option value="">Select a column</option>
						<?php
						foreach($report->get_report_columns() AS $key=>$column):
							?>
							<option <?=($report->get_link_column() == $column ? 'selected="selected"' : '');?>><?=$column;?></option>
						<?php
						endforeach;
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="link_url">URL Link</label>
				<div class="col-sm-9">
					<input class="form-control" type="text" placeholder="/admin/reports/add_edit_report/" name="link_url" id="link_url" value="<?=$report->get_link_url();?>"/>
				</div>
			</div>

            <div class="form-group vertically_center">
                <label class="col-sm-3 control-label" for="checkbox_column">Checkbox column</label>
				<div class="col-sm-9">
                    <div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default<?= ($report->get_checkbox_column() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ($report->get_checkbox_column() == 1) ? ' checked="checked"' : '' ?> value="1" name="checkbox_column">Yes
                        </label>
                        <label class="btn btn-default<?= ( ! $report->get_checkbox_column() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ( ! $report->get_checkbox_column() == 1) ? ' checked="checked"' : '' ?> value="0" name="checkbox_column">No
                        </label>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="checkbox_column_label">Column label</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="checkbox_column_label" name="checkbox_column_label" value="<?=$report->get_checkbox_column_label();?>"/>
				</div>
			</div>

            <div class="form-group vertically_center">
                <label class="col-sm-3 control-label" for="autosum">Autosum</label>
				<div class="col-sm-9">
                    <div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default<?= ($report->get_autosum() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ($report->get_autosum() == 1) ? ' checked="checked"' : '' ?> value="1" name="autosum">Yes
                        </label>
                        <label class="btn btn-default<?= ( ! $report->get_autosum() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ( ! $report->get_autosum() == 1) ? ' checked="checked"' : '' ?> value="0" name="autosum">No
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group vertically_center">
                <label class="col-sm-3 control-label" for="action_button">Action button</label>
				<div class="col-sm-9">
                    <div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default<?= ($report->get_action_button() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ($report->get_action_button() == 1) ? ' checked="checked"' : '' ?> value="1" name="action_button">Yes
                        </label>
                        <label class="btn btn-default<?= ( ! $report->get_action_button() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ( ! $report->get_action_button() == 1) ? ' checked="checked"' : '' ?> value="0" name="action_button">No
                        </label>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="action_button_label">Action button label</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="action_button_label" name="action_button_label" value="<?=$report->get_action_button_label();?>"/>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="action_event">Action event</label>
				<div class="col-sm-9">
					<textarea class="form-control" id="action_event" name="action_event"><?=$report->get_action_event();?></textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="column_value">Column value</label>
				<div class="col-sm-9">
					<select class="form-control"  name="column_value" id="column_value">
						<option value="">Select a column</option>
						<?php foreach($report->get_report_columns() AS $key=>$column): ?>
							<option <?=($report->get_column_value() == $column ? 'selected="selected"' : '');?>><?=$column;?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

            <div class="form-group vertically_center">
                <label class="col-sm-3 control-label" for="action_button">Autocheck Checkbox</label>
				<div class="col-sm-9">
                    <div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default<?= ($report->get_autocheck() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ($report->get_autocheck() == 1) ? ' checked="checked"' : '' ?> value="1" name="autocheck">Yes
                        </label>
                        <label class="btn btn-default<?= ( ! $report->get_autocheck() == 1) ? ' active' : '' ?>">
                            <input type="radio"<?= ( ! $report->get_autocheck() == 1) ? ' checked="checked"' : '' ?> value="0" name="autocheck">No
                        </label>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="custom_report_rules">Custom Report Rules</label>
				<div class="col-sm-9">
					<textarea class="form-control" id="custom_report_rules" name="custom_report_rules"><?=$report->get_custom_report_rules();?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="php_modifier">PHP Modifier</label>
				<div class="col-sm-9">
					<textarea class="form-control" rows="10" id="php_modifier" name="php_modifier"><?=$report->get_php_modifier()?></textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="php_post_filter">PHP POST Filter</label>
				<div class="col-sm-9">
					<textarea class="form-control" rows="10" id="php_post_filter" name="php_post_filter"><?=$report->get_php_post_filter()?></textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="totals_columns">Show Totals in Footer</label>
				<div class="col-sm-9">
                    <?php
                    $options = '';
                    $totals_columns = $report->get_totals_columns();
                    $totals_columns = $totals_columns ? explode(',', $totals_columns) : [];

                    foreach($report->get_report_columns() AS $column) {
                        $options .= '<option value="'.$column.'"'.(in_array($column, $totals_columns) ? ' selected="selected"' :'').'>'.$column.'</option>';
                    }

                    $attributes = ['id' => 'totals_columns', 'multiple' => 'multiple'];

                    echo Form::ib_select(null, 'total_columns[]', $options, null, $attributes);
                    ?>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="totals_group">Group Totals By Column</label>
				<div class="col-sm-3">
					<select class="form-control" name="totals_group" id="totals_group">
						<option value=""></option>
						<?php
						$totals_group = $report->get_totals_group();
						?>
						<?php foreach($report->get_report_columns() AS $key => $column): ?>
							<option <?=$column == $totals_group ? 'selected="selected"' :''?>><?=$column;?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="csv_columns" title="one per line. if not empty then only the listed columns will be displayed in csv export">CSV Columns</label>
				<div class="col-sm-9">
					<textarea class="form-control" rows="10" id="csv_columns" name="csv_columns"><?=$report->get_csv_columns()?></textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="screen_columns" title="one per line. if not empty then only the listed columns will be displayed on screen table">Screen Columns</label>
				<div class="col-sm-9">
					<textarea class="form-control" rows="10" id="screen_columns" name="screen_columns"><?=$report->get_screen_columns()?></textarea>
				</div>
			</div>


            <h2><?= __('Results count') ?></h2>

            <div class="form-group">
                <label class="col-sm-3 control-label"><?= __('Show results counter') ?></label>

                <div class="col-sm-9">
                    <?php $show_results_counter = $report->get_show_results_counter(); ?>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default<?= $show_results_counter ? ' active' : '' ?>">
                            <input type="radio" name="show_results_counter" value="1"<?= $show_results_counter ? ' checked="checked"' : '' ?> /><?= __('Yes') ?>
                        </label>

                        <label class="btn btn-default<?= !$show_results_counter ? ' active' : '' ?>">
                            <input type="radio" name="show_results_counter" value="0"<?= !$show_results_counter? ' checked="checked"' : '' ?> /><?= __('No') ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"><?= __('Results counter text') ?></label>

                <div class="col-sm-9">
                    <?php
                    $attributes = [
                        'class'        => 'popinit',
                        'placeholder'  => 'Records found',
                        'rel'          => 'popover',
                        'data-toggle'  => 'popover',
                        'data-content' => __('Text to display next to the number of results found')
                    ];
                    echo Form::ib_input(null, 'results_counter_text', $report->get_results_counter_text(), $attributes);
                    ?>
                </div>
            </div>

		</div>

		<div class="col-sm-9 tab-pane" id="cart_tab">
			<input type="hidden" name="chart_id" value="<?=$report->get_chart_id();?>"/>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="chart_title">Title</label>
				<div class="col-sm-9">
					<input type="text" placeholder="Chart Title" class="form-control" id="chart_title" name="chart_title" value="<?=$chart->get_title();?>"/>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="chart_type">Type</label>
				<div class="col-sm-9">
					<select class="form-control"  name="chart_type" id="chart_type">
						<option value="1" <?=($chart->get_type() == 1) ? 'selected="selected"' : '';?>>Line Graph</option>
						<option value="2" <?=($chart->get_type() == 2) ? 'selected="selected"' : '';?>>Bar Chart</option>
						<option value="3" <?=($chart->get_type() == 3) ? 'selected="selected"' : '';?>>Pie Chart</option>
						<option value="4" <?=($chart->get_type() == 4) ? 'selected="selected"' : '';?>>Gannt Chart</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="x-axis">X-Axis</label>
				<div class="col-sm-9">
					<input class="form-control" type="text" placeholder="X-Axis Fields" id="chart_x_axis" name="chart_x_axis" value="<?=$chart->get_x_axis();?>"/>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="y-axis">Y-Axis</label>
				<div class="col-sm-9">
					<input class="form-control" type="text" placeholder="Y-Axis Fields" id="chart_y_axis" name="chart_y_axis" value="<?=$chart->get_y_axis();?>"/>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="publish">Publish</label>
				<div class="btn-group col-sm-9" data-toggle="buttons">
					<label class="btn btn-default<?= ($chart->get_publish() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ($chart->get_publish() == 1) ? ' checked="checked"' : '' ?> value="1" name="chart_publish">Yes
					</label>
					<label class="btn btn-default<?= ( ! $chart->get_publish() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ( ! $chart->get_publish() == 1) ? ' checked="checked"' : '' ?> value="0" name="chart_publish">No
					</label>
				</div>
			</div>

		</div>

		<div class="col-sm-12 tab-pane" id="widget_tab">
			<div class="col-sm-8">
				<input type="hidden" id="widget_id" name="widget_id" value="<?=$report->get_widget_id();?>"/>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="widget_name">Title</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="widget_name" name="widget_name" value="<?= htmlspecialchars($report->get_widget_name()) ?>"/>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="widget_type">Type</label>
					<div class="col-sm-9">
						<select class="form-control"  name="widget_type" id="widget_type">
							<?php foreach ($widget_types as $widget_type): ?>
								<option
									value="<?= $widget_type->id ?>"
									data-type="<?= $widget_type->stub ?>"
									<?= ($report->get_widget_type() == $widget_type->id) ? ' selected="selected"' : '' ?>
									><?= $widget_type->name ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div id="report_widget_axes_wrapper"<?= $report_widget_type->stub == 'raw_html' ? ' style="display: none;"' : '' ?>>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="widget_x_axis">X-Axis</label>
						<div class="col-sm-9">
							<input class="form-control" type="text" id="widget_x_axis" name="widget_x_axis" value="<?=$report->get_widget_x_axis();?>"/>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" for="widget_y_axis">Y-Axis</label>
						<div class="col-sm-9">
							<input class="form-control" type="text" id="widget_y_axis" name="widget_y_axis" value="<?=$report->get_widget_y_axis();?>"/>
						</div>
					</div>
				</div>

				<div id="report_widget_raw_html_wrapper"<?= $report_widget_type->stub == 'raw_html' ? '' : ' style="display: none;"' ?>>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="edit_report_widget_html">HTML</label>
						<div class="col-sm-9">
							<textarea class="form-control edit_report_widget_html" id="edit_report_widget_html" name="widget_html"><?= $report->get_widget_html() ?></textarea>
						</div>
					</div>
				</div>

				<div id="widget_gannt_view" class="hide">
					<a id="widget_add_gannt_series">Add Series</a>
					<div id="widget_gannt_series">

					</div>
				</div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="edit_report_widget_fill_color">Fill colour</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="edit_report_widget_fill_color" name="widget_fill_color" value="<?= htmlspecialchars($report->get_widget_fill_color()) ?>" />
                    </div>
                </div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="edit_report_widget_sql">Widget SQL</label>
					<div class="col-sm-12">
						<textarea class="form-control" id="edit_report_widget_sql" name="widget_sql" style="height: 180px;"><?= $report->get_widget_sql(); ?></textarea>


						<a class="show_sql" data-source="#edit_report_widget_sql" data-target="#beautified_widget_sql">Beautify SQL</a>
						<div id="beautified_widget_sql" style="display: none;">
							<?= Model_SqlFormatter::format($report->get_widget_sql()) ?>
						</div>
					</div>
				</div>

                <div class="form-group">
                    <label class="col-sm-12 control-label text-left" for="edit_report_widget_extra_text">Additional text. Text entered here will appear below the widget</label>
                    <div class="col-sm-12">
                        <textarea class="form-control ckeditor" id="edit_report_widget_extra_text" name="widget_extra_text" rows="5"><?= htmlspecialchars($report->get_widget_extra_text()) ?></textarea>
                    </div>
                </div>
			</div>


			<div class="col-sm-4">
				<?php if ($report->get_widget_type() == 4): ?>
					<div style="width:500px;border:1px solid #ccc;margin:0;padding:0;" id="widget_<?=$report->get_widget_id();?>"></div>
				<?php else:?>
					<div style="width:280px;height:258px;border:1px solid #ccc;float:right;margin:0;padding:0;" id="widget_<?=$report->get_widget_id();?>"></div>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-sm-12 tab-pane" id="activity_tab">
			<?php if ($activities AND count($activities) > 0): ?>
				<table class="table table-striped dataTable" id="edit_report_activity_table">
					<thead>
						<tr>
							<th scope="col">ID</th>
							<th scope="col">Time</th>
							<th scope="col">User</th>
							<th scope="col">Action</th>
							<th scope="col">Options</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($activities as $activity): ?>
							<tr>
								<td><?= $activity['id'] ?></td>
								<td>
									<span class="hidden"><?= $activity['timestamp'] ?></span>
									<?= date('d/m/Y H:i:s', strtotime($activity['timestamp'])) ?>
								</td>
								<td><?= $activity['email'] ?></td>
								<td><?= $activity['action_name'] ?><?= ($activity['file_name']) ? ' ('.strtoupper(pathinfo($activity['file_name'], PATHINFO_EXTENSION)).')' : '' ?></td>
								<td>
									<?php if ($activity['file_id'] AND class_exists('Model_Files')): ?>
										<?php if(Model_Files::file_exists($activity['file_id'])){ ?>
										<a href="/admin/files/ajax_remove_file?file_id=<?= $activity['file_id'] ?>"><span class="icon-trash"></span></a>
										<a href="/admin/files/download_file?file_id=<?= $activity['file_id'] ?>"><span class="icon-download-alt"></span></a>
										<?php } else { ?>
										<span>deleted</span>
										<?php } ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else: ?>
				<div class="form-group">
					<span class="label label-warning left">No activities have been tracked.</span>
				</div>
			<?php endif; ?>
		</div>

		<div class="tab-pane" id="sparkline_tab">
			<div class="col-sm-5">

				<div class="form-group">
					<?php if ($report->sparkline->id): ?>
						<input type="hidden" name="sparkline[id]" value="<?= $report->sparkline->id ?>" id="edit_report_sparkline_id" />
					<?php endif; ?>

					<label class="col-sm-4 control-label" for="edit_report_sparkline_title">Title</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="edit_report_sparkline_title" name="sparkline[title]" value="<?= $report->sparkline->title ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="edit_report_sparkline_type">Chart type</label>
					<div class="col-sm-8">
						<select class="form-control" id="edit_report_sparkline_type" name="sparkline[chart_type_id]">
							<option value="">-- Please Select --</option>
							<?php foreach ($sparkline_chart_types as $sparkline_chart_type): ?>
								<option
									value="<?= $sparkline_chart_type->id ?>"
									data-type="<?= $sparkline_chart_type->stub ?>"
									<?= ($report->sparkline->chart_type_id == $sparkline_chart_type->id) ? 'selected="selected"' : '' ?>
									><?= $sparkline_chart_type->name ?></option>
							<?php endforeach ; ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="edit_report_sparkline_xaxis">X Axis</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="edit_report_sparkline_xaxis" name="sparkline[x_axis]" value="<?= $report->sparkline->x_axis ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="edit_report_sparkline_yaxis">Y Axis</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="edit_report_sparkline_yaxis" name="sparkline[y_axis]" value="<?= $report->sparkline->y_axis ?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="edit_report_sparkline_total_field">Total Field</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="edit_report_sparkline_total_field" name="sparkline[total_field]" value="<?= $report->sparkline->total_field ?>" />
					</div>
				</div>

				<?php if ( ! empty ($dashboards)): ?>
					<div class="form-group hidden" id="edit_report_sparkline_dashboard_wrapper">
						<label class="col-sm-4 control-label" for="edit_report_sparkline_dashboard">Dashboard Link</label>
						<div class="col-sm-8">
							<select class="form-control" name="sparkline[dashboard_link_id]" id="edit_report_sparkline_dashboard">
								<option value="">Please select</option>
								<?php foreach ($dashboards as $dashboard): ?>
									<option
										value="<?= $dashboard->id ?>"
										<?= ($report->sparkline->dashboard_link_id == $dashboard->id) ? 'selected="selected"' : '' ?>
										><?= $dashboard->title ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>


				<div class="form-group">
					<label class="col-sm-4 control-label" for="edit_report_sparkline_total_type">Total Type</label>
					<div class="col-sm-8">
						<select class="form-control" id="edit_report_sparkline_total_type" name="sparkline[total_type_id]">
							<option value="">-- Please Select --</option>
							<?php foreach ($sparkline_total_types as $sparkline_total_type): ?>
								<option
									value="<?= $sparkline_total_type->id ?>"
									data-type="<?= $sparkline_total_type->stub ?>"
									<?= ($report->sparkline->total_type_id == $sparkline_total_type->id) ? 'selected="selected"' : '' ?>
									><?= $sparkline_total_type->name ?></option>
							<?php endforeach ; ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="edit_report_sparkline_text_color">Text Colour</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" class="form-control color_picker_input" id="edit_report_sparkline_text_color" name="sparkline[text_color]" value="<?= $report->sparkline->text_color ?>" />
							<div class="input-group-addon" style="background:none;padding:0;">
								<div class="select_color_preview" class="select_color_preview" title="Preview" style="background-color:<?= $report->sparkline->text_color ?>"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="edit_report_sparkline_background_color">Background Colour</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" class="form-control color_picker_input" id="edit_report_sparkline_background_color" name="sparkline[background_color]" value="<?= $report->sparkline->background_color ?>" />
							<div class="input-group-addon" style="background:none;padding:0;">
								<div class="select_color_preview" class="select_color_preview" title="Preview" style="background-color:<?= $report->sparkline->background_color ?>"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-4 control-label" for="edit_report_sparkline_width">Width</label>
					<div class="col-sm-8">
						<div class="input-group">
							<div class="input-group-addon">%</div>
							<input type="number" class="form-control" id="edit_report_sparkline_width" name="sparkline[width]" value="<?= $report->sparkline->width ?>" />
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-4 control-label">Publish</div>
					<div class="col-sm-8">
						<?php $publish = ($report->get_id() == '' OR $report->sparkline->publish == 1) ?>
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-default<?= ($publish) ? ' active' : '' ?>">
								<input type="radio"<?= ($publish) ? ' checked="checked"' : '' ?> value="1" name="sparkline[publish]">Yes
							</label>
							<label class="btn btn-default<?= ( ! $publish) ? ' active' : '' ?>">
								<input type="radio"<?= ( ! $publish) ? ' checked="checked"' : '' ?> value="0" name="sparkline[publish]">No
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="col-sm-3">
				<button type="button" class="btn" id="sparkline_preview_button">Preview</button>
				<div id="sparkline_preview" style="margin-top: 10px;"></div>
			</div>
		</div>

		<div class="col-sm-9 tab-pane" id="share_tab">
			<div class="form-group">
				<label for="edit_report_favorite" class="col-sm-3 control-label">Favourite</label>

				<div class="col-sm-9">
					<label><input id="edit_report_favorite" class="star_checkbox" name="is_favorite" type="checkbox" value="1"<?= ($report->get_is_favorite()) ? ' checked="checked"' : '' ?> /><i class="icon-star"></i></label>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="edit_report_share_with">Share With</label>
				<div class="col-sm-9">
					<?php
                    $shared_with_groups = $report->get_shared_with_groups();
                    $args = [
                        'multiselect_options' => [
                            'includeSelectAllOption' => true,
                            'maxHeight' => 460,
                            'numberDisplayed' => 1,
                            'selectAllText' => __('ALL')
                        ]
                    ];
                    $select_roles = [];
                    foreach($roles as $role) {
                        $select_roles[$role['id']] = $role['role'];
                    }
                    echo Form::ib_select(__('Select groups'), 'shared_with_groups[]', $select_roles,
                        $shared_with_groups, array('id' => 'edit_report_share_with_groups', 'class' => 'multiple_select todo_categories', 'multiple' => 'multiple'), $args);?>
				</div>
			</div>

			<!--
			<div class="form-group">
				<div class="col-sm-3 control-label">Shares</div>
				<div class="col-sm-9">Shared with all users</div>
			</div>

			<div class="form-group">
				<div class="col-sm-3 control-label">Add Shares</div>
				<div class="col-sm-9">
					<label for="share_dropdown"></label>
					<label for="share_dropdown_groups"></label>

					<select id="share_dropdown">
						<option value="">Everyone</option>
						<option value="">Group</option>
					</select>

					<select multiple="multiple" class="multipleselect" id="share_dropdown_groups">

					</select>
					<button class="btn" type="button">Add</button>
				</div>
			</div>
			-->
		</div>
		<?php if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')): ?>
			<div class="col-sm-12 tab-pane" id="bulk_messaging">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="bulk_message_sms_number_column">Mobile Column</label>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_sms_number_column" id="bulk_message_sms_number_column" onchange="document.getElementById('bulk_message_email_column').selectedIndex=0;">
							<option value="">Mobile Number Column</option>
							<?php foreach($report->get_report_columns() AS $key=>$column): ?>
								<option <?=($report->get_bulk_message_sms_number_column() == $column ? 'selected="selected"' : '');?>><?=$column;?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="bulk_message_email_column">Email Column</label>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_email_column" id="bulk_message_email_column" onchange="document.getElementById('bulk_message_sms_number_column').selectedIndex=0;">
							<option value="">Email Column</option>
							<?php foreach($report->get_report_columns() AS $key=>$column): ?>
								<option <?=($report->get_bulk_message_email_column() == $column ? 'selected="selected"' : '');?>><?=$column;?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="bulk_message_subject_column">Subject<br /><span>(only for emails)</span></label>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_subject_column" id="bulk_message_subject_column">
							<option value="">Subject Column</option>
							<?php foreach($report->get_report_columns() AS $key=>$column): ?>
								<option <?=($report->get_bulk_message_subject_column() == $column ? 'selected="selected"' : '');?>><?=$column;?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<span class="input-group-addon">or</span>
							<input class="form-control" name="bulk_message_subject" value="<?=htmlspecialchars($report->get_bulk_message_subject())?>" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="bulk_message_body_column">Message</label>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_body_column" id="bulk_message_body_column">
							<option value="">Message Column</option>
							<?php foreach($report->get_report_columns() AS $key=>$column): ?>
								<option <?=($report->get_bulk_message_body_column() == $column ? 'selected="selected"' : '');?>><?=$column;?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<span class="input-group-addon">or</span>
							<textarea class="form-control" name="bulk_message_body"><?=htmlentities($report->get_bulk_message_body())?></textarea>
						</div>
					</div>
				</div>
				<?php
				$interval = $report->get_bulk_message_interval();
				if($interval == ''){
					$has_interval = false;
					$interval = array(0 => array(), array(), array(), array(), array());
				} else {
					$has_interval = true;
					$interval = Model_Messaging::parse_interval($interval);
				}
				?>
				<div class="form-group">
					<label class="col-sm-3 control-label">Schedule</label>
					<div class="col-sm-9">
						<div class="btn-group" data-toggle="buttons" id="has_interval">
							<label class="btn btn-default<?=($has_interval) ? ' active' : '' ?>">
								<input type="radio"<?= ($has_interval) ? ' checked="checked"' : '' ?> value="1" name="has_interval" id="has_interval_yes">Yes</label>
							<label class="btn btn-default<?=(!$has_interval) ? ' active' : '' ?>">
								<input type="radio"<?=(!$has_interval) ? ' checked="checked"' : '' ?> value="0" name="has_interval" id="has_interval_no">No</label>
						</div>
					</div>
				</div>
				<div class="form-group interval-parts" style="display:<?=$has_interval ? '' : 'none';?>">
					<div class="col-sm-3"></div>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_interval[minute][]" multiple="multiple" size="5">
							<option value=""></option>
							<option value="*" <?=in_array('*', $interval[0]) ? 'selected="selected"' : ''?>>Every minute</option>
							<?php for($i = 0 ; $i < 60 ; ++$i){ ?>
							<option value="<?=$i?>" <?=in_array((string)$i, $interval[0]) ? 'selected="selected"' : ''?>><?=$i?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_interval[hour][]" multiple="multiple" size="5">
							<option value=""></option>
							<option value="*" <?=in_array('*', $interval[1]) ? 'selected="selected"' : ''?>>Every hour</option>
							<?php for($i = 0 ; $i < 60 ; ++$i){ ?>
							<option value="<?=$i?>" <?=in_array((string)$i, $interval[1]) ? 'selected="selected"' : ''?>><?=$i?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_interval[day_of_month][]" multiple="multiple" size="5">
							<option value=""></option>
							<option value="*" <?=in_array('*', $interval[2]) ? 'selected="selected"' : ''?>>Every day of month</option>
							<option value="L" <?=in_array('L', $interval[2]) ? 'selected="selected"' : ''?>>Last day of month</option>
							<?php for($i = 0 ; $i < 32 ; ++$i){ ?>
							<option value="<?=$i?>" <?=in_array((string)$i, $interval[2]) ? 'selected="selected"' : ''?>><?=$i?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group interval-parts" style="display:<?=$has_interval ? '' : 'none';?>">
					<div class="col-sm-3"></div>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_interval[month][]" multiple="multiple" size="5">
							<option value=""></option>
							<option value="*" <?=in_array('*', $interval[3]) ? 'selected="selected"' : ''?>>Every month</option>
							<?php foreach(array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December') as $i => $month){ ?>
							<option value="<?=$i?>" <?=in_array((string)$i, $interval[3]) ? 'selected="selected"' : ''?>><?=$month?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-3">
						<select class="form-control" name="bulk_message_interval[day_of_week][]" multiple="multiple" size="5">
							<option value=""></option>
							<option value="*" <?=in_array('*', $interval[4]) ? 'selected="selected"' : ''?>>Every day of week</option>
							<?php foreach(array(0 => 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') as $i => $day_of_week){ ?>
							<option value="<?=$i?>" <?=in_array((string)$i, $interval[4]) ? 'selected="selected"' : ''?>><?=$day_of_week?></option>
							<?php } ?>
						</select>
					</div>
				</div>
                <div class="form-group">
                    <div class="col-sm-3"><?=__('Messages per minute')?></div>
                    <div class="col-sm-9">
                        <input type="text" name="bulk_messages_per_minute" value="<?=$report->get_bulk_messages_per_minute()?>" placeholder="Message / Per Minute" />
                    </div>
                </div>
				<div class="form-group">
					<div class="col-sm-3"></div>
					<div class="col-sm-3">
						<button type="button" class="btn btn-success" id="send_bulk_notification" data-action="save_and_send" >Send</button>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if (method_exists('Model_Files','getDirectoryTree')) { ?>
		<div class="col-sm-12 tab-pane" id="print_tab">
			<div class="form-group">
				<label class="col-sm-3 control-label" for="generate_documents">Generate Documents</label>
				<div class="btn-group col-sm-9" data-toggle="buttons">
					<label class="btn btn-default<?= ($report->get_generate_documents() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ($report->get_generate_documents() == 1) ? ' checked="checked"' : '' ?> value="1" name="generate_documents">On
					</label>
					<label class="btn btn-default<?= ( ! $report->get_generate_documents() == 1) ? ' active' : '' ?>">
						<input type="radio"<?= ( ! $report->get_generate_documents() == 1) ? ' checked="checked"' : '' ?> value="0" name="generate_documents">Off
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="generate_documents_template_file_id">Template</label>
				<div class="col-sm-9">
					<select class="form-contol" name="generate_documents_template_file_id">
                        <option value=""></option>
						<?=HTML::optionsFromRows("id", "name", Model_Files::getDirectoryTree('/templates', false), $report->get_generate_documents_template_file_id())?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="generate_documents_template_file_id">Helper Method</label>
				<div class="col-sm-9">
					<select class="form-contol" name="generate_documents_helper_method">
						<option value=""></option>
                        <?php
                        $printHelperClasses = array();
                        if (class_exists('Model_Docarrayhelper')) {
                            $printHelperClasses[] = 'Model_Docarrayhelper';
                        }
                        $options = '';
                        $selected = $report->get_generate_documents_helper_method();
                        foreach ($printHelperClasses as $printHelperClass) {
                            foreach (get_class_methods($printHelperClass) as $docHelper) {
                                $rm = new ReflectionMethod($printHelperClass, $docHelper);
                                $params = array();
                                foreach ($rm->getParameters() as $param) {
                                    $params[] = $param->getName();
                                }
                                $value = $printHelperClass . '->' . $docHelper;
                                $options .= '<option value="' . $value . '"' . ($selected == $value ? ' selected="selected"' : '') . '>' . $printHelperClass . '->' . $docHelper . '(' . implode(', ',
                                        $params) . ')</option>';
                            }
                        }
                        echo $options;
                        ?>
					</select>
				</div>
			</div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="generate_documents_pdf">Output Format</label>
                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= ($report->get_generate_documents_pdf() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($report->get_generate_documents_pdf() == 1) ? ' checked="checked"' : '' ?> value="1" name="generate_documents_pdf">PDF
                    </label>
                    <label class="btn btn-default<?= ( ! $report->get_generate_documents_pdf() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ( ! $report->get_generate_documents_pdf() == 1) ? ' checked="checked"' : '' ?> value="0" name="generate_documents_pdf">WORD
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="generate_documents_office_print">Office Print</label>
                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= ($report->get_generate_documents_office_print() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($report->get_generate_documents_office_print() == 1) ? ' checked="checked"' : '' ?> value="1" name="generate_documents_office_print">On
                    </label>
                    <label class="btn btn-default<?= ( ! $report->get_generate_documents_office_print() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ( ! $report->get_generate_documents_office_print() == 1) ? ' checked="checked"' : '' ?> value="0" name="generate_documents_office_print">Off
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="generate_documents_office_print_bulk">Bulk Office Print</label>
                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= ($report->get_generate_documents_office_print_bulk() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ($report->get_generate_documents_office_print_bulk() == 1) ? ' checked="checked"' : '' ?> value="1" name="generate_documents_office_print_bulk">On
                    </label>
                    <label class="btn btn-default<?= ( ! $report->get_generate_documents_office_print_bulk() == 1) ? ' active' : '' ?>">
                        <input type="radio"<?= ( ! $report->get_generate_documents_office_print_bulk() == 1) ? ' checked="checked"' : '' ?> value="0" name="generate_documents_office_print_bulk">Off
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="generate_documents_tray">Tray</label>
                <select class="form-contol" name="generate_documents_tray">
                    <?=HTML::optionsFromArray($print_trays, $report->get_generate_documents_tray())?>
                </select>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="generate_documents_link_to_contact">Link generated file to contacts(by parameter)</label>
                <div class="col-sm-9">
                    <select class="form-contol" name="generate_documents_link_to_contact">
                        <option value=""></option>
                        <?php
                        $options = '';
                        foreach ($parameters as $parameter) {
                            $options .= '<option value="' . $parameter['name'] . '"' . ($parameter['name'] == $report->get_generate_documents_link_to_contact() ? ' selected="selected"' : '') . '>' . $parameter['name'] . '</option>';
                        }
                        echo $options;
                        ?>
                    </select>
                </div>
            </div>

			<div class="form-group">
				<label class="col-sm-3 control-label" for="generate_documents_link_by_template_variable">Link generated file to contacts by template variable</label>
				<div class="col-sm-9">
					<input name="generate_documents_link_by_template_variable" value="<?=$report->get_generate_documents_link_by_template_variable()?>" />
				</div>
			</div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="generate_documents_mode">Document generation mode</label>
                <div class="col-sm-9">
                    <select class="form-contol" name="generate_documents_mode">
                        <option value=""></option>
                        <?php
                        echo html::optionsFromArray(array('PARAMETER' => 'PARAMETER', 'ROW' => 'ROW'), $report->get_generate_documents_mode())
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="generate_documents_row_variable">Generate document for each row by variable</label>
                <div class="col-sm-9">
                    <input name="generate_documents_row_variable" value="<?=$report->get_generate_documents_row_variable()?>" />
                </div>
            </div>
		</div>
		<?php } ?>

        <?php if(is_numeric($report->get_id())):{?>
        <div class="col-sm-12" id="edit_tabs">
            <?php } ?>
            <?php else :{?>
            <div class="col-sm-12" id="new_tabs">
                <?php } ?>
                <?php endif; ?>

		<div class="col-sm-12">
			<div class="form-actions form-action-group bottom-bar">
                <button type="button" class="btn btn-primary" id="generate_report">Run Report</button>
				<button type="button" data-action="save" class="save_btn btn btn-primary">Save</button>
				<button type="button" data-action="save_and_exit" class="save_btn btn btn-outline-primary">Save &amp; Exit</button>
				<?php if(is_numeric($report->get_id())): ?>
					<button type="button" data-action="rollback_to_version" class="save_btn btn btn-outline-primary">Rollback</button>
					<?php if($can_delete_report){ ?>
					<button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#delete_report">Delete</button>
					<?php } ?>
				<?php endif; ?>
                <button type="button" id="cancel_button" class="btn-cancel">Cancel</button>
			</div>
		</div>

		<div id="delete_report" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Delete Report" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">?</button>
						<h3 id="myModalLabel">Delete Report</h3>
					</div>
					<div class="modal-body">
						<p>Are you sure you want to delete this report?</p>
						<p>This cannot be undone.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
						<button type="button" class="btn btn-danger" id="delete_button" data-report_id="<?=$report->get_id();?>">Delete</button>
					</div>
				</div>
			</div>
		</div>
    </div>

</form>
<form action="/admin/reports/export_report_as_csv/<?= $report->get_id() ?>" id="csv_form" method="POST">
    <input type="hidden" id="csv_sql" name="csv_sql"/>
    <input type="hidden" id="csv_parameters" name="csv_parameters"/>
</form>
<div id="color_palette" class="color_palette">
	<table>
		<thead>
		<tr>
			<th colspan="8">Standard Colours</th>
		</tr>
		</thead>
		<tbody class="standard_palette">
		<tr>
			<td style="background-color:#000000;" title="rgb(0, 0, 0)"></td>
			<td style="background-color:#434343;" title="rgb(67, 67, 67)"></td>
			<td style="background-color:#666666;" title="rgb(102, 102, 102)"></td>
			<td style="background-color:#999999;" title="rgb(153, 153, 153)"></td>
			<td style="background-color:#B7B7B7;" title="rgb(183, 183, 183)"></td>
			<td style="background-color:#CCCCCC;" title="rgb(204, 204, 204)"></td>
			<td style="background-color:#D9D9D9;" title="rgb(217, 217, 217)"></td>
			<td style="background-color:#EFEFEF;" title="rgb(239, 239, 239)"></td>
			<td style="background-color:#F3F3F3;" title="rgb(243, 243, 243)"></td>
			<td style="background-color:#FFFFFF;" title="rgb(255, 255, 255)"></td>
		</tr>
		<tr>
			<td colspan="8" style="border:none;height:2px;"></td>
		</tr>
		<tr>
			<td style="background-color:#990000;" title="rgb(153, 0, 0)"></td>
			<td style="background-color:#FF0000;" title="rgb(255, 0, 0)"></td>
			<td style="background-color:#FF9900;" title="rgb(255, 153, 0)"></td>
			<td style="background-color:#FFFF00;" title="rgb(255, 255, 0)"></td>
			<td style="background-color:#00FF00;" title="rgb(0, 255, 0)"></td>
			<td style="background-color:#00FFFF;" title="rgb(0, 255, 255)"></td>
			<td style="background-color:#3399FF;" title="rgb(51, 153, 255)"></td>
			<td style="background-color:#0000FF;" title="rgb(0, 0, 255)"></td>
			<td style="background-color:#800080;" title="rgb(128, 0, 128)"></td>
			<td style="background-color:#FF00FF;" title="rgb(255, 0, 255)"></td>
		</tr>
		<tr>
			<td colspan="8" style="border:none;height:2px;"></td>
		</tr>
		<tr>
			<td style="background-color:#e6b8af;" title="rgb(230, 184, 175)"></td>
			<td style="background-color:#f4cccc;" title="rgb(244, 204, 204)"></td>
			<td style="background-color:#fce5cd;" title="rgb(252, 229, 205)"></td>
			<td style="background-color:#fff2cc;" title="rgb(255, 242, 204)"></td>
			<td style="background-color:#d9ead3;" title="rgb(217, 234, 211)"></td>
			<td style="background-color:#d0e0e3;" title="rgb(208, 224, 227)"></td>
			<td style="background-color:#c9daf8;" title="rgb(201, 218, 248)"></td>
			<td style="background-color:#cfe2f3;" title="rgb(207, 226, 243)"></td>
			<td style="background-color:#d9d2e9;" title="rgb(217, 210, 233)"></td>
			<td style="background-color:#ead1dc;" title="rgb(234, 209, 220)"></td>
		</tr>
		<tr>
			<td style="background-color:#db7e6b;" title="rgb(219, 126, 107)"></td>
			<td style="background-color:#e89898;" title="rgb(232, 152, 152)"></td>
			<td style="background-color:#f7c99b;" title="rgb(247, 201, 155)"></td>
			<td style="background-color:#fde398;" title="rgb(253, 227, 152)"></td>
			<td style="background-color:#b5d5a7;" title="rgb(181, 213, 167)"></td>
			<td style="background-color:#a1c2c7;" title="rgb(161, 194, 199)"></td>
			<td style="background-color:#a3c0f2;" title="rgb(163, 192, 242)"></td>
			<td style="background-color:#9ec3e6;" title="rgb(158, 195, 230)"></td>
			<td style="background-color:#b3a6d4;" title="rgb(179, 166, 212)"></td>
			<td style="background-color:#d3a5bc;" title="rgb(211, 165, 188)"></td>
		</tr>
		<tr>
			<td style="background-color:#ca4126;" title="rgb(202, 65, 38)"></td>
			<td style="background-color:#de6666;" title="rgb(222, 102, 102)"></td>
			<td style="background-color:#f4b16b;" title="rgb(244, 177, 107)"></td>
			<td style="background-color:#fdd766;" title="rgb(253, 215, 102)"></td>
			<td style="background-color:#92c27d;" title="rgb(146, 194, 125)"></td>
			<td style="background-color:#76a4ae;" title="rgb(118, 164, 174)"></td>
			<td style="background-color:#6d9de9;" title="rgb(109, 157, 233)"></td>
			<td style="background-color:#6fa7da;" title="rgb(111, 167, 218)"></td>
			<td style="background-color:#8d7cc1;" title="rgb(141, 124, 193)"></td>
			<td style="background-color:#c07b9f;" title="rgb(192, 123, 159)"></td>
		</tr>
		<tr>
			<td style="background-color:#a51d02;" title="rgb(165, 29, 2)"></td>
			<td style="background-color:#ca0202;" title="rgb(202, 2, 2)"></td>
			<td style="background-color:#e49039;" title="rgb(228, 144, 57)"></td>
			<td style="background-color:#efc033;" title="rgb(239, 192, 51)"></td>
			<td style="background-color:#6aa74f;" title="rgb(106, 167, 79)"></td>
			<td style="background-color:#45808d;" title="rgb(69, 128, 141)"></td>
			<td style="background-color:#3d78d6;" title="rgb(61, 120, 214)"></td>
			<td style="background-color:#3e84c4;" title="rgb(62, 132, 196)"></td>
			<td style="background-color:#674ea6;" title="rgb(103, 78, 166)"></td>
			<td style="background-color:#a54d79;" title="rgb(165, 77, 121)"></td>
		</tr>
		<tr>
			<td style="background-color:#85200c;" title="rgb(133, 32, 12)"></td>
			<td style="background-color:#990000;" title="rgb(153, 0, 0)"></td>
			<td style="background-color:#b45f06;" title="rgb(180, 95, 6)"></td>
			<td style="background-color:#bf9000;" title="rgb(191, 144, 0)"></td>
			<td style="background-color:#38761d;" title="rgb(56, 118, 29)"></td>
			<td style="background-color:#134f5c;" title="rgb(19, 79, 92)"></td>
			<td style="background-color:#1155cc;" title="rgb(17, 85, 204)"></td>
			<td style="background-color:#0b5394;" title="rgb(11, 83, 148)"></td>
			<td style="background-color:#351c75;" title="rgb(53, 28, 117)"></td>
			<td style="background-color:#741b47;" title="rgb(116, 27, 71)"></td>
		</tr>
		<tr>
			<td style="background-color:#5b0f00;" title="rgb(91, 15, 0)"></td>
			<td style="background-color:#660000;" title="rgb(102, 0, 0)"></td>
			<td style="background-color:#783f04;" title="rgb(120, 63, 4)"></td>
			<td style="background-color:#7f6000;" title="rgb(127, 96, 0)"></td>
			<td style="background-color:#274e13;" title="rgb(39, 78, 19)"></td>
			<td style="background-color:#0c343d;" title="rgb(12, 52, 61)"></td>
			<td style="background-color:#1c4587;" title="rgb(28, 69, 135)"></td>
			<td style="background-color:#073763;" title="rgb(7, 55, 99)"></td>
			<td style="background-color:#20124d;" title="rgb(32, 18, 77)"></td>
			<td style="background-color:#4c1130;" title="rgb(76, 17, 48)"></td>
		</tr>

		<tr>
			<td colspan="9">Transparent&nbsp;</td>
			<td style="background-color:transparent;" class="transparent_option" title="transparent"></td>
		</tr>
		</tbody>

		<thead>
		<tr>
			<th colspan="10">Custom Colours</th>
		</tr>
		</thead>
		<tbody class="custom_palette">
		</tbody>
		<tfoot>
		<tr>
			<td colspan="10"><div id="custom_color_link" style="text-align:left;"><input type="hidden"><a href="#">Add more colours ...</a></div></td>
		</tr>
		</tfoot>
	</table>
</div>

<div class="hidden" id="report-parameter-template-date">
    <?= Form::ib_input(
        NULL,
        NULL,
        NULL,
        array('class' => 'temporary_value datepicker input_date'),
        array('icon' => '<span class="flaticon-calendar-1"></span>', 'icon_position' => 'right')
    ); ?>
</div>

<div class="hidden" id="report-parameter-template-select">
    <?= Form::ib_select(null, null, array(), null, array('class' => 'temporary_value value_input input_select')); ?>
</div>

<style>
	table.table td a {display:inline;} <?php // The rule should really be taken out of the stylish.css, rather than overwritten here ?>
</style>
<script>
	// todo: move some of this to an appropriate .js file
	$(document).ready(function()
	{
		$('.multipleselect').multiselect({numberDisplayed:2,includeSelectAllOption:true,selectAllName:'multiselect_selectAll'});

		$('#edit_report_share_with').on('change', function()
		{
			var selected = this[this.selectedIndex].getAttribute('data-value');
			document.getElementById('edit_report_share_with_groups_wrapper').style.display = (selected == 'group') ? 'block' : 'none';
		});

		setTimeout(function(){
			$('#report_edit_form').on('change', function(e){
				if(e.target.name && e.target.name != "rollback_to_version"){
					this.modified.value = 1;
				}
			});
		}, 0);
	});

	$('.color_picker_input').on('change', function()
	{
		this.parentNode.getElementsByClassName('select_color_preview')[0].style.backgroundColor = this.value;
	});

	$('#widget_type').on('change', function()
	{
		var is_raw_html = (this[this.selectedIndex].getAttribute('data-type') == 'raw_html');
		document.getElementById('report_widget_axes_wrapper')    .style.display = (is_raw_html) ? 'none' : '';
		document.getElementById('report_widget_raw_html_wrapper').style.display = (is_raw_html) ? ''     : 'none';
	});

	$('#sparkline_preview_button').on('click', function()
	{
		var id = $('#edit_report_sparkline_id').val();
		var data = {};
		var name;
		$.each($('[name^="sparkline"]').serializeArray(), function()
		{
			name = this.name.replace('sparkline[', '').replace(']', '');
			if (data[this.name] !== undefined)
			{

				if (!data[name].push) {
					data[name] = [data[this.name]];
				}
				data[name].push(this.value || '');
			}
			else
			{
				data[name] = this.value || '';
			}
		});

		$.post('/admin/reports/ajax_get_sparkline/'+id, data).done(function(results)
		{
			$('#sparkline_preview').html(results);
		});
	});
$("#has_interval").on("change", function(){
	if($("#has_interval_no").prop("checked")){
		$('.interval-parts').hide();
	} else {
		$('.interval-parts').show();
	}
});

	$('#edit_report_sparkline_type').on('change', function()
	{
		if ($(this).find(':selected').attr('data-type') == 'total')
		{
			$('#edit_report_sparkline_dashboard_wrapper').removeClass('hidden');
		}
		else
		{
			$('#edit_report_sparkline_dashboard_wrapper').addClass('hidden');
		}
	}).trigger('change');

    $(document).ready(function() {
        // If the user changes a form field, prevent them from running the report while they have unsaved changes.
        $('#report_edit_form').on('change', ':input', function(ev) {
            // Check if the field was manually changed.
            if (ev.originalEvent) {
                $('#generate_report')
                    .prop('disabled', true)
                    .attr('title', 'You cannot run the report while you have unsaved changes. Save your changes or reload the page.');
            }
        });
    });
</script>
