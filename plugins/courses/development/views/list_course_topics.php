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
<div id="message"></div>
<table class="table table-striped dataTable" id="topics_table">
	<thead>
		<tr>
			<th>Title</th>
			<th>Description</th>
<!--			<th>Year</th>-->
<!--			<th>Level</th>-->
<!--			<th>Category</th>-->
<!--			<th>Subject</th>-->
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<div class="modal fade" id="confirm_delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Warning!</h3>
            </div>
            <div class="modal-body">
                <p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected Topic.</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
            </div>
        </div>
    </div>
</div>
<?php
	require_once('course_topic_popup.php');
?>
<?php // Moved from the KES theme stylesheet to here. Please convert to Bootstrap popovers, then delete this CSS ?>
<style>
    .table .tooltip-wrap{
        position: relative;
    }

    .table .tooltip-txt {
        text-overflow:ellipsis;
        overflow-x:hidden;
        display:inline-block;
        white-space:nowrap;
        width: 100%;
        max-width: 350px;
    }

    .table .tooltip-more {
        padding: 4px 8px;
        position: absolute;
        z-index: 20;
        background: #f3f3f3;
        box-shadow: 0px 1px 2px rgba(0,0,0,0.26);
        border-radius: 5px;
        width: auto;
        font-size: 14px;
        color: #929292;
        font-weight: 300;
        display: none;
        white-space: normal;

        left: 0px;
        right: 0px;
        margin: auto;
        margin-top: 15px;
    }
    .table .tooltip-txt:hover .tooltip-more{
        display: block;
    }
    .table .tooltip-more:before{
        content: "";
        position: absolute;
        background: #f3f3f3;
        width: 15px;
        height: 15px;
        border-radius: 1px;
        border-right: 1px solid #eee;
        border-top: 1px solid #eee;
        -ms-transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
        top:-7px;
        left: 0px;
        right: 0px;
        margin:auto;
    }
</style>