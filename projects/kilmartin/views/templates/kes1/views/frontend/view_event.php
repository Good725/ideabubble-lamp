<?php require_once Kohana::find_file('template_views', 'header');?>
<?= isset($alerts) ? $alerts : '' ?>

<div class="row"><?php include Kohana::find_file('views', 'checkout_progress'); ?></div>

<?php if (!$event_object->id || $event_object->has_ended()): ?>
    <div class="row">
        <div class="page-content">
            <p><?= __('No event found.') ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="row event-details" id="event-details">
        <div class="event-left">
            <div class="page-content">
                <?php $image = $event_object->get_image(array('placeholder' => false)); ?>

                <?php if ($image): ?>
                    <div class="form-row hidden--tablet hidden--desktop fullwidth--mobile">
                        <img src="<?= $image ?>" alt="" />
                    </div>
                <?php endif; ?>

                <h1><?= html::entities($event_object->name) ?></h1>

                <div class="event-description toggleable_height show_less" id="event-description">
                    <?= $event_object->description ?>
                </div>

                <div class="toggleable_height-toggles">
                    <button type="button" class="button--link toggleable_height-show_more"><?= __('Show more') ?></button>
                    <button type="button" class="button--link toggleable_height-show_less"><?= __('Show less') ?></button>
                </div>

                <?php if ($event_object->venue->id): ?>
                    <div class="event-venue">
                        <header>
                            <p><?= __('Where') ?></p>
                            <h1><?= $event_object->venue->name ?></h1>
                        </header>

                        <?php if (trim($event_object->venue->telephone)): ?>
                            <p><?= __('Phone: $1', array('$1' => $event_object->venue->telephone)) ?></p>
                        <?php endif; ?>

                        <address>
                            <?php if (trim($event_object->venue->address_1)): ?>
                                <span class="line"><?= $event_object->venue->address_1 ?></span>
                            <?php endif; ?>
                            <?php if (trim($event_object->venue->address_2)): ?>
                                <span class="line"><?= $event_object->venue->address_2 ?></span>
                            <?php endif; ?>
                            <?php if (trim($event_object->venue->city)): ?>
                                <span class="line"><?= $event_object->venue->city ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty($event_object->venue->county)): ?>
                                <span class="line">Co. <?= $event_object->venue->county ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty($event_object->venue->country) && trim($event_object->venue->country) != 'Ireland'): ?>
                                <span class="line"><?= $event_object->venue->country ?></span>
                            <?php endif; ?>
                        </address>

                        <?php if ($event_object->display_map): ?>
                            <div class="map-event">
                                <div id="event-map"
                                     style="width: 100%; height: 230px;"
                                     data-init-x="<?= $event_object->venue->map_lat ?>"
                                     data-init-y="<?= $event_object->venue->map_lng ?>"
                                     data-init-z="10"
                                    ></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($event_object->age_restriction > 0): ?>
                    <header>
                        <p><?= __('Age restriction') ?></p>
                        <h2>+<?=$event_object->age_restriction ?></h2>
                    </header>
                <?php endif; ?>

                <?php $dates = $event_object->get_dates(); ?>

                <?php if ($event_object->display_start || $event_object->display_end): ?>
                    <?php
                    if (!empty($dates)) {
                        $tzlist = array(
                            "-1" => "(UTC-01:00) Azores Time Zone",
                            "0" => "(UTC+00:00) Western European Time Zone",
                            "1" => "(UTC+00:00) Irish Time Zone",
                            "2" => "(UTC+00:00) Greenwich Mean Time Zone",
                            "3" => "(UTC+01:00) Central European Time Zone",
                            "4" => "(UTC+02:00) Eastern European Time Zone",
                            "5" => "(UTC+03:00) Moscow Time Zone",
                            "6" => "(UTC+03:00) Further-eastern European Time",
                        );
                    }
                    ?>

                    <header>
                        <p><?= __('When') ?></p>
                        <?php if ($event_object->display_timezone): ?>
                            <p><?= $tzlist[$event_object->timezone] ?></p>
                        <?php endif; ?>

                        <?php foreach ($dates as $date): ?>
                            <h1>
                                <?php if ($event_object->display_start && $event_object->display_end) { ?>
                                    <?= date('F j, g:ia', strtotime($date['starts'])) . ($date['ends'] ? ' - ' . date('F j, g:ia', strtotime($date['ends'])) : '') ?>
                                <?php } else if ($event_object->display_end && $date['ends']) { ?>
                                    <?= __('Ends: ') . date('F j, g:ia', strtotime($date['ends'])) ?>
                                <?php } else { ?>
                                    <?= date('F j, g:ia', strtotime($date['starts'])) ?>
                                <?php } ?>
                            </h1>
                        <?php endforeach; ?>
                    </header>
                <?php endif; ?>

                <?php if ($event['display_othertime'] == 1 && isset($event['other_times']->title) && count($event['other_times']->title) > 0 && @$event['other_times']->title[0]) { ?>
                    <header>
                        <p><?= __('Other Times') ?></p>

                        <?php foreach ($event['other_times']->title as $otIndex => $otherTime): ?>
                            <h1><?= __($otherTime) . ': ' . date('g:ia', strtotime($event['other_times']->time[$otIndex])) ?></h1>
                        <?php endforeach; ?>
                    </header>

                <?php } ?>

                <?php $videos = $event_object->get_videos(array('youtube', 'vimeo')); ?>

                <?php if (count($videos) > 0): ?>
                    <?php // If there is exactly one, video, do not show this section on desktop. The video is visible on the banner. ?>
                    <div class="event-videos<?= (count($videos) == 1) ? 'hidden--tablet hidden--desktop' : '' ?>">
                        <header>
                            <p><?= __('Videos') ?></p>
                        </header>

                        <div class="row gutters">
                            <?php foreach ($videos as $key => $video): ?>
                                <?php // Do not show the first video on desktop. It is already visible in the banner. ?>
                                <div class="col-sm-6<?= ($key == 0) ? ' hidden--tablet hidden--desktop' : '' ?>">
                                    <div class="video-wrapper" data-provider="<?= $video['provider'] ?>">
                                        <iframe src="<?= $video['embed_url'] ?>" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            $organizer = $event_object->get_primary_organizer();
            $venue = $event_object->venue;
            ?>

            <?php if ($organizer->contact_id || $venue->id): ?>
                <div class="row gutters event-organizer_and_venue">
                    <div class="col-sm-6">
                        <?php include 'snippets/organizers_widget.php'; ?>
                    </div>

                    <div class="col-sm-6">
                        <?php include 'snippets/venue_widget.php'; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php $related_events = $event_object->get_related_events(); ?>

            <?php if (count($related_events)): ?>
                <div class="event-related">
                    <h2 class="event-related-title"><?= __('You might also like') ?></h2>

                    <div class="row gutters">
                        <?php foreach ($related_events as $related): ?>
                            <div class="col-sm-4">
                                <a href="<?= $related->get_url() ?>">
                                    <img src="<?= $related->get_image(array('placeholder' => true)) ?>" />
                                    <p><strong class="event-related-event-name"><?= $related->name ?></strong></p>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="event-right" id="fixed_sidebar-positioner">
            <div class="page-content event-details-back">
                <p><a class="event-details-back-link" href="<?= $event_object->get_search_url(true) ?>"><?= __('Back to search results')?></a></p>
            </div>

            <div class="fixed_sidebar-wrapper" id="fixed_sidebar-wrapper">
                <div class="fixed_sidebar" id="fixed_sidebar">
                    <?php
                    $ticket_widget_display = 'desktop';
                    include 'snippets/ticket_widget.php';
                    ?>
                </div>
            </div>
        </div>

        <div class="quick_contact hidden--tablet hidden--desktop">
            <p class="text-center">
                <?= __('From $1 per ticket', array(
                    '$1' => '<strong>'.$event['from_price_currency'].number_format($event['from_price'], 2).'</strong>'
                )) ?>
            </p>

            <div class="form-row">
                <div class="col-xs-12">
                    <button
                        type="button"
                        class="button button--full text-uppercase"
                        data-hide_toggle="#event-details .widget--tickets"
                        data-hide_toggle-class="hidden--mobile"
                        ><?= __('Tickets') ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php if ( ! empty($event['venue']) && $event['display_map']): ?>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= Settings::instance()->get('google_map_key') ?>"></script>
    <?php endif; ?>
    <script>
        window.currencies = <?=json_encode(Model_Currency::getCurrencies(true))?>;
        <?php if ($preview) { ?>
        try {
            window.opener.document.getElementById("edit-event-id").value = <?=$event['id']?>;
        } catch (exc) {
            console.log(exc);
        }
        <?php } ?>
        $(".event-book[value=buy]").on("click", function(ev){
            if ($(this).parents('form').validationEngine('validate'))
            {
                var $form = $('.checkout_form:visible');
                var validate = false;
                $form.find('.form_field').each(function(){
                    var res=$(this).val();
                    ev.preventDefault();
                    if(res > 0 && res != null){
                        validate = true;
                    }
                });
                if(!validate){
                    ev.preventDefault();
                    $form.find('.qty_error').show();
                }else{
                    $form.submit();
                }
            }else{
                ev.preventDefault();
            }
        });
        $(".ticket_error").on("click", function(){
            $(this).hide();
        });
        $('.checkout_form .form_field').on('click keyup', function(e){
            var $form = $(this).parents('form');
            $form.find('.qty_error').hide();
            var remaining_tckt=$(this).attr('data-remaining');
            var selcted_tckt=$(this).val();
            var max_tckt=$(this).attr('max');
            if(parseInt(selcted_tckt) > parseInt(remaining_tckt)){
                $(this).parent('.ticket-val').parent('.ticket-container').prev(".ticket_error").show();
                $(this).val(max_tckt);
            }else if((parseInt(max_tckt) != 0) && (parseInt(selcted_tckt) > parseInt(max_tckt))){
                $(this).parent('.ticket-val').parent('.ticket-container').prev(".ticket_error").html('Max Limit Reached For This Ticket').show();
                $(this).val(max_tckt);
            }else{
                $(this).parent('.ticket-val').parent('.ticket-container').prev(".ticket_error").hide();
            }
        });

        $('.multiple_payers_quantity').on('change', function() {
            if ($(this).is(':checked')) {
                $('.multiple_payers_quantity').prop('checked', false);
                $(this).prop('checked', true);
            }
        });
    </script>
<?php endif; ?>
<?php require_once Kohana::find_file('views', 'footer'); ?>
