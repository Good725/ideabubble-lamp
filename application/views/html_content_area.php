<?= (isset($alert)) ? $alert : '' ?>

<?php
$display_on_dashboard = Settings::instance()->get('feature_dashboard_labels');
$count_todos          = ((strpos(URL::site(), 'ibis') == TRUE)  AND class_exists('Model_Todos')) ? Model_Todos::get_related_todos_count() : 0;
$current_page         = str_replace('/', '', $_SERVER["REQUEST_URI"]);
$is_dashboard_page    = (URL::site('admin') == URL::site() . $current_page OR $current_page == 'admindashboard');
$user                 = Auth::instance()->get_user();
$display_feature_text = Settings::instance()->get('dashboard_display_feature_text');
?>
<?php if ($count_todos > 0 AND ! strpos($_SERVER["REQUEST_URI"], 'admin/todos')): ?>
	<div class="row">
		<div id="todo_alert" class="alert alert_success">You have <?= $count_todos ?> tasks open in your todos. <a href="<?= URL::site() ?>admin/todos">Click here</a> for more information.</div>
	</div>
<?php endif; ?>

<div>
	<!-- Main Content -->
	<?php if ($is_dashboard_page): ?>
		<?php if ($display_on_dashboard AND $show_welcome_text): ?>
			<div class="CMSdashboard dashboard-welcome">
				<?= Settings::instance()->get('dashboard_welcome_text') ?>
			</div>

			<hr />
		<?php endif; ?>

		<?php echo $body ?>

<!--
		<div>
			<div class="col-sm-4">
				<div class="quick-message-form">
					<div class="quick-message-form-header">
						<h3>My Todos</h3>
					</div>
					<div class="tabbable">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#quick-message-todo-add-tab" data-toggle="tab">Add</a></li>
							<li><a href="#quick-message-todo-list-tab" data-toggle="tab">List</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="quick-message-todo-add-tab">
								<form class="form-horizontal">
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" placeholder="Todo title" />
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<select class="form-control">
												<option>Assign to Me</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control" placeholder="Todo description"></textarea>
										</div>
									</div>
								</form>
							</div>
							<div class="tab-pane active" id="quick-message-todo-list-tab">

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
-->

        <?php if ($display_on_dashboard == TRUE && trim($on)): ?>
            <div class="CMSdashboard">
                <?php if ($display_feature_text && $user['role_id'] == 2): ?>
                    <?= Settings::instance()->get('active_feature_text'); ?>
                <?php endif; ?>

                <hr />

                <?= $on ?>
            </div>

            <?php if (Settings::instance()->get('display_inactive_features') == 1 && trim($off) && $user['role_id'] == 2): ?>
                <div class="CMSdashboard">
                    <?php if ($display_feature_text): ?>
                        <h2>Inactive Features</h2>
                        <p>Upgrade your options</p>
                    <?php endif; ?>

                    <hr />

                    <?= $off ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

		<?php if ($jira->jira_enabled()): ?>
			<?php $issues = $jira->get_issues(); ?>
			<div class="CMSTickets">
				<hr>
				<h2>Tickets</h2>

				<p>See active tickets listed below</p>
				<div class="cms-tickets-table-wrapper">
					<table class="table-striped table-condensed">
						<thead>
						<tr>
							<th scope="col">KEY</th>
							<th scope="col">SUMMARY</th>
							<th scope="col">DESCRIPTION</th>
							<th scope="col">ESTIMATE</th>
							<th scope="col">RESOLUTION</th>
							<th scope="col">DUE DATE</th>
							<th scope="col">LAST UPDATED</th>
						</tr>
						</thead>
						<?php if ( ! empty($issues)): ?>
							<tbody>
							<?php foreach ($issues['issues'] AS $key => $value):?>
								<tr class="<?=$value['fields']['status']['name'];?>">
									<td><?=$value['key'];?></td>
									<td><?=$value['fields']['summary'];?></td>
									<td class="jira_description" data-description="<?=strlen($value['fields']['description']) > 300 ? 1 : 0;?>">
														<span class="description">
															<?=strlen($value['fields']['description']) > 300 ? $value['fields']['description'] : '';?>
														</span>
										<?=strlen($value['fields']['description']) > 300 ? substr($value['fields']['description'],0,300) : $value['fields']['description'];?>
									</td>
									<td><?=gmdate("H:i:s", $value['fields']['timeoriginalestimate']);?></td>
									<td><?=$value['fields']['resolution']['name'];?></td>
									<td><?=date('d-m-Y', strtotime($value['fields']['duedate']));?></td>
									<td><?=date('d-m-Y H:i:s', strtotime($value['fields']['updated']));?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
							<?php
							if (@$issues['error']) {
							?>
							<tfoot><tr><th colspan="7"><?=@$issues['error']?></th></tr></tfoot>
							<?php
							}
							?>
						<?php endif; ?>
					</table>

				</div>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<?= $body ?>
	<?php endif; ?>

	<!-- End Main Content -->
</div>