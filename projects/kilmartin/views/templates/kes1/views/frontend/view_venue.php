<?php require_once Kohana::find_file('template_views', 'header'); ?>

<?php if (!$venue_object->id): ?>
    <div class="row">
        <div class="page-content">
            <p><?= __('No venue found.') ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="row form-row">
        <?php if ($venue_object->get_image()): ?>
            <img src="<?= $venue_object->get_image() ?>" alt="" style="display: block; width: 100%;" />
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="row gutters">
            <div class="col-sm-4">
                <div class="page-content">
                    <h1><?= $venue_object->name ?></h1>
                </div>

                <address>
                    <?php if (trim($venue_object->address_1)): ?>
                        <span class="line"><?= $venue_object->address_1 ?></span>
                    <?php endif; ?>
                    <?php if (trim($venue_object->address_2)): ?>
                        <span class="line"><?= $venue_object->address_2 ?></span>
                    <?php endif; ?>
                    <?php if (trim($venue_object->address_3)): ?>
                        <span class="line"><?= $venue_object->address_3 ?></span>
                    <?php endif; ?>
                    <?php if (trim($venue_object->city)): ?>
                        <span class="line"><?= $venue_object->city ?></span>
                    <?php endif; ?>
                    <?php if ( ! empty($venue_object->county)): ?>
                        <span class="line"><?= $venue_object->county ?></span>
                    <?php endif; ?>
                    <?php if ( ! empty($venue_object->country)): ?>
                        <span class="line"><?= $venue_object->country ?></span>
                    <?php endif; ?>
                </address>

                <?php if (trim($venue_object->telephone)): ?>
                    <p><strong>Telephone</strong> <?= $venue_object->telephone ?></p>
                <?php endif; ?>

                <?= View::factory('frontend/snippets/widget_contact_details')->set(array(
                    'contact_text' => __('Contact Venue'),
                    'email'        => $venue_object->email,
                    'type'         => 'venue',
                    'vertical'     => true,
                    'website'      => $venue_object->website,
                )); ?>

                <?= View::factory('frontend/snippets/widget_social_media')->set('social_media', $venue_object->get_social_media()); ?>
            </div>

            <div class="col-sm-8 venue-map-block">
                <div id="event-map"
                     style="width: 100%; height: 300px;"
                     data-target-x="#edit-event-venue-lat"
                     data-target-y="#edit-event-venue-lng"
                     data-init-x="<?= $venue_object->map_lat ?>"
                     data-init-y="<?= $venue_object->map_lng ?>"
                     data-init-z="10"
                     data-button="#get-address-from-map"
                     data-button-target="#edit-event-venue-eircode"
                    >
                </div>
            </div>
        </div>

        <h1 class="feed-heading text-center"><?= __('Upcoming events') ?></h1>

        <input type="hidden" id="search-results-venue_id" value="<?= $venue_object->id ?>" />

        <div class="row" id="course-results">
            <?php
            echo View::factory('front_end/course_feed_items_snippet')
                ->set('display_mode', 'list')
                ->set('events_only', true)
                ->set('search', $search);
            ?>
        </div>
    </div>

    <?= View::factory('frontend/snippets/contact_venue_modal')->set('venue_id', $venue_object->id) ?>

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= Settings::instance()->get('google_map_key') ?>"></script>
<?php endif; ?>

<?php require_once Kohana::find_file('views', 'footer'); ?>
