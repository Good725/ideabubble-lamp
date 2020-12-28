<div class="col-sm-12">
	<?= (isset($alert)) ? $alert : '' ?>
	<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
	?>
	<h2 class="">Import CSV</h2>
</div>

<form class="col-sm-12 form-horizontal" id="form_import_csv" name="form_import_csv" action="/admin/contacts2/import_csv?step=2" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>File Import</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="csv">CSV Source File</label>
            <div class="col-sm-5">
                <input type="file" id="csv" name="csv" required="required" />
            </div>
        </div>
        <p>You can use this file as a sample: <a href="/admin/contacts2/sample_csv_import">download</a></p>
    </fieldset>

    <fieldset>
        <legend>Advanced</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="encoding">File Encoding</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="encoding" name="encoding" value="UTF-8" required="required" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="delimiter">Delimiter</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="delimiter" name="delimiter" value="," required="required" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="enclosure">Enclosure</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="enclosure" name="enclosure" value="&quot;" required="required" />
            </div>
        </div>
    </fieldset>

    <div class="well">
        <button type="submit" class="btn btn-primary" name="next">Next</button>
        <a href="/admin/contacts2" class="btn">Cancel</a>
    </div>
</form>
<script>
    $(document).ready(function(){
        $("#form_import_csv").on("submit", function(){
            if ($("#form_import_csv #csv").val().search(/\.csv/gi) == -1){
                alert("Please use a .csv file");
                return false;
            }
            return true;
        });
    });
</script>
