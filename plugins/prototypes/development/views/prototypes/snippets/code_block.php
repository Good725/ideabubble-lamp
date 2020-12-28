<div class="form-group collapse" id="<?= $id ?>">
    <label class="col-xs-12">
        <textarea
            class="form-control code_editor"
            data-height="auto"
            data-mode="application/x-httpd-php"
            id="<?= $id ?>-textarea"
            rows="<?= 1 + substr_count($code, "\n") ?>"
            ><?= htmlentities(trim($code)) ?></textarea>
    </label>
</div>