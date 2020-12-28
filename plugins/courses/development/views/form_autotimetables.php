<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>
<script>
    $(document).ready(function(){
        $('.multiselect').multiselect();

        setTimeout(initButtons, 2000);

        $("#autotimetables_table_preview_wrapper").find("button").click(function(ev){
            ev.preventDefault();
        });

        $('.DTTT_button_csv').addClass('icon-download-alt');
        $('.DTTT_button_pdf').addClass('icon-download-alt');

        $("#btn_save").click(function(){
            var id = $('#att_id').val();
            $("#att_redirect").val("/admin/courses/edit_autotimetable/?id="+id);
            $("#form_add_edit_autotimetable").submit();
        });
        $("#btn_save_exit").click(function(){
            $("#att_redirect").val("/admin/courses/autotimetables");
            $("#form_add_edit_autotimetable").submit();
        });
    });
    function initButtons(){
        $('.autotimetable_list').dataTable({
            "sDom": 'T<"clear">lrt',
            "sPaginationType":"bootstrap",
            "bDestroy": true,
            "bRetrieve": true,
            "bPaginate": false,
            "bSort": false,
            "oLanguage":{
                "sLengthMenu":"_MENU_ records per page"
            },
            "aaSorting": [],
            "oTableTools": {
                "sSwfPath": "<?=URL::get_engine_plugin_assets_base('courses');?>swf/copy_cvs_xls_pdf.swf",
                "aButtons": [ "csv", "pdf" ]
            }
        });
    }
    //http://static.kilmartin.websitecms.dev/engine/plugins/courses/swf/copy_cvs_xls_pdf.swf
