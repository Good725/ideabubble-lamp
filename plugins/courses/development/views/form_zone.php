<?php $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

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

<form class="col-sm-12 form-inline form-horizontal" id="form_add_zone" name="form_add_zone" action="/admin/courses/save_zone/" method="post">

    <input type="hidden" id="redirect" name="redirect" />


       <!-- Name -->
       <div class="form-group col-sm-12">
           <label class="col-sm-2 control-label" for="name">Zone</label>
           <div class="col-sm-10">
               <input type="text" class="form-control required" id="name" name="name" placeholder="Enter Zone name here" value="<?=isset($data['name']) ? $data['name'] : ''?>"/>
           </div>
       </div>

       <!-- Price -->
<!--       <div class="form-group col-sm-6">-->
<!--           <label class="col-sm-4 control-label" for="price">Price of zone</label>-->
<!---->
<!--           <div class="col-sm-7">-->
<!--               <div class="input-group">-->
<!--                   <input type="number" min="0" class="form-control required"  id="price" name="price" value="--><?//=isset($data['price']) ? $data['price'] : ''?><!--"/>-->
<!--                   <span class="input-group-addon" id="percentage">%</span>-->
<!--               </div>-->
<!--           </div>-->
<!--       </div>-->


    <!-- Zone Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>

    <div class="well">
        <button type="button" class="btn btn-primary save_button" data-redirect="save">Save</button>
        <button type="button" class="btn btn-link cancel_button" >Cancel</button>
        <button type="reset" class="btn">Reset</button>
        <?php if (isset($data['id'])) : ?>
<!--            <a href="#" class="btn btn-danger" id="btn_delete" data-id="--><?//=$data['id']?><!--">Delete</a>-->
        <?php endif; ?>
    </div>
</form>
<?php if (isset($data['id'])) : ?>
	<div class="modal hide fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Warning!</h3>
				</div>

				<div class="modal-body">
					<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected Zone.</p>
				</div>

				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
