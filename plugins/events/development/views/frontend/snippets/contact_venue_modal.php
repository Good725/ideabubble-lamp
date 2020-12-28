<?php ob_start(); ?>
    <form action="/frontend/events/contact" method="post" class="validate-on-submit">
        <input type="hidden" name="venue_id" value="<?= isset($venue_id) ? $venue_id : '' ?>" class="modal-item_id" />
        <?php if (!empty($event_object)): ?>
            <input type="hidden" name="event_id" value="<?= $event_object->id ?>" />
        <?php endif; ?>

        <div class="form-group">
            <label class="col-sm-4" for="modal--contact_venue-name">
                <?= __('Name') ?>
            </label>
            <div class="col-sm-8">
                <?= Form::ib_input(null, 'name', null, array('autocomplete' => 'name', 'class' => 'validate[required]', 'id' => 'modal--contact_venue-name')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="modal--contact_venue-email">
                <?= __('Email') ?>
            </label>
            <div class="col-sm-8">
                <?= Form::ib_input(null, 'email', null, array('autocomplete' => 'email', 'class' => 'validate[required],custom[email]', 'id' => 'modal--contact_venue-email')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="modal--contact_venue-telephone">
                <?= __('Telephone') ?>
            </label>
            <div class="col-sm-8">
                <?= Form::ib_input(null, 'telephone', null, array('autocomplete' => 'tel', 'class' => 'validate[required]', 'id' => 'modal--contact_venue-telephone')); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4" for="modal--contact_venue-message">
                <?= __('Message') ?>
            </label>
            <div class="col-sm-8">
                <?= Form::ib_textarea(null, 'message', null, array('class' => 'validate[required]', 'id' => 'modal--contact_venue-message', 'rows' => 4)); ?>
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
    ->set('id',    'modal--contact_venue')
    ->set('width', '500px')
    ->set('title', __('Contact Venue'))
    ->set('body',  $modal_body)
?>