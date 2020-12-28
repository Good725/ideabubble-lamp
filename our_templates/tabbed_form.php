<div class="col-sm-12">
    <?=(isset($alert)) ? $alert : ''?>
</div>

<form class="col-sm-12 form-horizontal" id="form_add_edit_" name="form_add_edit_" action="/admin/______/save_/" method="post">

    <div class="form-group">
        <div class="col-sm-9">
            <label class="sr-only" for="title">Title</label>
            <input type="text" class="form-control required" id="title" name="title" placeholder="Enter product title here" value="<?= $item->title?>"/>
        </div>
        <input type="hidden" id="id" name="id" value="<?= $item->id;?>">
    </div>

    <ul class="nav nav-tabs">
        <li><a href="#summary_tab" data-toggle="tab">Configuration</a></li>
        <li><a href="#_tab" data-toggle="tab"></a></li>
    </ul>

    <div class="tab-content clearfix">
        <? // Summary Tab ?>
        <div class="col-sm-9 tab-pane active" id="summary_tab">

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

        </div>

        <? // Tab ?>
        <div class="col-sm-9 tab-pane" id="_tab">

        </div>


    </div>

    <!-- Product Identifier -->
    <input type="hidden" id="save_exit" name="save_exit" value="false" />
    <div class="col-sm-12">
        <div class="well">
            <button type="submit" id="save_button" data-redirect="self" class="btn btn-primary save_button">Save</button>
            <button type="submit" data-redirect="products" onclick="$('#save_exit')[0].setAttribute('value', 'true');" class="btn btn-success save_button">Save &amp; Exit</button>
            <button type="reset" class="btn">Reset</button>
        </div>
    </div>
</form>


<script type="text/javascript">
    $(document).ready(function(){
        $('[rel="popover"]').popover();
    });
</script>
