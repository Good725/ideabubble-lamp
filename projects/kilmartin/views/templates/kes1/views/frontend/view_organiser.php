<?php require_once Kohana::find_file('template_views', 'header'); ?>

<?php $organizer = isset($organiser_object) ? $organiser_object : null; ?>

<?php if ($organizer == null || !$organizer->contact_id): ?>
    <div class="row">
        <div class="page-content">
            <p><?= __('No organiser found.') ?></p>
        </div>
    </div>
<?php else: ?>
    <style>
        .organizer-image{position:relative;}
        .organizer-image img {display:block;width:100%;}
        .organizer-image-profile{margin:auto;width: 244px;}
        .organizer-image-banner + .organizer-image-profile {position:absolute;left:.5em;bottom:.5em;width: 33%;max-width:244px;}
        .social_media-list {font-size: 1.1rem;}
    </style>

    <div class="row">
        <?php
        $banner_image  = $organizer->get_banner_image();
        $profile_image = $organizer->get_profile_image();
        ?>

        <?php if ($banner_image): ?>
            <div class="organizer-image">
                <div class="organizer-image-banner">
                    <img src="<?= $banner_image ?>" alt="" />
                </div>

                <?php if ($profile_image): ?>
                    <div class="organizer-image-profile">
                        <img src="<?= $profile_image ?>" alt="" width="244" height="244" />
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($profile_image): ?>
            <div class="organizer-image-profile">
                <img src="<?= $profile_image ?>" alt="" />
            </div>
        <?php endif; ?>

        <div class="row text-center">
            <div class="page-content">
                <div>
                    <h1><?= $organizer->get_name() ?></h1>
                </div>
            </div>

            <?php if ($organizer->contact->address1): ?>
                <address>
                    <?php if ($organizer->contact->address1): ?>
                        <span class="line"><?= $organizer->contact->address1 ?></span>
                    <?php endif; ?>

                    <?php if ($organizer->contact->address2): ?>
                        <span class="line"><?= $organizer->address2 ?></span>
                    <?php endif; ?>

                    <?php if ($organizer->contact->address3): ?>
                        <span class="line"><?= $organizer->address3 ?></span>
                    <?php endif; ?>

                    <?php if ($organizer->contact->address4): ?>
                        <span class="line"><?= $organizer->contact->address4 ?></span>
                    <?php endif; ?>
                </address>
            <?php endif; ?>

            <?= View::factory('frontend/snippets/widget_contact_details')->set(array(
                'contact_text' => __('Contact Organiser'),
                'email'        => $organizer->email,
                'type'         => 'organizer',
                'website'      => $organizer->website,
            )); ?>

            <?= View::factory('frontend/snippets/widget_social_media')->set('social_media', $organizer->get_social_media()); ?>
        </div>
    </div>

    <?php if ( ! empty($events)): ?>
        <div class="row text-center">
            <h2 class="feed-heading"><?= __('Upcoming events') ?></h2>
        </div>

        <input type="hidden" id="search-results-organizer_id" value="<?= $organizer->contact_id ?>" />

        <div class="row" id="course-results">
            <?php
            echo View::factory('front_end/course_feed_items_snippet')
                ->set('display_mode', 'list')
                ->set('events_only',  true)
                ->set('search',       $search);
            ?>
        </div>
    <?php endif; ?>

    <?php ob_start(); ?>
        <form action="/frontend/events/contact" method="post" class="validate-on-submit">
            <input type="hidden" name="organiser_id" value="<?=$organizer->contact_id ?>" />

            <div class="form-group">
                <label class="col-sm-4" for="modal--contact_organizer-name">
                    <?= __('Name') ?>
                </label>

                <div class="col-sm-8">
                    <?= Form::ib_input(null, 'name', null, array('id' => 'modal--contact_organizer-name')); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4" for="modal--contact_organizer-email">
                    <?= __('Email') ?>
                </label>

                <div class="col-sm-8">
                    <?= Form::ib_input(null, 'email', null, array('class' => 'validate[required,custom[email]]', 'id' => 'modal--contact_organizer-email')); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4" for="modal--contact_organizer-telephone">
                    <?= __('Telephone') ?>
                </label>

                <div class="col-sm-8">
                    <?= Form::ib_input(null, 'telephone', null, array('id' => 'modal--contact_organizer-telephone')); ?>
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

            <?php if (Settings::instance()->get('captcha_enabled')): ?>
                <div class="mx-auto mb-3" style="max-width: 300px;">
                    <?php
                    require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                    $captcha_public_key = Settings::instance()->get('captcha_public_key');
                    echo recaptcha_get_html($captcha_public_key);
                    ?>
                </div>
            <?php endif; ?>

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
<?php endif; ?>
<?php require_once Kohana::find_file('views', 'footer'); ?>