</script>
<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('courses');?>js/lmcbutton.js"></script>
<style>
    .new_date { border-top: 3px solid #000; }
</style>

<div class="col-sm-12">
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

<form class="col-sm-12 form-horizontal" id="form_add_edit_autotimetable" name="form_add_edit_autotimetable" action="/admin/courses/save_autotimetable/" method="post">
    <!-- Redirect -->
    <input type="hidden" id="att_redirect" name="redirect" value="/admin/courses/autotimetables" />

    <!-- Name -->
    <div class="form-group">
		<div class="col-sm-7">
			<label class="sr-only" for="att_name">Name</label>
			<input type="text" class="form-control required" id="att_name" name="name" placeholder="Enter timetable name here" value="<?=isset($data['name']) ? $data['name'] : ''?>"/>
		</div>
    </div>

    <ul class="nav nav-tabs">
        <li><a href="#details_tab" data-toggle="tab">Details</a></li>
        <li><a href="#preview_tab" data-toggle="tab">Preview</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="details_tab">
            <!-- Category -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="att_category_id">Category</label>

                <div class="col-sm-5">
                    <select class="form-control" id="att_category_id" name="category_id">
                        <option
                            value=""<?=(!isset($data['category_id']) OR ($data['category_id'] == '')) ? ' selected="selected"' : '' ?>>
                            Select Category
                        </option>

                        <?php foreach ($categories as $category): ?>
                            <option
                                value="<?= $category['id'] ?>"<?=(isset($data['category_id']) AND ($data['category_id'] == $category['id'])) ? ' selected="selected"' : '' ?>><?=$category['category']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Location -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="att_location_id">Location</label>

                <div class="col-sm-5">
                    <select class="form-control" id="att_location_id" name="location_id">
                        <option
                            value=""<?=(!isset($data['location_id']) OR ($data['location_id'] == '')) ? ' selected="selected"' : '' ?>>
                            Select Location
                        </option>

                        <?php foreach ($locations as $location): ?>
                            <option
                                value="<?= $location['id'] ?>"<?=(isset($data['location_id']) AND ($data['location_id'] == $location['id'])) ? ' selected="selected"' : '' ?>><?=$location['name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Start Date -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="att_date_start">Start Period</label>
                <div class="col-sm-2">
                    <input type="text" id="att_date_start" class="form-control datepicker" name="date_start" value="<? if(isset($data['date_start'])) echo date('d-m-Y', strtotime($data['date_start'])); ?>" />
                </div>
            </div>

            <!-- Expiry Date -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="att_date_end">End Period</label>
                <div class="col-sm-2">
                    <input type="text" id="att_date_end" class="form-control datepicker" name="date_end" value="<? if(isset($data['date_end'])) echo date('d-m-Y', strtotime($data['date_end'])); ?>"/>
                </div>
            </div>

            <!-- Years -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="att_years">Years</label>
                <div class="col-sm-3">
                    <select class="multiselect" multiple="multiple" id="att_years" name="years[]">
                        <?php
                        foreach ($years as $year)
                        {
                            $selected = '';
                            if (isset($data['years']))
                            {
                                foreach ($data['years'] as $data_year)
                                {
                                    if ($data_year['id'] == $year['id'])
                                    {
                                        $selected = ' selected="selected"';
                                    }
                                }
                            }
                            echo '<option value="'.$year['id'].'"'.$selected.'>'.$year['year'].'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="att_description">Description</label>

                <div class="col-sm-7">
                    <textarea class="form-control" id="att_description" name="description" rows="4"><?=isset($data['description']) ? $data['description'] : ''?></textarea>
                </div>
            </div>

            <!-- Publish -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="publish">Publish</label>

                <div class="col-sm-7">
                     <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default<?= (!isset($data['publish']) OR $data['publish'] == '1') ? ' active' : '' ?>">
                            <input type="radio"<?= (!isset($data['publish']) OR $data['publish'] == '1') ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
                        </label>
                        <label class="btn btn-default<?= ( isset($data['publish']) AND $data['publish'] == '0') ? ' active' : '' ?>">
                            <input type="radio"<?= ( isset($data['publish']) AND $data['publish'] == '0') ? ' checked="checked"' : '' ?> value="0" name="publish">No
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="preview_tab">
            <div id="container_copy"></div>
            <?
            /*
            ** Get all years
            ** Get all unique dates
            ** Get all schedules per-date, per-year
            ** Open table, thead, tr
            ** Loop through each year, printing each in a th
            ** Close tr, thead, open tbody
            ** Loop through each date
            *** Count the number of schedules in each year for the date
            *** Get the year with the most schedules
            *** Loop from i = 0 to the number of schedules in that year
            **** if i = 0
            ***** Open tr, with class = "new_date". Print td, with date inside.
            **** else
            ***** Open tr, print empty td
            **** Loop through each year
            ***** Print the i-th schedule in each year for that date in a td. If not set, print an empty td.
            **** Close tr
            ** Close tbody
            ** Print tfoot
            ** Close table
            */

            if (isset($preview_data))
            {
                // Get all years
                if (isset($data['years'])) {
                    sort($data['years']);
                }

                // Get all unique dates
                $previous_date = ' ';
                $dates = array();
                foreach ($preview_data as $course)
                {
                    $date = date('D jS M', strtotime($course['date']));
                    if (isset($course['date']) AND $date != $previous_date)
                    {
                        $previous_date = $course['date'];
                        array_push($dates, $course['date']);
                    }
                }
                $dates = array_unique($dates);

                // Get all schedules per-date, per-year
                $schedules = array();
                foreach ($dates as $date)
                {
                    foreach($data['years'] as $year)
                    {
                        $schedules[$date][$year['id']] = array();
                        foreach($preview_data as $event)
                        {
                            if ($event['year_id'] == $year['id'] AND $event['date'] == $date)
                            {
                                array_push($schedules[$date][$year['id']], $event);
                            }
                        }
                    }
                }

                // Open table, thead, tr
                echo '<table class="table table-striped autotimetable_list" id="autotimetables_table_preview">
                            <caption>'.$data['name'].'</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>';

                // Loop through each year, printing it in a th
                foreach ($data['years'] as $year)
                {
                    echo '<th scope="col">'.$year['year'].'</th>';
                }

                // Close tr, thead, open tbody
                echo '</tr></thead><tbody>';

                // Loop through each date
                foreach ($dates as $date)
                {
                    // Get the year with the most schedules
                    $max = 0;
                    foreach ($schedules[$date] as $years)
                    {
                        if (count($years) > $max)
                        {
                            $max = count($years);
                        }
                    }

                    // Loop form i = 0 to the number of schedules in that year
                    for ($i = 0; $i < $max; $i++)
                    {
                        echo "\n";
                        if ($i == 0)
                            echo '<tr class="new_date"><td>'.date('D jS M', strtotime($date)).'</td>';
                        else
                            echo '<tr><td></td>';

                        foreach ($data['years'] as $year)
                        {
                            echo '<td><a href="/course-detail/'.@$schedules[$date][$year['id']][$i]['title'].'.html/'.
								'?id='.@$schedules[$date][$year['id']][$i]['course_id'].
								'&schedule_id='.@$schedules[$date][$year['id']][$i]['schedule_id'].
								'">'.@$schedules[$date][$year['id']][$i]['title'].'</a></td>';
                        }
                        echo '</tr>';
                    }
                }
                echo '<tbody>';

                if (isset($data['years']) AND isset($data['description']))
                {
                    echo '<tfoot>
                        <tr>
                            <td colspan="'.(count($data['years']) + 1).'">'.$data['description'].'</td>
                        </tr>
                    </tfoot>';
                }

                echo '</table>';
            }
            ?>

        </div>
    </div>

    <!-- Identifier -->
    <input type="hidden" id="att_id" name="id" value="<?= isset($data['id']) ? $data['id'] : '' ?>"/>

    <div class="well">
        <a href="#" class="btn btn-primary save_button" id="btn_save">Save</a>
        <a href="#" class="btn btn-primary save_button" id="btn_save_exit">Save &amp; Exit</a>
        <button type="reset" class="btn">Reset</button>
        <?php if (isset($data['id'])) : ?>
            <a href="#" class="btn btn-danger" id="btn_delete" data-id="<?= $data['id'] ?>">Delete</a>
        <?php endif; ?>
    </div>
</form>

<?php if (isset($data['id'])) : ?>
    <div class="modal fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Warning!</h3>
				</div>
				<div class="modal-body">
					<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected course.</p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" data-id="0" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
    </div>
<?php endif; ?>

<script>
	$(document).ready(function()
	{
		ShowLMCButton('copy html', 'Copy', '', "<?=URL::get_engine_plugin_assets_base('courses');?>swf/lmcbutton.swf");
	});
</script>
