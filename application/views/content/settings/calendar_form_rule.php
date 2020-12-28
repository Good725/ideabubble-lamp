<div id="calendar_add_edit_rule" class="col-sm-12">
    <form id="calendar_rule_edit_form" class="form-horizontal" action="<?php echo URL::Site('admin/calendars/save_rule/') ?>" method="post">
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
        <fieldset>
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">Title</label>

                <div class="col-sm-7">
                    <input type="text" id="title" name="title" class="form-control popinit" rel="popover" value="<?= isset($rule->title) ? $rule->title : '';?>" required="required">
                </div>
            </div>
            <input type="hidden" id="id" name="id" value="<?= isset($rule->id) ? $rule->id : '';?>">

            <? // Plugin affected by event rule ?>
            <div class="form-group">
                <label for="plugin_name" class="col-sm-2 control-label">Plugin</label>

                <div class="col-sm-7">
                    <div class="selectbox">
                        <select id="plugin_name" name="plugin_name" class="form-control popinit">
                            <option value="">Please select a plugin to apply</option>
                            <?php foreach($plugins as $plugin):
                            $selected = $rule->plugin_name == $plugin['name'] ? 'selected="selected"' : ''
                            ?>
                                <option value="<?= $plugin['name'];?>" <?= $selected;?>"><?= $plugin['friendly_name'] ;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>    
                </div>
            </div>

            <? // Description of the rule ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="description">Description</label>

                <div class="col-sm-7">
                    <textarea class="form-control" id="description" name="description" rows="4"><?=isset($rule->description) ? $rule->description : ''?></textarea>
                </div>
            </div>

            <? // Publish ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="publish">Publish</label>

                <div class="col-sm-4">
                    <div class="btn-group" data-toggle="buttons">
                        <?php $publish = ( $rule->publish == '1' OR is_null($rule->publish) ); ?>
                        <label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
                            <input type="radio" name="publish" value="1" id="publish_yes"<?= $publish ? ' checked' : '' ?> />Yes
                        </label>
                        <label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
                            <input type="radio" name="publish" value="0" id="publish_no"<?= ( ! $publish) ? ' checked' : '' ?> />No
                        </label>
                    </div>
                </div>
            </div>

        </fieldset>


        <? // Action Buttons ?>
        <input type="hidden" id="save_exit" name="save_exit" value="false" />

        <div class="col-sm-12">
            <div class="well">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="submit" class="btn btn-primary" onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit</button>
                <button type="reset" class="btn" id="rule-form-reset">Reset</button>
                <?php if (is_numeric($rule->id)) : ?>
                    <a class="btn btn-danger" id="btn_delete" data-item="calendar_rules" data-id="<?=$rule->id?>">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
