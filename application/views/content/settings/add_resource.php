<script>

$(document).ready(function(){

    $('select[name=type_id]').change(function(){

        //if resource type is "Action"
        if($(this).val()=='1') {

            $('div#controllers').show();

        } else {

            $('div#controllers').hide();
            $('select[name=parent_controller]').val("");

        }

    })

});

</script>


<div class="col-sm-12">

	<h1 style="margin: 10px 0px;">
		<?php if(Request::current()->param('id')): ?>
			Edit resource
		<?php else: ?>
			New resource
		<?php endif; ?>
	</h1>

    <form class="form-horizontal" action="<?php echo URL::Site('admin/settings/add_resources/'.Request::current()->param('id')) ?>" method="post">

        <?php
        //This is needed to display any error that might be loaded into the messages queue
        if (isset($alert)) {
            echo $alert;
        }
        ?>
        <?php
			if(isset($alert)){
			?>
				<script>
					remove_popbox();
				</script>
			<?php
			}
		?>

        <fieldset>
            <legend>Resource info</legend>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="type_id">Resource type</label>
                <div class="col-sm-7">
                    <select data-original-title="Resource type" rel="popover" data-content="" class="form-control popinit" name="type_id" id="type_id">
                        <option value="">- select -</option>
                        <?php foreach(Kohana::$config->load('resource_types') as $id => $type): ?>

                            <?php if($id == Arr::get($data, 'type_id')): ?>
                                <option selected value="<?=$id; ?>"><?=$type; ?></option>
                            <?php else: ?>
                                <option value="<?=$id; ?>"><?=$type; ?></option>
                            <?php endif; ?>

                        <?php endforeach; ?>

                    </select>
                </div>
            </div>

            <?php //echo Debug::vars(Kohana::$config->load('resource_types.'.Arr::get($data, 'type_id'))); ?>

            <?php if(Kohana::$config->load('resource_types.'.Arr::get($data, 'type_id'))=='Action'): ?>
                <div class="form-group" id="controllers" style="">
            <?php else: ?>
                <div class="form-group" id="controllers" style="display: none;">
            <?php endif; ?>

                <label class="col-sm-2 control-label" for="parent_controller">Controller name</label>
                <div class="col-sm-7">
                    <select data-original-title="Resource type" rel="popover" data-content="" class="form-control popinit" name="parent_controller" id="parent_controller">
                        <option value="">- select -</option>
                        <?php foreach($controllers as $id => $name): ?>

                            <?php if($id == Arr::get($data, 'parent_controller')): ?>
                                <option selected value="<?=$id; ?>"><?= $name; ?></option>
                            <?php else: ?>
                                <option value="<?=$id; ?>"><?= $name; ?></option>
                            <?php endif; ?>

                        <?php endforeach; ?>

                    </select>
                </div>
            </div>


            <div class="form-group">
                <label for="alias" class="col-sm-2 control-label">Resource alias</label>

                <div class="col-sm-7">
                    <input type="text" id="alias" name="alias"
                           data-content="Resource alias" rel="popover"
                           data-original-title="Resource alias" class="form-control popinit" rel="popover" value="<?=Arr::get($data, 'alias')?>">
                </div>
            </div>

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Resource name</label>

                <div class="col-sm-7">
                    <input type="text" id="name" name="name"
                           data-content="Resource name" rel="popover"
                           data-original-title="Resource name" class="form-control popinit" rel="popover" value="<?=Arr::get($data, 'name')?>">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="col-sm-2 control-label">Resource description</label>

                <div class="col-sm-7">
                    <textarea class="form-control popinit"
                           id="description" name="description"
                           data-content="Resource description" rel="popover"
                           data-original-title="Resource description" rel="popover"><?=Arr::get($data, 'description')?></textarea>

                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save</button>
            </div>

        </fieldset>

    </form>

</div>
