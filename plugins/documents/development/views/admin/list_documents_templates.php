<?= (isset($alert)) ? $alert : '' ?>
<!--  the datatable for the listing of saved documents -->
<?= $datatable ?>

<!-- The modal window for the click generate documents -->
<div id="generate_documents_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Generate a document for Contact ID #<span id="generate_documents_contact_id"></span></h3>
            </div>

            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>

                <div class="form-group">
                    <label class="col-sm-5 control-label" for="doc_template">Document Template:</label>

                    <div class="col-sm-7">
                        <?php
                        $options = html::optionsFromRows('name', 'name', $templates, null, ['value' => '', 'label' => '-- Please select --']);
                        echo Form::ib_select(null, null, $options, null, ['id' => 'doc_template']);
                        ?>
                    </div>
                </div>

                <div id="documents_templates_parameters"><?= $doc_parameters ?></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" data-dismiss="modal" aria-hidden="true" id="generate_save">Generate &amp; Save</button>
                <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true" id="generate_save_download">Generate &amp; Download</button>
                <button class="btn btn-cancel" data-dismiss="modal" aria-hidden="true" id="cancel">Cancel</button>
            </div>
        </div>
    </div>
</div>