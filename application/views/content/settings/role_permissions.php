<script>

    $(document).ready(function(){

        $('input.main').click(function(){

            $checkboxes = $('div#controller_' + $(this).data('id')).find(':checkbox');

            console.log($checkboxes);

            if ($(this).is(':checked')) {
                $checkboxes.prop('checked', true);
            }
            else {
                $checkboxes.prop('checked', false);
            }


        });

        $('div#code_pices a.check').click(function(){

            $checkboxes = $('div#code_pices').find(':checkbox');

            $checkboxes.prop('checked', true);

        });

        $('div#code_pices a.uncheck').click(function(){

            $checkboxes = $('div#code_pices').find(':checkbox');

            $checkboxes.prop('checked', false);

        });

    });


</script>
<style>
	.permissions_form label{display:block;}
</style>



<div class="span12">
    <h1 style="margin: 10px 0; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
        Permissions for: <u><?=$role->role; ?></u> role
    </h1>
</div>

<form action="/admin/settings/set_permissions/<?=$role->id; ?>" method="POST" class="permissions_form">
    <div class="span6">

        <h2>Controllers / Actions</h2>

        <?php foreach($controllers as $controller): ?>

			<b><?=$controller->name; ?></b>
            <label>
				<input type="checkbox" name="" value="" class="main" data-id="<?=$controller->id; ?>"/> check all
            </label>

            <div style="margin-left: 20px;" id="controller_<?=$controller->id; ?>">
				<label>
					<input type="checkbox"<?= $role->has('resource', $controller->id) ? ' checked="checked"' : '' ?> name="resource[<?=$controller->id; ?>]" value="1" />
					<?= $controller->name ?> <span style="color: #999;">(<?= $controller->alias ?>)</span>
				</label>

                <?php foreach($controller->get_actions_4_controller() as $action): ?>

                    <label>
						<input type="checkbox"<?= $role->has('resource', $action->id) ? ' checked="checked"' : '' ?> name="resource[<?=$action->id; ?>]" value="1" />
						<?=$action->name; ?> <span style="color: #999;">(<?=$action->alias; ?>)</span>
                    </label>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </div>

    <div id="code_pices" class="span6">

        <h2>Code pieces</h2>

        <p><a class="check" href="#">check all</a> / <a class="uncheck" href="#">uncheck all</a></p>


        <?php foreach($code_pieces as $code): ?>

            <label>
				<input type="checkbox" <?= $role->has('resource', $code->id) ? 'checked' : '' ?> name="resource[<?=$code->id; ?>]" value="1" />
				<?=$code->name; ?> <span style="color: #999;">(<?=$code->alias; ?>)</span>
            </label>

        <?php endforeach; ?>



    </div>

    <div class="span12">
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
    </div>
</form>