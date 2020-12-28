<div id="c" class="col-sm-12">
    <form id="_add_edit_form" class="form-horizontal" action="admin/__________/save_/') ?>" method="post">
        <?= (isset($alert)) ? $alert : '' ?>
        <fieldset>
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">Title</label>

                <div class="col-sm-7">
                    <input type="text" id="title" name="title" class="form-control popinit" rel="popover" value="<?= $item->title ?>" placeholder="Enter Title" required="required">
                </div>
            </div>
            <input type="hidden" id="id" name="id" value="<?= $item->id;?>">

            <? // Input?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="publish">Publish</label>
                <div class="col-sm-9" >
                    <input type="text" id="" name="" value="<? ?>">
                </div>
            </div>

            <? // Select?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="publish">Publish</label>
                <div class="col-sm-9" >
                    <select id="" name="">
                        <option value="">Please Select</option>
                        <?php foreach($_options as $option):
                            $selected = $data->option == $option->id ? ' selected="selected' : '' ;
                            ?>

                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <? // Publish ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="disable_purchase">Publish</label>
                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <?php $publish = ( ! $data->publish) OR $data->publish == '1'; ?>
                    <label class="btn btn-default<?= $publish ? ' active' : '' ?>">
                        <input type="radio"<?= $publish ? ' checked' : '' ?>  value="1" name="publish">Yes
                    </label>
                    <label class="btn btn-default<?= ! $publish ? ' active' : '' ?>">
                        <input type="radio"<?= ! $publish ? ' checked' : '' ?>  value="0" name="publish">No
                    </label>
                    <p class="help-inline"></p>
                </div>
            </div>

        </fieldset>

        <? // Action Buttons ?>
        <input type="hidden" id="save_exit" name="save_exit" value="false" />

        <div class="col-sm-12">
            <div class="well">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="submit" class="btn btn-primary" onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit</button>
                <button type="reset" class="btn" id="event-form-reset">Reset</button>
                <?php if (is_numeric($item->id)) : ?>
                    <a class="btn btn-danger" id="btn_delete" data-id="<?=$item->id ?>">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>