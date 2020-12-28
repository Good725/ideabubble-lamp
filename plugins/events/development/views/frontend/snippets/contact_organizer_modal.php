<?php ob_start(); ?>
    <form action="/frontend/events/contact" method="post" class="validate-on-submit">
        <input type="hidden" name="organiser_id" value="<?= isset($organizer_id) ? $organizer_id : '' ?>" class="modal-item_id" />
        <?php if (isset($event_object)): ?>
            <input type="hidden" name="event_id" value="<?= $event_object->id ?>" />
        <?php endif; ?>

        <div class="form-group">
            <label class="col-sm-4" for="modal--contact_organizer-name">
                <?= __('Name') ?>
            </label>

            <div class="col-sm-8">
                <?= Form::ib_input(null, 'name', null, array('autocomplete' => 'name', 'id' => 'modal--contact_organizer-name')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="modal--contact_organizer-email">
                <?= __('Email') ?>
            </label>

            <div class="col-sm-8">
                <?= Form::ib_input(null, 'email', null, array('autocomplete' => 'email', 'class' => 'validate[required,custom[email]]', 'id' => 'modal--contact_organizer-email')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="modal--contact_organizer-telephone">
                <?= __('Telephone') ?>
            </label>

            <div class="col-sm-8">
                <?= Form::ib_input(null, 'telephone', null, array('autocomplete' => 'tel', 'id' => 'modal--contact_organizer-telephone')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="modal--contact_organizer-message">
                <?= __('Message') ?>
            </label>

            <div class="col-sm-8">
                <?= Form::ib_textarea(null, 'message', null, array('class' => 'validate[required]', 'id' => 'modal--contact_organizer-message', 'rows' => 4)); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-8">
                <button type="submit" class="button button--full secondary"><?= __('Send') ?></button>
            </div>
        </div>
    </form>
<?php $modal_body = ob_get_clean(); ?>

<?php
echo View::factory('front_end/snippets/modal')
    ->set('id',    'modal--contact_organizer')
    ->set('width', '500px')
    ->set('title',  __('Contact Organiser'))
    ->set('body',   $modal_body)
;
?>