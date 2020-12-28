<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : ''?>
            <h2 class="">Projects Report</h2>
        </div>
    </div>
</div>

<style type="text/css">
    #date_from, #date_to, #date_from input, #date_from label, #date_to input, #date_to label { display: inline-block;}
</style>

<form class="form-horizontal">
    <div id="date_from">
        <label class="col-sm-5 control-label" for="group_by[]">Display Fields</label>
		<div class="col-sm-7">
			<select id="group_by[]" name="group_by[]" multiple="multiple" class="multipleselect">
				<?php foreach(array('Project', 'Author', 'Month', 'Sprint', 'Estimate', 'Ticket', 'Status', 'Resolution') as $group_by): ?>
					<option value="<?= $group_by ?>" <?= @in_array($group_by, $params['group_by']) ? ' selected="selected"' : '' ?>><?= $group_by ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<hr />
		<label class="col-sm-5 control-label"><h2>Filters</h2></label>
		<label class="col-sm-5 control-label" for="month">Month</label>
		<div class="col-sm-7">
			<select id="month" name="month" class="form-control">
				<option value="">All</option>
				<?php
				foreach($months as $month): 
				?>
					<option value="<?= $month['month'] ?>" <?= $month['month'] == @$params['month'] ? ' selected="selected"' : '' ?>><?= date('M, Y', strtotime($month['month'])) ?></option>
				<?php
				endforeach; 
				?>
			</select>
		</div>

		<label class="col-sm-5 control-label" for="project_id">Project</label>
		<div class="col-sm-7">
			<select id="project_id" name="project_id" class="form-control">
				<option value="">All</option>
				<?php
				foreach($projects as $project): 
				?>
					<option value="<?= $project['id'] ?>" <?= $project['id'] == @$params['project_id'] ? ' selected="selected"' : '' ?>><?= $project['title'] ?></option>
				<?php
				endforeach; 
				?>
			</select>
		</div>
		
		<label class="col-sm-5 control-label" for="author">Author</label>
		<div class="col-sm-7">
			<select id="author" name="author" class="form-control">
				<option value="">All</option>
				<?php
				foreach($authors as $author): 
				?>
					<option value="<?= $author['author'] ?>" <?= $author['author'] == @$params['author'] ? ' selected="selected"' : '' ?>><?= $author['author'] ?></option>
				<?php
				endforeach; 
				?>
			</select>
		</div>
    </div>
    <button type="submit" id="btn_sort" class="btn">List</button>
</form>

<table class="table table-striped dataTable" id="worklog_table">
    <thead>
        <tr>
			<?php if(@array_search('Project', $params['group_by']) !== false){ ?>
            <th scope="col">Project</th>
			<?php } ?>
			<?php if(@array_search('Sprint', $params['group_by']) !== false){ ?>
				<th scope="col">Sprint</th>
			<?php } ?>
			<?php if(@array_search('Author', $params['group_by']) !== false){ ?>
            <th scope="col">Author</th>
			<?php } ?>
			<?php if(@array_search('Month', $params['group_by']) !== false){ ?>
            <th scope="col">Month</th>
			<?php } ?>
            <?php if(@array_search('Estimate', $params['group_by']) !== false){ ?>
                <th scope="col">Estimate</th>
            <?php } ?>
			<th scope="col">Time Spent</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($report as $worklog): ?>
            <tr>
				<?php if(@array_search('Project', $params['group_by']) !== false){ ?>
				<td><?='<b>' . $worklog['project'] . '</b>'?></td>
				<?php } ?>
				<?php if(@array_search('Sprint', $params['group_by']) !== false){ ?>
					<td><?=$worklog['sprint']?></td>
				<?php } ?>
				<?php if(@array_search('Author', $params['group_by']) !== false){ ?>
				<td><?=$worklog['author']?></td>
				<?php } ?>
				<?php if(@array_search('Month', $params['group_by']) !== false){ ?>
				<td><?=$worklog['month']?></td>
				<?php } ?>
                <?php if(@array_search('Status', $params['group_by']) !== false){ ?>
                    <td><?=$worklog['status']?></td>
                <?php } ?>
                <?php if(@array_search('Resolution', $params['group_by']) !== false){ ?>
                    <td><?=$worklog['resolution']?></td>
                <?php } ?>
                <?php if(@array_search('Estimate', $params['group_by']) !== false){ ?>
                    <td><?=round(@$worklog['timeoriginalestimate'] / 3600.0, 2)?>h</td>
                <?php } ?>
				<td><?=round(@$worklog['timespent'] / 3600.0, 2)?>h</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
$(document).ready(function(){
	$('.multipleselect').multiselect({ 
		enableFiltering: false, 
		numberDisplayed: 3 
	});
});
</script>